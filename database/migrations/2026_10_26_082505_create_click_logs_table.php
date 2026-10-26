<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['car_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_logs');
    }
};
