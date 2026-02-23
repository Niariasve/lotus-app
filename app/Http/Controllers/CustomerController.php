<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\StoreRequest;
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
        return Inertia::render('customers/Create');
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        Customer::create($validated);

        return redirect()->route('customers.index');
    }
}
