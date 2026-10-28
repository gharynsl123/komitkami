<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $user = User::all();
        return view('user.index-user', compact('user'));
    }

    function profile() {
        $user = User::find(Auth::user()->id);
        return view('user.profile-user', compact('user'));
    }
    
    function create() {
        $user = User::all();
        $roles = Role::all();

        return view('user.create-user', compact('user', 'roles'));
    }

    function store(Request $req) {
        $user = User::create([
            'name' => $req->name,
            'username' => $req->username,
            'email' => $req->email,
            'tipe' => $req->tipe,
            'password' => bcrypt($req->password),
            'raw_pass' => $req->password,
            'address' => $req->address,
            'npwp' => $req->npwp,
        ]);
        $user->roles()->attach($req->roles);
        return redirect('user-configuration')->with('success', 'User Berhasil di buat');
    }

    function detail($id)  {
        $user = User::find($id);
        return view('user.detail-user', compact('user'));
    }
    
    function edit($id)  {
        $user = User::with('roles')->findOrFail($id);
        $allRoles = Role::all();
        return view('user.edit-user', compact('user', 'allRoles'));
    }

    function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $dataUpdate = $request->except('roles'); // hindari konflik antara kolom user dan role input
        
        // Password handling
        if ($request->filled('password')) {
            $dataUpdate['password'] = bcrypt($request->password);
            $dataUpdate['raw_pass'] = $request->password;
        } else {
            $dataUpdate['password'] = $user->password;
            $dataUpdate['raw_pass'] = $user->raw_pass;
        }

        // Update user table
        $user->update($dataUpdate);

        // Sync roles ke tabel pivot
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles); // roles is array of role_id
        }

        return redirect('/user-configuration')->with('success', "$user->name Berhasil Di Update");
    }


    function editProfile($id) {
        $user = User::find($id);
        return view('user.edit-profile', compact('user'));
    }
    
    function delete($id) {
        $user = User::find($id);
        $user->delete();

        return redirect('/user-configuration')->with('susccess', "$user->name berhasil di hapus");
    }
}