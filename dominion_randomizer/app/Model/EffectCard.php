<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EffectCard extends Model
{
  protected $fillable = [
    'id',
    'name_jp',
    'name_jp_pronunciations',
    'name_en',
    'expansion_id',
    'cost_coin',
    'cost_potion',
    'cost_debt',
    'text_jp',
    'text_en',
  ];
  protected $hidden = [
    'created_at',
    'updated_at'
  ];
}
