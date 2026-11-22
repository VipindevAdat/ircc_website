<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Plank\Mediable\Media;
use MediaUploader;
class GalleryController extends Controller {
    public function index(Request $request, Datatables $datatables) {
        if ($request->ajax()) {
            $gallery = Gallery::select("galleries.*");
            if ($request->filled('year')) {
                $gallery->where('year', $request->year);
            }
            return $datatables->eloquent($gallery)->addColumn("image", function (Gallery $gallery) {
                $img = $gallery->getMedia("gallery")->first() ? $gallery->getMedia("gallery")->first()->getUrl() : url("assets/images/no-image.png");
                return '<a href="#"><img src="' . $img . '" class="rounded" width="30" height="30" alt="..."></a>';
            })->editColumn("status", function ($query) use ($request) {
                return $query->status == 1 ? '<span class="text-success">Enabled</span>' : '<span class="text-danger">Disabled</span>';
                //return ($location->status == 0 ? '<span class="label label-lg font-weight-bold label-light-danger label-inline">Disabled</span>' : '<span class="label label-lg font-weight-bold label-light-success label-inline">Enabled</span>');
            })->editColumn("is_home", function (Gallery $gallery) {
                return $gallery->is_home == 1 ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<span class="text-danger">--</span>';
               
            })->addColumn("action", function (Gallery $gallery) use ($request) {
                return '<div class="d-flex">
                    <div class="dropdown ms-auto">
                      <a href="' . route("admin.gallery.edit", $gallery->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a data-id="' . $gallery->id . '"  data-href="' . route("admin.gallery.destroy", $gallery->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-gallery" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </div>
                    </div>';
            })->rawColumns(["image","is_home", "status", "action", "created_at"])->make(true);
        }
        return view("admin.gallery.list");
    }
    public function create(\Illuminate\Http\Request $request) {
        $gallery = new Gallery();
        if ($request->filled('year')) {
            $gallery->year = $request->year;
        }
        return view("admin.gallery.form")->with(["gallery" => $gallery ]);
    }
    public function store(Request $request) {
        $this->validate($request, ["name" => "required"]);
        if ($request->gallery_type == 1) {
            if (!empty($request->file("file-upload"))) {
                $gallery = gallery::create($request->all());
            } else {
                return redirect()->route("admin.gallery.index")->with(["failure_message" => "You cannot add gallery without image.", ]);
            }
        } else {
            $gallery = gallery::create($request->all());
        }
        if (!empty($request->file("file-upload"))) {
            foreach ($request->file("file-upload") as $file) {
                $time = time();
                $filename = "IRCC_" . $time;
                $media = MediaUploader::fromSource($file)
                    ->useFilename($filename)
                    ->toDirectory("gallery/" . $gallery->id)
                    ->toDisk('uploads')
                    ->upload();
                $gallery->attachMedia($media, ["gallery"]);
            }
        }
        return redirect()->route("admin.gallery.index")->with(["success_message" => "Gallery added successfully."]);
    }
    public function edit(Gallery $gallery) {
        $galleryImg = "";
        if (!empty($gallery->getMedia("gallery")->first())) {
            $galleryImg = $gallery->getMedia("gallery")->first();
        }
        // dd($featured);
        return view("admin.gallery.form")->with(["gallery" => $gallery, "featured" => $galleryImg, ]);
    }
    public function update(Request $request, Gallery $gallery) {
        $gallery_images = $gallery->getMedia("gallery");
        $gallery->update($request->all());
        if (!empty($request->file("file-upload"))) {
            foreach ($request->file("file-upload") as $file) {
                $time = time();
                $filename = "IRCC_" . $time;
                $media = MediaUploader::fromSource($file)
                    ->useFilename($filename)
                    ->toDirectory("gallery/" . $gallery->id)
                    ->toDisk('uploads')
                    ->upload();
                $gallery->attachMedia($media, ["gallery"]);
            }
        }
        return redirect()->route("admin.gallery.index")->with(["success_message" => "Gallery updated successfully."]);
    }
    public function destroy(string $id) {
        Gallery::where("id", $id)->delete();
        return response()->json(["success" => "gallery deleteted successfully"], 200);
    }
    public function deleteGalleryById(Request $request) {
        $galleryId = $request->galleryId;
        $gallery = Gallery::where("id", $galleryId)->first();
        $status = $gallery->getMedia("gallery")->where("id", $request->id)->first()->delete();
        return $status;
    }
}
