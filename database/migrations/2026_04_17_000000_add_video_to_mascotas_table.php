<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoToMascotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            if (!Schema::hasColumn('mascotas', 'video')) {
                $table->string('video')->nullable()->after('imagen');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            if (Schema::hasColumn('mascotas', 'video')) {
                $table->dropColumn('video');
            }
        });
    }
}
