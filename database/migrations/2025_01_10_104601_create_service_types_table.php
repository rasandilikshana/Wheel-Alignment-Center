<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('service_type');  // Remove old column
            $table->foreignId('service_type_id')
                  ->constrained()
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeignId('service_type_id');
            $table->string('service_type');  // Restore old column
        });

        Schema::dropIfExists('service_types');
    }
};
