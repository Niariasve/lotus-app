<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\StoreRequest;
use App\Http\Requests\Customers\UpdateRequest;
use App\Models\ContactPlatform;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()
            ->select(['id', 'full_name', 'identification', 'email', 'phone', 'city'])
            ->with([
                'primaryContactPlatform:id,customer_id,platform_id,contact_identifier',
                'primaryContactPlatform.contactPlatform:id,name',
            ])
            ->latest('id')
            ->get();

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
        
        DB::transaction(function () use ($validated, $platforms, $primary) {
            $customer = Customer::create($validated);
            $this->syncCustomerContacts($customer, $platforms, $primary);
        });

        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        $contactPlatforms = ContactPlatform::where('is_active', true)
            ->get(['id', 'name', 'slug']);

        $customer->loadMissing(['contactPlatforms', 'primaryContactPlatform.contactPlatform']);

        $customerContacts = $customer->contactPlatforms;

        $primary = null;

        if ($customer->primaryContactPlatform) {
            $primary = $customer->primaryContactPlatform->contactPlatform;
        }

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

        DB::transaction(function () use ($customer, $validated, $platforms, $primary) {
            $customer->update($validated);

            $activePlatformsIds = ContactPlatform::query()
                ->where('is_active', true)
                ->pluck('id');

            $customer->contactPlatforms()
                ->whereIn('platform_id', $activePlatformsIds)
                ->delete();

            $this->syncCustomerContacts($customer, $platforms, $primary);
        });

        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index');
    }

    private function syncCustomerContacts(Customer $customer, array $platforms, ?string $primary): void
    {
        $normalizedPlatforms = collect($platforms)
            ->filter(fn ($identifier) => filled($identifier));

        if ($normalizedPlatforms->isEmpty()) {
            return;
        }

        $platformsBySlug = ContactPlatform::query()
            ->where('is_active', true)
            ->whereIn('slug', $normalizedPlatforms->keys()->all())
            ->pluck('id', 'slug');

        foreach ($normalizedPlatforms as $slug => $identifier) {
            $platformId = $platformsBySlug->get($slug);

            if (!$platformId) continue;

            $customer->contactPlatforms()->create([
                'platform_id' => $platformId,
                'contact_identifier' => $identifier,
                'is_primary' => $primary === $slug,
            ]);
        }
    }
}
