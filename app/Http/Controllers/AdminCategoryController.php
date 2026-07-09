<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }
}
