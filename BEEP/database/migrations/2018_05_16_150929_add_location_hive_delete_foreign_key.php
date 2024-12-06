<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationHiveDeleteForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the database driver is SQLite
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('hives', function (Blueprint $table) {
                // Drop the foreign key if it exists, and add the new foreign key
                $table->dropForeign(['location_id']);
                $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the database driver is SQLite
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('hives', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade');
            });
        }
    }
}
