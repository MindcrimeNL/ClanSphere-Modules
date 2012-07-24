<?php 
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$imgsize = 16;

$disable_link = false;
if (!$disable_link)
	echo '<script src="mods/teamspeak/teamspeak.js" type="text/javascript"></script>';

$show_playerflags = ($options['player_flags'] == 1 ? true : false);
$show_channelflags = ($options['channel_flags'] == 1 ? true : false);
$show_empty = ($options['show_empty'] == 1 ? true : false);

$data = array();
$data['head']['body'] = cs_getmsg();

// TeamSpeak2 Adresse sowie seperate Funktionen includen
include('mods/teamspeak/config.inc.php');

$data['server']['server_clan'] = '???';
$data['server']['server_id'] = '???';
$data['server']['server_name'] = '???';
$data['server']['server_prot'] = '???';
$data['server']['server_channels'] = '???';
$data['server']['server_musers'] = '???';
$data['server']['server_version'] = '???';
$data['server']['server_platform'] = '???';
$data['server']['server_address'] = '???';
$data['link']['ts_connect'] = '???';
$data['server']['name'] = '???';
$data['server']['output'] = '';
$data['server']['uptime'] = '';

/* check if we may see it at all */
if (!$teamspeakaccess)
{
	$data['head']['body'] .= $cs_lang['access_denied'];
	$connect = false;
	
	$data['if']['show_players'] = false;
	$data['if']['show_players_empty'] = true;

	echo cs_subtemplate(__FILE__,$data,'teamspeak','view');
	return;
}

$data['server']['server_address'] = $adr . ':' . $udp;

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

$tss = new tss($ver, $teamspeakcharset);

$connect = true;
// Verbindung zum Server herstellen
if (!$tss->connect($adr, $tcp, $udp, $options['timeout']))
{
	$data['head']['body'] .= $cs_lang['fail_connect'];
	$connect = false;
	
	$data['if']['show_players'] = false;
	$data['if']['show_players_empty'] = true;

	echo cs_subtemplate(__FILE__,$data,'teamspeak','view');
	return;
}

if (!empty($suseradmin) OR !empty($suserpw)) {
	if (!$tss->loginSuperAdmin($suseradmin,$suserpw))
	{
		$data['head']['body'] .= $cs_lang['fail_login']; 
	}
} else {
	if (!$tss->login($useradmin,$userpw))
	{
		$data['head']['body'] .= $cs_lang['fail_login']; 
	}
}

// ServerInfos abrufen
$sInfo = $tss->serverInfo();

// Version abfragen
$version = $tss->serverVersion();

// Serveuptime auslesen
$upTime = $tss->serverUptime();

$data['link']['ts_connect'] = cs_html_link($tss->protocol().'://' . $adr . ':' . $udp, $adr . ':' . $udp, '_blank');

$data['server']['server_clan'] = $sInfo['virtualserver_clan_server'] == '1' ? $cs_lang['yes'] : $cs_lang['no'];
$data['server']['server_id'] = $sInfo['virtualserver_id'];
$data['server']['server_name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $teamspeakcharset));
$data['server']['server_prot'] = $sInfo['virtualserver_flag_password'] == '1' ? $cs_lang['yes'] : $cs_lang['no'];
$data['server']['server_channels'] = $sInfo['virtualserver_channelsonline'];
$data['server']['server_musers'] = ($sInfo['virtualserver_clientsonline'] - $sInfo['virtualserver_queryclientsonline']). '/' . $sInfo['virtualserver_maxclients'];
$data['server']['server_address'] = $adr . ':' . $udp;
$data['server']['server_version'] = cs_secure(str_replace('OK','',cs_encode($version, $teamspeakcharset)));
$data['server']['server_platform'] = cs_secure(cs_encode($sInfo['virtualserver_platform']));

// show connected clients
$list2 = $tss->clientList();
if (!empty($list2)) {
	$data['if']['show_players'] = true;
	$data['if']['show_players_empty'] = false;
	$data['players'] = array();
	$run = 0;
	$now = time();
	foreach ($list2 as $client)
	{ 
		/* skip query clients */
		if ($client['client_type'] == tss::TYPE_QUERY_CLIENT)
			continue;
		$data['players'][$run]['pid'] = $client['clid'];
		$data['players'][$run]['pname'] = cs_secure(cs_encode($client['client_nickname'], $teamspeakcharset));
		$data['players'][$run]['ponline'] = teamspeak_make_timestringuser($now-$client['client_lastconnected']);
		$run++;
	}
	if ($run == 0)
	{
		unset($data['players']);
		$data['if']['show_players'] = false;
		$data['if']['show_players_empty'] = true;
	}
} else {
	$data['if']['show_players'] = false;
	$data['if']['show_players_empty'] = true;
}

// HauptChannel abrufen
$data['server']['uptime'] = sprintf($cs_lang['uptime'], teamspeak_make_timestring($upTime, $cs_lang['days']));
$data['server']['name'] = $data['server']['server_name'];

$list = $tss->channelList(); 

$output = '';
usort($list, 'teamspeak_sorter');
$hcount=0;
$channelcount = 0;
$zcount=0;
$subchannels = array();
$rootchannels = array();
foreach ($list as $channel)
{
	if ($channel['pid'] == '0')
	{
		$rootchannels[] = $channel;
		continue;
	}
	if (!isset($subchannels[$channel['pid']]))
	{
		$subchannels[$channel['pid']] = array();
	}
	/* make a list per parent channel */
	$subchannels[$channel['pid']][] = $channel;
}

foreach ($rootchannels as $channel)
{
	$output .= teamspeak_show_channel($tss, $subchannels, $channel, 0, false,
		array('charset' => $teamspeakcharset, 'dl' => $disable_link,
					'scf' => $show_channelflags, 'spf' => $show_playerflags,
					'ver' => $ver, 'imgsize' => $imgsize,
					'se' => $show_empty));
}

$data['server']['output'] = $output;

echo cs_subtemplate(__FILE__,$data,'teamspeak','view');

// Verbindung zum Server unterbrechen
$tss->disconnect(); 

?> 
