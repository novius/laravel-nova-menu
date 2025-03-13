<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('nova_menu_items')
            ->where('internal_link', 'LIKE', 'linkable_route%')
            ->update(['internal_link' => DB::raw("REPLACE(`internal_link`, 'linkable_route', 'route')")]);

        DB::table('nova_menu_items')
            ->where('internal_link', 'LIKE', 'linkable_object%')
            ->update(['internal_link' => DB::raw("REPLACE(`internal_link`, 'linkable_object:', '')")]);
    }
};
