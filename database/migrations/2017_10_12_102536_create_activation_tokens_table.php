<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activation_tokens', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('user_id')->unsigned()->index();
	        $table->string('token');
	        $table->enum('status', ['available', 'used', 'expired'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activation_tokens');
    }
}
