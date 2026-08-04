<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class CategoryClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $category = CategoryClass::with('category')->select('category_classes.*');

            return $datatables->eloquent($category)
                ->editColumn('category_id', function (CategoryClass $categoryClass) {
                    return $categoryClass->category ? $categoryClass->category->name : 'No Category';
                })
                ->editColumn('status', function (CategoryClass $category) {
                    return  $category->status==1?'<span class="text-success">Enable</span>':'<span class="text-danger">Disable</span>';

                })
//                ->addColumn('action', function (CategoryClass $category) use($request) {
//
//                    return '<div class="d-flex">
//                        <div class="dropdown ms-auto">
//                        <a href="' . route('admin.category-class.edit', $category->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
//                        <a data-id="'. $category->id.'" data-href="' . route("admin.category-class.destroy", $category->id) . '"   data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-review" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
//
//                        </div>
//                        </div>';
//                })

                ->rawColumns([ 'action','category','status','class'])
                ->make(true);

        }

        return view('admin.category_class.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();

        return view('admin.category_class.form')->with([
            'categoryClass' => new CategoryClass(),
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validClasses = range(1, 12);
        $validClasses[] = 'other';

        $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'class' => ['required', 'array'],
            'class.*' => ['required', Rule::in($validClasses)],
            'status' => 'required|boolean',
        ]);

        $categoryId = $request->category_id;
        $status = $request->status;

        foreach ($request->class as $class) {
            CategoryClass::create([
                'category_id' => $categoryId,
                'class' => $class,
                'status' => $status,
            ]);
        }

        return redirect()->route('admin.category-class.index')->with(['success_message' => 'Classes submitted successfully']);
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
        $categoryClass = CategoryClass::findOrFail($id);

        $categories = Category::where('status', 1)->get();

        return view('admin.category_class.form', compact('categoryClass', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validClasses = range(1, 12);
        $validClasses[] = 'other';

        $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'class' => ['required', 'array'],
            'class.*' => ['required', Rule::in($validClasses)],
            'status' => 'required|boolean',
        ]);

        $existingCategoryClass = CategoryClass::findOrFail($id);

        $categoryId = $request->category_id;
        $status = $request->status;

        CategoryClass::where('category_id', $categoryId)->delete();

        foreach ($request->class as $class) {
            CategoryClass::create([
                'category_id' => $categoryId,
                'class' => $class,
                'status' => $status,
            ]);
        }

        return redirect()->route('admin.category-class.index')->with(['success_message' => 'Classes updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        CategoryClass::where("id", $id)->delete();
        return response()
            ->json(["success" => "Review deleted successfully"]);
    }
}
