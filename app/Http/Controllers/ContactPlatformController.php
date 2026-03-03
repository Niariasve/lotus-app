<?php

namespace App\Http\Controllers;

use App\Models\ContactPlatform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $request->merge([
            'slug' => strtolower($request->name),
            'is_active' => $request->boolean('is_active'),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('contact_platforms', 'slug')
                    ->where(
                        fn ($query) => 
                        $query->where('slug', strtolower($request->name))
                    )
            ],
            'slug' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        ContactPlatform::create($validated);

        return redirect(route('contact-platforms.index'));
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
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('contact_platforms', 'slug')
                    ->ignore($contactPlatform->id)
                    ->where(
                        fn($query) =>
                        $query->where('slug', strtolower($request->name))
                    ),
            ],
            'is_active' => 'required|boolean',
        ]);

        $contactPlatform->update($validated);

        return redirect(route('contact-platforms.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactPlatform $contactPlatform)
    {
        //
    }
}
