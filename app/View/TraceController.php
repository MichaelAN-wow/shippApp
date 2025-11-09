<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\TracePatch;
use App\Services\TraceCommandService;

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

            if (!File::exists($templatePath)) {
                return redirect()->back()->with('error', 'all_material.blade.php not found.');
            }

            File::copy($templatePath, $backupPath);
            $fixedContent = view('Admin._patches.fixed_all_material')->render();
            File::put($templatePath, $fixedContent);

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

        try {
            return TraceCommandService::handle($message);
        } catch (\Exception $e) {
            Log::error('Trace Chat Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong with Trace’s response.');
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

    try {
        if (str_contains($input, 'fix materials')) {
            // Run the fix
            $response = $this->fixMaterials($request);

            // Log the patch
            $this->logPatch(
                'Auto Fix',
                'views',
                null,
                'Trace detected missing all_material.blade.php and applied fix.',
                null,
                'Restored template from /_patches/'
            );

            return $response;
        }

        if (str_contains($input, 'run diagnostics') || str_contains($input, 'diagnose')) {
            $response = $this->diagnose($request);

            $this->logPatch(
                'Diagnostics',
                'system',
                null,
                'Trace diagnostics manually triggered.',
                null,
                null
            );

            return $response;
        }

        if (str_contains($input, 'clear cache')) {
            Artisan::call('optimize:clear');

            $this->logPatch(
                'Cache Cleared',
                'system',
                null,
                'User triggered full cache clear via Trace.',
                null,
                null
            );

            return response()->json(['message' => 'All caches cleared successfully.']);
        }

        return response()->json([
            'message' => "Trace heard: \"$input\" but doesn't recognize this command yet."
        ]);

    } catch (\Exception $e) {
        Log::error('Trace Command Error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Trace encountered an error.']);
    }
}
}