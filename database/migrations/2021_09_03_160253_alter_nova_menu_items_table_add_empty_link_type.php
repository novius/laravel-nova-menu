<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNovaMenuItemsTableAddEmptyLinkType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nova_menu_items', function (Blueprint $table) {
            $table->text('is_empty_link')->default(0)->after('html');
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
            $table->dropColumn('is_empty_link');
        });
    }
}
