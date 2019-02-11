<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\DeckResourceCard;
use App\Model\Expansion;

class KingdomController extends Controller
{
    /**
     * Display a recommend of the kingdom.
     *
     * @return {num_of_cards: XX, kingdom:[{"id": Y, "name_jp": "ZZ" ~~ }]}
     */
    public function index(Request $request)
    {
      $kingdom_params = $this->parse_request($request);
      // echo json_encode($kingdom_params,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      $kingdom = $this->build_kingdom($kingdom_params);

      $response = array(
        "num_of_card"=>count($kingdom),
        "cost_coin"=>array(
          "2"=>count($kingdom->where("cost_coin",2)),
          "3"=>count($kingdom->where("cost_coin",3)),
          "4"=>count($kingdom->where("cost_coin",4)),
          "5"=>count($kingdom->where("cost_coin",5)),
          "6"=>count($kingdom->where("cost_coin",6)),
          "7"=>count($kingdom->where("cost_coin",7))
        ),
        "kingdom"=> $kingdom
      );

      return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function parse_request(Request $request)
    {
      $num_of_card = (int)($request->input('num_of_card'));

      $all_expansions_id = array_column(Expansion::get( )->toArray(), 'id');
      $expansion_ids = is_null($request->input('expansions')) ? $all_expansions_id : json_decode($request->input('expansions'));
      $max_attack = is_null($request->input('max_attack')) ? $num_of_card : (int)($request->input('max_attack'));
      $action_gain_required = ( 'true'  == $request->input('action_gain_required')) ? true  : false;
      $card_gain_required = ( 'true'  == $request->input('card_gain_required')) ? true  : false;
      $buy_gain_required = ( 'true'  == $request->input('buy_gain_required')) ? true  : false;
      $required_costs = is_null($request->input('required_costs')) ? []  : json_decode($request->input('required_costs'), true);

      $requested_params = array(
        "num_of_card" => $num_of_card,
        "max_attack" => $max_attack,
        "expansion_ids" => $expansion_ids,
        "action_gain_required"=>$action_gain_required,
        "card_gain_required"=>$card_gain_required,
        "buy_gain_required"=>$buy_gain_required,
        "required_costs"=>$required_costs
      );
      return $requested_params;
    }

    protected function build_kingdom($kingdom_params)
    {
      $kingdom = DeckResourceCard::take(0)->get();
      // アクション追加カード
      $action_gain_card =  $this->get_action_gain_card($kingdom_params);
      $kingdom = is_null($action_gain_card) ? $kingdom : $kingdom->concat($action_gain_card);

      // カード追加カード
      $card_gain_card =  $this->get_card_gain_card($kingdom_params);
      $kingdom = is_null($card_gain_card) ? $kingdom : $kingdom->concat($card_gain_card);

      //  購入追加カード
      $buy_gain_card =  $this->get_card_gain_card($kingdom_params);
      $kingdom = is_null($buy_gain_card) ? $kingdom : $kingdom->concat($buy_gain_card);

      // 特定のコストのカードを追加
      $specific_cost_cards =  $this->get_specific_cost_card($kingdom, $kingdom_params);
      $kingdom = is_null($specific_cost_cards) ? $kingdom : $kingdom->concat($specific_cost_cards);

      //  その他のカードを追加
      $kingdom = $this->fill_kingdom($kingdom, $kingdom_params);

      //  Attack カードの枚数を制限
      $kingdom = $this->regulate_attack_card_num($kingdom, $kingdom_params);

      return $kingdom;
    }
    //
    protected function get_action_gain_card($kingdom_params)
    {
      // アクション増加カードが要求されているかチェック
      $action_gain_required = $kingdom_params["action_gain_required"];
      if(! $action_gain_required){
        return NULL;
      }
      $deck_resource = $this->get_deck_resource($kingdom_params["expansion_ids"]);

      $action_gain_card = $deck_resource->where('gain_action', '>=',  2)->take(1)->get();

      return $action_gain_card;
    }
    //
    protected function get_card_gain_card($kingdom_params)
    {
      //  カード増加カードが要求されているかチェック
      $card_gain_required = $kingdom_params["card_gain_required"];
      if(! $card_gain_required){
        return NULL;
      }
      $deck_resource = $this->get_deck_resource($kingdom_params["expansion_ids"]);
      $card_gain_card = $deck_resource->where('gain_card', '>=',  2)->take(1)->get();

      return $card_gain_card;
    }
    //
    protected function get_buy_gain_card($kingdom_params)
    {
      //  購入権増加カードが要求されているかチェック
      $buy_gain_required = $kingdom_params["buy_gain_required"];
      if(! $buy_gain_required){
        return NULL;
      }

      $deck_resource = $this->get_deck_resource($kingdom_params["expansion_ids"]);

      $buy_gain_card = $deck_resource->where('gain_buy', '>=',  1)->take(1)->get();

      return $buy_gain_card;
    }
    //
    protected function get_specific_cost_card($kingdom, $kingdom_params)
    {
      $specific_cost_cards = DeckResourceCard::take(0)->get();
      foreach ($kingdom_params["required_costs"] as $cost => $num) {
        if(count($kingdom->where('cost_coin', (int)$cost)) < $num){
          $add_card_num = $num - count($kingdom->where('cost_coin', (int)$cost)) ;
          $deck_resource = $this->get_deck_resource($kingdom_params["expansion_ids"]);
          $specific_cost_cards = $specific_cost_cards
          ->concat($deck_resource->where('cost_coin', (int)$cost)->take($add_card_num)->get());
        }
      }

      return $specific_cost_cards;
    }

    protected function fill_kingdom($kingdom, $kingdom_params)
    {
      // 重複を削除
      $kingdom = $kingdom->unique('id');
      $deck_resource = $this->get_deck_resource($kingdom_params["expansion_ids"]);

      $new_card_num = $kingdom_params["num_of_card"] - count($kingdom);
      if($new_card_num <= 0){
        return $kingdom;
      }

      $new_cards = $deck_resource->whereNotIn('id', array_column($kingdom->toArray(), 'id'))
      ->take($new_card_num)->get();
      $kingdom = $kingdom->concat($new_cards);
      return $kingdom;
    }

    protected function get_deck_resource($expansion_ids)
    {
      $deck_resource = DeckResourceCard::where('randomizer', true)
      ->whereIn('expansion_id', $expansion_ids)->inRandomOrder();
      return $deck_resource;
    }

    protected function regulate_attack_card_num($kingdom, $kingdom_params)
    {
      $max_attack = $kingdom_params["max_attack"];
      $expansion_ids = $kingdom_params["expansion_ids"];
      $attack_cards = $kingdom->where('attack', true);
      $other_cards = $kingdom->where('attack', false);
      // すでにアタックカードの枚数が条件を満たしているならreturn .
      if(count($attack_cards) <= $max_attack){
        return $kingdom;
      }

      // Attack カードを減らす
      $reduced_attack_cards = $attack_cards->take($max_attack);
      // 減らした Attack カードの代替カードを取得
      $alternative_cards = DeckResourceCard::where('randomizer', true)
      ->whereIn('expansion_id', $expansion_ids)
      ->whereNotIn('id', array_column($other_cards->toArray(), 'id'))->where('attack', false)
      ->take(count($attack_cards) - $max_attack)->get();
      // echo json_encode(count($other_cards), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n\r";
      $kingdom = $alternative_cards->concat($other_cards)->concat($reduced_attack_cards);
      return $kingdom;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
