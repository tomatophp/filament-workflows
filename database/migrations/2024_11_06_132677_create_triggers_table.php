
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
        Schema::create('triggers', function (Blueprint $table) {
            $table->id();

            $table->string('team_id')->nullable();

            $table->string('name')->index();
            $table->string('type')->default('model')->nullable();

            // If Model
            $table->string('model_action')->default('created')->nullable();
            $table->string('model_type')->nullable();
            $table->string('model_id')->nullable();

            // If Event
            $table->string('event')->nullable();

            // If Webhook
            $table->string('webhook')->nullable();

            $table->string('where')->nullable();
            $table->string('is')->default('=')->nullable();
            $table->json('value')->nullable();

            $table->string('color')->default('gray')->nullable();
            $table->string('icon')->default('icon')->nullable();

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
        Schema::dropIfExists('triggers');
        Schema::enableForeignKeyConstraints();
    }
};
