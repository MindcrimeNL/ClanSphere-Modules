<?php
// Clansphere 2009
// ticker - userticker.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

global $account;

$cs_lang = cs_translate('ticker');

$ticker_opt = cs_sql_option(__FILE__,'ticker');
$cs_option = cs_sql_option(__FILE__,'wars');

$data = array();

$key = 'lang='.$account['users_lang'].'&type=upcoming&size='.$ticker_opt['max_wars'].'&access='.$account['access_wars'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('wars', 'ticker', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
        echo $cachedata;
        return;
}


$select = 'war.wars_date AS wars_date, war.games_id AS games_id, cln.clans_short AS clans_short, war.wars_score1 AS wars_score1, '
        . 'war.wars_score2 AS wars_score2, war.wars_id AS wars_id, ga.games_name AS games_name';
$from = 'wars war INNER JOIN {pre}_clans cln ON war.clans_id = cln.clans_id LEFT JOIN {pre}_games ga ON war.games_id = ga.games_id';
$order = 'war.wars_date ASC';
$where = "war.wars_status = 'upcoming' AND war.wars_date > ".(cs_time()-7200);

$cs_wars = cs_sql_select(__FILE__,$from,$select,$where,$order,0,$ticker_opt['max_wars']);

if (!empty($cs_wars))
{
  if ($ticker_opt['max_wars'] == 1)
  	$cs_wars = array(0 => $cs_wars);
  
	$all = count($cs_wars);

	$cachedata = '';
	for ($i = 0; $i < $all; $i++)
	{
		$data['war']['icon'] = file_exists('uploads/games/' . $cs_wars[$i]['games_id'] . '.gif') ?
			cs_html_img('uploads/games/' . $cs_wars[$i]['games_id'] . '.gif', 16, 16, 'class="ticker_image"', htmlentities($cs_wars[$i]['games_name']), htmlentities($cs_wars[$i]['games_name'])) : '';  
		$secure_short = cs_secure($cs_wars[$i]['clans_short']);
    $data['war']['opponent'] = cs_link($secure_short,'wars','view','id=' . $cs_wars[$i]['wars_id'],'ticker');
    $data['war']['date'] = cs_date('unix', $cs_wars[$i]['wars_date'], 1, 1, 'Y.m.d@h:i');
		$cachedata .= cs_subtemplate(__FILE__,$data,'ticker','warsnextticker');
		$cachedata .= $i < ($all - 1) ? ' ' . $ticker_opt['separator'] . ' ' : '';
	}
}
else
  $cachedata = $cs_lang['no_data'];

if (function_exists('cs_datacache_load'))
	cs_datacache_create('wars', 'ticker', $key, $cachedata, 0);
echo $cachedata;
?>
