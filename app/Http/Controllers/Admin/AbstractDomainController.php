<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbstractDomain;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AbstractDomainController extends Controller
{
    /**
     * Display a listing of the abstract domains.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $domains = AbstractDomain::select('abstract_domains.*');

            return DataTables::eloquent($domains)
                ->editColumn('status', function (AbstractDomain $domain) {
                    return $domain->status ? 'Enabled' : 'Disabled';
                })
                ->addColumn('action', function (AbstractDomain $domain) use($request) {

                    return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.domain.edit', $domain->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $domain->id.'" data-href="' . route("admin.domain.destroy", $domain->id) . '"   data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-review" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>

                        </div>
                        </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.abstract_domain.list');
    }

    /**
     * Show the form for creating a new abstract domain.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.abstract_domain.form')->with([
            'domain' => new AbstractDomain(),
        ]);
    }

    /**
     * Store a newly created abstract domain in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255|unique:abstract_domains,domain',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        AbstractDomain::create($request->all());

        return redirect()->route('admin.domain.index')->with('success', 'Abstract Domain added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified abstract domain.
     *
     * @param \App\Models\AbstractDomain $abstractDomain
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(AbstractDomain $domain)
    {
        return view('admin.abstract_domain.form', compact('domain'));
    }

    /**
     * Update the specified abstract domain in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AbstractDomain $abstractDomain
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AbstractDomain $domain)
    {

        $domain->update($request->all());

        return redirect()->route('admin.domain.index')->with('success', 'Abstract Domain updated successfully.');
    }

    /**
     * Remove the specified abstract domain from the database.
     *
     * @param \App\Models\AbstractDomain $abstractDomain
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $reviewer= AbstractDomain::where("id", $id)->first();
        AbstractDomain::where("id", $id)->delete();
        return response()
            ->json(["success" => "Review deleted successfully"]);
    }
}
