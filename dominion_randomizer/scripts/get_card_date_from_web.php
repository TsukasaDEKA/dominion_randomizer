<?php
class CardData
{
  public $name_jp;
  public $name_en;
  public $pronunciations;
  public $expansion;
  public $cost = array(
    "coin"=>"",
    "potion"=>"",
    "debt"=>""
  );
  public $category;
  public $types;
  public $treasure;
  public $vp;
  public $gain = array(
    "card"=>"",
    "action"=>"",
    "buy"=>"",
    "money"=>"",
    "vp"=>"",
    "villager"=>"",
    "coffer"=>""
  );
  public $text_jp;
  public $text_en;
  public function wash_table_element($target_string){
    if($target_string == "-"){
      return NULL;
    }
    $target_string = str_replace('$', '', $target_string);
    $target_string = str_replace('P', '', $target_string);
    $target_string = str_replace('D', '', $target_string);
    $target_string = str_replace('*', '', $target_string);
    return $target_string;
  }
}
?>
<?php
function get_html_include_card_data($id){
  $url = "http://suka.s5.xrea.com/dom/list.cgi?id=".(string)$id."&mode=show";
  $raw_html = file_get_contents($url);
  $char_from = 'SJIS';
  $char_to = 'UTF-8';
  $html = mb_convert_encoding($raw_html, $char_to, $char_from);
  return $html;
}

function get_card_data_from_html($html){
  require_once("./phpQuery-onefile.php");
  $contents = phpQuery::newDocument($html)->find("body")->text();
  $body = phpQuery::newDocument($html)->find("body")[0];
  $name = $body[".name"]->text();
  $property = $body["table:eq(1)"];
  $comments = $body["table:eq(2)"];

  $text_jp = str_replace("【効果】", "", explode("【Text】", $comments[".com3"]->text())[0]);
  $text_en = explode("【説明】", explode("【Text】", $comments[".com3"]->text())[1])[0];

  $card_data = new CardData;
  $card_data->name_jp = $name;
  $card_data->name_en = $property->find("td:contains('Name') + td:eq(0)")->text();
  $card_data->pronunciations = $property->find("td:contains('読み') + td:eq(0)")->text();
  $card_data->expansion = $property->find("td:contains('セット') + td:eq(0)")->text();
  $cost = array(
    "coin"=>$card_data->wash_table_element($property->find("td:contains('コストコイン') + td:eq(0)")->text()),
    "potion"=>$card_data->wash_table_element($property->find("td:contains('コストポーション') + td:eq(0)")->text()),
    "debt"=>$card_data->wash_table_element($property->find("td:contains('コスト負債') + td:eq(0)")->text())
  );
  $card_data->cost = $cost;

  $card_data->category = $property->find("td:contains('分類') + td:eq(0)")->text();
  $card_data->types = explode("−", str_replace("－", "−", $property->find("td:contains('種類') + td:eq(0)")->text()));
  $card_data->treasure = $card_data->wash_table_element($property->find("td:contains('財宝') + td:eq(0)")->text());
  $card_data->vp =$card_data->wash_table_element($property->find("td:contains('勝利点') + td:eq(0)")->text());

  $gain = array(
    "card"=>$card_data->wash_table_element($property->find("td:contains('+Card') + td:eq(0)")->text()),
    "action"=>$card_data->wash_table_element($property->find("td:contains('+Action') + td:eq(0)")->text()),
    "buy"=>$card_data->wash_table_element($property->find("td:contains('+Buy') + td:eq(0)")->text()),
    "money"=>$card_data->wash_table_element($property->find("td:contains('+$') + td:eq(0)")->text()),
    "vp"=>$card_data->wash_table_element($property->find("td:contains('+VP') + td:eq(0)")->text()),
    "villager"=>$card_data->wash_table_element($property->find("td:contains('+Villager') + td:eq(0)")->text()),
    "coffer"=>$card_data->wash_table_element($property->find("td:contains('+Coffer') + td:eq(0)")->text())
  );
  $card_data->gain = $gain;
  $card_data->text_jp = $text_jp;
  $card_data->text_en = $text_en;
  return $card_data;
}
?>

<?php
$card_ids = range(1, 514, 1);
// $card_ids = range(1, 20, 1);
$card_datas = array();
foreach ($card_ids as $card_id) {
  print("Target card id is : ".$card_id."\n");
  $html = get_html_include_card_data($card_id);
  // $html = file_get_contents('./date/0001_copper.html');
  $card_data = get_card_data_from_html($html);
  array_push($card_datas, $card_data);
  sleep(5);
}

$card_json = json_encode($card_datas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$json = fopen('cards.json', 'w+b');
fwrite($json, $card_json);
fclose($json);
print("\n");
?>
