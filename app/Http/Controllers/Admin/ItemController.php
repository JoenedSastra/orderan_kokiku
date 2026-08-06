<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::with('category')->latest()->paginate(15);
        return view('admin.items.index', compact('items'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'unit'        => 'required|string|max:30',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
        ]);
        Item::create($request->only('name', 'category_id', 'unit', 'min_stock', 'description'));
        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'unit'        => 'required|string|max:30',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
        ]);
        $item->update($request->only('name', 'category_id', 'unit', 'min_stock', 'description'));
        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
