<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNovaMenuItemsTableAddHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nova_menu_items', function (Blueprint $table) {
            $table->text('html')->nullable()->after('html_classes');
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
            $table->dropColumn('html');
        });
    }
}
