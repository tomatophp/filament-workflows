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
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();

            $table->string('team_id')->nullable();

            $table->string('user_type')->nullable();
            $table->string('user_id')->nullable();

            $table->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');

            $table->foreignId('trigger_id')->nullable()->constrained('triggers')->onDelete('cascade');
            $table->foreignId('action_id')->nullable()->constrained('actions')->onDelete('cascade');

            $table->json('log')->nullable();
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
        Schema::dropIfExists('workflow_logs');
    }
};
