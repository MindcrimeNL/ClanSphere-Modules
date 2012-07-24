<?php
// Clansphere 2009
// ticker - boardticker.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

global $account;

$ticker_opt = cs_sql_option(__FILE__,'ticker');

$from  = 'threads thr INNER JOIN {pre}_board frm ON frm.board_id = thr.board_id';
$select  = 'thr.threads_headline AS threads_headline, thr.threads_last_time AS threads_last_time, thr.threads_id AS threads_id';
$where = 'frm.board_access <= ' . (int) $account['access_board'] . ' AND thr.threads_last_user != ' . (int) $account['users_id'] . ' AND board_pwd = \'\'';
$order = 'thr.threads_last_time DESC';
$cs_threads = cs_sql_select(__FILE__,$from,$select,$where,$order,0,$ticker_opt['max_threads']);
$threads_loop = count($cs_threads);

if($ticker_opt['max_threads'] == 1)
{
	echo cs_date('unix',$cs_threads['threads_last_time'],1) . ': ';
	$headline = strlen($cs_threads['threads_headline']) <= 15 ? $cs_threads['threads_headline'] : substr($cs_threads['threads_headline'],0,15) . '...';
	$headline = cs_secure($headline);
	$more	  = 'where=' . $cs_threads['threads_id'];
	echo cs_link($headline,'board','thread',$more,0,cs_secure($cs_threads['threads_headline']));
}
else
{
	for ($run = 0; $run < $threads_loop; $run++)
	{
		echo cs_date('unix',$cs_threads[$run]['threads_last_time'],1) . ': ';
		$headline = strlen($cs_threads[$run]['threads_headline']) <= 15 ? $cs_threads[$run]['threads_headline'] : substr($cs_threads[$run]['threads_headline'],0,15) . '...';
		$headline = cs_secure($headline);
		$more	  = 'where=' . $cs_threads[$run]['threads_id'];
		echo cs_link($headline,'board','thread',$more,0,cs_secure($cs_threads[$run]['threads_headline']));
		$end = $run < ($threads_loop - 1) ? ' ' . $ticker_opt['separator'] . ' ' : '';
		echo $end;
	}
}

?>