<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMenuItemsAddClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nova_menu_items', function (Blueprint $table) {
            $table->string('html_classes', 255)->nullable()->after('internal_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nova_menu_items', function (Blueprint $table) {
            $table->dropColumn('html_classes');
        });
    }
}
