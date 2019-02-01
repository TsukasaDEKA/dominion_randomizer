<?php

use Illuminate\Database\Seeder;
use App\Model\DeckResourceCard;
use App\Model\EffectCard;
use App\Model\Expansion;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $type_dictionary = array(
      'アクション'=>'action',
      'アタック'=>'attack',
      'ARTIFACT'=>'artifact',
      '祝福'=>'boon',
      '城'=>'castle',
      '呪い'=>'curse',
      '不運'=>'doom',
      '持続'=>'duration',
      'イベント'=>'event',
      '幸運'=>'fate',
      '集合'=>'gathering',
      '家宝'=>'heirloom',
      '呪詛'=>'hex',
      '騎士'=>'knight',
      '夜行'=>'night',
      '略奪者'=>'looter',
      'ランドマーク'=>'landmark',
      '褒賞'=>'prize',
      'PROJECT'=>'project',
      'リアクション'=>'reaction',
      'リザーブ'=>'reserve',
      '廃墟'=>'ruins',
      '精霊'=>'spirit',
      '状態'=>'state',
      '避難所'=>'shelter',
      '財宝'=>'treasure',
      'トラベラー'=>'traveller',
      '勝利点'=>'victory',
      'ゾンビ'=>'zombie',
    );
    private $non_randomizer_deck_resource_name = array(
      '崩れた城',
      '小さい城',
      '幽霊城',
      '華やかな城',
      '広大な城',
      '壮大な城',
      '王城',
      '兵士',
      '脱走兵',
      '門下生',
      '教師',
      'トレジャーハンター',
      'ウォリアー',
      'ヒーロー',
      'チャンピオン',
      'デイム・アンナ',
      'デイム・ジョセフィーヌ',
      'デイム・モリー',
      'デイム・ナタリー',
      'デイム・シルビア',
      'サー・ベイリー',
      'サー・デストリー',
      'サー・マーチン',
      'サー・マイケル',
      'サー・ヴァンデル',
      'エンポリウム',
      '鹵獲品',
      '騒がしい村',
      '石',
      '大金',
      'アヴァント'
    );
    private $split_pile_type = array('城', '騎士');
    private $non_supply_type = array( '精霊', '避難所', '家宝', 'ゾンビ', '褒賞');
    private $base_supply_name = array(
      '銅貨',
      '銀貨',
      '金貨',
      '白金貨',
      '屋敷',
      '公領',
      '属州',
      '植民地',
      '呪い',
      'ポーション'
    );
    private $effect_type = array('PROJECT', 'ARTIFACT', 'イベント', 'ランドマーク', '呪詛',  '祝福', '状態');

    public function run()
    {
      DB::table('deck_resource_cards')->delete();
      DB::table('effect_cards')->delete();

      $expansions = Expansion::get();
      $json = File::get("./database/seeds/data_files/cards.json");
      $card_list = json_decode($json);

      foreach ($card_list as $card){
        $cost_coin = empty($card->cost->coin) ? NULL : (int)($card->cost->coin);
        $cost_potion = empty($card->cost->potion) ? NULL : (int)($card->cost->potion);
        $cost_debt = empty($card->cost->debt) ? NULL : (int)($card->cost->debt);
        $gain_card = empty($card->gain->card) ? NULL : (int)($card->gain->card);
        $gain_action = empty($card->gain->action) ? NULL : (int)($card->gain->action);
        $gain_buy = empty($card->gain->buy) ? NULL : (int)($card->gain->buy);
        $gain_money = empty($card->gain->money) ? NULL : (int)($card->gain->money);
        $gain_vp = empty($card->gain->vp) ? NULL : (int)($card->gain->vp);
        $gain_villager = empty($card->gain->villager) ? NULL : (int)($card->gain->villager);
        $gain_coffer = empty($card->gain->coffer) ? NULL : (int)($card->gain->coffer);
        $expansion_id = $expansions->where('name_jp', $card->expansion)->first()->id;
        $is_effect_card = $this->is_effect_card($card);
        if($is_effect_card){
          EffectCard::create(array(
            'name_jp' => $card->name_jp,
            'name_jp_pronunciations' => $card->pronunciations,
            'name_en' => $card->name_en,
            'expansion_id' => $expansion_id,
            'cost_coin' => $cost_coin,
            'cost_potion' => $cost_potion,
            'cost_debt' => $cost_debt,
            'type'=>$card->types[0],
            'text_jp'=>$card->text_jp,
            'text_en'=>$card->text_en,
          ));
        }else{
          $pile_position = $this->get_pile_position($card);
          $randomizer = $this->is_randomizer($card, $pile_position);
          DeckResourceCard::create(array(
              'name_jp' => $card->name_jp,
              'name_jp_pronunciations' => $card->pronunciations,
              'name_en' => $card->name_en,
              'expansion_id' => $expansion_id,
              'treasures' => empty($card->treasure) ? NULL : (int)($card->treasure),
              'vp' => empty($card->vp) ? NULL : (int)($card->vp),

              'cost_coin' => $cost_coin,
              'cost_potion' => $cost_potion,
              'cost_debt' => $cost_debt,

              'gain_card' => $gain_card,
              'gain_action' => $gain_action,
              'gain_buy' => $gain_buy,
              'gain_money' => $gain_money,
              'gain_vp' => $gain_vp,
              'gain_villager' => $gain_villager,
              'gain_coffer' => $gain_coffer,

              'action'=>in_array('アクション', $card->types),
              'attack'=>in_array('アタック', $card->types),
              'castle'=>in_array('城', $card->types),
              'curse'=>in_array('呪い', $card->types),
              'doom'=>in_array('不運', $card->types),
              'duration'=>in_array('持続', $card->types),
              'fate'=>in_array('幸運', $card->types),
              'gathering'=>in_array('集合', $card->types),
              'heirloom'=>in_array('家宝', $card->types),
              'knight'=>in_array('騎士', $card->types),
              'night'=>in_array('夜行', $card->types),
              'looter'=>in_array('略奪者', $card->types),
              'prize'=>in_array('褒賞', $card->types),
              'reaction'=>in_array('リアクション', $card->types),
              'reserve'=>in_array('リザーブ', $card->types),
              'ruins'=>in_array('廃墟', $card->types),
              'spirit'=>in_array('精霊', $card->types),
              'shelter'=>in_array('避難所', $card->types),
              'treasure'=>in_array('財宝', $card->types),
              'traveller'=>in_array('トラベラー', $card->types),
              'victory'=>in_array('勝利点', $card->types),
              'zombie'=>in_array('ゾンビ', $card->types),

              'pile_position' => $pile_position,
              'randomizer' => $randomizer,
              'text_jp'=>$card->text_jp,
              'text_en'=>$card->text_en,
          ));
        }
      }
    }

    private function get_pile_position($card){
      if(in_array($card->name_jp, $this->base_supply_name)){
        return 'base';
      }

      foreach ($card->types as $type){
        if(in_array($type, $this->non_supply_type)){
          return 'non_supply';
        }
        if($type == "廃墟"){
          return 'base';
        }
      }
      return 'kingdom';
    }

    private function is_randomizer($card, $pile_position){
      if ($pile_position != 'kingdom'){
        return false;
      }
      foreach ($card->types as $type){
        if(in_array($type, $this->non_randomizer_deck_resource_name)){
          return false;
        }
      }
      return true;
    }

    private function is_effect_card($card){
      foreach ($card->types as $type){
        if(in_array($type, $this->effect_type)){
          return true;
        }
      }
      return false;
    }
}
