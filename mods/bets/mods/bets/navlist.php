<?php

require_once('mods/bets/class_bet.php');
$cs_lang = cs_translate('bets');
$cs_option = cs_sql_option(__FILE__,'bets');

$categories_id = (!empty($_GET['categories_id']) ? (int) $_GET['categories_id'] : 0);

$select = 'bet.bets_id AS bets_id, bet.bets_title AS bets_title, bet.bets_closed_at AS bets_closed_at, cat.categories_name AS categories_name';
$where = 'bet.bets_status = '.cs_bet::STATUS_OPEN;
if (!empty($categories_id))
	$where .= ' AND bet.categories_id = '.$categories_id;
$order = 'bet.bets_closed_at ASC';
$tables = 'bets bet LEFT JOIN {pre}_categories cat ON bet.categories_id = cat.categories_id';
$cs_bets = cs_sql_select(__FILE__,$tables,$select,$where,$order,0,$cs_option['max_navlist']);

if($cs_option['max_navlist'] == '1') {
  $abets = array();
  array_push($abets,$cs_bets);
  unset($cs_bets);
  $cs_bets = $abets;
}

if(empty($cs_bets)) {
  echo $cs_lang['no_data'];
}
else {
  $data = array();
  $run = 0;
  foreach ($cs_bets AS $bets) {
    $data['bets'][$run]['bets_shorttitle'] = cs_secure(cs_textcut($bets['bets_title'], $cs_option['max_navlist_title']));
    $data['bets'][$run]['bets_title'] = cs_secure($bets['bets_title']);
		$data['bets'][$run]['bets_date'] =  cs_date('unix',$bets['bets_closed_at'],0,1,$cs_option['date_format']);
    $data['bets'][$run]['bets_url'] = cs_url('bets','view','id=' . $bets['bets_id']);
    $data['bets'][$run]['bets_category'] = cs_secure($bets['categories_name']);
    $run++;
  }
  echo cs_subtemplate(__FILE__,$data,'bets','navlist');
}
?>
