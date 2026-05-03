<?php

namespace App\Services;

use App\Models\NpvCategory;
use Yajra\DataTables\Facades\DataTables;

class NpvCategoryService
{
    public function datatable()
    {
        return DataTables::eloquent(NpvCategory::query())
            ->addIndexColumn()
            ->addColumn('status', fn($c) => $c->is_active
                ? '<span class="badge-active"><i class="fas fa-check-circle mr-1"></i>Active</span>'
                : '<span class="badge-inactive"><i class="fas fa-times-circle mr-1"></i>Inactive</span>'
            )
            ->addColumn('action', fn($c) =>
                '<a href="' . route('npv-categories.edit', $c) . '" class="btn btn-warning btn-action mr-1" title="Edit"><i class="fas fa-edit" style="font-size:13px;"></i></a>'
                . '<button class="btn btn-danger btn-action btn-delete" data-url="' . route('npv-categories.destroy', $c) . '" title="Delete"><i class="fas fa-trash" style="font-size:13px;"></i></button>'
            )
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create(array $data): NpvCategory
    {
        return NpvCategory::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? 1,
        ]);
    }

    public function update(NpvCategory $category, array $data): NpvCategory
    {
        $category->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? 1,
        ]);

        return $category;
    }

    public function delete(NpvCategory $category): void
    {
        $category->delete();
    }
}
