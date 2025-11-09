<?php

namespace App\Services;

use App\Services\TraceInterpreter;
use App\Services\TraceBuilder;
use Illuminate\Support\Facades\Log;

class TraceFeatureService
{
    public static function handle($command)
    {
        if (!env('TRACE_ENABLED')) {
            return ['status' => 'disabled', 'message' => 'Trace is disabled via .env'];
        }

        try {
            $parsed = TraceInterpreter::parse($command);

            if ($parsed['action'] === 'unknown') {
                return ['status' => 'unknown', 'message' => "❌ Trace didn’t recognize: {$parsed['raw']}"];
            }

            $output = TraceBuilder::execute($parsed['action']);

            return ['status' => 'success', 'message' => $output];
        } catch (\Exception $e) {
            Log::error('TraceFeatureService Error', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => '❌ Trace failed internally.'];
        }
    }
}