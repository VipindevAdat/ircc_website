<?php

namespace App\Exports;

use App\Models\Participant;
use App\Traits\GivebutterHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportParticipants implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    use GivebutterHelper;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $search = trim((string) $this->request->get('search', ''));
        $paymentStatus = trim((string) $this->request->get('payment_status', ''));

        $participants = Participant::query()
            ->with('registration')
            ->where('type', '<>', 2)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('registration', function ($registrationQuery) use ($search) {
                            $registrationQuery->where('registration_number', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->get();

        $rows = $participants->map(function ($participant) {
            $payment = $this->user_payments($participant->registration_id);
            $amountPayable = (float) str_replace(',', '', $payment['amountPayable'] ?? '0.00');
            $status = $amountPayable > 0 ? 'Unpaid' : 'Paid';

            return [
                'participant_name' => trim(($participant->first_name ?? '') . ' ' . ($participant->last_name ?? '')),
                'role' => ($participant->role === 'adult' || (int) $participant->type === 1) ? 'Adult' : 'Student',
                'email' => $participant->email ?? '-',
                'age' => $participant->age ?? '-',
                'class' => $participant->class ?? '-',
                'registration_code' => optional($participant->registration)->registration_number ?? '-',
                'registration_contact' => trim((optional($participant->registration)->first_name ?? '') . ' ' . (optional($participant->registration)->last_name ?? '')),
                'registration_email' => optional($participant->registration)->email ?? '-',
                'payment_status' => $status,
            ];
        });

        if (in_array($paymentStatus, ['paid', 'unpaid'], true)) {
            $rows = $rows->filter(function ($row) use ($paymentStatus) {
                return $paymentStatus === 'paid'
                    ? $row['payment_status'] === 'Paid'
                    : $row['payment_status'] === 'Unpaid';
            })->values();
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Participant Name',
            'Role',
            'Email',
            'Age',
            'Class',
            'Registration Code',
            'Registration Contact',
            'Registration Email',
            'Payment Status',
        ];
    }

    public function title(): string
    {
        return 'Participants';
    }
}
