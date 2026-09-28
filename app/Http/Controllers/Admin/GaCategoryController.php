<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga\GaCategory;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Seosetting;
use App\Models\Pages;
use Illuminate\Http\Request;

class GaCategoryController extends Controller
{
    private function getSharedData()
    {
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $seopage = Seosetting::first();
        $page = Pages::all();
        return compact('title', 'footertext', 'seopage', 'page');
    }

    public function index()
    {
        $data = $this->getSharedData();
        $categories = GaCategory::orderBy('name')->get();
        $data['categories'] = $categories;
        $data['current_page'] = 'ga-categories';
        return view('admin.ga.category.index', $data);
    }

    public function create()
    {
        $data = $this->getSharedData();
        $data['current_page'] = 'ga-categories';
        return view('admin.ga.category.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:ga_categories,code',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        GaCategory::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('admin.ga.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = $this->getSharedData();
        $category = GaCategory::findOrFail($id);
        $data['category'] = $category;
        $data['current_page'] = 'ga-categories';
        return view('admin.ga.category.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $category = GaCategory::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:ga_categories,code,' . $id,
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $category->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('admin.ga.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = GaCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.ga.categories.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}