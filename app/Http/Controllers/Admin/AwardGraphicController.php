<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AwardGraphic;
use App\Models\Setting;
use Illuminate\Http\Request;
use MediaUploader;
use Plank\Mediable\Media;

class AwardGraphicController extends Controller
{
    private const AWARDS_HEADING_CODES = ['awards_banner_title', 'awards_section_heading'];

    public function index()
    {
        $graphics = AwardGraphic::query()
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $settings = Setting::query()->whereIn('code', self::AWARDS_HEADING_CODES)->get()->keyBy('code');

        return view('admin.award-graphics.index', [
            'graphics' => $graphics,
            'bannerTitle' => $settings->get('awards_banner_title'),
            'sectionHeading' => $settings->get('awards_section_heading'),
        ]);
    }

    public function updateHeadings(Request $request)
    {
        $request->validate([
            'awards_banner_title' => 'nullable|string|max:255',
            'awards_section_heading' => 'nullable|string|max:255',
        ]);

        foreach (self::AWARDS_HEADING_CODES as $code) {
            $v = trim((string) $request->input($code, ''));
            Setting::query()->where('code', $code)->update(['value' => $v]);
        }

        return redirect()->route('admin.award-graphics.index')
            ->with('success_message', 'Awards page headings updated.');
    }

    public function create()
    {
        $nextSort = (int) (AwardGraphic::query()->max('sort_order') ?? 0) + 1;

        return view('admin.award-graphics.form', [
            'graphic' => new AwardGraphic(['sort_order' => $nextSort, 'status' => 1]),
            'featured' => null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
            'featured' => 'required|file|image|max:10240',
        ]);

        $graphic = AwardGraphic::query()->create([
            'alt_text' => $request->input('alt_text'),
            'sort_order' => (int) $request->input('sort_order'),
            'status' => (int) $request->input('status'),
        ]);

        $this->attachFeatured($graphic, $request);

        return redirect()->route('admin.award-graphics.index')
            ->with('success_message', 'Award graphic added.');
    }

    public function edit(string $id)
    {
        $graphic = AwardGraphic::query()->findOrFail($id);
        $featured = $graphic->firstMedia('award');

        return view('admin.award-graphics.form', [
            'graphic' => $graphic,
            'featured' => $featured,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $graphic = AwardGraphic::query()->findOrFail($id);

        $request->validate([
            'alt_text' => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|file|image|max:10240',
        ]);

        $graphic->update([
            'alt_text' => $request->input('alt_text'),
            'sort_order' => (int) $request->input('sort_order'),
            'status' => (int) $request->input('status'),
        ]);

        if ($request->hasFile('featured')) {
            foreach ($graphic->getMedia('award') as $m) {
                $m->delete();
            }
            $this->attachFeatured($graphic, $request);
            $graphic->update(['public_asset_path' => null]);
        }

        return redirect()->route('admin.award-graphics.index')
            ->with('success_message', 'Award graphic updated.');
    }

    public function destroy(string $id)
    {
        $graphic = AwardGraphic::query()->findOrFail($id);
        foreach ($graphic->getMedia('award') as $m) {
            $m->delete();
        }
        $graphic->delete();

        return redirect()->route('admin.award-graphics.index')
            ->with('success_message', 'Award graphic removed.');
    }

    private function attachFeatured(AwardGraphic $graphic, Request $request): void
    {
        $time = time();
        $filename = 'award_' . $graphic->id . '_' . $time;
        $media = MediaUploader::fromSource($request->file('featured'))
            ->useFilename($filename)
            ->toDirectory('awards/' . $graphic->id)
            ->toDisk('uploads')
            ->upload();
        $graphic->attachMedia($media, ['award']);
    }
}
