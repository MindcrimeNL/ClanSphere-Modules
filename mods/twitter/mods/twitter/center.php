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

require_once('mods/twitter/include/functions.php');

$cs_twitter = cs_sql_select(__FILE__, 'twitter', '*', 'users_id = '.$account['users_id'], 0, 0, 1, 0);
$twitter_id = 0;
if (isset($cs_twitter['users_id']))
	$twitter_id = $cs_twitter['twitter_id']; 

$data = array();

if (isset($_POST['request']))
{
	require_once('mods/twitter/include/twitter_oauth.php');
	
	$twitter = new Twitter(cs_encode($cs_option['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($cs_option['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
	$twitter->setTimeout($cs_option['timeout']);
	$twitter->setUserAgent('ClanSphere Twitter Module');
	$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'].'&request=token';
	try
	{
		$response = $twitter->oAuthRequestToken($url);
		$twitter->oAuthAuthorize($response['oauth_token']);
	}
	catch (Exception $e)
	{
		echo $cs_lang['error'];
		return;
	}
	return;
}
if (isset($_GET['request']))
{
	require_once('mods/twitter/include/twitter_oauth.php');

	if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']))
	{
		$data['twitter_oauth_token'] = $_GET['oauth_token'];
		$data['twitter_oauth_verifier'] = $_GET['oauth_verifier'];
		/* now try to get the access tokens we need for posting tweets and stuff */
		try
		{
			$twitter = new Twitter(cs_encode($cs_option['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($cs_option['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
			$twitter->setTimeout($cs_option['timeout']);
			$twitter->setUserAgent($twitterUserAgent);
			$result = $twitter->oAuthAccessToken($data['twitter_oauth_token'], $data['twitter_oauth_verifier']);
			$data = array();
			$data['twitter_access_token'] = $result['oauth_token'];
			$data['twitter_access_secret'] = base64_encode(cs_crypt($result['oauth_token_secret'], $cs_main['crypt_key']));
			if ($twitter_id > 0)
				cs_sql_update(__FILE__, 'twitter', array_keys($data), array_values($data), $twitter_id);
			else
			{
				$data['users_id'] = $account['users_id'];
				cs_sql_insert(__FILE__, 'twitter', array_keys($data), array_values($data));
			}
			if (!$twitter->accountVerifyCredentials())
			{
				echo $cs_lang['login_failed'];
				return;
			}
		}
		catch (Exception $e)
		{
//			echo $e->getMessage().'<br />';
//			echo $e->getTraceAsString().'<br />';
			echo $cs_lang['error'];
			return;
		}
		cs_redirect($message, 'users', 'settings');
	}
}
if (isset($_GET['denied']))
{
		echo $cs_lang['error'];
		return;
}
if (isset($_POST['submit']))
{
	if (!empty($twitter_id))
	{
		if (!empty($_POST['twitter_delete']))
		{
			cs_sql_delete(__FILE__, 'twitter', $twitter_id);
			cs_redirect($cs_lang['account_deleted'], 'users', 'settings');
		}
	}
	cs_redirect($cs_lang['account_updated'], 'users', 'settings');
}
else
{
	if (!empty($twitter_id))
	{
		$data['twitter']['twitter_access_token'] = cs_encode($cs_twitter['twitter_access_token'], 'UTF-8', $cs_main['charset']);
		$data['twitter']['users_id'] = $account['users_id'];
	}
	else
	{
		$data['twitter']['twitter_access_token'] = '';
		$data['twitter']['users_id'] = $account['users_id'];
	}
	echo cs_subtemplate(__FILE__,$data,'twitter','center');
}
