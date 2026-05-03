<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService)
    {
    }

    public function index()
    {
        return view('app.roles.index');
    }

    public function datatable()
    {
        return $this->roleService->datatable();
    }

    public function create()
    {
        $permissions = $this->roleService->getPermissions();
        return view('app.roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $this->roleService->create($request->all());

        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'admin', 403, 'Admin role cannot be modified.');
        $permissions = $this->roleService->getPermissions();
        return view('app.roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'admin', 403, 'Admin role cannot be modified.');

        $request->validate([
            'name'        => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $this->roleService->update($role, $request->all());

        return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->name === 'admin', 403, 'Admin role cannot be deleted.');
        $this->roleService->delete($role);
        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }
}
