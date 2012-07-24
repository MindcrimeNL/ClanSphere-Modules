<?php

$cs_lang = cs_translate('bets');

$coin_options = cs_sql_option(__FILE__, 'coins');
require_once('mods/bets/class_bet.php');

$data = array();

$bets_status = empty($_REQUEST['status']) ? (empty($_REQUEST['where']) ? 0 : (int)$_REQUEST['where']) : (int) $_REQUEST['status'];
$start = empty($_REQUEST['start']) ? 0 : (int) $_REQUEST['start'];
$sort = empty($_REQUEST['sort']) ? ($bets_status == 1 ? 1 : 2) : (int) $_REQUEST['sort'];

$select = 'busr.*, bcon.bets_winner, b.bets_status, b.bets_title, b.bets_closed_at, c.categories_name';
$from = 'bets_users busr '
			.'LEFT JOIN {pre}_bets b ON b.bets_id = busr.bets_id '
			.'LEFT JOIN {pre}_bets_contestants bcon ON bcon.bets_id = busr.bets_id AND bcon.contestants_id = busr.contestants_id '
			.'LEFT JOIN {pre}_categories c ON c.categories_id = b.categories_id';
$where = 'busr.users_id = '.$account['users_id'].' AND b.bets_status = '.$bets_status;

// SORTIERUNG / PAGENUMMER ANZEIGE
$cs_sort[1] = 'b.bets_closed_at DESC';
$cs_sort[2] = 'b.bets_closed_at ASC';
$cs_sort[3] = 'b.bets_title DESC';
$cs_sort[4] = 'b.bets_title ASC';
$cs_sort[5] = 'c.categories_id DESC';
$cs_sort[6] = 'c.categories_id ASC';
$order = $cs_sort[$sort];

$data['count']['all'] = cs_sql_count(__FILE__,$from, $where);

$data['pages']['list'] = cs_pages('bets','center',$data['count']['all'],$start,$bets_status,$sort);

$data['sort']['date'] = cs_sort('bets','center',$start,$bets_status,1,$sort);
$data['sort']['title'] = cs_sort('bets','center',$start,$bets_status,3,$sort);
$data['sort']['category'] = cs_sort('bets','center',$start,$bets_status,5,$sort);

$cs_bets = cs_sql_select(__FILE__, $from, $select, $where, $order, $start, $account['users_limit']);
$cs_bets_count = (is_array($cs_bets) ? count($cs_bets) : 0);

$data['if']['show_open'] = $bets_status == 0 ? false : true; 
$data['if']['show_calc'] = $bets_status == 1 ? false : true;
$data['if']['show_closed'] = $bets_status == 2 ? false : true;

/* does the user already have a coin record? */
$cs_coins = cs_coins_exists($account['users_id']);
if ($cs_coins === false)
{
	/* no, try to create one */
	$cs_coins = cs_coins_create($account['users_id']);
	if ($cs_coins === false)
		$cs_coins['coins_total'] = 0;
}
$data['user']['points'] = number_format($cs_coins['coins_total'], $coin_options['coin_decimals']);
$data['head']['getmsg'] = cs_getmsg();

$data['bets'] = array();
$data['if']['show_earning'] = false;
for ($run = 0; $run < $cs_bets_count; $run++)
{
	$data['bets'][$run]['date'] = cs_link(cs_date('unix',$cs_bets[$run]['bets_closed_at'],1), 'bets', 'view', 'id='.$cs_bets[$run]['bets_id']);
	$data['bets'][$run]['title'] =  cs_secure($cs_bets[$run]['bets_title']);
	$data['bets'][$run]['category'] = cs_secure($cs_bets[$run]['categories_name']);
	$data['bets'][$run]['amount'] = number_format($cs_bets[$run]['bets_amount'], $coin_options['coin_decimals']);
	$data['bets'][$run]['earned'] = number_format($cs_bets[$run]['bets_pay_amount'], $coin_options['coin_decimals']);
	$data['bets'][$run]['earnedcolor'] = ($cs_bets[$run]['bets_pay_amount'] > 0.0 ? 'green' : ($cs_bets[$run]['bets_pay_amount'] < 0.0 ? 'red' : 'black'));
	$data['bets'][$run]['if']['user_win'] = false;
	$data['bets'][$run]['if']['user_loose'] = false;
	$data['bets'][$run]['if']['show_earnings'] = false;
	if ($cs_bets[$run]['bets_status'] == cs_bet::STATUS_FINISHED)
	{
		$data['if']['show_earning'] = true;
		$data['bets'][$run]['if']['show_earnings'] = true;
		if ($cs_bets[$run]['bets_winner'] == 1)
			$data['bets'][$run]['if']['user_win'] = true;
		else
			$data['bets'][$run]['if']['user_loose'] = true;
	}
}

echo cs_subtemplate(__FILE__, $data, 'bets', 'center');
?>
