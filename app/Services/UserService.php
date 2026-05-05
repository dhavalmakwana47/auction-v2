<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserService
{
    public function datatable()
    {
        $users = User::with('roles')->select('users.*');

        return DataTables::eloquent($users)
            ->addIndexColumn()
            ->addColumn('role', fn($u) => $u->getRoleNames()->map(fn($r) =>
                '<span class="badge-role">'.ucfirst($r).'</span>'
            )->implode(' ') ?: '<span class="text-muted">—</span>')
            ->addColumn('status', fn($u) => $u->is_active
                ? '<span class="badge-active"><i class="fas fa-check-circle mr-1"></i>Active</span>'
                : '<span class="badge-inactive"><i class="fas fa-times-circle mr-1"></i>Inactive</span>'
            )
            ->addColumn('created_date', fn($u) => $u->created_at ? $u->created_at->format('d M Y') : '—')
            ->addColumn('action', fn($u) =>
                '<a href="'.route('users.edit', $u).'" class="btn btn-warning btn-action mr-1" title="Edit"><i class="fas fa-edit" style="font-size:13px;"></i></a>'
                .'<button class="btn btn-danger btn-action btn-delete" data-url="'.route('users.destroy', $u).'" title="Delete"><i class="fas fa-trash" style="font-size:13px;"></i></button>'
            )
            ->rawColumns(['role', 'status', 'action'])
            ->make(true);
    }

    public function getRoles()
    {
        return Role::pluck('name', 'name');
    }

    public function create(array $data): User
    {
        $isRa = strtolower($data['role'] ?? '') === 'ra';

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($isRa ? Str::random(16) : $data['password']),
            'is_active' => $data['is_active'] ?? 1,
        ]);

        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
