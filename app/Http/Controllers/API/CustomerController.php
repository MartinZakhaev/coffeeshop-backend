<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(): AnonymousResourceCollection
    {
        $customers = Customer::all();
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): CustomerResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers',
        ]);

        $customer = Customer::create($validated);
        return new CustomerResource($customer);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): CustomerResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20|unique:customers,phone_number,' . $customer->id,
        ]);

        $customer->update($validated);
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): Response
    {
        $customer->delete();
        return response()->noContent();
    }
}