<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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
        return view('admin.account.profile');
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',

        ]);
        $user = User::find($user);
        $user->update($request->all());
        return redirect()->route('admin.account.index')->with('success_message', 'Profile Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
    public function allusers(Request $request, DataTables $datatables)
    {

        if ($request->ajax()) {
            $query = User::select('users.*');

            return $datatables->eloquent($query)
                    
            ->addColumn('status', function (User $user) {
                return  $user->status==1?'<span class="text-success">Active</span>':'<span class="text-danger">Inactive</span>';
            })
            ->addColumn('action', function (User $user) {
                return '<div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating " aria-haspopup="true" aria-expanded="true">
                    <i class="bi bi-three-dots"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end " >'
                    .(Auth::user()->can('user-edit') ? '<a href="'.route('admin.account.edit', $user->id).'" class="dropdown-item">Edit</a>' : '' ).
                    (Auth::user()->can('user-delete') ?  '<a data-bs-toggle="modal" href="#client-delete" data-href="' . route('admin.account.destroy', $user->id) . '" class="dropdown-item text-danger client-delete">Delete</a>':'').
                '</div>
            </div>';
            })
            ->rawColumns(['action','status',])
   
            ->make(true);
        }
        return view('admin.user.list');
      
    }
    public function updatePassword(Request $request)
    {

        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:6|same:password',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::find(Auth::user()->id);

        if (!Hash::check($request->current_password, $user->password)) {

            return redirect()->route('admin.account.index')->with('error_message', 'Current password does not match');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.account.index')->with('success_message', 'Password Updated Successfully');
    }

}
