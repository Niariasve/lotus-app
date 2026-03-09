<?php

namespace App\Http\Controllers;

use App\Models\SupplierProductOffer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierProductOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplierProductOffers = SupplierProductOffer::all();

        return Inertia::render('supplierProductOffer/Index', [
            'supplierProductOffers' => $supplierProductOffers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierProductOffer $supplierProductOffer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierProductOffer $supplierProductOffer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierProductOffer $supplierProductOffer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierProductOffer $supplierProductOffer)
    {
        //
    }
}
