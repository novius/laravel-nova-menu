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
        Schema::create('nova_menus', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('locale', 6)->default('en');
            $table->unsignedInteger('locale_parent_id')->nullable();
            $table->timestamps();

            $table->foreign('locale_parent_id')
                ->references('id')
                ->on('nova_menus')
                ->restrictOnDelete();
        });

        Schema::create('nova_menu_items', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('menu_id')->unsigned();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('left')->unsigned();
            $table->integer('right')->unsigned();
            $table->string('external_link')->nullable();
            $table->string('internal_link')->nullable();
            $table->string('html_classes', 255)->nullable();
            $table->text('html')->nullable();
            $table->boolean('is_empty_link')->default(false);
            $table->boolean('target_blank')->default(false);
            $table->timestamps();

            $table->foreign('menu_id')
                ->references('id')
                ->on('nova_menus')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('nova_menu_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('nova_menu_items');
        Schema::drop('nova_menus');
    }
};
