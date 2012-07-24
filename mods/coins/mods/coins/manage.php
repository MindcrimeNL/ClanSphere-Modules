<?php

$cs_lang = cs_translate('coins');

$options = cs_sql_option(__FILE__, 'coins');

$data = array();
$data['head']['getmsg'] = cs_getmsg();

$start = empty($_REQUEST['start']) ? 0 : intval($_REQUEST['start']);
$cs_sort[1] = 'usr.users_nick DESC';
$cs_sort[2] = 'usr.users_nick ASC';
$cs_sort[3] = 'cns.coins_total DESC';
$cs_sort[4] = 'cns.coins_total ASC';

$sort = empty($_REQUEST['sort']) ? 2 : (int) $_REQUEST['sort'];
$order = $cs_sort[$sort];

$where = 0;

$coins_count = cs_sql_count(__FILE__, 'coins', $where);

$data['head']['total'] = $coins_count;
$data['head']['pages'] = cs_pages('coins', 'manage', $coins_count, $start, 0, $sort);

$data['sort']['user'] = cs_sort('coins', 'manage', $start, 0, 1, $sort, 0);
$data['sort']['total'] = cs_sort('coins', 'manage', $start, 0, 3, $sort, 0);

$from = 'coins cns LEFT JOIN {pre}_users usr ON cns.users_id = usr.users_id';
$select = 'cns.*, usr.users_nick, usr.users_active, usr.users_delete';

$cs_coins = cs_sql_select(__FILE__, $from, $select, $where, $order, $start, $account['users_limit']);
$count = (is_array($cs_coins) ? count($cs_coins) : 0);

for ($run = 0; $run < $count; $run++)
{
	$data['coins'][$run] = $cs_coins[$run];
	$data['coins'][$run]['user'] = cs_user($cs_coins[$run]['users_id'], $cs_coins[$run]['users_nick'], $cs_coins[$run]['users_active'], $cs_coins[$run]['users_delete']);
	$data['coins'][$run]['total'] = number_format($cs_coins[$run]['coins_total'], $options['coin_decimals']);
	$color = 'black';
	if ($cs_coins[$run]['coins_total'] < 0.0)
		$color = 'red';
  else if ($cs_coins[$run]['coins_total'] > 0.0)
		$color = 'green';
	$data['coins'][$run]['color'] = $color;
}

echo cs_subtemplate(__FILE__, $data, 'coins', 'manage');
?>
