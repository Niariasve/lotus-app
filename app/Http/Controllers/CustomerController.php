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
        $customers = Customer::with('primaryContactPlatform.contactPlatform')->get();

        return Inertia::render('customers/Index', [
            'customers' => $customers,
        ]);
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
        $validated = $request->validated();

        $platforms = $validated['platform'];
        $primary = $validated['primary_platform'];

        unset($validated['platform'], $validated['primary_platform']);

        $customer = Customer::create($validated);

        foreach ($platforms as $slug => $identifier) {
            if (!filled($identifier)) continue;

            $platform = ContactPlatform::where('slug', $slug)->first();

            if (!$platform) continue;

            $customer->contactPlatforms()->create([
                'customer_id' => $customer->id,
                'platform_id' => $platform->id,
                'contact_identifier' => $identifier,
                'is_primary' => $primary === $slug,
            ]);
        }

        return redirect()->route('customers.index');
    }
}
