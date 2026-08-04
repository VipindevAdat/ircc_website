<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Reviewer;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Yajra\Datatables\Datatables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviewer = Reviewer::find(Auth::guard('reviewer')->id());
        return view('reviewer.account.profile', compact('reviewer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reviewer  $Reviewer
     * @return \Illuminate\Http\Response
     */
    public function show(Reviewer $Reviewer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reviewer  $Reviewer
     * @return \Illuminate\Http\Response
     */
    public function edit(Reviewer $Reviewer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reviewer  $Reviewer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Reviewer)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',

        ]);
        $Reviewer = Reviewer::find($Reviewer);
        $Reviewer->update($request->all());
        return redirect()->route('reviewer.account.index')->with('success_message', 'Profile Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reviewer  $Reviewer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reviewer $Reviewer)
    {
        //
    }
    public function allReviewers(Request $request, DataTables $datatables)
    {

        if ($request->ajax()) {
            $query = Reviewer::select('Reviewers.*');

            return $datatables->eloquent($query)
                    
            ->addColumn('status', function (Reviewer $Reviewer) {
                return  $Reviewer->status==1?'<span class="text-success">Active</span>':'<span class="text-danger">Inactive</span>';
            })
            ->addColumn('action', function (Reviewer $Reviewer) {
                return '<div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating " aria-haspopup="true" aria-expanded="true">
                    <i class="bi bi-three-dots"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end " >'
                    .(Auth::Reviewer()->can('Reviewer-edit') ? '<a href="'.route('reviewer.account.edit', $Reviewer->id).'" class="dropdown-item">Edit</a>' : '' ).
                    (Auth::Reviewer()->can('Reviewer-delete') ?  '<a data-bs-toggle="modal" href="#client-delete" data-href="' . route('reviewer.account.destroy', $Reviewer->id) . '" class="dropdown-item text-danger client-delete">Delete</a>':'').
                '</div>
            </div>';
            })
            ->rawColumns(['action','status',])
   
            ->make(true);
        }
        return view('reviewer.Reviewer.list');
      
    }
    public function updatePassword(Request $request)
    {

        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:6|same:password',
            'password_confirmation' => 'required|same:password',
        ]);


        $Reviewer = Reviewer::find(Auth::guard('reviewer')->id());

        if (!Hash::check($request->current_password, $Reviewer->password)) {

            return redirect()->route('reviewer.account.index')->with('error_message', 'Current password does not match');
        }

        $Reviewer->password = Hash::make($request->password);
        $Reviewer->save();

        return redirect()->route('reviewer.account.index')->with('success_message', 'Password Updated Successfully');
    }

}
