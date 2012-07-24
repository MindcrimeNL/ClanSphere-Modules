<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('twitter');
$cs_post = cs_post('start');
$cs_get = cs_get('start');

if (!defined('MINDCRIME_CLANSPHERE_TOOLS'))
{
	echo $cs_lang['mindcrime_tools_required'];
	return;
}

if (!extension_loaded('curl'))
{
	echo $cs_lang['curl_required'];
	return;
}

$cs_option = cs_sql_option(__FILE__, 'twitter');

if (empty($cs_option['users_enable']))
{
	echo $cs_lang['users_disabled'];
	return;
}

$start = empty($cs_get['start']) ? 0 : $cs_get['start'];
if (!empty($cs_post['start']))  $start = $cs_post['start'];

$key = 'lang='.$account['users_lang'].'&id='.$account['users_id'].'&start='.$start.'&size='.$account['users_limit'].'&access='.$account['access_twitter'];
$cachedata = cs_datacache_load('twitter', 'viewown', $key, false);
if ($cachedata !== false)
{
	echo '<!-- cached START -->'.$cachedata.'<!-- cached END -->';
	return;
}

$cs_twitter = cs_sql_select(__FILE__, 'twitter', '*', 'users_id = '.$account['users_id'], 0, 0, 1, 0);
if (!isset($cs_twitter['users_id']))
{
	echo sprintf($cs_lang['no_user_data'], cs_url('twitter', 'center'));
	return;
}

require_once('mods/twitter/include/functions.php');
require_once('mods/twitter/include/twitter_oauth.php');

try
{
	$twitter = new Twitter(cs_encode($cs_option['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($cs_option['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
	$twitter->setTimeout($cs_option['timeout']);
	$twitter->setUserAgent($twitterUserAgent);
	$twitter->setOAuthToken($cs_twitter['twitter_access_token']);
	$twitter->setOAuthTokenSecret(cs_crypt(base64_decode($cs_twitter['twitter_access_secret']), $cs_main['crypt_key']));

	if (!$twitter->accountVerifyCredentials())
	{
		echo $cs_lang['login_failed'];
		return;
	}

	$data = array();
	$twitters = $twitter->statusesUserTimeline(null, null, null, null, $account['users_limit'], floor($start / $account['users_limit']) + 1);
	$run = 0;
	foreach ($twitters as $tweet)
	{
		$data['tweets'][$run]['date'] = cs_date('unix', strtotime($tweet['created_at']), 1);
		$data['tweets'][$run]['name'] = cs_html_link('http://twitter.com/'.$tweet['user']['screen_name'], cs_secure($tweet['user']['name']));
		$data['tweets'][$run]['image'] = cs_html_img($tweet['user']['profile_image_url'], 48, 48);
		$data['tweets'][$run]['message'] = cs_secure(cs_detweet($tweet['text']), 1);
		$run++;
		if ($run == $account['users_limit'])
			break;
	}
	$data['tweet']['next'] = (floor($start / $account['users_limit']) + 1)*$account['users_limit'];
	if (floor($start / $account['users_limit']) >= 1)
	{
		$data['tweet']['previous'] = (floor($start / $account['users_limit']) - 1)*$account['users_limit'];
		$data['if']['prev'] = true;
	}
	else
	{
		$data['tweet']['previous'] = 0;
		$data['if']['prev'] = false;
	}
	
	$cachedata = cs_subtemplate(__FILE__,$data,'twitter','viewown');
	cs_datacache_create('twitter', 'viewown', $key, $cachedata, 60);
}
catch (Exception $e)
{
	$cachedata = $cs_lang['error'];
}
echo $cachedata;
