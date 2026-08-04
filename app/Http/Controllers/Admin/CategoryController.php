<?php

namespace App\Http\Controllers\Admin;
use App\Models\CategoryClass;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $category = Category::select('categories.*');

            return $datatables->eloquent($category)
                ->editColumn('type', function (Category $category) {
                    return $category->type==1?'No Gade/Age limit':'Grade';
                })
                ->editColumn('grade', function (Category $category) {
                    $classes = '-';

                    if ($category->type == 2 && $category->categoryClasses()->exists()) {
                        $classes = $category->categoryClasses->pluck('class')->join(', ');
                    }

                    return $classes;
                })
                ->editColumn('status', function (Category $category) {
                    return  $category->status==1?'<span class="text-success">Enable</span>':'<span class="text-danger">Disable</span>';
                    //return ($team->status == 0 ? '<span class="label label-lg font-weight-bold label-light-danger label-inline">Disabled</span>' : '<span class="label label-lg font-weight-bold label-light-success label-inline">Enabled</span>');

                })
                ->addColumn('action', function (Category $category) use($request) {

                    return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.category.edit', $category->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $category->id.'" data-href="' . route("admin.category.destroy", $category->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-category" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>

                        </div>
                        </div>';
                })

                ->rawColumns([ 'action','grade','type','status'])
                ->make(true);

        }

        return view('admin.category.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = range(1, 12);
        $classes[] = 'other';

        return view('admin.category.form')->with([
            'category' => new Category(),
            'classes' => $classes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validClasses = range(1, 12);
        $validClasses[] = 'Others/ Special Schools';


        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'class' => ['required_if:type,==,2', 'array'], // Ensure class is an array
            'class.*' => ['required_if:type,==,2', Rule::in($validClasses)], // Validate each class
        ],
        [
            'class.required_if'=> 'The class field is required in Grade type category', 
        ]);


        // Save the category
        $request->merge(['prefix' => '', 'last_number' => 0]);
        $category = Category::create($request->only(['name', 'type', 'prefix', 'last_number']));
        if(!empty($request->class)){
        foreach ($request->class as $class) {
            CategoryClass::create([
                'category_id' => $category->id,
                'class' => $class,
                'status' => 1,
            ]);
        }
      }

        return redirect()->route('admin.category.index')->with(['success_message' => 'Category submitted successfully']);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $classes = range(1, 12);
        $classes[] = 'Others/ Special Schools';

        $category = Category::with('categoryClasses')->whereId($id)->firstOrFail();

        $selectedClasses = $category->categoryClasses->pluck('class')->toArray();

        return view('admin.category.form')->with([
            'category' => $category,
            'classes' => $classes,
            'selectedClasses' => $selectedClasses,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validClasses = range(1, 12);
        $validClasses[] = 'Others/ Special Schools';

        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'class' => ['required_if:type,==,2', 'array'], // Ensure class is an array
            'class.*' => ['required_if:type,==,2', Rule::in($validClasses)], // Validate each class
        ],
        [
            'class.required_if'=> 'The class field is required in Grade type category', 
        ]);

        $category = Category::whereId($id)->firstOrFail();
        $category->update($request->only(['name', 'type','status']));

        $currentClasses = $category->categoryClasses->pluck('class')->toArray();

        $newClasses = $request->class;
        if(empty($newClasses))
        $newClasses=[];
        $classesToAdd = array_diff($newClasses, $currentClasses);
        $classesToRemove = array_diff($currentClasses, $newClasses);

        if (!empty($classesToRemove)) {
            CategoryClass::where('category_id', $category->id)
                ->whereIn('class', $classesToRemove)
                ->delete();
        }
        if(!empty($classesToAdd)){
        foreach ($classesToAdd as $class) {
            CategoryClass::create([
                'category_id' => $category->id,
                'class' => $class,
                'status' => 1,
            ]);
        }
       }

        return redirect()->route('admin.category.index')->with(['success_message' => 'Category updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category= Category::where("id", $id)->first();
        Category::where("id", $id)->delete();
        return response()
            ->json(["success" => "Category deleted successfully"]);
    }
}
