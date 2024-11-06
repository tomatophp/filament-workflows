<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();

            $table->string('team_id')->nullable();

            $table->string('user_id')->nullable();
            $table->string('user_type')->nullable();

            $table->string('model_id')->nullable();
            $table->string('model_type')->nullable();

            $table->json('name');
            $table->string('key')->unique()->index();
            $table->json('description')->nullable();

            $table->string('type')->default('workflow')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->json('payload')->nullable();

            $table->boolean('is_active')->default(false)->nullable();

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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('workflows');
        Schema::enableForeignKeyConstraints();
    }
};
