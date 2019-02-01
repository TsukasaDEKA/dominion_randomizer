<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeckResourceCard extends Model
{
    protected $table = 'deck_resource_cards';
    protected $fillable = [
      'id',
      'name_jp_kanji',
      'name_jp_pronunciations',
      'name_en',
      'expansion_id',
      'cost_coin',
      'cost_potion',
      'cost_debt',
      'gain_card',
      'gain_action',
      'gain_buy',
      'gain_money',
      'gain_vp',
      'gain_villager',
      'gain_coffer',
      'text_jp',
      'text_en',
      'action',
      'attack',
      'castle',
      'curse',
      'doom',
      'duration',
      'fate',
      'gathering',
      'heirloom',
      'knight',
      'night',
      'looter',
      'prize',
      'reaction',
      'reserve',
      'ruins',
      'spirit',
      'shelter',
      'treasure',
      'traveller',
      'victory',
      'zombie',
      'pile_position',
      'randomizer'
    ];
    protected $hidden = [
      'action',
      'attack',
      'castle',
      'curse',
      'doom',
      'duration',
      'fate',
      'gathering',
      'heirloom',
      'knight',
      'night',
      'looter',
      'prize',
      'reaction',
      'reserve',
      'ruins',
      'spirit',
      'shelter',
      'treasure',
      'traveller',
      'victory',
      'zombie',
      'pile_position',
      'randomizer',
      'created_at',
      'updated_at'
    ];

}
