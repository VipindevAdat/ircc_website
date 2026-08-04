<?php

namespace App\Http\Controllers;
use App\Models\AbstractDomain;
use App\Models\AbstractSubmission;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\AbstractReviewer;
use App\Models\Reviewer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\GivebutterHelper;
use Carbon\Carbon;
use Throwable;

class AbstractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use GivebutterHelper;

    private const ABSTRACT_MAX_WORDS = 250;

    /**
     * Trim and collapse whitespace so word count matches what we persist.
     */
    private function normalizedAbstractContent(string $content): string
    {
        return trim(preg_replace('/\s+/u', ' ', $content));
    }

    private function countAbstractWords(string $content): int
    {
        $normalized = $this->normalizedAbstractContent($content);
        if ($normalized === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY));
    }

    private function addAbstractWordLimitValidation(\Illuminate\Validation\Validator $validator, Request $request): void
    {
        $validator->after(function ($validator) use ($request) {
            $wordCount = $this->countAbstractWords((string) $request->input('abstract_content', ''));
            if ($wordCount === 0) {
                return;
            }
            if ($wordCount > self::ABSTRACT_MAX_WORDS) {
                $validator->errors()->add(
                    'abstract_content',
                    'The abstract may contain at most '.self::ABSTRACT_MAX_WORDS.' words (this is a word limit, not a character limit). Line breaks and extra spaces are not counted as words—please shorten your text.'
                );
            }
        });
    }

    private function abstractValidationUserMessage(\Illuminate\Validation\Validator $validator): string
    {
        $errors = $validator->errors();

        return $errors->first('abstract_content')
            ?? $errors->first('references')
            ?? $errors->first();
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::guard('customer')->user();
        
        // Check if user has completed payment before allowing abstract submission
        $hasCompletedPayment = Payment::where('registration_id', $user->id)
            ->where('payment_status', 3) // Completed
            ->exists();
        
        // Check if user is fully paid (amountPayable == 0)
        $payment_summary = $this->user_payments();
        $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
        
        if (!$hasCompletedPayment || !$isFullyPaid) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Please complete your full payment (Amount Payable must be $0) before submitting an abstract.'
                ], 403);
            }
            return redirect()->route('accounts.dashboard')
                ->with('error', 'Please complete your full payment (Amount Payable must be $0) before submitting an abstract.');
        }

        $data = $this->dashboardAbstractFormData($user, null);

        if (request()->ajax()) {
            return view('dashboard.abstract-form', $data);
        }

        return view('dashboard.abstract-form', $data);
    }

    private function decodeJsonField($field) {
        if (is_string($field)) {
            return json_decode($field, true) ?? [];
        }

        return is_array($field) ? $field : [];
    }

    /**
     * View data for the dashboard abstract form (blank create or editing one abstract).
     */
    private function dashboardAbstractFormData($user, ?AbstractSubmission $abstract = null): array
    {
        $participants = Participant::where('registration_id', $user->id)->get();
        $abstractDomains = AbstractDomain::where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('domain', 'ASC')
            ->get();
        $categories = Category::where('status', 1)
            ->orderByRaw('COALESCE(sort_order, 999) ASC')
            ->get();
        $abstracts = AbstractSubmission::where('registration_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $payment_summary = $this->user_payments();
        $paymentApprovePending = Payment::where('registration_id', $user->id)->where('payment_mode', 'Direct Bank Transfer')->where('payment_status', 1)->first();
        $paymentApproveRejected = Payment::where('registration_id', $user->id)->where('payment_mode', 'Direct Bank Transfer')->where('payment_status', 4)->first();

        if ($abstract === null) {
            $storedAuthors = [];
            $selectedDomainId = null;
            $reviewedAbstract = null;
        } else {
            $storedAuthors = $this->decodeJsonField($abstract->student_authors);
            $selectedDomainId = $abstract->abstract_domain;
            $reviewedAbstract = AbstractReviewer::where('abstract_id', $abstract->id)->first();
        }

        return [
            'type' => 'Register',
            'categories' => $categories,
            'settings' => Setting::where('status', 1)->get(),
            'user' => $user,
            'participants' => $participants,
            'abstractDomains' => $abstractDomains,
            'abstract' => $abstract,
            'abstracts' => $abstracts,
            'storedAuthors' => $storedAuthors,
            'selectedDomainId' => $selectedDomainId,
            'payment_summary' => $payment_summary,
            'reviewedAbstract' => $reviewedAbstract,
            'paymentApprovePending' => $paymentApprovePending,
            'paymentApproveRejected' => $paymentApproveRejected,
        ];
    }

    /**
     * JSON response expected by dashboard AJAX abstract submit (index.blade.php).
     */
    private function abstractFormAjaxSuccessResponse(string $message, $user, ?AbstractSubmission $abstract = null)
    {
        $data = $this->dashboardAbstractFormData($user, $abstract);

        $html = null;
        try {
            $html = view('dashboard.abstract-form', $data)->render();
        } catch (Throwable $e) {
            Log::error('abstract_form_ajax: failed to render form HTML after save', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
                'registration_id' => $user->id ?? null,
            ]);
        }

        // Do not use JSON_PARTIAL_OUTPUT_ON_ERROR: it can yield truncated/invalid JSON that
        // browsers cannot parse (jQuery.ajax dataType: 'json' then fails with parsererror).
        $jsonFlags = JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE;

        return response()->json([
            'error' => 0,
            'success' => $message,
            'html' => $html,
            'reload' => $html === null,
        ], 200, [], $jsonFlags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'abstract_domain' => 'required|string|max:255',
        'abstract_title' => 'required|string|max:255',
        'student_authors' => 'required|array|min:1',
        'student_authors.*' => 'string|max:255',
        'additional_authors' => 'nullable|string|max:255',
        'correspondent_author' => 'required|string|max:255',
        'abstract_content' => 'required|string',
    ]);

    // Custom check: ensure at least one reference has actual content
    $validator->after(function ($validator) use ($request) {
        $hasContent = false;
        if ($request->has('references')) {
            $refs = $request->references;
            if (is_array($refs)) {
                // Repeater sends references[0][references] = 'value'
                foreach ($refs as $ref) {
                    $val = is_array($ref) ? ($ref['references'] ?? '') : $ref;
                    if (is_string($val) && trim($val) !== '') {
                        $hasContent = true;
                        break;
                    }
                }
            } elseif (is_string($refs) && trim($refs) !== '') {
                // Fallback: repeater not initialized, single textarea sends string
                $hasContent = true;
            }
        }
        if (!$hasContent) {
            $validator->errors()->add('references', 'Reference field is required.');
        }
    });

    $this->addAbstractWordLimitValidation($validator, $request);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 1,
                'message' => $this->abstractValidationUserMessage($validator),
                'errors' => $validator->errors(),
            ], 422);
        }

        return redirect()->back()->withErrors($validator)->withInput();
    }

    $validatedData = $validator->validated();

    if ($validatedData) {
        $user = Auth::guard('customer')->user();
        
        // Check if user has completed payment before allowing abstract submission
        $hasCompletedPayment = Payment::where('registration_id', $user->id)
            ->where('payment_status', 3) // Completed
            ->exists();
        
        // Check if user is fully paid (amountPayable == 0)
        $payment_summary = $this->user_payments();
        $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
        
        if (!$hasCompletedPayment || !$isFullyPaid) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Please complete your full payment (Amount Payable must be $0) before submitting an abstract.',
                ], 403);
            }

            return redirect()
                ->route('accounts.dashboard')
                ->with('error', 'Please complete your full payment (Amount Payable must be $0) before submitting an abstract.');
        }

        // Enforce max 2 abstracts per registration
        $existingCount = AbstractSubmission::where('registration_id', $user->id)->count();
        if ($existingCount >= 2) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'You can submit a maximum of 2 abstracts for this registration.',
                ], 422);
            }

            return redirect()
                ->route('abstract.create')
                ->with('error', 'You can submit a maximum of 2 abstracts for this registration.');
        }

        $additionalAuthors = is_array($request->additional_authors)
            ? implode(',', $request->additional_authors)
            : $request->additional_authors;

        $lastSubmission = AbstractSubmission::orderBy('id', 'desc')->first();
        if ($lastSubmission) {
            preg_match('/(\d+)/', $lastSubmission->pid, $matches);
            $lastId = (int) $matches[0];
            $newId = 'PID' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'PID001';
        }

        $reviewer = Reviewer::where('category', $request->abstract_domain)->where('status',1)->first();

        if (!$reviewer) {
           $reviewer = null;
        }
        $referenceArray = [];
        if (!empty($request->references)) {
            $refs = $request->references;
            if (is_array($refs)) {
                foreach ($refs as $reference) {
                    $val = is_array($reference) ? ($reference['references'] ?? '') : $reference;
                    if (!empty($val)) {
                        $referenceArray[] = $val;
                    }
                }
            } elseif (is_string($refs) && trim($refs) !== '') {
                $referenceArray[] = $refs;
            }
        }

        // Determine status: draft (0) or submitted (1)
        $submitAction = $request->input('submit_action', 'draft');
        $status = $submitAction === 'submit' ? 1 : 0;

        $abstractBody = $this->normalizedAbstractContent((string) $request->input('abstract_content', ''));

        // Create a new abstract (no longer update-or-create by registration)
        $abstract = AbstractSubmission::create([
            'registration_id'      => $user->id,
            'pid'                  => $newId,
            'category_id'          => $request->category_id,
            'abstract_domain'      => $request->abstract_domain,
            'abstract_title'       => $request->abstract_title,
            'student_authors'      => $request->student_authors,
            'additional_authors'   => $additionalAuthors,
            'correspondent_author' => $request->correspondent_author_name,
            'abstract_content'     => $abstractBody,
            'references'           => json_encode($referenceArray),
            'reviewer_id'          => $reviewer ? $reviewer->id : null,
            'status'               => $status,
        ]);
    
            if ($reviewer) {
                AbstractReviewer::updateOrCreate([
                    'abstract_id' => $abstract->id
                    ],['reviewer_id' => $reviewer->id]);
            }

        $message = $status === 0
            ? 'Abstract Saved'
            : 'Abstract Submitted';

        if ($request->expectsJson()) {
            return $this->abstractFormAjaxSuccessResponse($message, $user, $abstract->fresh());
        }

        return redirect()
            ->route('abstract.create')
            ->with('success', $message);
    } else {
        return redirect()->route('abstract.create')->withErrors($validatedData)->withInput();
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
     * Used by dashboard to load a specific abstract into the form.
     */
    public function edit(string $id)
    {
        $user = Auth::guard('customer')->user();

        $abstract = AbstractSubmission::where('id', $id)
            ->where('registration_id', $user->id)
            ->firstOrFail();

        $data = $this->dashboardAbstractFormData($user, $abstract);

        if (request()->ajax()) {
            return view('dashboard.abstract-form', $data);
        }

        return view('dashboard.abstract-form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'category_id' => 'required',
            'abstract_domain' => 'required|string|max:255',
            'abstract_title' => 'required|string|max:255',
            'student_authors' => 'required|array|min:1',
            'student_authors.*' => 'string|max:255',
            'additional_authors' => 'nullable|string|max:255',
            'correspondent_author' => 'required|string|max:255',
            'abstract_content' => 'required|string',
        ]);

        // Custom check: ensure at least one reference has actual content
        $validator->after(function ($validator) use ($request) {
            $hasContent = false;
            if ($request->has('references')) {
                $refs = $request->references;
                if (is_array($refs)) {
                    // Repeater sends references[0][references] = 'value'
                    foreach ($refs as $ref) {
                        $val = is_array($ref) ? ($ref['references'] ?? '') : $ref;
                        if (is_string($val) && trim($val) !== '') {
                            $hasContent = true;
                            break;
                        }
                    }
                } elseif (is_string($refs) && trim($refs) !== '') {
                    // Fallback: repeater not initialized, single textarea sends string
                    $hasContent = true;
                }
            }
            if (!$hasContent) {
                $validator->errors()->add('references', 'Reference field is required.');
            }
        });

        $this->addAbstractWordLimitValidation($validator, $request);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 1,
                    'message' => $this->abstractValidationUserMessage($validator),
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
    if ($validatedData) {
        $user = Auth::guard('customer')->user();
        
        // Check if user has completed payment before allowing abstract update
        $hasCompletedPayment = Payment::where('registration_id', $user->id)
            ->where('payment_status', 3) // Completed
            ->exists();
        
        // Check if user is fully paid (amountPayable == 0)
        $payment_summary = $this->user_payments();
        $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
        
        if (!$hasCompletedPayment || !$isFullyPaid) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Please complete your full payment (Amount Payable must be $0) before updating your abstract.',
                ], 403);
            }

            return redirect()
                ->route('accounts.dashboard')
                ->with('error', 'Please complete your full payment (Amount Payable must be $0) before updating your abstract.');
        }

        $additionalAuthors = is_array($request->additional_authors)
            ? implode(',', $request->additional_authors)
            : $request->additional_authors;

        // Only allow editing of user's own abstracts
        $abstractSubmission = AbstractSubmission::where('id', $id)
            ->where('registration_id', $user->id)
            ->firstOrFail();

        // Once submitted, disable further editing
        if ((int) $abstractSubmission->status === 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'This abstract has already been submitted and can no longer be edited.',
                ], 422);
            }

            return redirect()
                ->route('abstract.create')
                ->with('error', 'This abstract has already been submitted and can no longer be edited.');
        }
           $reviewer = Reviewer::where('category', $request->abstract_domain)->where('status',1)->first();

           if (!$reviewer) {
              $reviewer = null;
           }
           $referenceArray = [];
           if (!empty($request->references)) {
               $refs = $request->references;
               if (is_array($refs)) {
                   foreach ($refs as $reference) {
                       $val = is_array($reference) ? ($reference['references'] ?? '') : $reference;
                       if (!empty($val)) {
                           $referenceArray[] = $val;
                       }
                   }
               } elseif (is_string($refs) && trim($refs) !== '') {
                   $referenceArray[] = $refs;
               }
           }

           // Determine status based on action (allow updating draft or re-submitting before review)
           $submitAction = $request->input('submit_action', 'draft');
           $status = $submitAction === 'submit' ? 1 : 0;

           $abstractBody = $this->normalizedAbstractContent((string) $request->input('abstract_content', ''));

           $abstractSubmission->update([
                'category_id' => $request->category_id,
                'abstract_domain' => $request->abstract_domain,
                'abstract_title' => $request->abstract_title,
                'student_authors' => $request->student_authors,
                'additional_authors' => $additionalAuthors,
                'correspondent_author' => $request->correspondent_author,
                'abstract_content' => $abstractBody,
                'references' => json_encode($referenceArray),
                'reviewer_id' => $reviewer ? $reviewer->id : null,
                'status' => $status,
            ]);

        $abstract = $abstractSubmission;
        $abstractReviewer=AbstractReviewer::where('abstract_id',$abstractSubmission->id)->first();
        if ($abstractReviewer && $reviewer) {
            $abstractReviewer->update([
                'abstract_id'=>$abstract->id,
                'reviewer_id' => $reviewer->id,
            ]);
        }
        elseif($reviewer){
            AbstractReviewer::create([
                'abstract_id' => $abstract->id,
                'reviewer_id' => $reviewer->id,
            ]);
        }

        $message = $status === 0
            ? 'Abstract Saved'
            : 'Abstract Submitted';

        if ($request->expectsJson()) {
            return $this->abstractFormAjaxSuccessResponse($message, $user, $abstractSubmission->fresh());
        }

        return redirect()
            ->route('abstract.create')
            ->with('success', $message);
    }
    else{

        return redirect()->route('abstract.create')->withErrors($validatedData)->withInput();
    }
    }

    /**
     * Remove the specified resource from storage.
     * Only drafts (status = 0) can be deleted by the user.
     */
    public function destroy(string $id)
    {
        $user = Auth::guard('customer')->user();

        $abstract = AbstractSubmission::where('id', $id)
            ->where('registration_id', $user->id)
            ->firstOrFail();

        if ((int) $abstract->status === 1) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Submitted abstracts cannot be deleted.'
                ], 403);
            }

            return redirect()
                ->back()
                ->with('error', 'Submitted abstracts cannot be deleted.');
        }

        $abstract->delete();

        if (request()->ajax()) {
            return response()->json([
                'error' => 0,
                'message' => 'Abstract deleted successfully.',
                'reload_url' => route('abstract.create'),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Abstract deleted successfully.');
    }
    public function participantsCategory(Request $request)
    {
        $participantIds = $request->input('participants', []);

        $participants = Participant::whereIn('id', $participantIds)->get();

        if ($participants->isEmpty()) {
            return response()->json(['error' => 1, 'message' => 'No participants selected.']);
        }

        $categories = Category::where('status', 1)
            ->orderByRaw('COALESCE(sort_order, 999) ASC')
            ->get();

        return response()->json([
            'error' => 0,
            'categories' => $categories,
        ]);
    }
}
