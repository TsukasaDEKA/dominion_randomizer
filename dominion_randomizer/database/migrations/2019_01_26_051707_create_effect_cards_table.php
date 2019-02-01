<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEffectCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('effect_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_jp')->nullable();
            $table->string('name_jp_pronunciations')->nullable();
            $table->string('name_en');
            $table->string('text_jp', 4096)->nullable();
            $table->string('text_en', 4096)->nullable();
            $table->integer('expansion_id')->unsigned()->nullable();
            $table->foreign('expansion_id')
            ->references('id')->on('expansions')->onDelete('set null');
            $table->integer('cost_coin')->nullable();
            $table->integer('cost_potion')->nullable();
            $table->integer('cost_debt')->nullable();
            $table->string('type');
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
        Schema::dropIfExists('effect_cards');
    }
}
