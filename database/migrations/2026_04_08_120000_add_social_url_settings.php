<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_facebook',
                'title' => 'Facebook URL',
                'placeholder' => 'https://www.facebook.com/yourpage',
                'value' => 'https://www.facebook.com/STEMIRCC',
            ],
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_facebook_stem4g',
                'title' => 'Facebook URL (STEM4Girls block on About)',
                'placeholder' => 'https://www.facebook.com/STEM4G',
                'value' => 'https://www.facebook.com/STEM4G',
            ],
            [
                'field_type' => 'text',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_whatsapp',
                'title' => 'WhatsApp (full https://wa.me/… or digits only)',
                'placeholder' => 'https://wa.me/9714008683',
                'value' => 'https://wa.me/9714008683',
            ],
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_twitter',
                'title' => 'Twitter / X URL',
                'placeholder' => 'https://twitter.com/yourhandle',
                'value' => 'https://www.twitter.com/',
            ],
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_youtube',
                'title' => 'YouTube URL',
                'placeholder' => 'https://www.youtube.com/@channel',
                'value' => 'https://www.youtube.com/',
            ],
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_instagram',
                'title' => 'Instagram URL',
                'placeholder' => 'https://www.instagram.com/yourprofile',
                'value' => 'https://www.instagram.com/',
            ],
            [
                'field_type' => 'url',
                'dataset' => null,
                'data_source' => '',
                'code' => 'social_url_linkedin',
                'title' => 'LinkedIn URL',
                'placeholder' => 'https://www.linkedin.com/company/…',
                'value' => 'https://www.linkedin.com/',
            ],
        ];

        foreach ($rows as $row) {
            if (! Setting::query()->where('code', $row['code'])->exists()) {
                Setting::query()->create(array_merge($row, [
                    'status' => 1,
                ]));
            }
        }
    }

    public function down(): void
    {
        Setting::query()->whereIn('code', [
            'social_url_facebook',
            'social_url_facebook_stem4g',
            'social_url_whatsapp',
            'social_url_twitter',
            'social_url_youtube',
            'social_url_instagram',
            'social_url_linkedin',
        ])->delete();
    }
};
