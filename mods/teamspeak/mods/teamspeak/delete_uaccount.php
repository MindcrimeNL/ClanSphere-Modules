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

/* protect 'admin' and 'superadmin' accounts */
if ($account['users_nick'] == $useradmin || $account['users_nick'] == $suseradmin)
{
	cs_redirect($cs_lang['fail_protected'],'teamspeak','center');
	return;
}

$tss = new tss($ver, $teamspeakcharset); 

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
		cs_redirect($cs_lang['fail_login'],'teamspeak','center');
		return;
	}
}

$cldbid = intval($_REQUEST['cldbid']);
// Userabfrage
if ($slogin == true || $ver == tss::VERSION_TS3)
	$clientList = $tss->clientDbFind($account['users_nick'], $account['users_id']);
else
	$clientList = $tss->clientDbList();

if (isset($_POST['submit']))
{
	$cs_teamspeak['cldbid'] = intval($_POST['cldbid']);
}

/* check if the user may delete this account */
$aok = false;
foreach ($clientList as $client)
{
	/* only delete if the found client matches the cldbid and is not an admin */
  if ((($ver == tss::VERSION_TS2 && cs_encode($client['client_login_name'], $cs_main['charset'], $teamspeakcharset) == $account['users_nick'])
  		|| ($ver == tss::VERSION_TS3 && isset($client['ident']) && $client['ident'] == 'cs_id'
						&& isset($client['value']) && $client['value'] == $account['users_id']))
			AND intval($client['cldbid']) == $cldbid AND $client['client_privileges'] != '-1') 
	{
		$aok = true;
	}
}

if (!$aok)
{
	cs_redirect($cs_lang['noaccess'],'teamspeak','center');
	return;
}

$data['link']['head_center'] = cs_link(cs_secure($cs_lang['head_center']),'teamspeak','center');

if (!isset($_POST['submit']))
{
	$data['head']['body'] = cs_getmsg();
	$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
	$data['teamspeak']['cldbid'] = $cldbid;
	echo cs_subtemplate(__FILE__,$data,'teamspeak','delete_uaccount');
}
else
{
	if ($tss->clientDbDelete($cs_teamspeak['cldbid']))
	{
		cs_redirect($cs_lang['deleted'],'teamspeak','center');
	}
	else
	{
		$data['head']['body'] = cs_getmsg().'<br>'.$cs_lang['account_deletion_failed'];
		$data['teamspeak']['user_nick'] = cs_secure($account['users_nick']);
		$data['teamspeak']['cldbid'] = $cldbid;
		echo cs_subtemplate(__FILE__,$data,'teamspeak','delete_uaccount');
	}
}

?>
