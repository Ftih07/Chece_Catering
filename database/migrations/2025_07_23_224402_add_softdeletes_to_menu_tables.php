<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menu_subcategories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menu_variants', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menu_addons', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('gallery_menu_categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('gallery_categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('menu_subcategories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('menu', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('menu_variants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('menu_addons', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('gallery_menu_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('gallery_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
