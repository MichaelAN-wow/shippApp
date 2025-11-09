<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Label;
use App\Models\Contact;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::with(['sender', 'recipient'])->latest()->get();
        return view('shipping.labels', compact('labels'));
    }

    public function create()
    {
        $contacts = Contact::orderBy('name')->get();
        return view('shipping.create_label', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_id'    => 'required|exists:contacts,id',
            'recipient_id' => 'required|exists:contacts,id',
            'package'      => 'required|string|max:255',
        ]);

        Label::create([
            'sender_id'    => $request->sender_id,
            'recipient_id' => $request->recipient_id,
            'package'      => $request->package,
        ]);

        return redirect()->route('shipping.labels')->with('success', 'Label created successfully.');
    }
}
