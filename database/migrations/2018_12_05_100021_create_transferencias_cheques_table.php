<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferenciasChequesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencias_cheques', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('banco');
            $table->string('referencia');
            $table->string('importe');
            $table->string('no_cuenta');

            $table->integer('pendiente_cobro_id')->unsigned();
            $table->foreign('pendiente_cobro_id')
                  ->references('id')
                  ->on('pendientes_cobros')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('pendiente_cobro_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transferencias_cheques');
    }
}
