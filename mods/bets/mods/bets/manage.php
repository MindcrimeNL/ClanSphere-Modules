<?php
// bx clanportal 0.3.0.0
// $Id: manage.php 1273 2009-03-24 15:35:47Z hajo $

$cs_lang = cs_translate('bets');

require_once('mods/bets/class_bet.php');
####################################################################################################

// REQUESTS
$bets_status = empty($_REQUEST['status']) ? 0 : (int) $_REQUEST['status'];
$page_start = empty($_REQUEST['start']) ? 0 : (int) $_REQUEST['start'];
$sort = empty($_REQUEST['sort']) ? ($bets_status == 1 ? 1 : 2) : (int) $_REQUEST['sort'];
$category = empty($_REQUEST['where']) ? 0 : (int) $_REQUEST['where'];

// COUNTROW
$where = 'bets_status =' . $bets_status;
$where .= $category != 0 ?  ' AND categories_id = ' . $category : '';
$data['count']['all'] = cs_sql_count(__FILE__,'bets', $where);

// STATUSAUSWAHL
$data['if']['show_open'] = $bets_status == 0 ? false : true; 
$data['if']['show_calc'] = $bets_status == 1 ? false : true;
$data['if']['show_closed'] = $bets_status == 2 ? false : true;

// SORTIERUNG / PAGENUMMER ANZEIGE
$cs_sort[1] = 'bet.bets_closed_at DESC';
$cs_sort[2] = 'bet.bets_closed_at ASC';
$cs_sort[3] = 'bet.bets_title DESC';
$cs_sort[4] = 'bet.bets_title ASC';
$cs_sort[5] = 'cat.categories_id DESC';
$cs_sort[6] = 'cat.categories_id ASC';
$order = $cs_sort[$sort];

$data['pages']['list'] = cs_pages('bets','manage',$data['count']['all'],$page_start,$category.'&amp;status='.$bets_status,$sort);

$data['sort']['date'] = cs_sort('bets','manage',$page_start,$category,1,$sort,'status='.$bets_status);
$data['sort']['title'] = cs_sort('bets','manage',$page_start,$category,3,$sort,'status='.$bets_status);
$data['sort']['category'] = cs_sort('bets','manage',$page_start,$category,5,$sort,'status='.$bets_status);
$data['value']['status'] = $bets_status;

$data['head']['message'] = cs_getmsg();

// KATEGORIEDROPDOWN
$betsmod = "categories_mod = 'bets' AND categories_access <= '" . $account['access_bets'] . "'";
$cat_data = cs_sql_select(__FILE__,'categories','*',$betsmod,'categories_name',0,0);
$data['head']['dropdown'] = cs_dropdown('where','categories_name',$cat_data,$category,'categories_id');

// DATENBANKABFRAGE
$where = 'bets_status =' . $bets_status;
$where .= $category != 0 ?  ' AND bet.categories_id = ' . $category : '';
$select = 'bet.bets_title AS bets_title, bet.bets_closed_at AS bets_closed_at';
$select .= ', cat.categories_name AS categories_name, bet.bets_id AS bets_id';
$select .= ', bet.bets_starts_at AS bets_starts_at, bet.categories_id AS cat_id';
$from = 'bets bet LEFT JOIN {pre}_categories cat ON cat.categories_id = bet.categories_id';

$data['bets'] = cs_sql_select(__FILE__,$from,$select,$where,$order,$page_start,$account['users_limit']);
$count_bets = (is_array($data['bets']) ? count($data['bets']) : 0);

$data['if']['result'] = $bets_status == 1 ? true : false; 
$data['if']['open'] = $bets_status == 0 ? true: false;

// LOOP FUER DIE WETTEN
for ($run = 0; $run < $count_bets; $run++) {
  	
	//Check ob Wette abgelaufen

	if($data['bets'][$run]['bets_closed_at'] < cs_time() && $bets_status == cs_bet::STATUS_OPEN){
		cs_sql_update(__FILE__, 'bets', array('bets_status'), array(cs_bet::STATUS_CLOSED),$data['bets'][$run]['bets_id']);	
		$data['bets'][$run]['if']['nicht_abgelaufen'] = false;
	}
	else {$data['bets'][$run]['if']['nicht_abgelaufen'] = true;}
	
  	$data['bets'][$run]['date'] = cs_date('unix',$data['bets'][$run]['bets_closed_at'],1);
  	$data['bets'][$run]['if']['public'] = $data['bets'][$run]['bets_starts_at'] < cs_time() ? true:false;
}



echo cs_subtemplate(__FILE__,$data,'bets','manage');
?>
