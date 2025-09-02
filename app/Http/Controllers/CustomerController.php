<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function create()
    {
        return view('customers.create');
    }

    public function show(\App\Models\Customer $customer)
    {
        if (tenant_id() && $customer->tenant_id !== tenant_id()) {
            abort(404);
        }
        return view('customers.show', compact('customer'));
    }

    public function edit(\App\Models\Customer $customer)
    {
        if (tenant_id() && $customer->tenant_id !== tenant_id()) {
            abort(404);
        }
        return view('customers.edit', compact('customer'));
    }
}
