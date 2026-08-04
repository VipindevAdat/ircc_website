<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TeamCategory;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
class TeamCategoryController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables) {
        if ($request->ajax()) {
            $teamCategory = TeamCategory::select('team_categories.*');
            return $datatables->eloquent($teamCategory)->editColumn('parent', function (TeamCategory $teamCategory) {
                return $teamCategory->parent_id ? optional(TeamCategory::whereId($teamCategory->parent_id)->first())->name : '--';
            })->editColumn('status', function (TeamCategory $teamCategory) {
                return $teamCategory->status == 1 ? '<span class="text-success">Enable</span>' : '<span class="text-danger">Disable</span>';
                //return ($team->status == 0 ? '<span class="label label-lg font-weight-bold label-light-danger label-inline">Disabled</span>' : '<span class="label label-lg font-weight-bold label-light-success label-inline">Enabled</span>');
                
            })->addColumn('action', function (TeamCategory $teamCategory) use ($request) {
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.team-category.edit', $teamCategory->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="' . $teamCategory->id . '" data-href="' . route("admin.team-category.destroy", $teamCategory->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-category" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })->rawColumns(['action', 'grade', 'type', 'status'])->make(true);
        }
        return view('admin.team_category.list');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('admin.team_category.form')->with(['teamCategory' => new TeamCategory(), 'categories' => TeamCategory::where('status', 1)->get() ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $this->validate($request, ['name' => 'required', ]);
        $teamCategory = TeamCategory::create($request->all());
        return redirect()->route('admin.team-category.index')->with(['success_message' => 'Category submitted succesfully']);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
        
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        return view('admin.team_category.form')->with(['teamCategory' => TeamCategory::whereId($id)->first(), 'categories' => TeamCategory::where('id', '<>', $id)->where('status', 1)->get() ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        $teamCategory = TeamCategory::whereId($id)->first();
        $teamCategory->update($request->all());
        return redirect()->route('admin.team-category.index')->with(['success_message' => 'Category updated succesfully']);;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
    
        if(($id<>1)&&($id<>2)){
           
            $teamCategory=TeamCategory::with('teams')->whereId($id)->first();
            if(!$teamCategory->teams->count()){
              TeamCategory::where("id", $id)->delete();
                return response()->json(["success" => "Team category deleteted successfully"], 200);
            }
            else{
                return response()->json(["error" =>"You can't delete this category. Please delete category teams first"], 500);   
            }
           
        }
        else{
            return response()->json(["error" => "You can't delete this category"], 500);
        }
        
    }
}
