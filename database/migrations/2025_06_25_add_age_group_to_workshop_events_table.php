<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshop_events', function (Blueprint $table) {
            $table->string('age_group', 50)->nullable()->after('max_attendees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_events', function (Blueprint $table) {
            $table->dropColumn('age_group');
        });
    }
};
