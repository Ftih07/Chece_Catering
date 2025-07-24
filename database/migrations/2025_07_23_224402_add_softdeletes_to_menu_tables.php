<?php

use Illuminate\Database\Migrations\Migration; // Imports the base Migration class.
use Illuminate\Database\Schema\Blueprint;    // Imports the Blueprint class for defining table structures.
use Illuminate\Support\Facades\Schema;       // Imports the Schema Facade for database schema interactions.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_add_soft_deletes_to_multiple_tables.php
 * (Suggested migration file name based on its action)
 *
 * This migration is responsible for adding the 'deleted_at' column to multiple
 * tables across your application. This column is essential for enabling
 * Laravel's Soft Deletes feature on these tables, allowing records to be
 * "soft deleted" (marked as deleted) instead of being permanently removed from the database.
 */
return new class extends Migration {
    /**
     * up()
     *
     * The `up` method is executed when the migration is run (e.g., with `php artisan migrate`).
     * This is where the logic for modifying the database schema is defined.
     *
     * @return void
     */
    public function up(): void
    {
        // Add softDeletes column to 'menu_categories' table
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->softDeletes(); // Adds a nullable TIMESTAMP column named 'deleted_at'.
        });

        // Add softDeletes column to 'menu_subcategories' table
        Schema::table('menu_subcategories', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'menus' table
        Schema::table('menus', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'menu_variants' table
        Schema::table('menu_variants', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'menu_addons' table
        Schema::table('menu_addons', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'gallery_menu_categories' table
        Schema::table('gallery_menu_categories', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'gallery_categories' table
        Schema::table('gallery_categories', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });

        // Add softDeletes column to 'galleries' table
        Schema::table('galleries', function (Blueprint $table) {
            $table->softDeletes(); // Adds 'deleted_at' column.
        });
    }

    /**
     * down()
     *
     * The `down` method is executed when the migration is rolled back (e.g., with `php artisan migrate:rollback`).
     * This is where the logic to reverse the changes made by the `up` method is defined.
     *
     * @return void
     */
    public function down(): void
    {
        // Remove softDeletes column from 'menu_categories' table
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'menu_subcategories' table
        Schema::table('menu_subcategories', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // !!! CAUTION !!!
        // There's a potential typo here. It should likely be 'menus' (plural)
        // instead of 'menu' (singular) to match the 'up' method's target table.
        Schema::table('menu', function (Blueprint $table) { // Potentially 'menus'
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'menu_variants' table
        Schema::table('menu_variants', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'menu_addons' table
        Schema::table('menu_addons', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'gallery_menu_categories' table
        Schema::table('gallery_menu_categories', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'gallery_categories' table
        Schema::table('gallery_categories', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });

        // Remove softDeletes column from 'galleries' table
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column.
        });
    }
};
