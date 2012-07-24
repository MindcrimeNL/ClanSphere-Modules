<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();
$data['head']['body'] = cs_getmsg();

$imglarge = 14;
$imgsmall = 12;

$disable_link = true;

$show_playerflags = ($options['player_flags'] == 1 ? true : false);
$show_channelflags = ($options['channel_flags'] == 1 ? true : false);
$show_empty_navlist = ($options['show_empty_navlist'] == 1 ? true : false);

// TeamSpeak2 Adresse sowie seperate Funktionen includen
include('mods/teamspeak/config.inc.php');

/* check if we may see it at all */
if (!$teamspeakaccess)
{
	echo $cs_lang['access_denied'];
	return;
}

if (!$disable_link)
	echo '<script src="mods/teamspeak/teamspeak.js" type="text/javascript"></script>';

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

$tss = new tss($ver, $teamspeakcharset); 

$connect = true;
// Verbindung zum Server herstellen
if (empty($adr) || empty($tcp) || !$tss->connect($adr, $tcp, $udp, $options['timeout']))
{
	$data['head']['body'] .= $cs_lang['fail_connect'];
	$connect = false; 
}
else
{
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
}

if ($connect)
{
	// ServerInfos abrufen
	$sInfo = $tss->serverInfo();
	
	// Version abfragen
	$version = $tss->serverVersion();
	
	// Serveuptime auslesen
	$upTime = $tss->serverUptime();
}
else
{
	// some fake settings
	$sInfo = array(
			'virtualserver_clientsonline' => 0,
			'virtualserver_queryclientsonline' => 0,
			'virtualserver_maxclients' => 1,
			'virtualserver_id' => '???',
			'virtualserver_name' => '???',
			'virtualserver_flag_password' => 0,
			'virtualserver_channelsonline' => 0,
			'virtualserver_clan_server' => 0
	);
}

$data['link']['ts_connect'] = cs_html_link($tss->protocol() . '://' . $adr . ':' . $udp, $adr . ':' . $udp, '_blank');
$data['server']['server_musers'] = ($sInfo['virtualserver_clientsonline'] - $sInfo['virtualserver_queryclientsonline']). '/' . $sInfo['virtualserver_maxclients'];

$data['server']['server_id'] = $sInfo['virtualserver_id'];
$data['server']['server_name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $teamspeakcharset));
$data['server']['server_prot'] = $sInfo['virtualserver_flag_password'] == '1' ? $cs_lang['yes'] : $cs_lang['no'];
$data['server']['server_channels'] = $sInfo['virtualserver_channelsonline'];
$data['server']['server_clan'] = $sInfo['virtualserver_clan_server'] == '1' ? $cs_lang['yes'] : $cs_lang['no'];

$list = array();
if ($connect)
	$list = $tss->channelList(); 
//ksort($list,'order');

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
					'ver' => $ver, 'imgsize' => $imglarge,
					'se' => $show_empty_navlist));
}

$data['server']['output'] = $output;
echo cs_subtemplate(__FILE__,$data,'teamspeak','navlist_tree');

if ($connect)
	$tss->disconnect();
?>
