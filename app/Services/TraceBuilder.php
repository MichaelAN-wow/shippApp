<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class TraceBuilder
{
    public static function execute($action)
    {
        switch ($action) {
            case 'clear_cache':
                Artisan::call('optimize:clear');
                return '✅ Cache cleared.';

            case 'rebuild_materials_blade':
                return self::restoreBlade('all_material', '_patches/fixed_all_material');

            case 'fix_admin_layout':
                return self::restoreBlade('layouts/admin_master', '_patches/fixed_admin_master');

            case 'install_toss_tracker':
                return self::installFromStub('toss_tracker', 'Tossed It Tracker');

            case 'install_smart_reorder':
                return self::installFromStub('smart_reorder', 'Smart Reorder Suggestions');

            case 'install_production_page':
                return self::installFromStub('production', 'Production Control Panel');

            case 'generate_tossed_report':
                return self::installFromStub('tossed_report', 'Tossed Items Report');

            default:
                return '❌ Trace doesn’t recognize this action yet.';
        }
    }

    private static function restoreBlade($targetView, $patchView)
    {
        $targetPath = resource_path('views/' . str_replace('.', '/', $targetView) . '.blade.php');
        $patchPath = resource_path('views/' . str_replace('.', '/', $patchView) . '.blade.php');

        if (!File::exists($patchPath)) {
            return "❌ Patch view not found: $patchPath";
        }

        File::copy($patchPath, $targetPath);
        return "✅ Restored: $targetView";
    }

    private static function installFromStub($stubName, $label)
    {
        $stubSource = resource_path("views/Admin/_patches/{$stubName}.blade.php");
        $targetPath = resource_path("views/Admin/{$stubName}.blade.php");

        if (!File::exists($stubSource)) {
            return "❌ Missing stub file: $stubSource";
        }

        File::copy($stubSource, $targetPath);
        return "✅ Installed {$label} at /Admin/{$stubName}.blade.php";
    }
}