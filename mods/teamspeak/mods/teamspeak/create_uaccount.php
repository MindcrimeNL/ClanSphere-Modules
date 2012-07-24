<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();

include('mods/teamspeak/config.inc.php');

/* check if we may see it at all */
if (!$teamspeakaccess)
{
	cs_redirect($cs_lang['access_denied'],'teamspeak','center');
	return;
}

/* check if we may register at all */
if (!$teamspeakregister) 
{
	cs_redirect($cs_lang['fail_permission_register'],'teamspeak','center');
	return;
}

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

$tss = new tss($ver, $teamspeakcharset);
$data['if']['ts3'] = false; 
if ($ver == tss::VERSION_TS3)
	$data['if']['ts3'] = true; 

// Verbindung zum Server herstellen
if (!$tss->connect($adr, $tcp, $udp, $options['timeout'])) 
{
	cs_redirect($cs_lang['fail_connect'],'teamspeak','center');
	return;
}

$slogin = false;
if (!empty($suseradmin) && !empty($suserpw))
{
	if ($tss->loginSuperAdmin($suseradmin,$suserpw))
	{
		$slogin = true;
	}
}
if (!$slogin)
{
	if (!$tss->login($useradmin,$userpw))
	{
		$tss->disconnect();
		cs_redirect($cs_lang['fail_login'],'teamspeak','center');
		return;
	}
}

// Userabfrage
if ($slogin == true || $ver == tss::VERSION_TS3)
	$clientList = $tss->clientDbFind($account['users_nick'], $account['users_id']);
else
	$clientList = $tss->clientDbList();

if (isset($_POST['submit']))
{
	foreach ($clientList as $client)
	{
		if (($ver == tss::VERSION_TS2 && cs_encode($client['client_login_name'], $cs_main['charset'], $teamspeakcharset) == $account['users_nick'])
				|| ($ver == tss::VERSION_TS3 && isset($client['ident']) && $client['ident'] == 'cs_id'
						&& isset($client['value']) && $client['value'] == $account['users_id']))
		{
			$data['head']['body'] = cs_getmsg().$cs_lang['account_exists'];
			$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
			if ($account['users_nick'] == $useradmin || $account['users_nick'] == $suseradmin || $client['client_privileges'] == '-1')
				$data['teamspeak']['user_delete'] = $cs_lang['fail_protected'];
			else
				$data['teamspeak']['user_delete'] = cs_link($cs_lang['udelete'],'teamspeak','delete_uaccount','cldbid=' . $client['cldbid']);
			$data['teamspeak']['serveradmin'] = $client['client_privileges'] == '-1' ? $cs_lang['yes'] : $cs_lang['no'];
			$data['teamspeak']['created'] = $client['client_created'];
			$data['teamspeak']['last_login'] = $client['client_lastconnected'];

			/* there should be only one account with the same name, we can stop */
			echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount_exists');
			return;
		}
	}
  $error = 0;
	$errormsg = '';

	$cs_teamspeak['login_nick'] = $account['users_nick'];
	if ($ver != tss::VERSION_TS3)
	{
		$cs_teamspeak['login_pw'] = $_POST['login_pw'];
		/* we need to convert this password to the teamspeak server character set */
		$create_pw = cs_encode($cs_teamspeak['login_pw'], $cs_main['charset'], $teamspeakcharset);
	
		if (empty($cs_teamspeak['login_pw']))
		{
	    $error++;
	    $errormsg .= $cs_lang['no_pw'] . cs_html_br(1);
	  }
	  else if (empty($create_pw))
	  {
	  	/* conversion might result in empty string */
			$error++;
			$errmsg .= $cs_lang['fail_invalid_password'] . cs_html_br(1);
	  }
	}
}
else
{
	$cs_teamspeak['login_nick'] = $account['users_nick'];
	if ($ver != tss::VERSION_TS3)
		$cs_teamspeak['login_pw'] = '';
}

/* we should not create an account or token if we can't even seem to use the exact same nick on the server */
$create_nick = cs_encode($cs_teamspeak['login_nick'], $cs_main['charset'], $teamspeakcharset);
if (empty($create_nick))
{
 	/* conversion might result in empty string */
	$error++;
	$errmsg .= $cs_lang['fail_invalid_nick'] . cs_html_br(1);
}

$data['head']['body'] = cs_getmsg();
if(!isset($_POST['submit']))
{
  $data['head']['body'] .= $cs_lang['body_create'];
}
elseif(!empty($error)) {
  $data['head']['body'] .= $errormsg;
}

$data['link']['head_center'] = cs_link(cs_secure($cs_lang['head_center']),'teamspeak','center');
if (!empty($error) OR !isset($_POST['submit']))
{
	foreach ($clientList as $client)
	{
		if (($ver == tss::VERSION_TS2 && cs_encode($client['client_login_name'], $cs_main['charset'], $teamspeakcharset) == $account['users_nick'])
				|| ($ver == tss::VERSION_TS3 && isset($client['ident']) && $client['ident'] == 'cs_id'
						&& isset($client['value']) && $client['value'] == $account['users_id']))
		{
			$data['head']['body'] = cs_getmsg().$cs_lang['account_exists'];
			$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
			if ($account['users_nick'] == $useradmin || $account['users_nick'] == $suseradmin || $client['client_privileges'] == '-1')
				$data['teamspeak']['user_delete'] = $cs_lang['fail_protected'];
			else
				$data['teamspeak']['user_delete'] = cs_link($cs_lang['udelete'],'teamspeak','delete_uaccount','teamspeakid='.$teamspeakid.'&cldbid=' . $client['cldbid']);
			$data['teamspeak']['serveradmin'] = $client['client_privileges'] == '-1' ? $cs_lang['yes'] : $cs_lang['no'];
			$data['teamspeak']['created'] = cs_date('unix', $client['client_created'], 1, 1);
			$data['teamspeak']['last_login'] = cs_date('unix', $client['client_lastconnected'], 1, 1);

			/* there should be only one account with the same name, we can stop */
			echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount_exists');
			return;
		} 
	} 
	$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
	if ($ver != tss::VERSION_TS3)
		$data['teamspeak']['login_pw'] = $cs_teamspeak['login_pw'];
	echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount');
}
else
{
	if ($ver == tss::VERSION_TS3)
	{
		$token = $tss->clientDbCreate($cs_teamspeak['login_nick'], '', false, $account['users_id']);
		if ($token !== false)
		{
			$data['teamspeak']['token'] = cs_secure(cs_encode($token['token'], $teamspeakcharset));
			$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
			$data['head']['body'] = cs_getmsg().'<br>'.$cs_lang['create_token_done'];
			echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount_token');
		}
		else
		{
			$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
			$data['teamspeak']['login_pw'] = $cs_teamspeak['login_pw'];
			$data['head']['body'] = cs_getmsg().'<br>'.$cs_lang['account_creation_failed'];
			echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount');
		}
	}
	else
	{
		if ($tss->clientDbCreate($cs_teamspeak['login_nick'], $cs_teamspeak['login_pw'], false, 0))
		{
		  cs_redirect($cs_lang['create_done'],'teamspeak', 'center');
		}
		else
		{
			$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
			$data['teamspeak']['login_pw'] = $cs_teamspeak['login_pw'];
			$data['head']['body'] = cs_getmsg().'<br>'.$cs_lang['account_creation_failed'];
			echo cs_subtemplate(__FILE__,$data,'teamspeak','create_uaccount');
		}
	}
}

?>
