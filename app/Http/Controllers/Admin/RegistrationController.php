<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Registration;
use App\Models\Participant;
use App\Models\AbstractSubmission;
use App\Models\Setting;
use App\Models\ReviewStatus;
use App\Models\PaymentCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\Register;
use App\Mail\AdminRegister;
use App\Exports\ExportParticipants;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\GivebutterHelper;
use App\Support\PaymentCoupon;
use Illuminate\Support\Facades\Schema;

class RegistrationController extends Controller
{
    use GivebutterHelper;

    public function participants(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $paymentStatus = trim((string) $request->get('payment_status', ''));
        $hearAboutFilter = trim((string) $request->get('hear_about_us', ''));

        $participantsQuery = Participant::query()
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
            ->orderByDesc('id');

        $participantsCollection = $participantsQuery->get()->map(function ($participant) {
            $payment = $this->user_payments($participant->registration_id);
            $amountPayable = (float) str_replace(',', '', $payment['amountPayable'] ?? '0.00');
            $participant->payment_status_label = $amountPayable > 0 ? 'Unpaid' : 'Paid';
            return $participant;
        });

        if (in_array($paymentStatus, ['paid', 'unpaid'], true)) {
            $participantsCollection = $participantsCollection->filter(function ($participant) use ($paymentStatus) {
                return $paymentStatus === 'paid'
                    ? $participant->payment_status_label === 'Paid'
                    : $participant->payment_status_label === 'Unpaid';
            })->values();
        }

        // Filter by "How did you hear about us?" (from registration, case-insensitive)
        if ($hearAboutFilter !== '') {
            $filterValue = strtolower($hearAboutFilter);

            $participantsCollection = $participantsCollection->filter(function ($participant) use ($filterValue) {
                $value = strtolower(trim((string) optional($participant->registration)->hear_about_us));

                // Match exact option ignoring case (MANCA, GYRA, STEM4Girls, Bay Area Champions, PTA, Other)
                return $value !== '' && $value === $filterValue;
            })->values();
        }

        // Fixed list of "How did you hear about us?" options (same as registration form)
        $hearAboutOptions = collect([
            'MANCA',
            'GYRA',
            'STEM4Girls',
            'Bay Area Champions',
            'PTA',
            'Other',
        ]);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 25;
        $participants = new LengthAwarePaginator(
            $participantsCollection->slice(($page - 1) * $perPage, $perPage)->values(),
            $participantsCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.registration.participants', compact('participants', 'search', 'paymentStatus', 'hearAboutFilter', 'hearAboutOptions'));
    }

    public function exportParticipants(Request $request)
    {
        $fileName = 'participants-' . date('Y-m-d-h-i-s') . '.xls';
        return Excel::download(new ExportParticipants($request), $fileName);
    }

    public function index(Request $request, Datatables $datatables)
    {
        
        if ($request->ajax()) {
            $registrations = Registration::withCount('participants')->with('abstract.abstractReviewer')
                ->orderBy('id', 'DESC');

            return $datatables->eloquent($registrations)
                ->editColumn('created_at', function ($query) {
                    return date('Y-m-d', strtotime($query->created_at)) . '<br>' . date('H:i:s', strtotime($query->created_at));
                })
                ->editColumn('email', function (Registration $registration) {
                    return wordwrap($registration->email, 10, '<br>');
                })
                ->editColumn('abstract_status', function (Registration $registration) {

                    return $registration->abstract()->exists()? '<span class="badge bg-success">Abstract Submitted</span>' : '<span class="badge bg-warning">Not Submitted</span>';
                   
                })
                ->addColumn('participants_count', function (Registration $registration) {
                    return $registration->participants_count ?? 0;
                })
                ->addColumn('payment_status', function (Registration $registration) {
                    $payment = $this->user_payments($registration->id);
                    $paidAmount = (float) str_replace(',', '', $payment['paidAmount'] ?? '0.00');
                    $amountPayable = (float) str_replace(',', '', $payment['amountPayable'] ?? '0.00');
                    
                    if ($paidAmount == 0 && $amountPayable == 0) {
                        $paymentStatus = 'Pending';
                    } elseif ($amountPayable > 0) {
                        $paymentStatus = 'Pending';
                    } else {
                        $paymentStatus = 'Completed';
                    }
                    
                    return $paymentStatus == 'Completed' 
                        ? '<span class="badge bg-success">' . $paymentStatus . '</span>' 
                        : '<span class="badge bg-warning">' . $paymentStatus . '</span>';
                })
                ->orderColumn('payment_status', function ($query, $order) {
                    $adultFee = (float) (optional(Setting::where('code', 'adult_fee')->first())->value ?? 50);
                    $query->reorder()->orderByRaw("
                        (CASE 
                            WHEN COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                                           FROM payments p 
                                           WHERE p.registration_id = registrations.id 
                                           AND p.payment_status = 3), 0) > 0
                                 AND COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                                               FROM payments p 
                                               WHERE p.registration_id = registrations.id 
                                               AND p.payment_status = 3), 0) >= 
                                     COALESCE((SELECT SUM(CASE WHEN par.role = 'adult' THEN {$adultFee} ELSE COALESCE(registrations.student_participant_unit_price, 150) END) 
                                               FROM participants par 
                                               WHERE par.registration_id = registrations.id 
                                               AND par.type <> 2), 0)
                            THEN 1
                            ELSE 0
                        END) {$order}
                    ");
                })                
                ->addColumn('review_status', function (Registration $registration) {
                    if($registration->abstract()->exists()){
                        if($registration->abstract->abstractReviewer()->exists()){
                            $reviewer = $registration->abstract?->abstractReviewer;
                            if ($reviewer && $reviewer->review_status) {
                                $status = ReviewStatus::find($reviewer->review_status);
                                return $status ? ($status->status=='Approved'?'<span class="badge bg-success">'.$status->status.'</span>':($status->status=='Rejected'?'<span class="badge bg-danger">'.$status->status.'</span>':'<span class="badge bg-warning">'.$status->status.'</span>' )): 'Reviewer Not Assigned';
                            }

                        }
                    }
                                    
                
                    return '<span class="badge bg-info">Unknown</span>';
                })
//                ->editColumn('category', function (Registration $registration) {
//                    return wordwrap($registration->abstract->category, 10, '<br>');
//                })
                ->editColumn('phone', function (Registration $registration) {
                    return wordwrap($registration->phone, 10, '<br>');
                })
                ->addColumn('hear_about_us', function (Registration $registration) {
                    return $registration->hear_about_us ? wordwrap($registration->hear_about_us, 20, '<br>') : '--';
                })
                ->addColumn('name', function (Registration $registration) {
                    return wordwrap($registration->first_name.' '.$registration->last_name, 15, '<br>');
                })
                ->addColumn('action', function (Registration $registration) {
                    // return '<a data-id="' . $registration->id . '" data-toggle="modal" data-target="#detailModal" id="registerDetail" style="cursor: pointer;">
                    //         <i class="bi bi-eye-fill"></i></a>';
                    return '<a href="' . route('admin.registration.show', $registration->id) . '" id="registerDetail" style="cursor: pointer;">
                    <i class="bi bi-eye-fill"></i></a>
                    <a href="javascript:void(0)" data-id="'. $registration->id .'" data-href="' . route("admin.registration.destroy", $registration->id) . '"   data-bs-toggle="modal" data-bs-target="#delete-modal" 
                    class="btn btn-sm btn-clean btn-icon delete-registration" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    ';
                })
                ->rawColumns(['action', 'email', 'phone', 'created_at', 'name','abstract_status','payment_status','review_status','hear_about_us'])
                ->make(true);
        }

        return view('admin.registration.list')->with(['type' => 'Register']);
    }
    public function show(Registration $registration)
    {
       
        $registration_id = $registration->id;
        $payments = Payment::where('registration_id', $registration_id)->orderBy('id','desc')->get();
        $abstracts = AbstractSubmission::with(['category', 'abstractReviewer.reviewStatus', 'abstractDomain'])
            ->where('registration_id', $registration->id)
            ->orderBy('id')
            ->get();

        $payment=$this->user_payments($registration->id);
        $paymentStatus=$payment['amountPayable']>0?'Payment Pending':'Payment Completed';
        if ($abstracts->isEmpty()) {
            $abstractStatus = $payment['amountPayable'] > 0 ? 'Payment Pending' : 'Abstract not Submitted';
            $abstractBadgeSuccess = false;
        } else {
            $draftCount = $abstracts->filter(fn ($a) => (int) $a->status === 0)->count();
            $submittedCount = $abstracts->filter(fn ($a) => (int) $a->status === 1)->count();
            $abstractBadgeSuccess = $draftCount === 0 && $submittedCount > 0;
            if ($draftCount === 0) {
                $abstractStatus = $submittedCount > 1
                    ? $submittedCount.' abstracts submitted'
                    : 'Abstract Submitted';
            } elseif ($submittedCount === 0) {
                $abstractStatus = $draftCount > 1
                    ? $draftCount.' abstract drafts'
                    : 'Abstract draft';
            } else {
                $abstractStatus = $submittedCount.' submitted, '.$draftCount.' draft';
            }
        }
        return view('admin.registration.detail')->with([
            'registration'=>$registration,
            'payment'=>$payment,
            'payments'=>$payments,
            'abstracts'=>$abstracts,
            'abstractStatus'=>$abstractStatus,
            'abstractBadgeSuccess'=>$abstractBadgeSuccess,
            'paymentStatus'=>$paymentStatus
        ]);
    }

    public function downloadPdf($abstractId)
    {
         $abstract = AbstractSubmission::findOrFail($abstractId);

        $filePath = public_path('uploads/abstract/' . $abstract->abstract_file);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return abort(404, 'File not found');
        }
    }

    public function getRegistrationById(Request $request)
    {
        $id = $request->id;
        $registration = Registration::where('id', $id)->first();
        $participants = Participant::where('registration_id', $registration->id)->get();
        $abstract = AbstractSubmission::where('registration_id', $registration->id)->first();

        $abstractStatus = $abstract ? $abstract->status : 'Not Submitted';

        $payments = Payment::where('registration_id', $registration->id)->get();
        $amountPerParticipant = 500;
        $totalAmountPaid = $payments->sum('amount');
        $totalAmountPayable = $participants->count() * $amountPerParticipant;
        $amountPending = $totalAmountPayable - $totalAmountPaid;


        $type = 'register';
        $div = view('admin.components.registration_details_admin', compact([
            'registration', 'type', 'participants', 'abstract',
            'abstractStatus', 'payments', 'amountPending','totalAmountPaid'
        ]))->render();

        $registration['html'] = $div;
        echo json_encode($registration);
    }



    public function editRegistration(Request $request)
    {
        $id = $request->id;
        $registration = Registration::with(['participants', 'abstract'])->where('id', $id)->first();

        $div = view('admin.components.registration-detail', compact('registration'))->render();
        $registration['html'] = $div;

        echo json_encode($registration);
    }

    public function updateRegistration(Request $request)
    {
        $registration = Registration::whereId($request->registrationId)->first();
        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found'], 404);
        }

        // Update registration details
        $registration->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'institution_name' => $request->institution_name ?? '-',
        ]);

        // Update participants if provided
        if (!empty($request->participants)) {
            foreach ($request->participants as $participantData) {
                $role = $participantData['role'] ?? 'student';
                $type = ($role === 'adult') ? 1 : 0;

                if (!empty($participantData['id'])) {
                    // Update existing participant
                    $participant = Participant::where('id', $participantData['id'])
                        ->where('registration_id', $registration->id)
                        ->first();
                    if ($participant) {
                        $participant->update([
                            'first_name' => $participantData['first_name'],
                            'last_name' => $participantData['last_name'],
                            'email' => $participantData['email'] ?? null,
                            'role' => $role,
                            'type' => $type,
                            'age' => $participantData['age'] ?? null,
                            'class' => $participantData['class'] ?? null,
                        ]);
                    }
                } else {
                    // Create new participant
                    Participant::create([
                        'registration_id' => $registration->id,
                        'first_name' => $participantData['first_name'],
                        'last_name' => $participantData['last_name'],
                        'email' => $participantData['email'] ?? null,
                        'role' => $role,
                        'type' => $type,
                        'age' => $participantData['age'] ?? null,
                        'class' => $participantData['class'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Registration updated successfully.']);
    }

    public function deleteParticipant(Request $request)
    {
        $participant = Participant::where('id', $request->id)->first();
        if (!$participant) {
            return response()->json(['success' => false, 'message' => 'Participant not found'], 404);
        }

        $registrationId = $participant->registration_id;
        $participant->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Participant deleted successfully.',
            'participants_count' => Participant::where('registration_id', $registrationId)->where('type', '<>', 2)->count()
        ]);
    }

    public function destroy(string $id)
    {
        $registration = Registration::where('id', $id)->first();
        $registration->abstract()->delete();
        $registration->delete();

        return response()->json(["success" => "Registration deleted successfully"], 200);
    }

    public function registerSendMail($id)
    {
        $registration = Registration::with(['participants', 'abstract'])->where('id', $id)->first();
        Mail::to($registration->email, $registration->first_name)->send(new Register($registration));
        Mail::to('admin@example.com')->cc(['contact@example.com'])->send(new AdminRegister($registration));

        return redirect()->route('admin.registration.registration')->with(['success_message' => $registration->id . ' Register mail sent successfully.']);
    }
    public function addPayment(Request $request)
    {
        $registration = Registration::where('id', $request->registration_id)->first();
        $count = Payment::where('registration_id', $request->registration_id)->count();
        $slug = strtoupper(Str::slug($registration->registration_number));
        $paymentOrderId = $count ? "{$slug}-{$count}" : $slug;
        $participantsCount = Participant::where('registration_id', $registration->id)->count();
        $paidParticipantsCount = Payment::where('registration_id', $registration->id)->where('payment_status', 3)->sum('participants');
        $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value;
        $payment_summary = $this->user_payments($registration->id);
        $isFirstPayment = Payment::where('registration_id', $registration->id)->where('payment_status', 3)->doesntExist();

        $request->validate([
            'payment_reference' => 'required|unique:payments,payment_reference',
            'payment_date' => 'required|date',
            'payment_amount' => 'nullable|string|max:32',
            'coupon_code' => 'nullable|string|max:64',
        ]);

        $baseAmountStr = $request->input('payment_amount');
        if ($baseAmountStr !== null && trim((string) $baseAmountStr) !== '') {
            $baseAmount = (float) str_replace(',', '', (string) $baseAmountStr);
        } else {
            $baseAmount = (float) str_replace(',', '', $payment_summary['amountPayable']);
        }

        if (! is_finite($baseAmount) || $baseAmount < 0) {
            return redirect()->back()->withErrors(['payment_amount' => 'Enter a valid amount.'])->withInput();
        }

        $couponInput = trim((string) $request->input('coupon_code', ''));
        if (! PaymentCoupon::isValid($couponInput)) {
            return redirect()->back()->withErrors(['coupon_code' => 'Invalid coupon code.'])->withInput();
        }

        $studentPayable = (float) str_replace(',', '', $payment_summary['studentAmountPayable'] ?? '0');
        $adultPayable = (float) str_replace(',', '', $payment_summary['adultAmountPayable'] ?? '0');
        $explicitNoCouponTotal = $couponInput === '' ? $baseAmount : null;

        try {
            $applied = PaymentCoupon::apply($couponInput, $studentPayable, $adultPayable, (int) $registration->id, $explicitNoCouponTotal);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['coupon_code' => $e->getMessage()])->withInput();
        }

        $amount = $applied['amount'];
        $discountAmount = $applied['discount_amount'];
        $originalAmount = $applied['original_amount'];
        $couponStored = $applied['coupon_code'];

        $registrationFeeVal = ($registrationFee !== null && $registrationFee !== '')
            ? (float) $registrationFee
            : 0.0;

        if ($isFirstPayment) {
            $participantFee = max(0, $amount - $registrationFeeVal);
        } else {
            $participantFee = $amount;
            $registrationFee = null;
        }

        $paymentRow = [
            'payment_order_id' => $paymentOrderId,
            'registration_id' => $registration->id,
            'payment_reference' => $request->payment_reference,
            'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
            'participants' => $participantsCount - $paidParticipantsCount,
            'amount' => $amount,
            'payment_mode' => 'Direct Bank Transfer',
            'registration_fee' => $registrationFee,
            'participant_fee' => $participantFee,
            'payment_amount' => $amount,
            'paid_amount' => $amount,
            'currency' => 'INR',
            'customer_name' => $registration->first_name.' '.$registration->last_name,
            'mobile' => $registration->phone,
            'email' => $registration->email,
            'payment_status' => 3,
            'payment_status_name' => 'Completed',
        ];

        try {
            if (Schema::hasColumn('payments', 'coupon_code')) {
                $paymentRow['coupon_code'] = $couponStored;
            }
            if (Schema::hasColumn('payments', 'discount_amount')) {
                $paymentRow['discount_amount'] = $discountAmount > 0 ? $discountAmount : null;
            }
            if (Schema::hasColumn('payments', 'original_amount')) {
                $paymentRow['original_amount'] = $originalAmount;
            }
        } catch (\Exception $e) {
            \Log::warning('Admin addPayment: could not attach coupon columns', ['error' => $e->getMessage()]);
        }

        Payment::create($paymentRow);

        return redirect()->route('admin.registration.show', $registration->id)->with(['success_message' => 'Payment updated successfully.']);
    }
    // Removed old user_payments method - now using GivebutterHelper trait's method
    // The trait's user_payments method uses Registration::studentUnitPriceForNewAccount() for student rate (adults from adult_fee setting)
    // and doesn't add a separate registration fee

    public function paymentView(Request $request)
{
    $paymentDetails = Payment::find($request->id);

    if (!$paymentDetails) {
        return response()->json(['error' => 'Payment not found'], 404);
    }

    $category = PaymentCategory::where('category_id', $paymentDetails->payment_category)->first();

    $paymentData = [
        'payment_order_id' => $paymentDetails->payment_order_id,
        'bdOrderId' => $paymentDetails->bdOrderId,
        'transaction_id' => $paymentDetails->transaction_id,
        'transaction_error_type' => $paymentDetails->transaction_error_type,
        'transaction_error_code' => $paymentDetails->transaction_error_code,
        'transaction_error_description' => $paymentDetails->transaction_error_desc,
        'auth_status' => $paymentDetails->auth_status,
        'payment_mode' => $paymentDetails->payment_mode,
        'payment_mode_type' => $paymentDetails->payment_mode_type,
        'payment_category' => $category ? $category->category : 'Category not found',
        'amount' => $paymentDetails->amount,
        'payment_reference' => $paymentDetails->payment_reference,
        'currency' => $paymentDetails->currency,
        'email' => $paymentDetails->email,
        'payment_status_id' => $paymentDetails->payment_status,
        'payment_status' => $paymentDetails->status->status,
        'bank_reference_number' => $paymentDetails->bank_reference_number,
        'bank_id' => $paymentDetails->bank_id,
        'payment_date' => Carbon::parse($paymentDetails->payment_date)->format('d M Y h:i A'),
        'transaction_date' => Carbon::parse($paymentDetails->transaction_date)->format('d M Y h:i A'),
        'comments' => $paymentDetails->comments,
        'coupon_code' => $paymentDetails->coupon_code,
        'discount_amount' => $paymentDetails->discount_amount,
        'original_amount' => $paymentDetails->original_amount,
    ];

    
    return view('admin.components.admin-payment', compact('paymentData'));
}


}
