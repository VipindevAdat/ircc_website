<?php

namespace App\Exports;

use App\Models\Registration;
use App\Traits\GivebutterHelper;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportRegistration implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    use GivebutterHelper;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;

        $query = Registration::query()
            ->with([
                'participants',
                'abstract.abstractReviewer.reviewStatus',
                'abstract.category',
                'abstract.abstractDomain',
                'abstracts.abstractReviewer.reviewStatus',
                'abstracts.category',
                'abstracts.abstractDomain',
            ])
            ->orderByDesc('id');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', (int) $request->year);
        }

        if ($request->type === 'abstract') {
            $query->whereHas('abstracts');
        }

        $rows = [];
        foreach ($query->get() as $register) {
            $payment = $this->user_payments($register->id);
            $paidAmount = (float) str_replace(',', '', $payment['paidAmount'] ?? '0');
            $amountPayable = (float) str_replace(',', '', $payment['amountPayable'] ?? '0');
            $grandTotal = (float) str_replace(',', '', $payment['grandTotal'] ?? '0');

            $participants = $register->participants->where('type', '<>', 2);
            $studentCount = 0;
            $adultCount = 0;
            $detailParts = [];
            foreach ($participants->values() as $idx => $p) {
                $role = $p->role;
                if ($role === null || $role === '') {
                    $role = ((int) $p->type === 1) ? 'adult' : 'student';
                }
                if ($role === 'adult') {
                    $adultCount++;
                    $roleLabel = 'Adult';
                } else {
                    $studentCount++;
                    $roleLabel = 'Student';
                }
                $detailParts[] = ($idx + 1).'. '.trim(($p->first_name ?? '').' '.($p->last_name ?? '')).' ('.$roleLabel.')';
            }

            $abstracts = $request->type === 'abstract'
                ? $register->abstracts
                : collect([$register->abstract]);

            foreach ($abstracts as $abstract) {
                $reviewLabel = '';
                if ($abstract && $abstract->abstractReviewer) {
                    $reviewLabel = optional($abstract->abstractReviewer->reviewStatus)->status ?? '';
                }
                if ($abstract && $reviewLabel === '') {
                    $reviewLabel = 'Pending review';
                }

                $rows[] = [
                    $register->registration_number ?? '',
                    Carbon::parse($register->created_at)->format('d-M-Y H:i'),
                    $register->institution_name ?? '',
                    $register->first_name ?? '',
                    $register->last_name ?? '',
                    $register->email ?? '',
                    $register->phone ?? '',
                    $register->hear_about_us ?? '',
                    $participants->count(),
                    $studentCount,
                    $adultCount,
                    implode('; ', $detailParts),
                    $this->paymentStatusLabel($paidAmount, $amountPayable, $grandTotal),
                    $payment['paidAmount'] ?? '0.00',
                    $payment['amountPayable'] ?? '0.00',
                    $payment['grandTotal'] ?? '0.00',
                    $abstract ? 'Yes' : 'No',
                    $abstract ? ($abstract->pid ?? '') : '',
                    $abstract ? (string) ($abstract->abstract_title ?? '') : '',
                    $abstract ? $this->plainText($abstract->abstract_content ?? '') : '',
                    $abstract ? $this->formatReferences($abstract->references ?? null) : '',
                    $abstract ? (optional($abstract->category)->name ?? '') : '',
                    $abstract ? (optional($abstract->abstractDomain)->domain ?? '') : '',
                    $abstract ? $reviewLabel : '',
                ];
            }
        }

        return collect($rows);
    }

    /**
     * Paid: balance cleared. Not paid: balance due, no completed payments. Pending: partial payment or no fee line yet.
     */
    private function paymentStatusLabel(float $paidAmount, float $amountPayable, float $grandTotal): string
    {
        if ($amountPayable > 0.009) {
            return $paidAmount > 0.009 ? 'Pending' : 'Not paid';
        }

        if ($grandTotal <= 0.009 && $paidAmount <= 0.009) {
            return 'Pending';
        }

        return 'Paid';
    }

    private function plainText(?string $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    private function formatReferences($value): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            return collect($value)
                ->map(function ($reference) {
                    if (is_array($reference)) {
                        $reference = $reference['references'] ?? reset($reference);
                    }

                    return $this->plainText((string) $reference);
                })
                ->filter()
                ->values()
                ->implode("\n");
        }

        return $this->plainText((string) $value);
    }

    public function headings(): array
    {
        return [
            'Registration number',
            'Created date',
            'Institution name',
            'Contact first name',
            'Contact last name',
            'Email',
            'Phone',
            'How did you hear about us?',
            'Total participants',
            'Student count',
            'Adult count',
            'Participants (detail)',
            'Payment status',
            'Paid amount',
            'Amount payable',
            'Grand total',
            'Abstract submitted',
            'Abstract reference (PID)',
            'Abstract title',
            'Abstract',
            'References',
            'Abstract category',
            'Abstract topic',
            'Abstract review status',
        ];
    }

    public function title(): string
    {
        return $this->request->type === 'abstract' ? 'IRCC Abstract registrations' : 'IRCC Registrations';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
