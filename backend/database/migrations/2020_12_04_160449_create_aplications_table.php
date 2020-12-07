<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aplications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_firstName');
            $table->string('user_lastName');
            $table->string('user_email');
            $table->string('judet');
            $table->string('localitate');
            $table->string('adresa');
            $table->string('serie_ci');
            $table->string('numar_serie_ci');
            $table->string('cnp');
            $table->string('nr_certificat');
            $table->string('primaria');
            $table->string('alte_precizari')->default('None');
            $table->string('copie_ci')->default('none');
            $table->string('copie_certificat')->default('none');
            $table->boolean('pending')->default(true);
            $table->integer('status')->default(0);
            $table->string('motiv_respingere')->default('none');
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
        Schema::dropIfExists('aplications');
    }
}
