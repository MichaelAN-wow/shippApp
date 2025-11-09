<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketPrepController extends Controller
{
    public function loadout()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Later we'll pass real product inventory data here
        return view('market_prep.create_load');
    }

    public function restock()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Updated to match renamed view file
        return view('market_prep.restock_load');
    }
}