<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('reported_id');
            $table->integer('sender_id');
            $table->string('reason');
            $table->integer('solved')->default(0);
            $table->integer('action')->default(-1);
            $table->integer('report_type');
            $table->string('link');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
