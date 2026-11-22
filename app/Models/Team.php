<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;

class Team extends Model
{
    use HasFactory;
    use Mediable;
    protected $fillable = [
        'name', 'team_category_id', 'designation', 'qualification', 'organization', 'location','is_home', 'sort_order', 'status', 'year'
    ];
    public function category(){
        return $this->belongsTo(TeamCategory::class,'team_category_id','id');
    }

    /**
     * Absolute URL for uploaded portrait (mediable tag "team").
     * When $useCommitteeNameFallbacks is true (committee page only), uses known static / upload paths
     * for leaders who appear without a DB upload or with a duplicate Team row and no media.
     */
    public function portraitUrl(string $fallbackRelative = 'front/images/user-img.png', bool $useCommitteeNameFallbacks = false): string
    {
        $media = $this->firstMedia('team');
        if ($media) {
            return self::publicUrlForMedia($media);
        }
        if ($useCommitteeNameFallbacks) {
            $named = self::resolvePortraitForCommitteeByName($this->name);
            if ($named !== null) {
                return $named;
            }
        }

        return asset($fallbackRelative);
    }

    /**
     * Resolve portrait for hard-coded committee leadership rows: prefer uploaded Team media, then explicit paths.
     */
    public static function committeePortraitUrl(string $displayName, ?string $preferStaticRelative = null, ?string $preferUploadRelative = null): string
    {
        $team = self::query()->where('status', 1)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($displayName))])
            ->first();
        if ($team) {
            $media = $team->firstMedia('team');
            if ($media) {
                return self::publicUrlForMedia($media);
            }
        }
        if ($preferStaticRelative) {
            return self::publicThemeFileUrl($preferStaticRelative);
        }
        if ($preferUploadRelative) {
            return self::upgradeHttpToHttpsWhenNeeded(url('/' . ltrim($preferUploadRelative, '/')));
        }

        return self::resolvePortraitForCommitteeByName($displayName)
            ?? asset('front/images/user-img.png');
    }

    /**
     * Known committee faces when theme files or uploads exist under public docroot.
     */
    protected static function resolvePortraitForCommitteeByName(string $name): ?string
    {
        $n = mb_strtolower(trim($name));
        $static = match ($n) {
            'devi gireesh' => 'front/images/committee/chair.png',
            'dr. irimpan mathews', 'irimpan mathews' => 'front/images/committee/drmathews.png',
            'padmapriya palottu' => 'front/images/committee/padmapriya-palottu.png',
            default => null,
        };
        if ($static !== null) {
            return self::publicThemeFileUrl($static);
        }
        $uploadRel = match ($n) {
            'dr. finosh thankam', 'finosh thankam' => 'uploads/teams/19/GYRA_1773885376.jpg',
            default => null,
        };
        if ($uploadRel !== null) {
            return self::upgradeHttpToHttpsWhenNeeded(url('/' . ltrim($uploadRel, '/')));
        }

        return null;
    }

    /**
     * Public URL for a mediable row (e.g. speakers who use the same uploads disk).
     */
    public static function publicUrlForMedia(Media $media): string
    {
        $raw = $media->getUrl();

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return self::upgradeHttpToHttpsWhenNeeded($raw);
        }
        if (str_starts_with($raw, '//')) {
            $raw = (URL::getRequest()?->getScheme() ?? 'https') . ':' . $raw;
        } else {
            $raw = url('/' . ltrim($raw, '/'));
        }

        return self::upgradeHttpToHttpsWhenNeeded($raw);
    }

    /**
     * Absolute URL for a theme file under public/. Uses root-relative url() (not asset()) so ASSET_URL
     * cannot send browsers to a CDN path that does not host these files. When the file exists with a
     * different letter case on disk (e.g. chair.PNG), that path is used.
     */
    public static function publicThemeFileUrl(string $relative): string
    {
        $clean = str_replace('\\', '/', ltrim($relative, '/'));
        $full = public_path($clean);
        if (is_file($full)) {
            return self::upgradeHttpToHttpsWhenNeeded(url('/' . $clean));
        }
        $dir = dirname($full);
        $basename = basename($full);
        if (is_dir($dir)) {
            $wantedStem = pathinfo($basename, PATHINFO_FILENAME);
            $wantedExt = pathinfo($basename, PATHINFO_EXTENSION);
            foreach (scandir($dir) ?: [] as $f) {
                if ($f === '.' || $f === '..') {
                    continue;
                }
                if (strcasecmp((string) pathinfo($f, PATHINFO_FILENAME), (string) $wantedStem) !== 0) {
                    continue;
                }
                if ($wantedExt !== '' && strcasecmp((string) pathinfo($f, PATHINFO_EXTENSION), (string) $wantedExt) !== 0) {
                    continue;
                }
                if ($wantedExt === '' && pathinfo($f, PATHINFO_EXTENSION) !== '') {
                    continue;
                }
                $relDir = dirname($clean);
                $path = ($relDir === '.' ? '' : $relDir . '/') . $f;
                if (is_file(public_path($path))) {
                    return self::upgradeHttpToHttpsWhenNeeded(url('/' . $path));
                }
            }
        }

        return self::upgradeHttpToHttpsWhenNeeded(url('/' . $clean));
    }

    protected static function upgradeHttpToHttpsWhenNeeded(string $url): string
    {
        if (! str_starts_with($url, 'http://')) {
            return $url;
        }
        $app = rtrim((string) config('app.url'), '/');
        if (str_starts_with($app, 'https://') || request()?->secure()) {
            return 'https://' . substr($url, 7);
        }

        return $url;
    }
}
