<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimbradosFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timbrados_facturas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('serie');
            $table->string('folio');
            $table->string('folio_fiscal');
            $table->string('cadena_original');
            $table->string('xml');

            $table->integer('factura_id')->unsigned();
            $table->foreign('factura_id')
                  ->references('id')
                  ->on('facturas')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('factura_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timbrados_facturas');
    }
}
