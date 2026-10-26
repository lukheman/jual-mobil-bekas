<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_model_id')->constrained()->restrictOnDelete();
            $table->string('variant')->nullable();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedBigInteger('price')->index();
            $table->unsignedInteger('mileage')->index();
            $table->string('transmission', 20)->index();
            $table->string('fuel_type', 20)->index();
            $table->unsignedSmallInteger('engine_capacity')->nullable();
            $table->string('body_type', 30)->index();
            $table->string('color', 50)->nullable();
            $table->unsignedTinyInteger('owner_count')->default(1);
            $table->string('plate_area', 10)->nullable();
            $table->date('tax_expiry')->nullable();
            $table->string('document_completeness', 50)->nullable();
            $table->text('condition')->nullable();
            $table->longText('description')->nullable();
            $table->string('city', 100)->index();
            $table->string('status', 20)->default('available')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['status', 'is_featured']);
            $table->index(['status', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
