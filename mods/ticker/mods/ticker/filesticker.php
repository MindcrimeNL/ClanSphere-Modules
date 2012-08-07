<?php
// Clansphere 2009
// ticker - filesticker.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

global $account;

$cs_lang = cs_translate('ticker');

$ticker_opt = cs_sql_option(__FILE__,'ticker');

$select		= 'files_id, files_name, files_version';
$where		= $account['access_id'] >= 'files_access';
$order		= 'files_time DESC';
$cs_file	= cs_sql_select(__FILE__,'files',$select,$where,$order,0,$ticker_opt['max_dls']);
$file_loop	= count($cs_file);

if($ticker_opt['max_dls'] == 1)
{
	echo cs_link(substr($cs_file['files_name'],0,20),'files','view','where=' . $cs_file['files_id'],0,$cs_file['files_name']);
}
else
{
	for ($run = 0; $run < $file_loop; $run++)
	{
		echo cs_link(substr($cs_file[$run]['files_name'],0,20),'files','view','where=' . $cs_file[$run]['files_id'],0,$cs_file[$run]['files_name']);
		$end = $run < ($file_loop - 1) ? ' ' . $ticker_opt['separator'] . ' ' : '';
		echo $end;
	}
}

?>
