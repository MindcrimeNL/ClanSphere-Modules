<?php 
$cs_lang = cs_translate('twitter');

if (!defined('MINDCRIME_CLANSPHERE_TOOLS'))
{
	echo $cs_lang['mindcrime_tools_required'];
	return;
}

$data['if']['view_1'] = 1;
$data['if']['view_2'] = 0;

$options = cs_sql_option(__FILE__,'twitter', true);

require_once('mods/twitter/include/functions.php');

if (isset($_POST['request']))
{
	require_once('mods/twitter/include/twitter_oauth.php');
	
	$twitter = new Twitter(cs_encode($options['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($options['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
	$twitter->setTimeout($options['timeout']);
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
			$twitter = new Twitter(cs_encode($options['website_consumer_key'], $cs_main['charset'], 'UTF-8'), cs_encode(cs_crypt(base64_decode($options['website_consumer_secret']), $cs_main['crypt_key']), $cs_main['charset'], 'UTF-8'));
			$twitter->setTimeout($options['timeout']);
			$twitter->setUserAgent($twitterUserAgent);
			$result = $twitter->oAuthAccessToken($data['twitter_oauth_token'], $data['twitter_oauth_verifier']);
		  $save = array();
			$save['website_access_token'] = $result['oauth_token'];;
			$save['website_access_secret'] = base64_encode(cs_crypt($result['oauth_token_secret'], $cs_main['crypt_key']));
			
		  require_once 'mods/clansphere/func_options.php';
		  
		  cs_optionsave('twitter', $save);
			if (!$twitter->accountVerifyCredentials())
			{
				cs_redirect($cs_lang['login_failed'], 'options', 'roots');
			} 
		  cs_redirect($cs_lang['success'], 'options', 'roots');
		}
		catch (Exception $e)
		{
			cs_redirect($cs_lang['error'], 'options', 'roots');
		}
	}
}
if (isset($_GET['denied']))
{
		echo $cs_lang['error'];
		return;
}
if (!empty($_POST['submit']))
{
  $save = array();
	$save['users_enable'] = empty($_POST['users_enable']) ? 0 : 1;
	$save['website_enable'] = empty($_POST['website_enable']) ? 0 : 1;
	$save['website_consumer_key'] = $_POST['website_consumer_key'];
	$save['website_consumer_secret'] = base64_encode(cs_crypt($_POST['website_consumer_secret'], $cs_main['crypt_key']));
	$save['website_access_token'] = $_POST['website_access_token'];
	$save['website_access_secret'] = base64_encode(cs_crypt($_POST['website_access_secret'], $cs_main['crypt_key']));
	$save['timeout'] = intval($_POST['timeout']);
	$save['max_navlist'] = intval($_POST['max_navlist']);
	$save['max_headline'] = intval($_POST['max_headline']);
	
  require_once 'mods/clansphere/func_options.php';
  
  cs_optionsave('twitter', $save);
  
  cs_redirect($cs_lang['success'], 'options', 'roots');
	

} else {
	$data['options'] = $options;
	$data['options']['website_consumer_secret'] = cs_crypt(base64_decode($options['website_consumer_secret']), $cs_main['crypt_key']);
	$data['options']['website_access_secret'] = cs_crypt(base64_decode($options['website_access_secret']), $cs_main['crypt_key']);
	$sel = 'checked';
	$data['options']['users_enable_no'] = (intval($options['users_enable']) == 0 ? $sel : '');
	$data['options']['users_enable_yes'] = (intval($options['users_enable']) == 1 ? $sel : '');
	$sel = 'checked';
	$data['options']['website_enable_no'] = (intval($options['website_enable']) == 0 ? $sel : '');
	$data['options']['website_enable_yes'] = (intval($options['website_enable']) == 1 ? $sel : '');
}

echo cs_subtemplate(__FILE__,$data,'twitter','options');
?> 
