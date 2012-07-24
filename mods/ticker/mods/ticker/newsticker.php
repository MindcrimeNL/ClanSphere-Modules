<?php
// Clansphere 2009
// ticker - newsticker.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

global $account;

$cs_lang = cs_translate('ticker');

$ticker_opt = cs_sql_option(__FILE__,'ticker');

$key = 'lang='.$account['users_lang'].'&size='.$ticker_opt['max_news'].'&access='.$account['access_news'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('news', 'ticker', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
	echo $cachedata;
	return;
}

$select	 = 'news_id, news_headline, news_time';
$where	 = 'news_public > 0';
$order	 = 'news_time DESC';

$cs_news = cs_sql_select(__FILE__,'news',$select,$where,$order,0,$ticker_opt['max_news']);
$news_loop = count($cs_news);

$cachedata = '';
if($ticker_opt['max_news'] == 1)
{
	$data['news']['date'] = cs_date('unix',$cs_news['news_time'],1) . ': ';
	$data['news']['headline'] = cs_link($cs_news['news_headline'],'news','view','id=' . $cs_news['news_id']);
	$cachedata .= cs_subtemplate(__FILE__,$data,'ticker','newsticker');
}
else
{
	for ($run = 0; $run < $news_loop; $run++)
	{
		$data['news']['date'] = cs_date('unix',$cs_news[$run]['news_time'],1) . ': ';
		$data['news']['headline'] = cs_link($cs_news[$run]['news_headline'],'news','view','id=' . $cs_news[$run]['news_id']);
		$cachedata .= cs_subtemplate(__FILE__,$data,'ticker','newsticker');
		$end = $run < ($news_loop - 1) ? ' ' . $ticker_opt['separator'] . ' ' : '';
		$cachedata .= $end;
	}

}

if (function_exists('cs_datacache_load'))
	cs_datacache_create('news', 'ticker', $key, $cachedata, 0);
echo $cachedata;
?>
