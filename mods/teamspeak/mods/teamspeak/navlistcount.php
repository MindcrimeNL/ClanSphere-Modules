<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

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
$sInfo = $tss->serverInfo();

echo cs_link(($sInfo['virtualserver_clientsonline'] - $sInfo['virtualserver_queryclientsonline']) . ' / ' . $sInfo['virtualserver_maxclients'],'teamspeak','view');

$tss->disconnect();
?>
