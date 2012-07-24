<?php 
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$imglarge = 14;

// TeamSpeak2 Adresse sowie seperate Funktionen includen
include('mods/teamspeak/config.inc.php');

/* check if we may see it at all */
if (!$teamspeakaccess)
{
	echo $cs_lang['access_denied'];
	return;
}

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php');

$tss = new tss($ver, $teamspeakcharset);

// Verbindung zum Server herstellen
if (empty($adr) || empty($tcp) || !$tss->connect($adr, $tcp, $udp, $options['timeout']))
{
	echo $cs_lang['offline'];
	return;
}

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
	echo $cs_lang['fail_login'];
	return;
}

// ServerInfos abrufen
$sInfo = $tss->serverInfo();

// Connect IP
$data['teamspeak_info']['serverip'] = cs_html_link($tss->protocol(). '://' . $adr . ':' . $udp, $adr . ':' . $udp, '_blank');

// Act. Player / Max Player
$data['teamspeak_info']['actuser'] = ($sInfo['virtualserver_clientsonline'] - $sInfo['virtualserver_queryclientsonline']);
$data['teamspeak_info']['maxuser'] = $sInfo['virtualserver_maxclients'];

// Playerliste abfragen
$clientList = $tss->clientList(); 
$clientCount = 0;
$clientListCount = is_array($clientList) ? count($clientList) : 0;
for($run = 0; $run < $clientListCount; $run++)
{
	if ($clientList[$run]['client_type'] == tss::TYPE_QUERY_CLIENT)
		continue;
	$clientCount++;
	$data['teamspeak'][$run]['player'] = cs_secure(cs_encode($clientList[$run]['client_nickname'], $teamspeakcharset));
	$data['teamspeak'][$run]['p_img'] = $tss->clientFlagsStatus($clientList[$run]['client_flags_status'], tss::FLAG_PLAYER_MODE_STATUS_IMAGE, $imglarge);
}
if ($clientCount == 0)
{
	$data['teamspeak'] = array();
}


echo cs_subtemplate(__FILE__,$data,'teamspeak','navlist_1');

$tss->disconnect();

?>
