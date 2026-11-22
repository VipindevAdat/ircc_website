<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $landingPages = LandingPage::orderBy('year', 'desc')->get();
        return view('admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        return view('admin.landing-pages.form', ['landingPage' => new LandingPage()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|unique:landing_pages,year',
            'title' => 'nullable|string|max:255',
            'header_logo' => 'nullable|file|image|max:5120',
            'footer_logo' => 'nullable|file|image|max:5120',
            'banner_image' => 'nullable|file|image|max:10240',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:102400',
            'video_thumbnail' => 'nullable|file|image|max:5120',
        ]);

        $landingPage = LandingPage::create($request->only(['year', 'title', 'is_active']));
        $this->handleMediaUploads($request, $landingPage);

        return redirect()->route('admin.landing-pages.index')->with('success_message', 'Landing page created successfully');
    }

    public function edit(LandingPage $landingPage)
    {
        return view('admin.landing-pages.form', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $request->validate([
            'year' => 'required|integer|unique:landing_pages,year,' . $landingPage->id,
            'title' => 'nullable|string|max:255',
            'header_logo' => 'nullable|file|image|max:5120',
            'footer_logo' => 'nullable|file|image|max:5120',
            'banner_image' => 'nullable|file|image|max:10240',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:102400',
            'video_thumbnail' => 'nullable|file|image|max:5120',
        ]);
        $landingPage->update($request->only(['year', 'title', 'is_active']));
        $this->handleMediaUploads($request, $landingPage);

        return redirect()->route('admin.landing-pages.index')->with('success_message', 'Landing page updated successfully');
    }

    protected function handleMediaUploads(Request $request, LandingPage $landingPage): void
    {
        $imageFields = [
            'header_logo' => [1000, 400],
            'footer_logo' => [1000, 400],
            'banner_image' => [2000, 1200],
            'video_thumbnail' => [1600, 900],
        ];

        foreach (['header_logo', 'footer_logo', 'banner_image', 'video', 'video_thumbnail'] as $field) {
            if ($request->hasFile($field)) {
                if ($landingPage->{$field}) {
                    Storage::disk('uploads')->delete($landingPage->{$field});
                }

                if (isset($imageFields[$field])) {
                    $path = $this->storeImage(
                        $request->file($field),
                        'landing-pages/' . $landingPage->id,
                        $imageFields[$field][0],
                        $imageFields[$field][1]
                    );
                } else {
                    $path = $request->file($field)->store('landing-pages/' . $landingPage->id, 'uploads');
                }

                $landingPage->update([$field => $path]);
            }
        }
    }

    protected function storeImage(\Illuminate\Http\UploadedFile $file, string $directory, int $maxWidth, int $maxHeight): string
    {
        $sourcePath = $file->getRealPath();
        $info = @getimagesize($sourcePath);

        // Fall back to the original file if GD cannot read it.
        if ($info === false) {
            return $file->store($directory, 'uploads');
        }

        $mime = $info['mime'] ?? $file->getMimeType();
        $width = $info[0];
        $height = $info[1];

        $needsResize = $width > $maxWidth || $height > $maxHeight;
        $needsCompress = $file->getSize() > 2 * 1024 * 1024;

        // Store the original when it is already small enough.
        if (!$needsResize && !$needsCompress) {
            return $file->store($directory, 'uploads');
        }

        $image = @imagecreatefromstring(file_get_contents($sourcePath));
        if ($image === false) {
            return $file->store($directory, 'uploads');
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF/WebP.
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'], true)) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $extension = match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => $file->getClientOriginalExtension() ?: 'jpg',
        };

        $tempPath = tempnam(sys_get_temp_dir(), 'landing_img_');
        $saved = false;

        if ($extension === 'jpg') {
            $saved = imagejpeg($canvas, $tempPath, 80);
        } elseif ($extension === 'png') {
            $saved = imagepng($canvas, $tempPath, 6);
        } elseif ($extension === 'gif') {
            $saved = imagegif($canvas, $tempPath);
        } elseif ($extension === 'webp') {
            $saved = imagewebp($canvas, $tempPath, 80);
        }

        imagedestroy($image);
        imagedestroy($canvas);

        if (!$saved) {
            @unlink($tempPath);
            return $file->store($directory, 'uploads');
        }

        $filename = uniqid('img_', true) . '.' . $extension;
        $relativePath = trim($directory, '/') . '/' . $filename;
        Storage::disk('uploads')->put($relativePath, file_get_contents($tempPath));
        @unlink($tempPath);

        return $relativePath;
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();
        return response()->json(['success' => 'Landing page deleted successfully'], 200);
    }
}