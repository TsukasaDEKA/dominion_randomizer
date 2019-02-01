<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Expansion extends Model
{
  protected $table = 'expansions';
  protected $fillable = ['id', 'name_jp_kanji', 'name_jp_pronunciations', 'name_en'];
}
