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

if (empty($cs_option['users_enable']))
{
	echo $cs_lang['users_disabled'];
	return;
}

$cs_twitter = cs_sql_select(__FILE__, 'twitter', '*', 'users_id = '.$account['users_id'], 0, 0, 1, 0);
if (!isset($cs_twitter['users_id']))
{
	echo sprintf($cs_lang['no_user_data'], cs_url('twitter', 'center'));
	return;
}

$data['tweet']['message'] = '';
$data['tweet']['currently'] = '';
$error = '';
if (isset($_POST['message']))
{
	$data['tweet']['message'] = cs_secure(trim($_POST['message']));
	$len = strlen($data['tweet']['message']);
	if ($len <= 0 || $len > 140)
	{
		$error = $cs_lang['invalid_message'];
		$data['tweet']['currently'] = sprintf($cs_lang['message_size_current'], $len);
	}
}

if (!empty($_POST['submit']) && isset($_POST['message']) && empty($error))
{
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
		$twitter->statusesUpdate(cs_tweet($data['tweet']['message']));
		cs_redirect($cs_lang['message_sent'], 'twitter', 'viewown');
	}
	catch (Exception $e)
	{
		echo $cs_lang['error'];
	}
}
else
{
	$data['tweet']['error'] = $error;
	echo cs_subtemplate(__FILE__,$data,'twitter','create');
}

?>
