<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactPlatforms\StoreRequest;
use App\Http\Requests\ContactPlatforms\UpdateRequest;
use App\Models\ContactPlatform;
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
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        ContactPlatform::create($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Contact Platform created successfully!',
        ]);

        return to_route('contact-platforms.index');
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
    public function update(UpdateRequest $request, ContactPlatform $contactPlatform)
    {
        $validated = $request->validated();

        $contactPlatform->update($validated);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Contact Platform updated successfully!',
        ]);

        return to_route('contact-platforms.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactPlatform $contactPlatform)
    {
        if ($contactPlatform->customerContact()->exists()) {
            return Inertia::flash([
                'type' => 'error',
                'message' => 'Contact Platform can not be deleted because there are users associated',
            ])->back();
        }

        $contactPlatform->delete();

        return back();
    }
}
