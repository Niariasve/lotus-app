<?php

namespace App\Http\Controllers;

use App\Http\Requests\Suppliers\StoreRequest;
use App\Http\Requests\Suppliers\UpdateRequest;
use App\Models\Supplier;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();

        return Inertia::render('suppliers/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('suppliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        Supplier::create($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier created succesfully!'
        ]);

        return redirect(route('suppliers.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Supplier $supplier)
    {
        $validated = $request->validated();

        $supplier->update($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier updated succesfully!'
        ]);

        return redirect(route('suppliers.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Supplier deleted succesfully!'
        ]);

        return back();
    }
}
