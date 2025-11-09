<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TraceCoreService;

class TraceDiagnosticsCommand extends Command
{
    protected $signature = 'trace:diagnostics';
    protected $description = 'Run Trace system diagnostics to detect issues and cleanups';

    public function handle()
    {
        $this->info('🧠 Trace is thinking...');

        app(TraceCoreService::class)->runDiagnostics();

        $this->info('✅ Trace finished diagnostics.');
    }
}