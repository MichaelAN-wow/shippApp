<?php

namespace App\Trace;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Trace
{
    public static function info($message)
    {
        Log::info("[TRACE] $message");
    }

    public static function runCommand($command)
    {
        try {
            self::info("Running command: $command");
            return Artisan::call($command);
        } catch (\Exception $e) {
            self::info("Error: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public static function suggestFix($context)
    {
        return "Trace suggests: [manual instruction placeholder based on $context]";
    }
}