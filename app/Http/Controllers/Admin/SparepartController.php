<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Http\Requests\StoreSparepartRequest;
use App\Http\Requests\UpdateSparepartRequest;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sparepart::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
        }

        $spareparts = $query->paginate(10);
        return view('admin.sparepart.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sparepart.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSparepartRequest $request)
    {
        Sparepart::create($request->validated());
        return redirect()->route('admin.sparepart.index')->with('success', 'Suku cadang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return view('admin.sparepart.show', compact('sparepart'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return view('admin.sparepart.edit', compact('sparepart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSparepartRequest $request, string $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->update($request->validated());
        return redirect()->route('admin.sparepart.index')->with('success', 'Suku cadang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->delete();
        return redirect()->route('admin.sparepart.index')->with('success', 'Suku cadang berhasil dihapus.');
    }
}
