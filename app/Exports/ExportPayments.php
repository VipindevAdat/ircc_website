<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\PaymentCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ExportPayments implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;

        $from = isset($request->date_from) ? Carbon::parse($request->date_from)->format('Y-m-d') : null;
        $to = isset($request->date_to) ? Carbon::parse($request->date_to)->format('Y-m-d') : null;

        $query = Payment::query();

        // Apply filters based on request parameters
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($from && $to) {
            $query->whereBetween('payment_date', [$from, $to])
                  ->orWhereBetween('transaction_date', [$from, $to]);
        }

        $payments = $query->with('registration')->orderBy('id', 'DESC')->get();

        $paymentCollection = [];

        foreach($payments as $payment){
            $category = PaymentCategory::where('category_id', $payment->payment_category)->first();
            $paymentCollection[] = [
                'payment_order_id' => $payment->payment_order_id,
                'bdOrderId' => $payment->bdOrderId,
                'registration_number' => $payment->registration ? $payment->registration->registration_number : '',
                'payment_mode' => $payment->payment_mode,
                'payment_mode_type' => $payment->payment_mode_type,
                'payment_date' => $payment->payment_mode === 'Direct Bank Transfer' 
                    ? Carbon::parse($payment->payment_date)->format('d-M-Y H:i A') 
                    : Carbon::parse($payment->transaction_date)->format('d-M-Y H:i A'),
                'payment_amount' => number_format($payment->payment_mode === 'Direct Bank Transfer' ? $payment->amount : $payment->paid_amount, 2),
                'payment_status' => $payment->status->status ?? 'Pending',
                'payment_category' => $category ? $category->category : 'Category not found',
                'payment_reference' => $payment->payment_reference,
                'currency' => $payment->currency,
                'email' => $payment->email,
                'transaction_id' => $payment->transaction_id,
                'transaction_error_type' => $payment->transaction_error_type,
                'transaction_error_code' => $payment->transaction_error_code,
                'transaction_error_description' => $payment->transaction_error_desc,
                'auth_status' => $payment->auth_status,
                'bank_reference_number' => $payment->bank_reference_number,
                'bank_id' => $payment->bank_id,
            ];
        }

        return collect($paymentCollection);
    }

    public function headings(): array
    {
        return [
            ['Payments List'],
            [
                'Payment Order ID',
                'BdOrder Id',
                'Registration Number',
                'Payment Mode',
                'Payment Mode Type',
                'Payment Date',
                'Payment Amount',
                'Payment Status',
                'Payment Category',
                'Payment Reference',
                'Currency',
                'Email',
                'Transaction Id',
                'Transaction Error Type',
                'Transaction Error Code',
                'Transaction Error Description',
                'Auth Status',
                'Bank Reference Number',
                'Bank Id',
            ]
        ];
    }

    public function title(): string
    {
        return 'Payments Export';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->getRowDimension(1)->setRowHeight(30);
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'align' => 'center'], 'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],],
            2 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
