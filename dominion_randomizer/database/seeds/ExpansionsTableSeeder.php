<?php

use Illuminate\Database\Seeder;

class ExpansionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $expansions = array(
        array('id'=>1,      'name_jp'=>'基本',              'name_jp_pronunciations'=>'きほん',                'name_en'=>'Basic'),
        array('id'=>2,      'name_jp'=>'陰謀',              'name_jp_pronunciations'=>'いんぼう',            'name_en'=>'Intrigue'),
        array('id'=>3,      'name_jp'=>'海辺',              'name_jp_pronunciations'=>'うみべ',                 'name_en'=>'Seaside'),
        array('id'=>4,      'name_jp'=>'錬金術',          'name_jp_pronunciations'=>'れんきんじゅつ', 'name_en'=>'Alchemy'),
        array('id'=>5,      'name_jp'=>'繁栄',              'name_jp_pronunciations'=>'はんえい',             'name_en'=>'Prosperity'),
        array('id'=>6,      'name_jp'=>'収穫祭',          'name_jp_pronunciations'=>'しゅうかくさい', 'name_en'=>'Cornucopia'),
        array('id'=>7,      'name_jp'=>'異郷',              'name_jp_pronunciations'=>'いきょう',             'name_en'=>'Hinterlands'),
        array('id'=>8,      'name_jp'=>'暗黒時代',      'name_jp_pronunciations'=>'あんこくじだい', 'name_en'=>'Dark Ages'),
        array('id'=>9,      'name_jp'=>'ギルド',          'name_jp_pronunciations'=>'ぎるど',                 'name_en'=>'Guilds'),
        array('id'=>10,    'name_jp'=>'冒険',              'name_jp_pronunciations'=>'ぼうけん',             'name_en'=>'Adventures'),
        array('id'=>11,    'name_jp'=>'帝国',              'name_jp_pronunciations'=>'ていこく',             'name_en'=>'Empires'),
        array('id'=>12,    'name_jp'=>'夜想曲',          'name_jp_pronunciations'=>'やそうきょく',     'name_en'=>'Nocturne'),
        array('id'=>13,    'name_jp'=>'ルネサンス',  'name_jp_pronunciations'=>'るねさんす',         'name_en'=>'Renaissance'),
        array('id'=>14,    'name_jp'=>'プロモ',          'name_jp_pronunciations'=>'ぷろも',                 'name_en'=>'Promo'),
      );
      foreach ($expansions as $expansion) {
        DB::table('expansions')->insert($expansion);
      }
    }
}
