<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Sale;

class CustomerController extends Controller
{
    public function getAllCustomers()
    {
        $customers = Customer::where('company_id', session('company_id'))->orderBy('name')->get();
        return view('Admin.all_customers', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::where('company_id', session('company_id'))->findOrFail($id);
        return view('Admin.view_customer', compact('customer'));
    }

    public function showSales($id)
    {
        $customer = Customer::where('company_id', session('company_id'))->findOrFail($id);
        $sales = Sale::where('customer_id', $id)->where('company_id', session('company_id'))->get();
        return view('Admin.customer_sales', compact('customer', 'sales'));
    }
}
