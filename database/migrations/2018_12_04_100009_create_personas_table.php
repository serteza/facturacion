<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('rfc');
            $table->string('residencia_fiscal');
            $table->string('tipo');

            $table->integer('contacto_id')->unsigned();
            $table->foreign('contacto_id')
                  ->references('id')
                  ->on('contactos')
                  ->onDelete('cascade');
            $table->integer('origen_id')->unsigned();
            $table->foreign('origen_id')
                  ->references('id')
                  ->on('origenes')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('contacto_id');
            $table->index('origen_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
