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
        Schema::create('menu_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_addons_id')->nullable()->constrained('menu_addons')->nullOnDelete();
            $table->string('name');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_subcategories');
    }
};
