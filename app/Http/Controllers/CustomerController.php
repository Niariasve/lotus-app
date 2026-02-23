<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\StoreRequest;
use App\Models\ContactPlatform;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        return Inertia::render('customers/Index');
    }

    public function create()
    {
        $contactPlatforms = ContactPlatform::where('is_active', true)
            ->get(['id', 'name', 'slug']);
        
        return Inertia::render('customers/Create', [
            'contactPlatforms' => $contactPlatforms,
        ]);
    }

    public function store(StoreRequest $request)
    {
        // dd($request->all());
        $validated = $request->validated();

        Customer::create($validated);

        return redirect()->route('customers.index');
    }
}
