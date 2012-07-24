<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();
$data['head']['message'] = cs_getmsg();

// TeamSpeak2 Adresse sowie seperate Funktionen includen
include_once('mods/teamspeak/config.inc.php');

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

if (is_null($ver))
{
	cs_redirect($cs_lang['no_data'], 'teamspeak');
	return;
}
$tss = new tss($ver, $teamspeakcharset); 
if (!$tss->connect($adr, $tcp, $udp, $options['timeout']))
{
	$data['head']['message'] .= '<br />'.$cs_lang['fail_connect'];
}
else
{
	// ServerInfos abrufen
	$sInfo = $tss->serverInfo();
}

$data['link']['teamspeak_manage'] = cs_link($cs_lang['head_manage'],'teamspeak','manage');
$data['server']['server_name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $teamspeakcharset));

$data['users'] = array();

$login = false;
// First try superadmin
if (!empty($suseradmin) OR !empty($suserpw))
{
	if ($tss->loginSuperAdmin($suseradmin,$suserpw))
	{
		$login = true;
	}
}
// Try admin
if (!$login)
{
	if ($tss->login($useradmin,$userpw))
	{
		$login = true;
	}
}

if (!$login)
{
	cs_redirect($cs_lang['fail_login'], 'teamspeak');
	return;
}

$clientList = $tss->clientDbList();

$data['head']['count'] = is_array($clientList) ? count($clientList) : 0;

$data['users'] = array();

$run = 0;
if (empty($clientList))
{ 
	$data['head']['message'] .= '<br />'.$cs_lang['nouser'];
}
else
{
	foreach ($clientList as $client)
	{
		$data['users'][$run]['userid'] = $client['cldbid'];
		$data['users'][$run]['user'] = cs_secure(cs_encode($client['client_login_name'], $teamspeakcharset));
		$data['users'][$run]['sadmin'] = ($client['client_privileges'] == '-1') ? $cs_lang['yes'] : $cs_lang['no'];
		$data['users'][$run]['registered'] = cs_date('unix', $client['client_created'], 1, 1);
		$data['users'][$run]['lastlogin'] = cs_date('unix', $client['client_lastconnected'], 1, 1);
		$run++;
	}		
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','userdblist');
?>

