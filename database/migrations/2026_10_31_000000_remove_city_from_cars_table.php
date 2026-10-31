<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cars', 'city')) {
            Schema::table('cars', function (Blueprint $table): void {
                $table->dropColumn('city');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table): void {
            $table->string('city', 100)->nullable()->index();
        });
    }
};
