<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\TracePatch;
use App\Services\TraceFeatureService;
use App\Services\TraceBuilder;

class TraceController extends Controller
{
    public function index()
    {
        return view('Admin.trace_dashboard');
    }

    public function diagnose(Request $request)
    {
        try {
            $status = [
                'materials_view_exists' => View::exists('Admin.all_material'),
                'master_layout_exists' => View::exists('layouts.admin_master'),
            ];

            Log::channel('trace')->info('System Diagnostics Run', [
                'timestamp' => Carbon::now(),
                'results' => $status
            ]);

            return redirect()->back()->with('success', 'Trace diagnostics complete. Check logs for details.');
        } catch (\Exception $e) {
            Log::channel('trace')->error('Diagnostics Failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Trace diagnostics failed.');
        }
    }

    public function fixMaterials(Request $request)
    {
        try {
            $templatePath = resource_path('views/Admin/all_material.blade.php');
            $backupPath = resource_path('views/Admin/all_material_backup.blade.php');
            $fixedPath = resource_path('views/Admin/_patches/fixed_all_material.blade.php');

            if (!File::exists($fixedPath)) {
                return redirect()->back()->with('error', 'Patch file not found.');
            }

            if (File::exists($templatePath)) {
                File::copy($templatePath, $backupPath);
            }

            File::copy($fixedPath, $templatePath);

            Log::channel('trace')->info('Trace repaired all_material.blade.php', [
                'timestamp' => Carbon::now(),
                'action' => 'File patched',
            ]);

            return redirect()->back()->with('success', 'Materials page restored successfully!');
        } catch (\Exception $e) {
            Log::channel('trace')->error('Materials Page Fix Failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Trace could not fix the materials page.');
        }
    }

    public function logs()
    {
        $logPath = storage_path('logs/trace.log');
        if (!File::exists($logPath)) {
            return view('Admin.trace_logs', ['logs' => []]);
        }

        $logs = array_reverse(file($logPath));
        return view('Admin.trace_logs', ['logs' => $logs]);
    }

    public function logPatch($action, $affectedTable = null, $affectedId = null, $details = null, $oldValue = null, $newValue = null)
    {
        TracePatch::create([
            'action' => $action,
            'affected_table' => $affectedTable,
            'affected_id' => $affectedId,
            'details' => $details,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'company_id' => session('company_id'),
            'user_id' => auth()->id(),
        ]);
    }

    public function report()
    {
        $patches = TracePatch::orderBy('created_at', 'desc')->paginate(25);
        return view('Admin.Reports.trace_logs_reports', compact('patches'));
    }

public function chat(Request $request)
{
    $message = $request->input('message');

    if (auth()->user()->type !== 'super_admin') {
        return response()->json(['status' => '🚫 Unauthorized']);
    }

    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a Laravel backend AI. Respond only with a single valid internal action keyword (e.g., install_toss_tracker, rebuild_materials_blade, clear_cache).'],
                ['role' => 'user', 'content' => "Command: \"$message\""]
            ],
            'temperature' => 0.2
        ]);

        $action = trim($response['choices'][0]['message']['content'] ?? 'unknown');
        $result = TraceBuilder::execute($action);

        TracePatch::create([
            'action' => 'AI Chat Command',
            'details' => $action,
            'company_id' => session('company_id'),
            'user_id' => auth()->id(),
        ]);

        \Illuminate\Support\Facades\Log::channel('trace')->info('Trace AI Executed', [
            'input' => $message,
            'resolved_action' => $action,
            'result' => $result,
        ]);

        return response()->json(['status' => $result]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::channel('trace')->error('Trace AI Chat Failure', ['error' => $e->getMessage()]);
        return response()->json(['status' => '❌ AI Error: ' . $e->getMessage()]);
    }
}

    public function passiveMonitor()
    {
        try {
            $status = [
                'materials_blade_exists' => View::exists('Admin.all_material'),
                'trace_logs_view_exists' => View::exists('Admin.Reports.trace_logs_reports'),
            ];

            if (!$status['materials_blade_exists']) {
                Log::channel('trace')->warning('Missing View: Admin.all_material');
            }

            if (!$status['trace_logs_view_exists']) {
                Log::channel('trace')->warning('Missing View: Admin.Reports.trace_logs_reports');
            }

            return response('Passive monitor ran successfully.', 200);
        } catch (\Exception $e) {
            Log::channel('trace')->error('Passive monitor failed.', ['error' => $e->getMessage()]);
            return response('Passive monitor failed.', 500);
        }
    }

    public function command(Request $request)
    {
        $input = strtolower($request->input('command'));

        if (env('TRACE_ENABLED') !== 'true') {
            return response()->json(['message' => 'Trace is currently disabled via .env']);
        }

        try {
            $result = TraceFeatureService::handle($input);

            TracePatch::create([
                'action' => 'Trace Command',
                'details' => json_encode($result),
                'company_id' => session('company_id'),
                'user_id' => auth()->id(),
            ]);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Trace Execution Failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Trace execution failed']);
        }
    }
}