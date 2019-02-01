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
        "kingdom"=> $kingdom
      );
      return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function parse_request(Request $request)
    {
      $num_of_card = (int)($request->input('num_of_card'));

      $all_expansions_id = array_column(Expansion::get( )->toArray(), 'id');
      $expansion_ids = empty($request->input('expansions')) ? $all_expansions_id : json_decode($request->input('expansions'));

      $max_attack = empty($request->input('max_attack')) ? 10 : (int)($request->input('max_attack'));

      $requested_params = array(
        "num_of_card" => $num_of_card,
        "max_attack" => $max_attack,
        "expansion_ids" => $expansion_ids
      );
      return $requested_params;
    }

    protected function build_kingdom($kingdom_params)
    {
      $kingdom = DeckResourceCard::where('randomizer', 1)
      ->whereIn('expansion_id', $kingdom_params["expansion_ids"])
      ->inRandomOrder()->take($kingdom_params["num_of_card"])->get();

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
