<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('bets');

$options = cs_sql_option(__FILE__, 'bets');

require_once('mods/categories/functions.php');
require_once('mods/bets/class_bet.php');

$myBet = new cs_bet();
$bets_id = (int) $_REQUEST['bets_id'];
$myBet->loadBet($bets_id);

if(isset($_POST['submit'])) {
	$categories_id = empty($_POST['categories_name']) ? (int) $_POST['categories_id'] : cs_categories_create('bets',$_POST['categories_name']);
	
	$cs_bets['bets_auto_title'] = !empty($_POST['bets_auto_title']) ? 1 : 0;
	$cs_bets['bets_title'] = !empty($cs_bets['bets_auto_title']) ? '' : $_POST['bets_title'];

	$opponents = array();
	foreach ($_POST['contestant'] as $key => $value) {
	 	if(empty($value)) {
			$opponents[$key]['value'] = (int) $_POST['clans_id'][$key];
			$opponents[$key]['type'] = 'clan';
			if (!empty($cs_bets['bets_auto_title']))
			{
				if (!empty($cs_bets['bets_title']))
					$cs_bets['bets_title'] .= ' '.$options['auto_title_separator'].' ';
				$clan = cs_sql_select(__FILE__,'clans','clans_name,clans_id','clans_id = '.$opponents[$key]['value'],0,0,1);
				if (!empty($clan['clans_id']))
					$cs_bets['bets_title'] .= $clan['clans_name'];
			}
		}
		else {
			$opponents[$key]['value'] = $value;
			$opponents[$key]['type'] = 'name';
			if (!empty($cs_bets['bets_auto_title']))
			{
				if (!empty($cs_bets['bets_title']))
					$cs_bets['bets_title'] .= ' '.$options['auto_title_separator'].' ';
				$cs_bets['bets_title'] .= $value;
			}
		}
		$opponents[$key]['quote'] = (float) $_POST['quote'][$key];
		$opponents[$key]['draw'] = 0;
		$opponents[$key]['id'] = (int) $_POST['contestant_id'][$key];
	}
	
	if(!empty($_POST['bets_enable_draw'])){
		$next_index = count($opponents);
		$opponents[$next_index]['value'] = $cs_lang['draw'];
		$opponents[$next_index]['type'] = 'name';
		$opponents[$next_index]['quote'] = (float) $_POST['draw_quote'];
		$opponents[$next_index]['draw'] = 1;
		$opponents[$next_index]['id'] = (int) $_POST['draw_id']; 
	}	

	$cs_bets['bets_starts_at'] = cs_datepost('date_start','unix');
	$cs_bets['bets_closed_at'] = cs_datepost('date_end','unix');
	$cs_bets['categories_id'] = $categories_id;
	$cs_bets['bets_description'] = $_POST['bets_description'];
	$cs_bets['bets_quote_type'] = (int) $_POST['bets_quote_type'];
	$cs_bets['bets_enable_draw'] = !empty($_POST['bets_enable_draw']) ? 1 : 0;
	$cs_bets['bets_com_close'] = !empty($_POST['bets_com_close']) ? 1 : 0;
	
	$myBet->saveBet($bets_id, $opponents, $cs_bets);
	if (empty($myBet->error)) {	
		$msg  = $cs_lang['edit_done'];
		cs_redirect($msg,'bets');
	}
}

if(!isset($_POST['submit'])) {
  $data['var']['message'] = $cs_lang['body_edit'];
} elseif(!empty($myBet->error)) {
  $data['var']['message'] = $cs_lang['error_occured'] . cs_html_br(1) . $myBet->error;
}

if(!empty($myBet->error) OR !isset($_POST['submit'])) {
	
	$cs_bets = $myBet->bet_data;
	$cs_contestants = $myBet->contestant_data;
	$numCons = (is_array($cs_contestants) ? count($cs_contestants) : 0);
	
	$data_categories = cs_sql_select(__FILE__,'categories','categories_name, categories_id','categories_mod = \'bets\'','categories_name',0,0);
	$data_clans = cs_sql_select(__FILE__,'clans','clans_name,clans_id',0,'clans_name',0,0);
  	
	$data['categories'] = cs_dropdownsel($data_categories,$cs_bets['categories_id'],'categories_id');
  
	$data['dropdown']['date_start'] = cs_dateselect('date_start','unix',$cs_bets['bets_starts_at'],1995);
	$data['dropdown']['date_end'] = cs_dateselect('date_end','unix',$cs_bets['bets_closed_at'],1995);
	$data['value']['title'] = $cs_bets['bets_title'];
	$data['value']['report'] = $cs_bets['bets_description'];
	$data['value']['bets_enable_draw_checked'] = !empty($cs_bets['bets_enable_draw']) ? 'checked="checked"' : '';
	$data['value']['bets_auto_title_checked'] = !empty($cs_bets['bets_auto_title']) ? 'checked="checked"' : '';
	$data['value']['option_auto_title_separator'] = cs_secure($options['auto_title_separator']);
	$data['if']['draw_enable'] = !empty($cs_bets['bets_enable_draw']) ? true : false;
	
  $data['value']['quote_type_options'] = '';
  for ($i = 0; $i <= 2; $i++)
  {
  	$data['value']['quote_type_options'] .= cs_html_option($cs_lang['quote_type_'.$i], $i, $i == $cs_bets['bets_quote_type']);
  }
	$data['value']['id'] = $cs_bets['bets_id'];
	$data['abcode']['smileys'] = cs_abcode_smileys('wars_report');
  $data['abcode']['features'] = cs_abcode_features('wars_report');
	
	for ($run = 0; $run < $numCons; $run++) {
		$data['contestants'][$run]['bets_name'] = $cs_contestants[$run]['bets_name'];
		$data['contestants'][$run]['clan_sel'] = cs_dropdown('clans_id[]','clans_name',$data_clans,$cs_contestants[$run]['clans_id'],'clans_id');
		$data['contestants'][$run]['bets_quote'] = $cs_contestants[$run]['bets_quote'];
		$data['contestants'][$run]['id'] = $cs_contestants[$run]['contestants_id'];
		if($cs_contestants[$run]['bets_draw'] == 1) {
			$data['contestants'][$run]['if']['not_draw'] = false;
			$draw_data = $data['contestants'][$run];
		}
		else{
			$data['contestants'][$run]['if']['not_draw'] = true;
		}
	}
	
	if(!empty($draw_data)){
		$data['value']['draw_quote'] = $draw_data['bets_quote'];
		$data['value']['draw_id'] = $draw_data['id'];
	}
	else {
		$data['value']['draw_quote'] = 4;
	}
  	
  $data['bets']['com_close_check'] = '';
  if (!empty($cs_bets['bets_com_close']))
  	$data['bets']['com_close_check'] = 'checked="checked"';
  	
	echo cs_subtemplate(__FILE__,$data,'bets','edit');
}
?>
