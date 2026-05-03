<?php

namespace App\Http\Controllers;

use App\Models\NpvCategory;
use App\Services\NpvCategoryService;
use Illuminate\Http\Request;

class NpvCategoryController extends Controller
{
    public function __construct(protected NpvCategoryService $service) {}

    public function index()
    {
        return view('app.npv-categories.index');
    }

    public function datatable()
    {
        return $this->service->datatable();
    }

    public function create()
    {
        return view('app.npv-categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:npv_categories,name',
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        $this->service->create($request->all());

        return redirect()->route('npv-categories.index')->with('status', 'NPV Category created successfully.');
    }

    public function edit(NpvCategory $npvCategory)
    {
        return view('app.npv-categories.form', ['category' => $npvCategory]);
    }

    public function update(Request $request, NpvCategory $npvCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:npv_categories,name,' . $npvCategory->id,
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        $this->service->update($npvCategory, $request->all());

        return redirect()->route('npv-categories.index')->with('status', 'NPV Category updated successfully.');
    }

    public function destroy(NpvCategory $npvCategory)
    {
        $this->service->delete($npvCategory);
        return redirect()->route('npv-categories.index')->with('status', 'NPV Category deleted successfully.');
    }
}
