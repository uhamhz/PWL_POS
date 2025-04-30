<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('m_barang', function (Blueprint $table) {
            $table->string('barang_image')->nullable()->after('harga_jual');
        });
    }

    public function down()
    {
        Schema::table('m_barang', function (Blueprint $table) {
            $table->dropColumn('barang_image');
        });
    }
};
