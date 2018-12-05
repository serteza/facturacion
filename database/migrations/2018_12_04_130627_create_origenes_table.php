<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrigenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('origenes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('tag');
            $table->string('pais');
            $table->string('estado');
            $table->string('cp_inf');
            $table->string('cp_sup');
            $table->string('municipio');
            $table->string('localidad');
            
            $table->integer('direccion_id')->unsigned();
            $table->foreign('direccion_id')
                  ->references('id')
                  ->on('direcciones')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('direccion_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('origenes');
    }
}
