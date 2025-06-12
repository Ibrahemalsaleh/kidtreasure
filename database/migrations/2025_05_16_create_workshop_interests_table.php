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
        Schema::create('workshop_interests', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name');
            $table->string('parent_email');
            $table->string('parent_phone');
            $table->string('child_name');
            $table->unsignedInteger('child_age');
            $table->string('preferred_day');
            $table->text('special_requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_interests');
    }
};
