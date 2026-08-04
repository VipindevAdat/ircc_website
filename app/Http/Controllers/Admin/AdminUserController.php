<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request, DataTables $datatables)
    {
        if ($request->ajax()) {
            $users = User::select('users.*');

            return $datatables->eloquent($users)
                ->addColumn('status_label', function (User $user) {
                    if ($user->email_verified_at) {
                        return '<span class="badge bg-success">Active</span>';
                    }
                    return '<span class="badge bg-warning text-dark">Unverified</span>';
                })
                ->addColumn('created', function (User $user) {
                    return $user->created_at ? $user->created_at->format('d-M-Y h:i A') : 'N/A';
                })
                ->addColumn('action', function (User $user) {
                    $currentUserId = Auth::guard('admin')->id();
                    
                    $editBtn = '<a href="' . route('admin.admin-users.edit', $user->id) . '" class="btn btn-sm btn-clean btn-icon" title="Edit">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" style="color: #F15B43;"></i>
                                </a>';
                    
                    $deleteBtn = '';
                    if ($user->id !== $currentUserId) {
                        $deleteBtn = '<a data-id="' . $user->id . '" data-href="' . route('admin.admin-users.destroy', $user->id) . '" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon delete-admin-user" title="Delete">
                                        <i class="fa fa-trash-o" aria-hidden="true" style="color: #F15B43;"></i>
                                    </a>';
                    }

                    return '<div class="d-flex">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['action', 'status_label'])
                ->make(true);
        }

        return view('admin.admin_users.list');
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin.admin_users.form')->with([
            'user' => new User(),
        ]);
    }

    /**
     * Store a newly created admin user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admin-users.index')->with('success_message', 'Admin user created successfully.');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.admin_users.form')->with([
            'user' => $user,
        ]);
    }

    /**
     * Update the specified admin user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.admin-users.index')->with('success_message', 'Admin user updated successfully.');
    }

    /**
     * Remove the specified admin user.
     */
    public function destroy($id)
    {
        $currentUserId = Auth::guard('admin')->id();

        if ((int)$id === (int)$currentUserId) {
            return response()->json([
                'error' => 'You cannot delete your own account.'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => 'Admin user deleted successfully.'
        ]);
    }
}
