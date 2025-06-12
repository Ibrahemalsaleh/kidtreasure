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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->enum('status', ['active', 'abandoned', 'converted'])->default('active');
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع حسب معرف الجلسة
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};