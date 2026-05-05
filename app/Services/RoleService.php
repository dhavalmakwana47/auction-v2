<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleService
{
    public function datatable()
    {
        $roles = Role::with('permissions')->where('name', '!=', 'admin');

        return DataTables::eloquent($roles)
            ->addIndexColumn()
            ->addColumn('permissions', fn($r) => $r->permissions->count()
                ? $r->permissions->map(fn($p) => '<span class="badge-perm">'.ucfirst($p->name).'</span>')->implode(' ')
                : '<span class="text-muted">—</span>'
            )
            ->addColumn('created_date', fn($r) => $r->created_at ? $r->created_at->format('d M Y') : '—')
            ->addColumn('action', fn($r) =>
                '<a href="'.route('roles.edit', $r).'" class="btn btn-warning btn-action mr-1" data-toggle="tooltip" title="Edit"><i class="fas fa-edit" style="font-size:13px;"></i></a>'
                .'<button class="btn btn-danger btn-action btn-delete" data-url="'.route('roles.destroy', $r).'" data-toggle="tooltip" title="Delete"><i class="fas fa-trash" style="font-size:13px;"></i></button>'
            )
            ->rawColumns(['permissions', 'action'])
            ->make(true);
    }

    public function getPermissions()
    {
        return Permission::pluck('name', 'id');
    }

    public function create(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return $role;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
