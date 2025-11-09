<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TraceFixTossNotes extends Command
{
    protected $signature = 'trace:fix-toss-notes';
    protected $description = 'Ensure "reason" column exists and is synced in tossed_items table';

    public function handle()
    {
        $table = 'tossed_items';

        // Check if column exists
        if (!Schema::hasColumn($table, 'reason')) {
            $this->warn('"reason" column not found. Attempting to add...');

            try {
                DB::statement("ALTER TABLE $table ADD COLUMN reason TEXT DEFAULT NULL");
                $this->info('✅ "reason" column added successfully.');
            } catch (\Exception $e) {
                $this->error('❌ Failed to add "reason" column: ' . $e->getMessage());
                return;
            }
        } else {
            $this->info('🟢 "reason" column already exists. No changes made.');
        }

        // Optional: Sync any blanks with placeholder
        DB::table($table)
            ->whereNull('reason')
            ->update(['reason' => '—']);

        $this->info('🔁 All null reasons replaced with "—". Toss Tracker is now patched!');
    }
}