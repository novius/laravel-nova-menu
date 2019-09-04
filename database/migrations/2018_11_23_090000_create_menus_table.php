<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nova_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('nova_menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('menu_id')->unsigned();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('left')->unsigned();
            $table->integer('right')->unsigned();
            $table->string('external_link')->nullable();
            $table->string('internal_link')->nullable();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')
                ->on('nova_menus')
                ->onDelete('cascade');

            $table->foreign('parent_id')->references('id')
                ->on('nova_menu_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nova_menus');
        Schema::drop('nova_menu_items');
    }
}
