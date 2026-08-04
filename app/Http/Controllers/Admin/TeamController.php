<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamCategory;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Plank\Mediable\Media;
use MediaUploader;
class TeamController extends Controller {
   
    public function index(Request $request, Datatables $datatables) {
        if ($request->ajax()) {
            $team = Team::select('teams.*');
            return $datatables->eloquent($team)->editColumn('category', function (Team $team) {
                return $team->team_category_id ? ($team->category->parent ? optional($team->category)->name . ' - ' . optional($team->category->parent)->name : optional($team->category)->name) : '--';
            })->editColumn('is_home', function (Team $team) {
                return $team->is_home == 1 ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<span class="text-danger">--</span>';
            })->editColumn('status', function (Team $team) {
                return $team->status == 1 ? '<span class="text-success">Enable</span>' : '<span class="text-danger">Disable</span>';
                //return ($team->status == 0 ? '<span class="label label-lg font-weight-bold label-light-danger label-inline">Disabled</span>' : '<span class="label label-lg font-weight-bold label-light-success label-inline">Enabled</span>');
                
            })->addColumn('action', function (Team $team) use ($request) {
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.team.edit', $team->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="' . $team->id . '" data-href="' . route("admin.team.destroy", $team->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-team" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })->rawColumns(['action', 'category','is_home', 'status'])->make(true);
        }
        return view('admin.team.list');
    }

    public function create() {
        $teamCategories = TeamCategory::where('status', 1)->whereNull('parent_id')->get();
        $categories = [];
        foreach ($teamCategories as $teamCategory) {
            $categories[$teamCategory->id]['category'] = $teamCategory;
            $categories[$teamCategory->id]['sub_category'] = TeamCategory::where('status', 1)->where('parent_id', $teamCategory->id)->get();
        }
        return view('admin.team.form')->with(['team' => new Team(), 'categories' => $categories]);
    }
   
    public function store(Request $request) {
        $this->validate($request, ['name' => 'required', ]);
        $team = Team::create($request->all());
        if (!empty($request->file('featured'))) {
            $time = time();
            $filename = 'GYRA_' . $time;
            $media = MediaUploader::fromSource($request->file('featured'))
                ->useFilename($filename)
                ->toDirectory('teams/' . $team->id)
                ->toDisk('uploads')
                ->upload();
            $team->attachMedia($media, ['team']);
        }
        return redirect()->route('admin.team.index')->with(['success_message' => 'Team added succesfully']);
    }

    public function show(string $id) {
        //
        
    }

    public function edit(string $id) {
        $featured = '';
        $team = Team::whereId($id)->first();
        if (!empty($team->getMedia('team')->first())) {
            $featured = $team->getMedia('team')->first();
        }
        $teamCategories = TeamCategory::where('status', 1)->whereNull('parent_id')->get();
        $categories = [];
        foreach ($teamCategories as $teamCategory) {
            $categories[$teamCategory->id]['category'] = $teamCategory;
            $categories[$teamCategory->id]['sub_category'] = TeamCategory::where('status', 1)->where('parent_id', $teamCategory->id)->get();
        }
        return view('admin.team.form')->with(['team' => Team::whereId($id)->first(), 'categories' => $categories, 'featured' => $featured, ]);
    }

    public function update(Request $request, string $id) {
        $team = Team::whereId($id)->first();
        $team->update($request->all());
        if (!empty($request->file('featured'))) {
            $images = $team->getMedia('team');
            if (!empty($images)) {
                foreach ($images as $featured) {
                    $featured->delete();
                }
            }
            $time = time();
            $filename = 'GYRA_' . $time;
            $media = MediaUploader::fromSource($request->file('featured'))
                ->useFilename($filename)
                ->toDirectory('teams/' . $team->id)
                ->toDisk('uploads')
                ->upload();
            $team->attachMedia($media, ['team']);
        }
        return redirect()->route('admin.team.index')->with(['success_message' => 'Team updated succesfully']);;
    }

    public function destroy(string $id) {
        $Team = Team::where("id", $id)->first();
        Team::where("id", $id)->delete();
        return response()->json(["success" => "Team deleteted successfully"], 200);
    }
}
