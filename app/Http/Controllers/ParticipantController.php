<?php

namespace App\Http\Controllers;
use App\Models\AbstractDomain;
use App\Models\AbstractSubmission;
use App\Models\Category;
use App\Models\Participant;
use App\Models\ParticipantPayment;
use App\Models\Payment;
use App\Models\PaymentRefund;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Traits\GivebutterHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\Register;
use App\Models\Registration;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use GivebutterHelper;
    
    public function index()
    {
        $user = Auth::guard('customer')->user();
        $participants = Participant::where('registration_id', $user->id)->get();
        $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->get();
        $totalAmountPaid = $payments->isEmpty()?0:$payments->sum('amount');
        $participantCount = $participants->count()?$participants->count():0;
        $paidParticipantCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
        $categories=Category::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $minClass=$categories->min('grade_from');
        $maxClass=$categories->max('grade_to');
        $refund = PaymentRefund::where('registration_id', $user->id)->get();
        $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
        $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value;
        $adultFee = (float) optional(Setting::where('code', 'adult_fee')->first())->value ?? 50.00;
        $payment_summary=$this->user_payments();
        $registrationLastDate = optional(Setting::where('code', 'registration_last_date')->first())->value;        
        $refundHistory = PaymentRefund::where('registration_id', $user->id)->get();
        $abstract = AbstractSubmission::where('registration_id', $user->id)->where('status', 1)->first();
        $participant_edit = optional(Setting::where('code', 'participant_edit_status')->first())->value;
        $participant_delete = optional(Setting::where('code','participant_delete_status')->first())->value;
        $paymentApprovePending= Payment::where('registration_id', $user->id)->where('payment_mode','Direct Bank Transfer')->where('payment_status',1)->first();
        $gracePeriod=optional(Setting::where('code', 'registration_grace_period')->first())->value?optional(Setting::where('code', 'registration_grace_period')->first())->value:0;
        if($gracePeriod>0)
        $registrationLastDate=carbon::parse($registrationLastDate)->addDays($gracePeriod)->format("Y-m-d");

        return view('dashboard.participants')->with([
            'type' => 'Register',
            'categories' => Category::where('status', 1)->orderBy('sort_order', 'ASC')->get(),
            'settings' => Setting::where('status', 1)->get(),
            'user' => $user,
            'abstract'=>$abstract,
            'participants' => $participants,
            'payments' => $payments,
            'totalAmountPaid' => $totalAmountPaid,
            'participantCount' =>$participantCount,
            'paidParticipantCount' => $paidParticipantCount,
            'minClass'=>$minClass,
            'maxClass'=>$maxClass,
            'refund'=>$refund,
            'amountPerHead'=>$amountPerHead,
            'registrationFee'=>$registrationFee,
            'adultFee'=>$adultFee,
            'payment_summary'=>$payment_summary,
            'refundHistory'=>$refundHistory,
            'registrationLastDate'=>$registrationLastDate,
            'participant_edit'=>$participant_edit,
            'participant_delete'=>$participant_delete,
            'paymentApprovePending'=>$paymentApprovePending
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'participants.*.selected_type' => 'integer',
    //         'participants.*.first_name' => 'required|string|max:255',
    //         'participants.*.last_name' => 'required|string|max:255',
    //         'participants.*.class' => 'nullable|string|max:255',
    //     ]);

    //     $user = Auth::guard('customer')->user();

    //     foreach ($request->participants as $participant) {
    //         Participant::create([
    //             'type' => $participant['type'] ?? 0,
    //             'registration_id' => $user->id,
    //             'first_name' => $participant['first_name'],
    //             'last_name' => $participant['last_name'],
    //             'class' => $participant['class'] ?? null,
    //         ]);
    //     }
    //     $user = Auth::guard('customer')->user();
    //     $participants = Participant::where('registration_id', $user->id)->get();
    //     $payments = Payment::where('registration_id', $user->id)->get();
    //     $totalAmountPaid = $payments->isEmpty() ? 0 : $payments->sum('amount');
    //     $participantCount = $participants->count();
    //     $paidParticipantCount = Payment::where('registration_id', $user->id)->sum('participants');
    //     $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
    //     $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value;
    //     $payment_summary=$this->user_payments();
    //    $html= view('dashboard.payment-details')->with([
    //         'type' => 'Register',
    //         'categories' => Category::where('status', 1)->orderBy('sort_order', 'ASC')->get(),
    //         'settings' => Setting::where('status', 1)->get(),
    //         'user' => $user,
    //         'participants' => $participants,
    //         'payments' => $payments,
    //         'totalAmountPaid' => $totalAmountPaid,
    //         'participantCount' => $participantCount,
    //         'paidParticipantCount' => $paidParticipantCount,
    //         'amountPerHead'=>$amountPerHead,
    //         'registrationFee'=>$registrationFee,
    //         'payment_summary'=>$payment_summary
    //     ]);
    //     return response()
    //     ->json(["error"=>0,"success" => "Participant added successfully",'html'=>$html,'amount_payable'=>$payment_summary['amountPayable']?$payment_summary['amountPayable']:0], 200);
    // }
    public function store(Request $request)
{
    try {
        // Normalize request data: convert empty strings to null for optional fields
        $participants = $request->participants ?? [];
        foreach ($participants as $index => $participant) {
            // For students, email is optional - convert empty string to null
            if (isset($participant['role']) && $participant['role'] === 'student') {
                if (isset($participant['email']) && trim($participant['email']) === '') {
                    $participants[$index]['email'] = null;
                }
            }
            // For adults, age is optional - convert empty string to null
            if (isset($participant['role']) && $participant['role'] === 'adult') {
                if (isset($participant['age']) && trim($participant['age']) === '') {
                    $participants[$index]['age'] = null;
                }
            }
        }
        // Replace the request participants with normalized data
        $request->merge(['participants' => $participants]);
        
        // Custom validation rules
        $rules = [
            'participants.*.role' => 'required|in:student,adult',
            'participants.*.first_name' => 'required|string|max:255',
            'participants.*.last_name' => 'required|string|max:255',
            'participants.*.age' => 'nullable|integer|min:1|max:120',
            'participants.*.class' => 'nullable|string|max:255',
            'participants.*.type' => 'nullable|integer', // Keep for backward compatibility
        ];
        
        // Add conditional validation based on role
        $messages = [];
        foreach ($request->participants ?? [] as $index => $participant) {
            if (isset($participant['role'])) {
                if ($participant['role'] === 'student') {
                    $rules["participants.{$index}.age"] = 'required|integer|min:1|max:120';
                    // For students, email is optional - only validate if present and not null
                    if (isset($participant['email']) && $participant['email'] !== null && trim($participant['email']) !== '') {
                        $rules["participants.{$index}.email"] = 'email|max:255';
                    }
                } elseif ($participant['role'] === 'adult') {
                    $rules["participants.{$index}.email"] = 'required|email|max:255';
                    // For adults, age is optional
                    $rules["participants.{$index}.age"] = 'nullable|integer|min:1|max:120';
                }
            }
        }
        
        $request->validate($rules, $messages);

        $user = Auth::guard('customer')->user();
        
        if (!$user) {
            return response()->json([
                "error" => 1,
                "success" => "User not authenticated"
            ], 401);
        }
        
        // Additional custom validation: Student email validation
        // Rule: If a student email is provided:
        // 1. It must not match the primary registrant's email address
        // 2. It must be unique within the same registration (no duplicates)
        foreach ($request->participants ?? [] as $index => $participant) {
            if (isset($participant['role']) && $participant['role'] === 'student') {
                // Age is mandatory for students - already validated above
                // Email is optional, but if provided, validate it
                if (isset($participant['email']) && $participant['email'] && trim($participant['email']) !== '') {
                    $studentEmail = strtolower(trim($participant['email']));
                    $registrantEmail = strtolower(trim($user->email));
                    
                    // Check if student email matches registrant email
                    if ($studentEmail === $registrantEmail) {
                        return response()->json([
                            "error" => 1,
                            "success" => "Student participant email (participant #" . ($index + 1) . ") cannot be the same as the primary registrant's email address."
                        ], 422);
                    }
                    
                    // Check for duplicate emails within the same registration request
                    $duplicateCount = 0;
                    foreach ($request->participants ?? [] as $otherIndex => $otherParticipant) {
                        if ($otherIndex !== $index && 
                            isset($otherParticipant['email']) && 
                            $otherParticipant['email'] && 
                            strtolower(trim($otherParticipant['email'])) === $studentEmail) {
                            $duplicateCount++;
                        }
                    }
                    
                    if ($duplicateCount > 0) {
                        return response()->json([
                            "error" => 1,
                            "success" => "Duplicate email address found. Each participant must have a unique email address."
                        ], 422);
                    }
                    
                    // Check for duplicate emails with existing participants in database
                    $existingDuplicate = Participant::where('registration_id', $user->id)
                        ->where('type', '<>', 2) // Exclude deleted
                        ->where('id', '!=', $participant['id'] ?? 0) // Exclude current participant if updating
                        ->whereRaw('LOWER(TRIM(email)) = ?', [$studentEmail])
                        ->exists();
                        
                    if ($existingDuplicate) {
                        return response()->json([
                            "error" => 1,
                            "success" => "This email address is already used by another participant in your registration."
                        ], 422);
                    }
                }
            }
        }
        
        // Check for age-based adult requirement
        $students = collect($request->participants)->where('role', 'student');
        $adultsInRequest = collect($request->participants)->where('role', 'adult');
        $hasStudentUnder12 = $students->contains(function ($student) {
            $age = isset($student['age']) ? (int)$student['age'] : null;
            
            // If age is provided and valid, use it
            if ($age && $age > 0) {
                return $age < 12;
            }
            
            // If age is not provided, try to infer from class
            // Typical age ranges: K=5, 1st=6, 2nd=7, 3rd=8, 4th=9, 5th=10, 6th=11, 7th=12, 8th=13, etc.
            // Classes 1-5 are typically under 12, classes 6+ are typically 12 or older
            if (isset($student['class']) && $student['class']) {
                $class = (int)$student['class'];
                // If class is 5 or below, assume under 12 (5th grade is typically 10-11 years old)
                if ($class > 0 && $class <= 5) {
                    return true; // Likely under 12
                }
            }
            
            return false;
        });
        
        // Check for existing students under 12 in the database
        // Check by age if available, otherwise infer from class
        $existingStudentsUnder12 = Participant::where('registration_id', $user->id)
            ->where('type', '<>', 2) // Exclude deleted
            ->where('role', 'student')
            ->where(function($query) {
                // Check by age if available
                $query->where(function($q) {
                    $q->whereNotNull('age')
                      ->where('age', '>', 0)
                      ->where('age', '<', 12);
                })
                // OR check by class (classes 1-5 are typically under 12)
                ->orWhere(function($q) {
                    $q->whereNull('age')
                      ->whereNotNull('class')
                      ->whereRaw('CAST(class AS UNSIGNED) BETWEEN 1 AND 5');
                })
                // OR if age is 0/null but class is 1-5
                ->orWhere(function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereNull('age')->orWhere('age', 0);
                    })
                      ->whereNotNull('class')
                      ->whereRaw('CAST(class AS UNSIGNED) BETWEEN 1 AND 5');
                });
            })
            ->exists();
        
        // Check if registered user is already in participants list
        $registeredUserInParticipants = Participant::where('registration_id', $user->id)
            ->where('type', '<>', 2) // Exclude deleted
            ->where(function($query) use ($user) {
                $query->where(DB::raw('LOWER(CONCAT(first_name, " ", last_name))'), strtolower(trim($user->first_name . ' ' . $user->last_name)));
                if ($user->email) {
                    $query->orWhere(DB::raw('LOWER(email)'), strtolower(trim($user->email)));
                }
            })
            ->exists();
        
        // Count ALL existing adults in the database (including registered user if they're a participant)
        $allExistingAdults = Participant::where('registration_id', $user->id)
            ->where('type', '<>', 2) // Exclude deleted
            ->where('role', 'adult')
            ->count();
        
        // Total adults = all existing adults + adults in current request
        $totalAdults = $allExistingAdults + $adultsInRequest->count();
        
        // If there's a student under 12 (existing or new), automatically add registered user as participant
        // BUT only if they're not already in the participants list AND there are no other adults
        // (including adults being added in the current request)
        $autoAddedRegisteredUser = false;
        if (($hasStudentUnder12 || $existingStudentsUnder12) && !$registeredUserInParticipants && $totalAdults == 0) {
            // Only auto-add if there are no adults at all (existing in DB + in current request)
            // Automatically add registered user as adult participant
            Participant::create([
                'type' => 1, // Adult
                'role' => 'adult',
                'registration_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'age' => null,
                'class' => null,
            ]);
            $autoAddedRegisteredUser = true;
            $registeredUserInParticipants = true;
            $totalAdults += 1; // Count the auto-added registered user
        }
        
        // If there's a student under 12 and still no adults, return error
        // This should only happen if auto-add failed or there's a logic error
        if (($hasStudentUnder12 || $existingStudentsUnder12) && $totalAdults == 0) {
            return response()->json([
                "error" => 1,
                "success" => "Students under 12 must be accompanied by at least one adult attendee.",
                "requires_adult" => true
            ], 422);
        }

        foreach ($request->participants as $participant) {
            // Determine role - prioritize role field, fallback to type field
            $role = $participant['role'] ?? null;
            if (!$role) {
                // Fallback: if type is provided, map it to role
                $type = $participant['type'] ?? 0;
                $role = ($type == 1) ? 'adult' : 'student';
            }
            
            // Determine type for backward compatibility
            $type = $participant['type'] ?? null;
            if ($type === null) {
                // Map role to type for backward compatibility
                $type = ($role === 'adult') ? 1 : 0;
            }
            
            $storedParticipant = Participant::create([
                'type' => $type,
                'role' => $role,
                'age' => isset($participant['age']) && $participant['age'] ? (int)$participant['age'] : null,
                'email' => $participant['email'] ?? null,
                'registration_id' => $user->id,
                'first_name' => $participant['first_name'],
                'last_name' => $participant['last_name'],
                'class' => $participant['class'] ?? null,
            ]);

            // ParticipantPayment::create([
            //     'registration_id' => $user->id,
            //     'payment_id' => null,
            //     'participant_id' => $storedParticipant->id,
            //     'participant_name' => $storedParticipant->first_name . ' ' . $storedParticipant->last_name,
            // ]);
        }

        $participants = Participant::where('registration_id', $user->id)->get();
        $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->get();
        $totalAmountPaid = $payments->isEmpty() ? 0 : $payments->sum('amount');
        $participantCount = $participants->count();
        $paidParticipantCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
        $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value ?? 0;
        $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value ?? 0;
        $payment_summary = $this->user_payments();
        $hasTypeZero = $participants->where('type',0)->count();
        $abstract = AbstractSubmission::where('registration_id', $user->id)->where('status', 1)->first();

        $html = view('dashboard.payment-details')->with([
            'type' => 'Register',
            'categories' => Category::where('status', 1)->orderBy('sort_order', 'ASC')->get(),
            'settings' => Setting::where('status', 1)->get(),
            'user' => $user,
            'participants' => $participants,
            'payments' => $payments,
            'totalAmountPaid' => $totalAmountPaid,
            'participantCount' => $participantCount,
            'paidParticipantCount' => $paidParticipantCount,
            'amountPerHead' => $amountPerHead,
            'registrationFee' => $registrationFee,
            'payment_summary' => $payment_summary,
            'abstract' => $abstract
        ]);

        // Send participant added notification email
        try {
            $registration = Registration::find($user->id);
            if ($registration) {
                \Log::info('Sending participant added notification email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'participant_count' => $participantCount,
                ]);

                // Send to user (isFirstPayment = false → "New participants added" message)
                Mail::to($user->email)->send(new Register($registration, 'user', false));

                // Send to admin
                $adminEmail = Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';
                Mail::to($adminEmail)->send(new Register($registration, 'admin', false));

                \Log::info('Participant added notification email sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send participant added notification email', [
                'user_id' => $user->id,
                'email' => $user->email ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            // Don't fail participant creation if email fails
        }

        $successMessage = "Participant added successfully";
        if ($autoAddedRegisteredUser) {
            $successMessage = "Participant added successfully. Since you added a student under 12, you have been automatically added as an adult participant. Adult participant fee is $50.";
        }
        
        return response()->json([
            "error" => 0,
            "success" => $successMessage,
            'html' => $html->render(),
            'hasTypeZero' => $hasTypeZero,
            'participantCount' => $participantCount,
            'amount_payable' => $payment_summary['amountPayable'] ?? 0,
            'auto_added_registered_user' => $autoAddedRegisteredUser
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Participant store validation error', [
            'errors' => $e->errors(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            "error" => 1,
            "success" => "Validation failed: " . implode(', ', array_map(function($errors) {
                return implode(', ', $errors);
            }, $e->errors()))
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Participant store error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request_data' => $request->all()
        ]);
        return response()->json([
            "error" => 1,
            "success" => "Error adding participant: " . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::guard('customer')->user();
        $participant = Participant::findOrFail($id);
        
        // Check if payment is completed - if so, disable editing
        $paymentSummary = $this->user_payments($user->id);
        $amountPayable = (float) str_replace(',', '', $paymentSummary['amountPayable'] ?? '0.00');
        $hasCompletedPayment = Payment::where('registration_id', $user->id)->where('payment_status', 3)->exists();
        $isPaymentCompleted = ($amountPayable == 0 && $hasCompletedPayment);
        
        if ($isPaymentCompleted) {
            return response()->json([
                "error" => "Edit is disabled because payment for this registration has been completed."
            ], 403);
        }
        
        // Check if this participant is the registered user
        $isParticipantRegisteredUser = false;
        $participantFullName = strtolower(trim($participant->first_name . ' ' . $participant->last_name));
        $userFullName = strtolower(trim($user->first_name . ' ' . $user->last_name));
        $isParticipantRegisteredUser = ($participantFullName === $userFullName);
        
        // Also check by email if available
        if (!$isParticipantRegisteredUser && $participant->email && $user->email) {
            $isParticipantRegisteredUser = (strtolower(trim($participant->email)) === strtolower(trim($user->email)));
        }
        
        // Don't allow registered user to edit themselves
        if ($isParticipantRegisteredUser) {
            return response()->json([
                "error" => "You cannot edit your own participant record. Please update your registration details instead."
            ], 403);
        }
        
        // Validate based on role
        $rules = [
            'role' => 'required|in:student,adult',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'email' => 'nullable|email|max:255',
            'class' => 'nullable|string|max:255',
        ];
        
        // Add conditional validation based on role
        if ($request->role === 'student') {
            $rules['age'] = 'required|integer|min:1|max:120';
            // Email is optional for students
            $rules['email'] = 'nullable|email|max:255';
        } elseif ($request->role === 'adult') {
            // Email is required for adults
            $rules['email'] = 'required|email|max:255';
            // Age is optional for adults
            $rules['age'] = 'nullable|integer|min:1|max:120';
        }
        
        $request->validate($rules);
        
        // Additional custom validation: Student email validation
        // Rule: If a student email is provided:
        // 1. It must not match the primary registrant's email address
        // 2. It must be unique within the same registration (no duplicates)
        if ($request->role === 'student' && $request->email && trim($request->email) !== '') {
            $studentEmail = strtolower(trim($request->email));
            $registrantEmail = strtolower(trim($user->email));
            
            // Check if student email matches registrant email
            if ($studentEmail === $registrantEmail) {
                return response()->json([
                    "error" => "Student participant email cannot be the same as the primary registrant's email address."
                ], 422);
            }
            
            // Check for duplicate emails with other participants in the same registration
            $existingDuplicate = Participant::where('registration_id', $user->id)
                ->where('type', '<>', 2) // Exclude deleted
                ->where('id', '!=', $id) // Exclude current participant being updated
                ->whereRaw('LOWER(TRIM(email)) = ?', [$studentEmail])
                ->exists();
                
            if ($existingDuplicate) {
                return response()->json([
                    "error" => "This email address is already used by another participant in your registration."
                ], 422);
            }
        }
        
        $participant->update($request->all());
        $participants = Participant::where('registration_id', $user->id)->get();
        $hasTypeZero=$participants->where('type',0)->count();
        return response()->json(["success" => "Participant updated successfully",'hasTypeZero'=>$hasTypeZero]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::guard('customer')->user();
        
        // Get the participant to check if it's the registered user
        $participant = Participant::where("id", $id)->where('registration_id', $user->id)->first();
        
        if (!$participant) {
            return response()->json([
                "error" => 1,
                "success" => "Participant not found or you don't have permission to delete it."
            ], 404);
        }
        
        // Check if this participant is the registered user (adult)
        $isRegisteredUser = false;
        $participantFullName = strtolower(trim($participant->first_name . ' ' . $participant->last_name));
        $userFullName = strtolower(trim($user->first_name . ' ' . $user->last_name));
        $isRegisteredUser = ($participantFullName === $userFullName);
        
        // Also check by email if available
        if (!$isRegisteredUser && $participant->email && $user->email) {
            $isRegisteredUser = (strtolower(trim($participant->email)) === strtolower(trim($user->email)));
        }
        
        // If deleting an ADULT participant, check if they're the last adult when students under 12 exist
        $participantRole = $participant->role ?: (($participant->type == 1) ? 'adult' : 'student');
        
        if ($participantRole === 'adult') {
            // Check if there are any students under 12
            $hasStudentUnder12 = Participant::where('registration_id', $user->id)
                ->where('id', '!=', $participant->id) // Exclude participant being deleted
                ->where('type', '<>', 2) // Exclude deleted
                ->where('role', 'student')
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('age')
                ->where('age', '>', 0)
                          ->where('age', '<', 12);
                    })
                    ->orWhere(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNull('age')->orWhere('age', 0);
                        })
                        ->whereNotNull('class')
                        ->whereRaw('CAST(class AS UNSIGNED) BETWEEN 1 AND 5');
                    });
                })
                ->exists();
            
            if ($hasStudentUnder12) {
                // Count remaining adults after this deletion
                $remainingAdults = Participant::where('registration_id', $user->id)
                    ->where('id', '!=', $participant->id) // Exclude participant being deleted
                    ->where('type', '<>', 2) // Exclude deleted
                    ->where('role', 'adult')
                    ->count();
                
                if ($remainingAdults < 1) {
                return response()->json([
                    "error" => 1,
                        "success" => "Cannot delete this adult participant. At least one adult attendee is required when there are students under 12."
                ], 403);
            }
            }
        }
        
        // Allow deletion
        $participant->delete();
        $participantCount = optional($user->participants)->where('type','<>',2)->count();
        
        return response()
            ->json(["success" => "Participant deleted successfully","participantCount"=>$participantCount], 200);
    }
    public function participants(Datatables $datatables)
    {
        $user = Auth::guard('customer')->user();
    
        if (request()->ajax()) {
            if (request()->has('partial') && request('partial') === 'add_participant') {
                $participant_edit = optional(Setting::where('code', 'participant_edit_status')->first())->value;
                $participant_delete = optional(Setting::where('code', 'participant_delete_status')->first())->value;
                
                // Get payment summary to check if there's still amount payable
                // Allow deletion if there's still amount payable (payment not fully completed)
                $paymentSummary = $this->user_payments($user->id);
                $amountPayable = (float) str_replace(',', '', $paymentSummary['amountPayable'] ?? '0.00');
                
                // Allow delete if there's still amount payable (meaning payment hasn't been fully completed)
                // This allows users to delete participants before making payment
                $canDelete = $amountPayable > 0;
                // Always show action column - users should always be able to edit/delete participants
                $has_action_column = true;
    
                $participant = Participant::where('registration_id', $user->id)
                    ->where('type', '<>', 2);
    
                $datatable = $datatables->eloquent($participant)
                    ->addIndexColumn()
                    ->addColumn('role', function (Participant $participant) use ($user) {
                        // Use stored role for display - do NOT override DB values
                        $role = $participant->role;
                        
                        if (empty($role) || $role === null) {
                            // Fallback: use type field (0 = student, 1 = adult/guest)
                            $role = ($participant->type == 1) ? 'adult' : 'student';
                            // Auto-fill empty role for future (one-time fix)
                            try {
                                $participant->update(['role' => $role]);
                            } catch (\Exception $e) {
                                // Ignore update errors
                            }
                        }
                        return ucfirst($role ?: 'student');
                    })
                    ->addColumn('age', function (Participant $participant) {
                        return $participant->age ? $participant->age : 'N/A';
                    })
                    ->addColumn('email', function (Participant $participant) {
                        return $participant->email ? $participant->email : 'N/A';
                    });
    
              
                    $datatable->addColumn('action', function (Participant $participant) use ($participant_edit, $participant_delete, $user) {
                        // Skip deleted participants
                        if ($participant->type == 2) {
                            return '<span style="color: #999; font-size: 10px;">N/A</span>';
                        }
                        
                        // Check if this participant is the registered user (adult)
                        $isParticipantRegisteredUser = false;
                        $participantFullName = strtolower(trim($participant->first_name . ' ' . $participant->last_name));
                        $userFullName = strtolower(trim($user->first_name . ' ' . $user->last_name));
                        $isParticipantRegisteredUser = ($participantFullName === $userFullName);
                        
                        if (!$isParticipantRegisteredUser && $participant->email && $user->email) {
                            $isParticipantRegisteredUser = (strtolower(trim($participant->email)) === strtolower(trim($user->email)));
                        }
                        
                        // Determine this participant's role
                        $participantRole = $participant->role ?: (($participant->type == 1) ? 'adult' : 'student');
                        
                        // Check payment status
                        $paymentSummary = $this->user_payments($user->id);
                        $amountPayable = (float) str_replace(',', '', $paymentSummary['amountPayable'] ?? '0.00');
                        $hasCompletedPayment = Payment::where('registration_id', $user->id)->where('payment_status', 3)->exists();
                        $isPaymentCompleted = ($amountPayable == 0 && $hasCompletedPayment);
                        
                        // Determine if this participant can be deleted
                        $canDelete = true;
                        
                        // Rule: If this is an ADULT, check if they're the last adult with students under 12
                        if ($participantRole === 'adult') {
                            $hasStudentUnder12 = Participant::where('registration_id', $user->id)
                                ->where('id', '!=', $participant->id)
                                ->where('type', '<>', 2)
                                ->where('role', 'student')
                                ->where(function($query) {
                                    $query->where(function($q) {
                                        $q->whereNotNull('age')
                                ->where('age', '>', 0)
                                          ->where('age', '<', 12);
                                    })
                                    ->orWhere(function($q) {
                                        $q->where(function($subQ) {
                                            $subQ->whereNull('age')->orWhere('age', 0);
                                        })
                                        ->whereNotNull('class')
                                        ->whereRaw('CAST(class AS UNSIGNED) BETWEEN 1 AND 5');
                                    });
                                })
                                ->exists();
                            
                            if ($hasStudentUnder12) {
                                // Count remaining adults after potential deletion
                                $remainingAdults = Participant::where('registration_id', $user->id)
                                    ->where('id', '!=', $participant->id)
                                    ->where('type', '<>', 2)
                                    ->where('role', 'adult')
                                    ->count();
                                
                                if ($remainingAdults < 1) {
                                    $canDelete = false; // Last adult, can't delete
                                }
                            }
                        }
                        
                        // If payment is fully completed, only registered user can still delete others
                        if ($isPaymentCompleted && !$isParticipantRegisteredUser) {
                            // After full payment, only allow delete if amount payable > 0 (e.g. refund scenario)
                            // For now, block delete after full payment for non-registered-user check
                            $canDelete = false;
                        }
                        
                        // Build delete button
                        $deleteButton = '';
                            if ($canDelete) {
                                $deleteButton = '<a data-id="' . $participant->id . '" data-href="' . route("participant.destroy", $participant->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-participant" title="Delete" style="margin-left: 8px;">
                                            <i class="fas fa-trash-alt delete" aria-hidden="true" style="color: #f15b43;"></i>
                                        </a>';
                        }
                        
                        // Build edit button
                        $editButton = '';
                        if ($isParticipantRegisteredUser) {
                            // Registered user cannot edit themselves (edit registration details instead)
                            $editButton = '';
                        } elseif ($isPaymentCompleted) {
                            // Payment completed - disable edit
                            $editButton = '<a href="javascript:void(0);" class="btn btn-sm btn-clean btn-icon text-end" title="Edit disabled - Payment completed" style="cursor: not-allowed; color: #ccc;">
                                        <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                                    </a>';
                        } else {
                            // Active edit button
                        $emailValue = $participant->email;
                        if ($isParticipantRegisteredUser && $user && empty($emailValue)) {
                            $emailValue = $user->email;
                        }
                            $editButton = '<a href="javascript:void(0);" class="btn btn-sm btn-clean btn-icon text-end edit-participant" data-href="' . route("participant.update", $participant->id) . '" data-type="' . $participant->type . '" data-role="' . ($participant->role ?? '') . '" data-age="' . ($participant->age ?? '') . '" data-email="' . ($emailValue ?? '') . '" data-id="' . $participant->id . '" data-first-name="' . $participant->first_name . '" data-last-name="' . $participant->last_name . '" data-class="' . $participant->class . '" data-is-registered-user="' . ($isParticipantRegisteredUser ? '1' : '0') . '" title="Edit">
                                        <i class="fas fa-pencil-alt" aria-hidden="true" style="color: #f15b43;"></i>
                                    </a>';
                        }
                        
                        // If both are empty, show a disabled/faded delete icon
                        if (empty($editButton) && empty($deleteButton)) {
                            return '<div class="d-flex"><a href="javascript:void(0);" class="btn btn-sm btn-clean btn-icon text-end" title="Cannot delete - adult required for students under 12" style="cursor: not-allowed; margin-left: 8px;">
                                        <i class="fas fa-trash-alt" aria-hidden="true" style="color: #ccc;"></i>
                                    </a></div>';
                            }
                        
                        return '<div class="d-flex">' . $editButton . $deleteButton . '</div>';
                    })->rawColumns(['action']);
                
    
                return $datatable->with('has_action_column', $has_action_column)->make(true);
            }
        }
    }
    

    function payment_details(Request $request){
        try {
            $user = Auth::guard('customer')->user();
            
            if (!$user) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'User not authenticated'], 401);
                }
                return redirect()->route('login');
            }
            
            // Refresh user to get latest is_attending status
            $user->refresh();
            
            $participants = Participant::where('registration_id', $user->id)->get();
            $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->get();
            $totalAmountPaid = $payments->isEmpty() ? 0 : $payments->sum('amount');
            $participantCount = $participants->count();
            $paidParticipantCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
            $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
            $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value;
            $refund = PaymentRefund::where('registration_id', $user->id)->get();
            $payment_summary = $this->user_payments();
            $abstract = AbstractSubmission::where('registration_id', $user->id)->where('status', 1)->first();

            return view('dashboard.payment-details')->with([
                'type' => 'Register',
                'categories' => Category::where('status', 1)->orderBy('sort_order', 'ASC')->get(),
                'settings' => Setting::where('status', 1)->get(),
                'user' => $user,
                'participants' => $participants,
                'payments' => $payments,
                'totalAmountPaid' => $totalAmountPaid,
                'participantCount' => $participantCount,
                'paidParticipantCount' => $paidParticipantCount,
                'amountPerHead' => $amountPerHead,
                'registrationFee' => $registrationFee,
                'refund' => $refund,
                'payment_summary' => $payment_summary,
                'abstract' => $abstract
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment details error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error loading payment details: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error loading payment details. Please try again.');
        }
    }

    // public function user_payments(){
    //     $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
    //     $registration_fee = optional(Setting::where('code', 'registration_fee')->first())->value;
    //     $user = Auth::guard('customer')->user();
    //     $participants = Participant::where('registration_id', $user->id)->get();
    //     $participantsCount = $participants->count();
    //     $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->get();
    //     $paidParticipantsCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
    //     $paidAmount = $payments->isEmpty() ? 0 : $payments->sum('amount');
    //     $subTotal=$participantsCount* $amountPerHead;
    //     if($participantsCount==0)
    //     $grandTotal=0;
    //     else
    //     $grandTotal=($participantsCount* $amountPerHead)+$registration_fee;
    //     $totalAmountPayable=$grandTotal-$paidAmount;
    //     if($grandTotal>$paidAmount)
    //     $totalAmountPayable=$grandTotal-$paidAmount;
    //     else
    //     $totalAmountPayable=0;
    //     $refund=0;
    //     if($paidAmount>$grandTotal)
    //     $refund =$paidAmount-$grandTotal;
    //     return (['subTotal'=>$subTotal,'grandTotal'=>$grandTotal,'paidAmount'=>$paidAmount,'amountPayable'=>$totalAmountPayable,'refund'=>$refund]);
       
    // }

}
