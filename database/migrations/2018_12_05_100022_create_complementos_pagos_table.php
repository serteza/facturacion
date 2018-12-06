<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplementosPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complementos_pagos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('serie');
            $table->string('folio');
            $table->string('importe');
            $table->string('saldo');
            $table->string('fecha');

            $table->integer('factura_id')->unsigned();
            $table->foreign('factura_id')
                  ->references('id')
                  ->on('facturas')
                  ->onDelete('cascade');

            $table->integer('timbrado_complemento_id')->unsigned();
            $table->foreign('timbrado_complemento_id')
                  ->references('id')
                  ->on('timbrados_complementos')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('factura_id');
            $table->index('timbrado_complemento_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complementos_pagos');
    }
}
