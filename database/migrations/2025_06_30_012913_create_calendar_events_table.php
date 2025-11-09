<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('type', ['event', 'holiday', 'market', 'popup', 'shift', 'meeting'])->default('event');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->json('staff_tags')->nullable();
            $table->json('attendees')->nullable();
            $table->string('color', 7)->default('#3788d8');
            $table->boolean('all_day')->default(false);
            
            // Recurring event fields
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'monthly', 'yearly', 'custom'])->nullable();
            $table->integer('recurrence_interval')->nullable();
            $table->json('recurrence_days')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->integer('recurrence_count')->nullable();
            $table->unsignedBigInteger('parent_event_id')->nullable();
            
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('parent_event_id')->references('id')->on('calendar_events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_events');
    }
}
