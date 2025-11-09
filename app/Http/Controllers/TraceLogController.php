<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TraceLog;
use Illuminate\Support\Facades\Auth;

class TraceLogController extends Controller
{
    public function index()
    {
        if (Auth::user()->id === 1) {
            $logs = TraceLog::orderBy('created_at', 'desc')->paginate(50);
        } else {
            $logs = TraceLog::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(50);
        }

        return view('Admin.trace_logs', compact('logs'));
    }
}