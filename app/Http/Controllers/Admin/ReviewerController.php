<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbstractDomain;
use App\Models\Category;
use App\Models\Reviewer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReviewerController extends Controller
{
    /**
     * Display a listing of the reviewers.
     *
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {
            $reviewers = Reviewer::select('reviewers.*');

            return $datatables->eloquent($reviewers)
                ->addColumn('full_name', function (Reviewer $reviewer) {
                    return $reviewer->first_name . ' ' . $reviewer->last_name;
                })
                ->editColumn('category', function (Reviewer $reviewer) {
                    return $reviewer->category ? $reviewer->category : '--';
                })
                ->editColumn('email', function (Reviewer $reviewer) {
                    return wordwrap($reviewer->email, 20, '<br>');
                })
                ->addColumn('action', function (Reviewer $reviewer) use($request) {

                    return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.reviewer.edit', $reviewer->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $reviewer->id.'" data-href="' . route("admin.reviewer.destroy", $reviewer->id) . '"   data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-review" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>

                        </div>
                        </div>';
                })
                ->addColumn('sort_order', function (Reviewer $reviewer) {
                    return $reviewer->sort_order;
                })
                    ->editColumn('status', function (Reviewer $reviewer) {
                        return $reviewer->status ? 'Enabled' : 'Disabled';
                    })
                ->rawColumns(['action', 'email', 'category'])
                ->make(true);

        }


        return view('admin.reviewer.list');
    }

    /**
     * Show the form for creating a new reviewer.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create()
    {
        $categories = AbstractDomain::orderBy('domain', 'ASC')->get();
        return view('admin.reviewer.form')->with([
            'reviewer' => new Reviewer(),
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created reviewer in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:reviewers,email',
            'category' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);
        $request->merge(['password'=>Hash::make('12345678')]);
        Reviewer::create($request->all());

        return redirect()->route('admin.reviewer.index')->with('success', 'Reviewer added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified reviewer.
     *
     * @param  \App\Models\Reviewer  $reviewer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function edit(Reviewer $reviewer)
    {
        $categories = AbstractDomain::orderBy('domain', 'ASC')->get();
        return view('admin.reviewer.form')->with([
            'reviewer' => $reviewer,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified reviewer in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reviewer  $reviewer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Reviewer $reviewer)
    {
        $request->merge(['password'=>Hash::make('12345678')]);
        $reviewer->update($request->all());

        return redirect()->route('admin.reviewer.index')->with('success', 'Reviewer updated successfully.');
    }

    /**
     * Remove the specified reviewer from the database.
     */
    public function destroy(string $id)
    {
        $reviewer= Reviewer::where("id", $id)->first();
        Reviewer::where("id", $id)->delete();
        return response()
            ->json(["success" => "Review deleted successfully"]);
    }
}
