<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\StoreRequest;
use App\Http\Requests\Customers\UpdateRequest;
use App\Models\ContactPlatform;
use App\Models\Customer;
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

        $platforms = $validated['platform'] ?? [];
        $primary = $validated['primary_platform'] ?? null;

        unset($validated['platform'], $validated['primary_platform']);

        $customer = Customer::create($validated);

        $this->syncCustomerContacts($customer, $platforms, $primary);

        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        $contactPlatforms = ContactPlatform::where('is_active', true)
            ->get(['id', 'name', 'slug']);

        $customerContacts = $customer->contactPlatforms;

        $primary = $customer->primaryContactPlatform->contactPlatform;

        return Inertia::render('customers/Edit', [
            'customer' => $customer,
            'contactPlatforms' => $contactPlatforms,
            'customerContacts' => $customerContacts,
            'primary' => $primary
        ]);
    }

    public function update(UpdateRequest $request, Customer $customer)
    {
        $validated = $request->validated();

        $platforms = $validated['platform'] ?? [];
        $primary = $validated['primary_platform'] ?? null;

        unset($validated['platform'], $validated['primary_platform']);

        $customer->update($validated);

        $customer->contactPlatforms()->delete();
        $this->syncCustomerContacts($customer, $platforms, $primary);

        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index');
    }

    private function syncCustomerContacts(Customer $customer, array $platforms, ?string $primary): void
    {
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
    }
}
