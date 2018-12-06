<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallesFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_facturas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            
            $table->increments('id');
            $table->string('importe');
            $table->string('descuento');
            $table->string('iva');
            $table->string('ieps');
            $table->string('otros_impuestos');
            $table->string('moneda');
            $table->string('tipo_cambio');
            $table->string('uso_cfdi');
            $table->string('ine');
            $table->string('tipo_comprobante');
            $table->string('condiciones_pago');
            $table->string('num_cuenta');
            $table->string('observaciones');
            $table->string('estatus');

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
        Schema::dropIfExists('detalles_facturas');
    }
}
