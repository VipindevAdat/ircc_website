<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public const CODES = [
        'social_url_facebook',
        'social_url_facebook_stem4g',
        'social_url_whatsapp',
        'social_url_twitter',
        'social_url_youtube',
        'social_url_instagram',
        'social_url_linkedin',
    ];

    public function index()
    {
        $fetched = Setting::query()->whereIn('code', self::CODES)->get()->keyBy('code');
        $settings = collect(self::CODES)->mapWithKeys(function (string $code) use ($fetched) {
            return [$code => $fetched->get($code)];
        });

        return view('admin.social-media.index', [
            'settings' => $settings,
            'codes' => self::CODES,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach (self::CODES as $code) {
            $rules[$code] = 'nullable|string|max:2000';
        }
        $validated = $request->validate($rules);

        foreach (self::CODES as $code) {
            $value = isset($validated[$code]) ? trim((string) $validated[$code]) : '';
            Setting::query()->where('code', $code)->update(['value' => $value]);
        }

        return redirect()
            ->route('admin.social-media.index')
            ->with('success_message', 'Social media links updated successfully.');
    }
}
