<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\TraceController;

class TraceCommandService
{
    public static function handle($message)
    {
        $command = strtolower(trim($message));

        switch ($command) {
            case 'fix materials page':
                Artisan::call('view:clear');
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                return self::fixMaterials();

            case 'run diagnostics':
                return self::runDiagnostics();

            case 'clear cache':
                Artisan::call('optimize:clear');

                $controller = new TraceController();
                $controller->logPatch(
                    'Cache Cleared',
                    'system',
                    null,
                    'Trace command cleared Laravel caches.',
                    null,
                    null
                );

                return '✅ All Laravel caches cleared.';

            case 'install feature x':
                return '🛠️ Installation of Feature X is coming soon...';

            default:
                return '🤖 Command not recognized. Try: "Fix materials page", "Run diagnostics", or "Clear cache".';
        }
    }

    private static function fixMaterials()
    {
        try {
            $template = resource_path('views/Admin/all_material.blade.php');
            $backup = resource_path('views/Admin/all_material_backup.blade.php');

            if (!file_exists($template)) {
                return '❌ Error: all_material.blade.php not found.';
            }

            copy($template, $backup);
            $fixedContent = view('Admin._patches.fixed_all_material')->render();
            file_put_contents($template, $fixedContent);

            $controller = new TraceController();
            $controller->logPatch(
                'Auto Fix',
                'views',
                null,
                'Trace detected and restored all_material.blade.php',
                null,
                'View patched from Admin._patches.fixed_all_material'
            );

            Log::channel('trace')->info('Trace auto-fixed materials view.');
            return '🛠️ Materials page restored successfully.';
        } catch (\Exception $e) {
            Log::error('Trace auto-fix failed.', ['error' => $e->getMessage()]);
            return '❌ Error during auto-fix: ' . $e->getMessage();
        }
    }

    private static function runDiagnostics()
    {
        $results = [
            'materials_view' => view()->exists('Admin.all_material') ? '✅' : '❌',
            'master_layout' => view()->exists('layouts.admin_master') ? '✅' : '❌',
            'trace_report'  => view()->exists('Admin.Reports.trace_logs_reports') ? '✅' : '❌',
        ];

        Log::channel('trace')->info('Diagnostics run', ['results' => $results]);

        $controller = new TraceController();
        $controller->logPatch(
            'Diagnostics',
            'system',
            null,
            'Trace diagnostics executed.',
            null,
            json_encode($results)
        );

        return "📊 Diagnostics Results:<br>" .
            "• Materials View: {$results['materials_view']}<br>" .
            "• Master Layout: {$results['master_layout']}<br>" .
            "• Trace Report View: {$results['trace_report']}";
    }
}