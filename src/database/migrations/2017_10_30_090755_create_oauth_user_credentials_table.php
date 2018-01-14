<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthUserCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_user_credentials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name')->charset('ascii');
            $table->string('owner_id')->charset('ascii');
            $table->string('access_token', 512)->charset('ascii');
            $table->string('refresh_token', 512)->nullable()->charset('ascii');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->unique(['name', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_user_credentials');
    }
}
