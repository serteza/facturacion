<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendientesCobrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendientes_cobros', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('saldo');
            $table->string('importe');
            $table->string('fecha');
            $table->string('forma_pago');
            
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
        Schema::dropIfExists('pendientes_cobros');
    }
}
