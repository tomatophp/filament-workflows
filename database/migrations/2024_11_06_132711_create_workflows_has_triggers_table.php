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
        Schema::create('workflows_has_triggers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');
            $table->foreignId('trigger_id')->constrained('triggers')->onDelete('cascade');

            $table->json('payload')->nullable();

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
        Schema::dropIfExists('workflows_has_triggers');
        Schema::enableForeignKeyConstraints();
    }
};
