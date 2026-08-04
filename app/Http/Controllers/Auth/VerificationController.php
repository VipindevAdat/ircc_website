<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Register;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:customer');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            Log::info('Email verification resend skipped - already verified', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect($this->redirectPath());
        }

        Log::info('Email verification notification sending', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'ip' => $request->ip(),
        ]);

        $request->user()->sendEmailVerificationNotification();

        Log::info('Email verification notification sent successfully', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
        ]);

        return $request->wantsJson()
            ? new JsonResponse([], 202)
            : back()->with('resent', true);
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            Log::info('Email verification skipped - already verified', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            Log::info('Email verified successfully', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip' => $request->ip(),
                'verified_at' => now()->toDateTimeString(),
            ]);

            // Send registration confirmation email after verification
            try {
                Log::info('Sending registration confirmation email after verification', [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                ]);

                // Send to user (isFirstPayment = true → "Registration completed" message)
                Mail::to($request->user()->email)->send(new Register($request->user(), 'user', true));

                // Send to admin
                $adminEmail = \App\Models\Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';
                Mail::to($adminEmail)->send(new Register($request->user(), 'admin', true));

                Log::info('Registration confirmation email sent successfully after verification', [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'admin_email' => $adminEmail,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send registration confirmation email after verification', [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($this->redirectPath())->with('verified', true);
    }
}
