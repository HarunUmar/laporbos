<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAduanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aduan', function (Blueprint $table) {
            $table->increments('id');
            $table->string("judul");
            $table->string("isi");
            $table->string("lat");
            $table->string("long");
            $table->integer("user_id")->unsigned();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("masalah_id")->unsigned();
            $table->foreign("masalah_id")->references("id")->on("masalah")->onDelete("cascade")->onUpdate("cascade");
            $table->enum('status', ['1','2','3'])->default('1'); // 1. belum di proses, 2.dalam Proses, 3.selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aduan');
    }
}
