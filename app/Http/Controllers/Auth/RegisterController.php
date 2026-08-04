<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Registration;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Setting;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\Register;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo ='/register-success';
    // protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:customer')->except('logout');
       
    }
    
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $settings = Setting::where('status', 1)->get();
        
        // Get the source parameter from query string (for pre-populating "how did you hear about us")
        $source = $request->query('source', '');
        
        // Map source values to dropdown options
        $sourceMap = [
            'manca' => 'MANCA',
            'gyra' => 'GYRA',
            'stem4girls' => 'STEM4Girls',
            'bayareachampions' => 'Bay Area Champions',
            'bayareachampion' => 'Bay Area Champions',
            'youngchampions' => 'Bay Area Champions', // Keep for backward compatibility
        ];
        
        // Normalize source to lowercase for matching
        $normalizedSource = strtolower($source);
        $hearAboutUs = isset($sourceMap[$normalizedSource]) ? $sourceMap[$normalizedSource] : '';
        
        return view('auth.register', compact('settings', 'hearAboutUs'));
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ircc_form'=>['required','string'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'institution_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:registrations'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $decrypt=Crypt::decrypt($data['ircc_form']);
        if($decrypt=='IRCC2026'){
        $lastRegistration = Registration::orderBy('id', 'desc')->first();
        if ($lastRegistration) {
            preg_match('/(\d+)/', $lastRegistration->id, $matches);
            $lastId = (int) $matches[0];
            $newId = 'IRCC26' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'IRCC26001';
        }
        $reg_tag=optional(Setting::where('code','registration_tag')->first())->value;
        
        // Handle "hear_about_us" - if "Other" is selected, use the value from "hear_about_other"
        $hearAboutUs = $data['hear_about_us'] ?? null;
        if ($hearAboutUs === 'Other' && !empty($data['hear_about_other'])) {
            $hearAboutUs = $data['hear_about_other'];
        }
        
        $registrationData = [
            'registration_number' => $newId,
            'registration_tag'=> $reg_tag,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'institution_name' => $data['institution_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'hear_about_us' => $hearAboutUs,
        ];
        
        // Only add is_attending if the column exists in the database
        if (Schema::hasColumn('registrations', 'is_attending')) {
            $registrationData['is_attending'] = isset($data['is_attending']) && $data['is_attending'] == '1';
        }

        if (Schema::hasColumn('registrations', 'student_participant_unit_price')) {
            $registrationData['student_participant_unit_price'] = Registration::studentUnitPriceForNewAccount();
        }
        
        $registration = Registration::create($registrationData);
        
        // Verify registration was created
        if (!$registration || !$registration->id) {
            throw new Exception('Failed to create registration record');
        }
        
        // Do NOT create a participant record during registration
        // Participants should only be added when:
        // 1. User explicitly adds themselves or others via the "Add Participants" form
        // 2. User adds a student under 12 (then they're auto-added as an adult participant)
        // This ensures registered users are only charged if they actually participate
        
        return $registration;
        }
        else{
            throw new Exception('Invalid registration form code');
        }
    }
    public function register(Request $request)
    {
       $this->validator($request->all())->validate();
       $decrypt=Crypt::decrypt($request->ircc_form);
       
       if($decrypt=='IRCC2026'){
            if (!$this->validator($request->all())->validate()){
                return response()->json([
                        "status" => false,
                        "errors" =>$this->validator($request->all())->validate() 
                    ]);
            }
            
            $user = null;
            try{
                // Attempt to create registration
                $user = $this->create($request->all());
                
                // Verify registration was actually created
                if (!$user || !$user->id) {
                    throw new Exception('Registration creation failed - no user ID returned');
                }
                
                // Verify the registration exists in database
                $verifyRegistration = Registration::find($user->id);
                if (!$verifyRegistration) {
                    throw new Exception('Registration was not saved to database');
                }
                
                \Log::info('Firing Registered event - verification email will be sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'registration_number' => $user->registration_number ?? null,
                ]);

                event(new Registered($user));

                \Log::info('Registered event fired - verification email dispatched', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                // Registration confirmation email will be sent AFTER email verification
                // See VerificationController::verify()
                
                // Registration successful
                // Call registered hook but always return our own response for consistency
                $this->registered($request, $user);

                return $request->wantsJson()
                            ? new JsonResponse(['status' => true, 'message' => 'Registration successful'], 201)
                            : redirect($this->redirectPath());
            }
            catch (Exception $e) {
                \Log::error('Registration failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'email' => $request->email ?? 'unknown',
                ]);
                
                // If user was partially created, try to clean up
                if ($user && $user->id) {
                    try {
                        Registration::where('id', $user->id)->delete();
                        Participant::where('registration_id', $user->id)->delete();
                    } catch (\Exception $cleanupException) {
                        \Log::error('Failed to cleanup partial registration', [
                            'error' => $cleanupException->getMessage(),
                            'registration_id' => $user->id,
                        ]);
                    }
                }
                
                // Return error response
                return $request->wantsJson()
                    ? response()->json([
                        'status' => false,
                        'message' => 'Registration failed. Please try again or contact support.',
                        'errors' => ['registration' => ['Registration failed. Please try again or contact support.']]
                    ], 422)
                    : redirect()->route('register-failure')->with('error', 'Registration failed. Please try again or contact support.');
            }
        }
        else {
            return $request->wantsJson()
                ? response()->json([
                    'status' => false,
                    'message' => 'Invalid registration form.',
                    'errors' => ['ircc_form' => ['Invalid registration form.']]
                ], 422)
                : redirect()->route('register-failure')->with('error', 'Invalid registration form.');
        }
    }
    protected function guard()
    {
        return Auth::guard('customer');
    }
    public function checkEmail(Request $request)
    {
      
     $valid=Registration::where('email',$request->email)->first();
        if($valid){
            return response()->json(array("exists" => true,'errors'=>['email'=>'The email has already been taken.']));
        }else{
            return response()->json(array("exists" => false));
        }
        
    }
 
}
