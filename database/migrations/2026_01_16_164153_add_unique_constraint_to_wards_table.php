<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, remove duplicate records (keep the first one, delete the rest)
        $duplicates = DB::table('wards')
            ->select('province_id', 'name', DB::raw('COUNT(*) as count'))
            ->groupBy('province_id', 'name')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all IDs for this duplicate combination
            $ids = DB::table('wards')
                ->where('province_id', $duplicate->province_id)
                ->where('name', $duplicate->name)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Keep the first one, delete the rest
            if (count($ids) > 1) {
                $idsToDelete = array_slice($ids, 1); // All except the first
                DB::table('wards')->whereIn('id', $idsToDelete)->delete();
            }
        }

        // Add unique constraint
        Schema::table('wards', function (Blueprint $table) {
            $table->unique(['province_id', 'name'], 'wards_province_id_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wards', function (Blueprint $table) {
            $table->dropUnique('wards_province_id_name_unique');
        });
    }
};
