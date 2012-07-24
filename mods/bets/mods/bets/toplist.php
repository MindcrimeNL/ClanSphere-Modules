<?php
// $Id$

$cs_lang = cs_translate('bets');
$cs_get = cs_get('id,where');
$cs_post = cs_post('id');

$coin_options = cs_sql_option(__FILE__, 'coins');

$data = array();

$sort = empty($_REQUEST['sort']) ? 1 : (int) $_REQUEST['sort'];
$start = empty($_REQUEST['start']) ? 0 : (int) $_REQUEST['start'];
if ($start < 0) $start = 0;

$cs_sort[1] = 'bets_pay_amount DESC';
$cs_sort[2] = 'bets_pay_amount ASC';
$cs_sort[3] = 'bets_count DESC';
$cs_sort[4] = 'bets_count ASC';
$cs_sort[5] = 'bets_amount DESC';
$cs_sort[6] = 'bets_amount ASC';

$order = $cs_sort[$sort];

$limit = $account['users_limit'];

$from = 'bets_users bu LEFT JOIN {pre}_users u ON u.users_id = bu.users_id';
$where = 'bu.bets_pay_time > 0 AND u.users_active = 1 AND u.users_delete = 0 GROUP BY bu.users_id';
$bets = cs_sql_select(__FILE__, $from, 'SUM(bets_amount) AS bets_amount, SUM(bets_pay_amount) AS bets_pay_amount, COUNT(bets_users_id) AS bets_count, bu.users_id, u.users_nick', $where, $order, 0, 0, 0);

$data['count']['all'] = count($bets);

$data['pages']['list'] = cs_pages('bets','toplist', $data['count']['all'],$start,0,$sort);

$data['sort']['paid'] = cs_sort('bets','toplist',$start,0,1,$sort,0);
$data['sort']['count'] = cs_sort('bets','toplist',$start,0,3,$sort,0);
$data['sort']['amount'] = cs_sort('bets','toplist',$start,0,5,$sort,0);

$run = 0;
for ($i = $start; $i < $data['count']['all'] && $i < $start + $limit; $i++)
{
	$data['toplist'][$run]['rank'] = $i+1;
	$data['toplist'][$run]['user'] = cs_user($bets[$i]['users_id'], $bets[$i]['users_nick']);
	$data['toplist'][$run]['paid'] = number_format($bets[$i]['bets_pay_amount'], $coin_options['coin_decimals']);
	$data['toplist'][$run]['count'] = $bets[$i]['bets_count'];
	$data['toplist'][$run]['amount'] = number_format($bets[$i]['bets_amount'], $coin_options['coin_decimals']);
	$run++;
}

echo cs_subtemplate(__FILE__,$data,'bets','toplist');

?>
