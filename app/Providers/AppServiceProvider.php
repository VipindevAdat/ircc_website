<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Setting;
use App\Support\SocialLinkPresenter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override public_path() to use public_html if it exists
        $this->app->bind('path.public', function () {
            $basePath = $this->app->basePath();
            $publicHtmlPath = $basePath . DIRECTORY_SEPARATOR . 'public_html';
            $publicPath = $basePath . DIRECTORY_SEPARATOR . 'public';
            
            // Check if public_html exists, otherwise use public
            if (is_dir($publicHtmlPath)) {
                return $publicHtmlPath;
            }
            
            return $publicPath;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Mediable / Storage disk URLs use config('filesystems.disks.uploads.url'), not the URL generator.
        // If APP_URL is still http:// while the site is served over HTTPS (proxy/TLS), uploaded images are blocked
        // as mixed content and <img onerror> fallbacks show user-img.png everywhere.
        if (! $this->app->runningInConsole()) {
            $base = rtrim((string) config('app.url'), '/');
            if (str_starts_with($base, 'http://') && request()?->secure()) {
                $base = 'https://' . substr($base, 7);
                config(['filesystems.disks.uploads.url' => $base . '/uploads']);
            }
        }

        View::composer('layouts.app', function ($view) {
            $settings = $view->offsetExists('settings')
                ? $view->offsetGet('settings')
                : Setting::query()->where('status', 1)->get();
            if (! $view->offsetExists('settings')) {
                $view->with('settings', $settings);
            }
            if (! $view->offsetExists('socialLinks')) {
                $view->with('socialLinks', SocialLinkPresenter::map($settings));
            }
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $verificationUrl) {
            $loginUrl = rtrim(config('app.url'), '/').'/login';
            $registrationCode = $notifiable->registration_number ?? null;

            $mail = (new MailMessage)
                ->subject(__('Verify Email Address'))
                ->line(__('Please click the button below to verify your email address.'))
                ->action(__('Verify Email Address'), $verificationUrl)
                ->line('Your registration is not complete yet.')
                ->line('Please log in to the IRCC Dashboard to complete the remaining steps.')
                ->line('✅ Don’t Miss These Steps:')
                ->line('🔗 '.$loginUrl)
                ->line('➡️ Go to Dashboard')
                ->line('➡️ Add participant details')
                ->line('➡️ Complete payment to confirm your spot');

            if ($registrationCode) {
                $mail->line(new HtmlString(
                    '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:12px 0;">'
                    .'<tr><td style="background-color:#E8EEF6;padding:14px 18px;border-radius:8px;border-left:4px solid #F15B43;font-family:ui-sans-serif,system-ui,sans-serif;font-size:15px;line-height:1.5;color:#333;">'
                    .'<strong>Registration code:</strong> '.e($registrationCode)
                    .'</td></tr></table>'
                ));
            }

            return $mail->line(__('If you did not create an account, no further action is required.'));
        });
    }
}
