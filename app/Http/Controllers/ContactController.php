<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('name')->get();
        return view('shipping.contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'street'  => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
            'state'   => 'nullable|string|max:255',
            'zip'     => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
        ]);

        $address = $request->address;
        if (!$address) {
            $parts = array_filter([
                $request->street,
                $request->city,
                $request->state,
                $request->zip,
                $request->country
            ]);
            $address = implode(', ', $parts);
        }

        Contact::create([
            'name'    => $request->name,
            'company' => $request->company,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $address,
            'street'  => $request->street,
            'city'    => $request->city,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'country' => $request->country,
        ]);

        return redirect()->route('shipping.contacts')->with('success', 'Contact added successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getPathname();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['csv_file' => 'Unable to open CSV file.']);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV header is missing.']);
        }

        $norm = fn($s) => strtolower(trim(str_replace([' ', '-'], ['_', '_'], $s)));
        $header = array_map($norm, $header);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && ($row[0] === null || $row[0] === '')) { continue; }

            $data = [];
            foreach ($row as $i => $val) {
                $key = $header[$i] ?? "col_$i";
                $data[$key] = $val;
            }

            $street  = $data['street']       ?? $data['address1'] ?? $data['address_line1'] ?? null;
            $city    = $data['city']         ?? null;
            $state   = $data['state']        ?? $data['province'] ?? null;
            $zip     = $data['zip']          ?? $data['postal']   ?? $data['postal_code'] ?? null;
            $country = $data['country']      ?? null;
            $address = $data['address']      ?? null;

            if (!$address) {
                $address = implode(', ', array_filter([$street, $city, $state, $zip, $country]));
            }

            Contact::create([
                'name'    => $data['name']    ?? '',
                'company' => $data['company'] ?? null,
                'email'   => $data['email']   ?? null,
                'phone'   => $data['phone']   ?? null,
                'address' => $address,
                'street'  => $street,
                'city'    => $city,
                'state'   => $state,
                'zip'     => $zip,
                'country' => $country,
            ]);
        }

        fclose($handle);

        return redirect()->route('shipping.contacts')->with('success', 'Contacts imported successfully.');
    }

    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->route('shipping.contacts')->with('success', 'Contact deleted successfully.');
    }
}
