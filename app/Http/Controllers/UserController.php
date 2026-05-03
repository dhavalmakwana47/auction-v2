<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index()
    {
        return view('app.users.index');
    }

    public function datatable()
    {
        return $this->userService->datatable();
    }

    public function create()
    {
        $roles = $this->userService->getRoles();
        return view('app.users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'nullable|exists:roles,name',
        ]);

        $this->userService->create($request->all());

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = $this->userService->getRoles();
        return view('app.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role'     => 'nullable|exists:roles,name',
        ]);

        $this->userService->update($user, $request->all());

        return redirect()->route('users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);
        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }
}
