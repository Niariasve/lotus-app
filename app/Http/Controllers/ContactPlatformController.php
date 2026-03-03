<?php

namespace App\Http\Controllers;

use App\Models\ContactPlatform;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactPlatforms = ContactPlatform::all();

        return Inertia::render('contactPlatforms/Index', [
            'contactPlatforms' => $contactPlatforms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('contactPlatforms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactPlatform $contactPlatform)
    {   
        return Inertia::render('contactPlatforms/Edit', [
            'contactPlatform' => $contactPlatform,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactPlatform $contactPlatform)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactPlatform $contactPlatform)
    {
        //
    }
}
