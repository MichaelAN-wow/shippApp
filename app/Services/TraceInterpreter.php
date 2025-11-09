<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraceInterpreter
{
    public static function parse($rawCommand)
    {
        $prompt = "Match the user input to one of the following backend commands EXACTLY. Do not respond with anything else.

Available commands:
- install_toss_tracker
- rebuild_materials_blade
- clear_cache
- install_production_page
- fix_admin_layout
- install_smart_reorder
- generate_tossed_report

User command: \"$rawCommand\"";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Respond ONLY with a valid backend action name from the list.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0,
            ]);

            $action = trim($response['choices'][0]['message']['content']);

            Log::channel('trace')->info('Trace GPT Match', [
                'input' => $rawCommand,
                'matched_action' => $action,
            ]);

            return [
                'action' => $action,
                'raw' => $rawCommand
            ];
        } catch (\Exception $e) {
            Log::channel('trace')->error('Trace GPT Error', [
                'error' => $e->getMessage()
            ]);

            return [
                'action' => 'unknown',
                'raw' => $rawCommand
            ];
        }
    }
}