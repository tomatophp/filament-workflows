
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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();

            $table->string('team_id')->nullable();

            $table->string('name')->index();
            $table->json('description')->nullable();

            $table->string('type')->default('action')->nullable();

            $table->string('action')->nullable();

            $table->json('payload')->nullable();

            $table->string('color')->default('gray')->nullable();
            $table->string('icon')->default('icon')->nullable();

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
        Schema::dropIfExists('actions');
        Schema::enableForeignKeyConstraints();
    }
};
