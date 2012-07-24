<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('bets');
$cs_get = cs_get('id,where');
$cs_post = cs_post('id');

require_once('mods/bets/class_bet.php');
$options = cs_sql_option(__FILE__, 'bets');
$coin_options = cs_sql_option(__FILE__, 'coins');

$myBet = new cs_bet();

$bets_id = empty($_REQUEST['where']) ? (int) $cs_get['id'] : (int) $_REQUEST['where'];
//Contestants names
$conts = array();

$myBet->loadBet((int) $_REQUEST['id']);
if (!empty($cs_get['refresh']))
{
	$myBet->calcQuote();
	$myBet->loadBet((int) $_REQUEST['id']);
}
$cs_bets = $myBet->bet_data;
if (empty($cs_bets['bets_id']))
{
	echo $cs_lang['no_data'];
	return;
}
$cs_contestants = $myBet->contestant_data;
$cs_users = $myBet->bet_users;
$numCons = (is_array($cs_contestants) ? count($cs_contestants) : 0);
$numUsers = (is_array($cs_users) ? count($cs_users) : 0);
$data['bets'] = $cs_bets;

$data['bets']['base_fee'] = $options['base_fee'];
$data['if']['fee'] = false;
if ($options['base_fee'] > 0.0)
	$data['if']['fee'] = true;
$data['bets']['win_quote'] = $options['win_quote'];
$data['bets']['quote_type_text'] = $cs_lang['quote_type_'.$cs_bets['bets_quote_type']];
$clip = array(
	0 => '[clip='.$data['bets']['quote_type_text'].']'.$cs_lang['quote_type_explain'].'[/clip\]',
	1 => $data['bets']['quote_type_text'],
	2 => $cs_lang['quote_type_explain']
);
$data['bets']['quote_type_clip'] = cs_abcode_clip($clip);
$data['bets']['date'] = cs_date('unix',$cs_bets['bets_closed_at'],1);
$data['bets']['details'] = !empty($cs_bets['bets_description']) ? cs_secure($cs_bets['bets_description'],1,1) : $cs_lang['no_desc'];
$data['if']['closed'] = false;
$data['if']['open'] = false;

// Check ob Wette abgelaufen
if($cs_bets['bets_closed_at'] < cs_time() && $cs_bets['bets_status'] == cs_bet::STATUS_OPEN){
	cs_sql_update(__FILE__, 'bets', array('bets_status'), array(cs_bet::STATUS_CLOSED), $data['bets']['bets_id']);	
	$cs_bets['bets_status'] = 1;
}

switch($cs_bets['bets_status']) {
	case 0: $data['bets']['status'] = $cs_lang['open'];
			$data['if']['open'] = true;
			break;
	case 1: $data['bets']['status'] = $cs_lang['on_calc'];
			break;
	case 2: $data['bets']['status'] = $cs_lang['closed'];
			$data['if']['closed'] = true;
			break;
}

$data['bets']['pointsname'] = $options['pointsname'];
$data['bets']['cat_name'] = $cs_bets['categories_name'];

if (!empty($account['users_id']))
{
	$cs_coins = cs_coins_exists($account['users_id']);
	if ($cs_coins === false)
	{
		/* no, try to create one */
		$cs_coins = cs_coins_create($account['users_id']);
		if ($cs_coins === false)
			$cs_coins['coins_total'] = 0;
	}
	$data['bets']['account_balance'] = number_format($cs_coins['coins_total'], $coin_options['coin_decimals']) . " " .  $options['pointsname'];
}
else
	$data['bets']['account_balance'] = $cs_lang['no_user'];
$errorMsg = cs_getmsg();
$data['head']['message'] = empty($errorMsg) ? $cs_lang['body_details'] : $errorMsg;

 // Liste der Knadidaten
for ($run = 0; $run < $numCons; $run++) {
	
	if (!empty($cs_contestants[$run]['clans_id'])){ 
		$data['contestants'][$run]['name'] = $cs_contestants[$run]['clans_name'];
		$data['contestants'][$run]['country'] = cs_html_img('symbols/countries/' . $cs_contestants[$run]['clans_country'] . '.png',11,16);
		$data['contestants'][$run]['if']['clan'] = true;
		$data['contestants'][$run]['if']['name'] = false;
	}
	else {
		$data['contestants'][$run]['name'] = (!empty($cs_contestants[$run]['bets_draw']) ? $cs_lang['draw'] : $cs_contestants[$run]['bets_name']);
		$data['contestants'][$run]['if']['name'] = true;
		$data['contestants'][$run]['if']['clan'] = false;
	}
	
	$data['contestants'][$run]['if']['neue_zeile'] = ($run % 2) == 1 ?  true : false;
	$data['contestants'][$run]['if']['not_last'] = ($run == ($numCons-1)  || !empty($cs_contestants[$run+1]['bets_draw']) ) ?  false : true;
	$data['contestants'][$run]['if']['draw'] = !empty($cs_contestants[$run+1]['bets_draw']) ? true : false; 

	$data['contestants'][$run]['bets_name'] = $cs_contestants[$run]['bets_name'];
	$data['contestants'][$run]['bets_quote'] = $cs_contestants[$run] ['bets_quote'];
	$data['contestants'][$run]['id'] = $cs_contestants[$run] ['contestants_id'];
	
	if($cs_contestants[$run]['bets_winner'] == 1) {
		$data['value']['winner'] = $data['contestants'][$run]['name'];
		$winner = $cs_contestants[$run] ['contestants_id'];
		if($cs_contestants[$run]['bets_draw'] == 0) { $data['value']['winner'] .= " ".$cs_lang['wins']; }
	}
	
	if(empty($cs_contestants[$run]['placed'])){
		$data['contestants'][$run]['placed'] = 0;
		$data['contestants'][$run]['placed_perc'] = 0;
	}
	else {
		$data['contestants'][$run]['placed'] =  number_format($cs_contestants[$run]['placed'], $coin_options['coin_decimals']);
		$data['contestants'][$run]['placed_perc'] = (int)((100/$myBet->bet_total)*$cs_contestants[$run]['placed']);
	}
	
	// Speicher contestant bezeichnung fuer "Aktuell gesetzt" anzeige
	$conts[$data['contestants'][$run]['name']] = $cs_contestants[$run]['contestants_id'];
}

$data['value']['no_bets'] = $cs_lang['no_bets'];
$data['if']['users_enable'] = false;
$data['if']['already_bet'] = false;
	
// Aktuell gesetzt
for ($run = 0; $run < $numUsers; $run++) {
	
	$data['value']['no_bets'] = '';
	$data['if']['users_enable'] = true;
	$data['users'][$run]['if']['user_win'] = false;
	$data['users'][$run]['if']['user_loose'] = true;
	$data['users'][$run]['name'] = $cs_users[$run]['name'];
	$data['users'][$run]['id'] = $cs_users[$run]['users_id'];
	$data['users'][$run]['amount'] = number_format($cs_users[$run]['bets_amount'], $coin_options['coin_decimals']);
	$data['users'][$run]['contestant'] = array_search($cs_users[$run]['contestants_id'], $conts);
	$data['users'][$run]['date'] = cs_date('unix',$cs_users[$run]['bets_users_time'],0); 
	$data['users'][$run]['pay_amount'] = number_format($cs_users[$run]['bets_pay_amount'], $coin_options['coin_decimals']);
	if ($account['users_id'] == $cs_users[$run]['users_id'])
	{
		$data['if']['already_bet'] = true;
		$data['value']['remove_costs'] = round($cs_users[$run]['bets_amount']/100*$options['remove_quote'], 2) . " " . $options['pointsname'];
	} 
	if( $cs_bets['bets_status'] == cs_bet::STATUS_FINISHED && $cs_users[$run]['contestants_id'] == $winner ) {
		$data['users'][$run]['if']['user_win'] = true;
		$data['users'][$run]['if']['user_loose'] = false;
	}
}

$data['if']['cant_bet'] = false;
if (empty($account['users_id']))
{
	$data['if']['already_bet'] = false;
	$data['if']['cant_bet'] = true;
}
else if ($data['if']['already_bet'] == false)
	$data['contestants_drop'] = $data['contestants'];


echo cs_subtemplate(__FILE__,$data,'bets','view');
$where_com = "comments_mod = 'bets' AND comments_fid = '" . $bets_id . "'";
$count_com = cs_sql_count(__FILE__,'comments',$where_com);
include_once('mods/comments/functions.php');

if(!empty($count_com)) {
  echo cs_html_br(1);
	 echo cs_comments_view($bets_id,'bets','view',$count_com);
}

echo cs_comments_add($bets_id,'bets',$cs_bets['bets_com_close']);
?>
