<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adt_users_has_sucursales', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            
            $table->increments('id');
            $table->integer('adt_user_id')->unsigned();
            $table->foreign('adt_user_id')
                  ->references('id')
                  ->on('adt_users')
                  ->onDelete('cascade');

            $table->integer('sucursal_id')->unsigned();
            $table->foreign('sucursal_id')
                  ->references('id')
                  ->on('sucursales')
                  ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index('adt_user_id');
            $table->index('sucursal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adt_users_has_sucursales');
    }
}
