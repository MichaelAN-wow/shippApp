<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class RunTraceCommand extends Command
{
    protected $signature = 'trace:run';
    protected $description = 'Run Trace, the autonomous system admin for On Hand Solution';

    public function handle()
    {
        $this->info("🟢 Trace is online and ready.");

        // ✅ 1. Fix dashboard route if broken
        $webRoutes = base_path('routes/web.php');
        $routesCode = File::get($webRoutes);
        if (!str_contains($routesCode, "Route::get('/dashboard'")) {
            File::append($webRoutes, "\nRoute::get('/dashboard', function () {\n    return redirect('/materials/all');\n})->middleware(['auth'])->name('dashboard');\n");
            $this->info("✅ Dashboard route patched to redirect to /materials/all.");
        } else {
            $this->line("🔁 Dashboard route already exists.");
        }

        // ✅ 2. Replace report view with branded layout
        $reportPath = resource_path('views/tossed_items/report.blade.php');
        $reportTemplate = <<<BLADE
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">🗑️ Tossed Items Report</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(\$tossedItems->isEmpty())
        <div class="alert alert-info">
            No tossed items have been logged yet.
        </div>
    @else
        <table class="table table-bordered table-striped bg-white">
            <thead class="thead-dark">
                <tr>
                    <th>Date</th>
                    <th>Material</th>
                    <th>Quantity Tossed</th>
                    <th>Unit</th>
                    <th>Reason</th>
                    <th>Submitted By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\$tossedItems as \$item)
                    <tr>
                        <td>{{ \$item->created_at->format('M d, Y') }}</td>
                        <td>{{ \$item->material->name ?? '—' }}</td>
                        <td>{{ \$item->quantity }}</td>
                        <td>{{ \$item->material->unit ?? '—' }}</td>
                        <td>{{ \$item->reason ?? '—' }}</td>
                        <td>{{ \$item->user->name ?? 'Unknown' }}</td>
                        <td>
                            <form method="POST" action="{{ route('tossed_items.destroy', \$item->id) }}" onsubmit="return confirm('Restore inventory and delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
BLADE;

        File::put($reportPath, $reportTemplate);
        $this->info("✅ Tossed Items Report page branded and updated.");

        // ✅ 3. Remove form dropdown from layout if accidentally included
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $layoutContent = File::get($layoutPath);
        if (str_contains($layoutContent, 'id="tossItModal"')) {
            $cleaned = preg_replace('/<!-- Toss It Modal -->.*?<\/div>\s*<\/div>\s*<\/div>/s', '', $layoutContent);
            File::put($layoutPath, $cleaned);
            $this->info("✅ Removed embedded Toss It form from layout.");
        } else {
            $this->line("🔁 Layout already clean.");
        }

        // ✅ 4. Verify tossed_items table structure
        if (!Schema::hasTable('tossed_items')) {
            $this->error("⛔ Table 'tossed_items' does not exist.");
            return;
        }

        $expected = ['id', 'company_id', 'user_id', 'material_id', 'quantity', 'reason', 'created_at', 'updated_at'];
        $missing = [];
        foreach ($expected as $col) {
            if (!Schema::hasColumn('tossed_items', $col)) $missing[] = $col;
        }

        if (count($missing)) {
            $this->error("⛔ Missing columns in 'tossed_items': " . implode(', ', $missing));
        } else {
            $this->info("✅ 'tossed_items' structure verified.");
        }

        // ✅ 5. Try querying the table
        try {
            DB::table('tossed_items')->limit(1)->get();
            $this->info("✅ Tossed Items table is queryable.");
        } catch (\Exception $e) {
            $this->error("⛔ Failed querying 'tossed_items': " . $e->getMessage());
        }

        $this->info("✅ Trace cleanup complete.");
    }
}