<?php
// Clansphere 2009
// ticker - onlineticker.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker');

$ticker_opt = cs_sql_option(__FILE__,'ticker');

$five_min = cs_time() - 300;
$select	= 'users_id, users_nick';
$upcome = 'users_laston > ' . $five_min . ' AND users_invisible = 0 AND users_delete = 0';
$order	= 'users_laston DESC';
$cs_users = cs_sql_select(__FILE__,'users',$select,$upcome,$order,0,$ticker_opt['max_online']);
$users_loop = count($cs_users);

if($ticker_opt['max_online'] == 1)
{
	echo cs_link($cs_users['users_nick'],'users','view','id=' . $cs_users['users_id']);
}
else
{
	for ($run = 0; $run < $users_loop; $run++)
	{
		echo cs_link($cs_users[$run]['users_nick'],'users','view','id=' . $cs_users[$run]['users_id']);
		$end = $run < ($users_loop - 1) ? ' ' . $ticker_opt['separator'] . ' ' : '';
		echo $end;
	}
}

?>
