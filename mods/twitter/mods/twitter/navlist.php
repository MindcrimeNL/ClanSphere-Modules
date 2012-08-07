<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('twitter');

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

if (empty($cs_option['website_enable']))
{
	echo $cs_lang['website_disabled'];
	return;
}

$key = 'lang='.$account['users_lang'].'&size='.$cs_option['max_navlist'].'&length='.$cs_option['max_headline'].'&access='.$account['access_twitter'];
$cachedata = cs_datacache_load('twitter', 'navlist', $key, false);
if ($cachedata !== false)
{
	echo '<!-- cached START -->'.$cachedata.'<!-- cached END -->';
	return;
}

require_once('mods/twitter/include/functions.php');
require_once('mods/twitter/include/twitter_oauth.php');

try
{
	$twitter = new Twitter(cs_encode($cs_option['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($cs_option['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
	$twitter->setTimeout($cs_option['timeout']);
	$twitter->setUserAgent($twitterUserAgent);
	$twitter->setOAuthToken($cs_option['website_access_token']);
	$twitter->setOAuthTokenSecret(cs_crypt(base64_decode($cs_option['website_access_secret']), $cs_main['crypt_key']));

	if (!$twitter->accountVerifyCredentials())
	{
		echo $cs_lang['login_failed'];
		return;
	}

	$data = array();
	$twitters = $twitter->statusesUserTimeline(null, null, null, null, $cs_option['max_navlist'], 1);
	$run = 0;
	foreach ($twitters as $tweet)
	{
		$data['tweets'][$run]['date'] = cs_date('unix', strtotime($tweet['created_at']), 1);
		$data['tweets'][$run]['name'] = cs_html_link('http://twitter.com/'.$tweet['user']['screen_name'], cs_secure($tweet['user']['name']));
		$data['tweets'][$run]['image'] = cs_html_img($tweet['user']['profile_image_url'], 16, 16);
		$data['tweets'][$run]['message'] = cs_secure(cs_textcut(cs_detweet($tweet['text']), $cs_option['max_headline']), 1);
		$run++;
		if ($run == $cs_option['max_navlist'])
			break;
	}
	$cachedata = cs_subtemplate(__FILE__,$data,'twitter','navlist');
	cs_datacache_create('twitter', 'navlist', $key, $cachedata, 60);
}
catch (Exception $e)
{
	$cachedata = $cs_lang['error'];
}
echo $cachedata;
