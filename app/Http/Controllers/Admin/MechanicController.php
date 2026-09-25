<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use App\Http\Requests\StoreMechanicRequest;
use App\Http\Requests\UpdateMechanicRequest;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mechanic::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
        }

        $mechanics = $query->paginate(10);
        return view('admin.mechanic.index', compact('mechanics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mechanic.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMechanicRequest $request)
    {
        Mechanic::create($request->validated());
        return redirect()->route('admin.mechanic.index')->with('success', 'Mekanik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mechanic = Mechanic::findOrFail($id);
        return view('admin.mechanic.show', compact('mechanic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mechanic = Mechanic::findOrFail($id);
        return view('admin.mechanic.edit', compact('mechanic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMechanicRequest $request, string $id)
    {
        $mechanic = Mechanic::findOrFail($id);
        $mechanic->update($request->validated());
        return redirect()->route('admin.mechanic.index')->with('success', 'Data mekanik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mechanic = Mechanic::findOrFail($id);
        $mechanic->delete();
        return redirect()->route('admin.mechanic.index')->with('success', 'Mekanik berhasil dihapus.');
    }
}
