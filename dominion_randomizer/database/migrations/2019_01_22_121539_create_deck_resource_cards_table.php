<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeckResourceCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deck_resource_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_jp')->nullable();
            $table->string('name_jp_pronunciations')->nullable();
            $table->string('name_en');
            $table->integer('expansion_id')->unsigned()->nullable();
            $table->foreign('expansion_id')
            ->references('id')->on('expansions')->onDelete('set null');
            $table->integer('treasures')->nullable();
            $table->integer('vp')->nullable();
            $table->integer('cost_coin')->nullable();
            $table->integer('cost_potion')->nullable();
            $table->integer('cost_debt')->nullable();
            $table->integer('gain_card')->nullable();
            $table->integer('gain_action')->nullable();
            $table->integer('gain_buy')->nullable();
            $table->integer('gain_money')->nullable();
            $table->integer('gain_vp')->nullable();
            $table->integer('gain_villager')->nullable();
            $table->integer('gain_coffer')->nullable();
            $table->string('text_jp', 4096)->nullable();
            $table->string('text_en', 4096)->nullable();
            $table->boolean('action')->default(false);
            $table->boolean('attack')->default(false);
            $table->boolean('castle')->default(false);
            $table->boolean('curse')->default(false);
            $table->boolean('doom')->default(false);
            $table->boolean('duration')->default(false);
            $table->boolean('event')->default(false);
            $table->boolean('fate')->default(false);
            $table->boolean('gathering')->default(false);
            $table->boolean('heirloom')->default(false);
            $table->boolean('knight')->default(false);
            $table->boolean('night')->default(false);
            $table->boolean('looter')->default(false);
            $table->boolean('prize')->default(false);
            $table->boolean('reaction')->default(false);
            $table->boolean('reserve')->default(false);
            $table->boolean('ruins')->default(false);
            $table->boolean('spirit')->default(false);
            $table->boolean('shelter')->default(false);
            $table->boolean('treasure')->default(false);
            $table->boolean('traveller')->default(false);
            $table->boolean('victory')->default(false);
            $table->boolean('zombie')->default(false);
            $table->string('pile_position');/*base, kingdom or non_supply*/
            $table->boolean('randomizer');/*Included in randomizer*/
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
        Schema::dropIfExists('deck_resource_cards');
    }
}
