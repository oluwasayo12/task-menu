<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('it_id');
            $table->unsignedBigInteger('it_mn_id');
            $table->foreign('it_mn_id')->references('mn_id')->on('menus');
            $table->integer('it_parent_id')->nullable();
            $table->string('it_field');
            $table->integer('it_depth');
            $table->integer('it_children');
            $table->timestamps();
            $table->engine = 'InnoDB';	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
