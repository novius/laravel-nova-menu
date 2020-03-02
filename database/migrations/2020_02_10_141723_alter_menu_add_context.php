<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMenuAddContext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nova_menus', function (Blueprint $table) {
            $table->string('locale', 6)->default('en');
            $table->unsignedInteger('locale_parent_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nova_menus', function (Blueprint $table) {
            $table->dropColumn(['locale', 'locale_parent_id']);
        });
    }
}
