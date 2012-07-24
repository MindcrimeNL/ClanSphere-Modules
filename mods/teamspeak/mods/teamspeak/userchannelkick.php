<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$cs_file = 'mods/teamspeak/userchannelkick.php';
$data = array();

// TeamSpeak2 Adresse sowie seperate Funktionen includen
include_once('mods/teamspeak/config.inc.php');

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

$data['head']['message'] = cs_getmsg();

if (is_null($ver))
{
  cs_redirect($cs_lang['no_data'], 'teamspeak');
  return;
}
$tss = new tss($ver, $teamspeakcharset); 
if (!$tss->connect($adr, $tcp, $udp, $options['timeout']))
{
	$data['head']['message'] .= '<br />'.$cs_lang['fail_connect'];
	$data['users'] = array();
	echo cs_subtemplate(__FILE__,$data,'teamspeak','userchannelkick');
	return;
}

// ServerInfos abrufen
$sInfo = $tss->serverInfo();

$data['link']['teamspeak_manage'] = cs_link($cs_lang['head_manage'],'teamspeak','manage');
$data['server']['server_name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $teamspeakcharset));

$data['users'] = array();

// User kick
if (isset($_POST['submit']))
{
	// Als Admin einloggen
	if ($tss->login($useradmin,$userpw))
	{
		$clid = intval($_POST['clid']);
		if ($tss->clientKick($clid, tss::REASON_KICK_CHANNEL))
		{
			$data['head']['message'] .= '<br />'.$cs_lang['kickok'].'<br />'.cs_link($cs_lang['continue'],'teamspeak','userchannelkick');
		}
		else
		{
			$data['head']['message'] .= '<br />'.$cs_lang['fail_kick'].'<br />'.cs_link($cs_lang['continue'],'teamspeak','userchannelkick');
		}
	}
	else
	{
			$data['head']['message'] .= '<br />'.$cs_lang['fail_login'].'<br />'.cs_link($cs_lang['continue'],'teamspeak','userchannelkick');
	}

}
else
{	
	// Aktuelle Userliste holen
	$list2 = $tss->clientList(); 
	if (empty($list2))
	{ 
		$data['head']['message'] .= '<br />'.$cs_lang['nouser'];
	} else {
	
		// Aktuelle Userliste anzeigen
		$run = 0;
		$data['link']['form_action'] = cs_url('teamspeak', 'userchannelkick');
		foreach ($list2 as $client)
		{ 
			if ($client['client_type'] == tss::TYPE_QUERY_CLIENT)
				continue;
			$data['users'][$run]['user'] = cs_secure(cs_encode($client['client_nickname'], $teamspeakcharset));
			$channel = $tss->channelInfo($client['cid']);
			$data['users'][$run]['aktchannel'] = cs_secure(cs_encode($channel['channel_name'], $teamspeakcharset));
			$data['users'][$run]['clid'] = $client['clid'];
			$run++;
		}
	}
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','userchannelkick');

?>
