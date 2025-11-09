<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Trace
{
    public function boot()
    {
        Log::info('[Trace] Booting up...');

        // ✅ Check that tossed_items table exists and has all expected columns
        if (Schema::hasTable('tossed_items')) {
            $columns = Schema::getColumnListing('tossed_items');
            $expected = ['id', 'material_id', 'quantity', 'reason', 'created_at', 'updated_at'];
            $missing = array_diff($expected, $columns);

            if (empty($missing)) {
                Log::info('[Trace] All expected columns are present in tossed_items table.');
            } else {
                Log::warning('[Trace] Missing columns in tossed_items: ' . implode(', ', $missing));
            }
        } else {
            Log::error('[Trace] tossed_items table is missing entirely.');
        }

        // ✅ Run broken relation cleanup for tossed_items
        $this->scanForBrokenRelations();

        // ✅ Verify Toss It full submission workflow
        $this->verifyTossItWorkflow();
    }
public static function locateSmartReorderBlade()
{
    $base = resource_path('views');
    $matches = [];

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($base)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && str_contains($file->getFilename(), 'reorder')) {
            $path = $file->getRealPath();
            $contents = file_get_contents($path);
            if (str_contains($contents, 'Smart Reorder Suggestions')) {
                $matches[] = $path;
            }
        }
    }

    return $matches ?: ['⚠️ No matching Blade files found.'];
}
    protected function scanForBrokenRelations()
    {
        $issues = [];

        $orphans = DB::table('tossed_items')
            ->leftJoin('materials', 'tossed_items.material_id', '=', 'materials.id')
            ->whereNull('materials.id')
            ->select('tossed_items.id', 'tossed_items.material_id')
            ->get();

        if ($orphans->count()) {
            foreach ($orphans as $item) {
                $issues[] = "Auto-deleted Tossed Item ID {$item->id} (missing Material ID {$item->material_id})";
                DB::table('tossed_items')->where('id', $item->id)->delete();
            }

            Log::warning('[Trace] ⚠️ Tossed Items Cleanup Report:');
            foreach ($issues as $issue) {
                Log::warning('[Trace] ' . $issue);
            }
        } else {
            Log::info('[Trace] Tossed Items check complete — all links valid.');
        }
    }

    protected function verifyTossItWorkflow()
    {
        $errors = [];

        // 1. Check if 'materials' table exists and has 'id' + 'quantity' columns
        if (!Schema::hasTable('materials')) {
            $errors[] = "Missing materials table.";
        } else {
            $requiredMaterialColumns = ['id', 'name', 'quantity'];
            foreach ($requiredMaterialColumns as $column) {
                if (!Schema::hasColumn('materials', $column)) {
                    $errors[] = "Missing '$column' column in materials table.";

                    if ($column === 'quantity') {
                        try {
                            DB::statement("ALTER TABLE materials ADD COLUMN quantity DECIMAL(10,2) DEFAULT 0");
                            Log::info("[Trace] Auto-added 'quantity' column to materials table.");
                        } catch (\Exception $e) {
                            Log::error("[Trace] Failed to auto-add 'quantity' column: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // 1.5 Fix 'material' column if required
        if (Schema::hasColumn('tossed_items', 'material')) {
            try {
                DB::table('tossed_items')->insert([
                    'material' => null,
                    'material_id' => 999998,
                    'quantity' => 1,
                    'reason' => 'Trace test — material column check',
                    'company_id' => 1,
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('tossed_items')->where('material_id', 999998)->delete();
            } catch (\Exception $e) {
                try {
                    DB::statement("ALTER TABLE tossed_items MODIFY COLUMN material VARCHAR(255) NULL");
                    Log::info("[Trace] Auto-fixed 'material' column in tossed_items — made it nullable.");
                } catch (\Exception $inner) {
                    Log::error("[Trace] Failed to fix 'material' column: " . $inner->getMessage());
                }
            }
        }

        // 1.6 Fix 'unit' column if required
        if (Schema::hasColumn('tossed_items', 'unit')) {
            try {
                DB::table('tossed_items')->insert([
                    'unit' => null,
                    'material_id' => 999997,
                    'quantity' => 1,
                    'reason' => 'Trace test — unit column check',
                    'company_id' => 1,
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('tossed_items')->where('material_id', 999997)->delete();
            } catch (\Exception $e) {
                try {
                    DB::statement("ALTER TABLE tossed_items MODIFY COLUMN unit VARCHAR(50) NULL");
                    Log::info("[Trace] Auto-fixed 'unit' column in tossed_items — made it nullable.");
                } catch (\Exception $inner) {
                    Log::error("[Trace] Failed to fix 'unit' column: " . $inner->getMessage());
                }
            }
        }

        // 2. Check if route exists
        $routes = app('router')->getRoutes();
        $tossRouteExists = $routes->hasNamedRoute('tossed_items.store');
        if (!$tossRouteExists) {
            $errors[] = "Route 'tossed_items.store' is not registered.";
        }

        // 3. Check if controller and store method exist
        if (!class_exists(\App\Http\Controllers\TossedItemController::class)) {
            $errors[] = "TossedItemController does not exist.";
        } else if (!method_exists(\App\Http\Controllers\TossedItemController::class, 'store')) {
            $errors[] = "TossedItemController is missing 'store()' method.";
        }

        // 4. Check if tossed_items table is writable
        try {
            DB::table('tossed_items')->insert([
                'material_id' => 999999,
                'quantity' => 1,
                'unit' => 'each',
                'reason' => 'Trace test',
                'company_id' => 1,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('tossed_items')->where('material_id', 999999)->delete();
        } catch (\Exception $e) {
            $errors[] = "tossed_items table is not writable: " . $e->getMessage();
        }

        if (count($errors)) {
            Log::error('[Trace] ❌ Toss It Workflow Issues Found:');
            foreach ($errors as $error) {
                Log::error('[Trace] ' . $error);
            }
        } else {
            Log::info('[Trace] Toss It workflow verified — all systems operational.');
        }
    }
        public function scanSidebarForTossIt() {
        $results = [];

        // Check for toss icon
        $results['icon_exists'] = file_exists(public_path('images/svg/trash.svg'))
            ? '✅ Found trash.svg'
            : '❌ Missing trash.svg';

        // Blade file path
        $blade = resource_path('views/layouts/admin_master.blade.php');
        if (!file_exists($blade)) {
            return ['error' => 'admin_master.blade.php not found'];
        }

        $content = file_get_contents($blade);

        // Check for Toss It trigger block
        $results['blade_block'] = str_contains($content, 'data-target="#tossItModal"')
            ? '✅ Toss It trigger found'
            : '❌ Toss It trigger missing';

        // Check if modal exists
        $results['modal_exists'] = str_contains($content, 'id="tossItModal"')
            ? '✅ Modal #tossItModal exists'
            : '❌ Modal missing';

        // Check nav nesting
        $navOpen = strpos($content, '<nav');
        $navClose = strpos($content, '</nav>');
        $tossItPos = strpos($content, 'Toss It');

        $results['nav_placement'] = ($tossItPos > $navOpen && $tossItPos < $navClose)
            ? '✅ Toss It is inside the <nav>'
            : '⚠️ Toss It may be outside sidebar structure';

        // Role check (only visible to allowed roles)
        $user = \Auth::user();
        $results['user_role'] = in_array($user->type, ['admin', 'super_admin'])
            ? '✅ User allowed'
            : '❌ User not permitted to see Toss It';

        return $results;
    }
}