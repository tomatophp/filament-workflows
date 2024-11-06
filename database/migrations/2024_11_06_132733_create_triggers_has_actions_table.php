<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triggers_has_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflows_has_triggers_id')->constrained('workflows_has_triggers')->onDelete('cascade');
            $table->foreignId('action_id')->constrained('actions')->onDelete('cascade');

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
        Schema::dropIfExists('workflows_has_actions');
        Schema::enableForeignKeyConstraints();
    }
};
