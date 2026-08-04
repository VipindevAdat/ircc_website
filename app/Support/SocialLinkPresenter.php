<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Reads social URLs from settings (codes social_url_*) with fallbacks to legacy keys.
 */
class SocialLinkPresenter
{
    /**
     * @param  Collection<int, \App\Models\Setting>  $settings
     * @return array<string, string|null> href values (null = hide link)
     */
    public static function map(Collection $settings): array
    {
        $get = static fn (string $code): string => trim((string) optional($settings->where('code', $code)->first())->value);

        $facebook = $get('social_url_facebook');
        if ($facebook === '') {
            $legacy = $get('facebook');
            if ($legacy !== '') {
                $legacy = trim($legacy);
                if (str_starts_with($legacy, 'http://') || str_starts_with($legacy, 'https://')) {
                    $facebook = $legacy;
                } else {
                    $legacy = ltrim($legacy, '/');
                    $facebook = 'https://www.facebook.com/' . $legacy . (str_contains($legacy, '?') ? '' : '?mibextid=LQQJ4d');
                }
            }
        }

        $whatsapp = self::normalizeWhatsappHref($get('social_url_whatsapp') ?: $get('whatsapp'));

        return [
            'facebook' => self::nullIfEmpty($facebook),
            'whatsapp' => self::nullIfEmpty($whatsapp),
            'twitter' => self::nullIfEmpty($get('social_url_twitter')),
            'youtube' => self::nullIfEmpty($get('social_url_youtube')),
            'instagram' => self::nullIfEmpty($get('social_url_instagram')),
            'linkedin' => self::nullIfEmpty($get('social_url_linkedin')),
            'facebook_stem4g' => ($stem = $get('social_url_facebook_stem4g')) !== '' ? $stem : 'https://www.facebook.com/STEM4G',
        ];
    }

    public static function normalizeWhatsappHref(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }
        $digits = preg_replace('/\D+/', '', $raw);

        return $digits !== '' ? 'https://wa.me/' . $digits : '';
    }

    private static function nullIfEmpty(string $v): ?string
    {
        return $v === '' ? null : $v;
    }
}
