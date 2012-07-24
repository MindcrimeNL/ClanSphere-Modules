<?php
global $cs_main;
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();

$start = empty($_REQUEST['start']) ? 0 : intval($_REQUEST['start']);
$where = 'teamspeak_access <= '.$account['access_teamspeak'];
$cs_sort[1] = 'teamspeak_ip DESC';
$cs_sort[2] = 'teamspeak_ip ASC';
$cs_sort[3] = 'teamspeak_udp ASC';
$cs_sort[4] = 'teamspeak_udp DESC';
$sort = empty($_REQUEST['sort']) ? 1 : intval($_REQUEST['sort']);
$order = $cs_sort[$sort];
$teamspeak_count = cs_sql_count(__FILE__,'teamspeak');

$data['head']['teamspeak_count'] = $teamspeak_count;
$data['head']['pages'] = cs_pages('teamspeak','list',$teamspeak_count,$start,$where,$sort);
$data['head']['message'] = cs_getmsg();

$select = 'teamspeak_id, teamspeak_version, teamspeak_ip, teamspeak_udp, teamspeak_tcp, teamspeak_charset, teamspeak_admin, teamspeak_adminpw';
$cs_teamspeak = cs_sql_select(__FILE__,'teamspeak',$select,0,$order,$start,$account['users_limit']);
$teamspeak_loop = is_array($cs_teamspeak) ? count($cs_teamspeak) : 0;

// Classes includen
require_once('mods/teamspeak/classes/tss.class.php'); 

$tss = new tss();

$data['sort']['teamspeak_ip'] = cs_sort('teamspeak','list',$start,0,1,$sort);
$data['sort']['teamspeak_udp'] = cs_sort('teamspeak','list',$start,0,3,$sort);

$data['teamspeak'] = array();

for($run=0; $run<$teamspeak_loop; $run++)
{
	$tss->version($cs_teamspeak[$run]['teamspeak_version']);
	$tss->charset($cs_teamspeak[$run]['teamspeak_charset']);
	if (!$tss->connect($cs_teamspeak[$run]['teamspeak_ip'],$cs_teamspeak[$run]['teamspeak_tcp'], $cs_teamspeak[$run]['teamspeak_udp'], $options['timeout'])) 
	{
		$data['teamspeak'][$run]['if']['nodata'] = false;
		$data['teamspeak'][$run]['if']['data'] = false;
		continue;
	}
	/* try login, because of possible ban for anonymous connections */
	if (!$tss->login($cs_teamspeak[$run]['teamspeak_admin'], cs_crypt(base64_decode($cs_teamspeak[$run]['teamspeak_adminpw']), $cs_main['crypt_key'])))
	{
		/* we can try for TS2, but for TS3 stop */
		if ($cs_tss[$run]['teamspeak_admin'] == VERSION_TS3)
		{
			$data['tss'][$run]['if']['nodata'] = true;
			$data['tss'][$run]['if']['data'] = false;
			$tss->disconnect();
			continue;
		}
	}
	$sInfo = $tss->serverInfo();
	if (empty($sInfo))
	{ 
    $data['teamspeak'][$run]['if']['nodata'] = true;
		$data['teamspeak'][$run]['if']['data'] = false;
		$data['teamspeak'][$run]['no_data'] = $cs_lang['noserverview1'] . $cs_teamspeak[$run]['teamspeak_ip']. ':' . $cs_teamspeak[$run]['teamspeak_udp'];
		$data['teamspeak'][$run]['no_data'] .= $cs_lang['noserverview2'] . cs_html_br(1) . $cs_lang['noserverview3'];
		if (@fsockopen('udp://127.0.0.1', 1)) {
		  $data['teamspeak'][$run]['no_data'] .= $cs_lang['noserverview4'];
		} else {
		  $data['teamspeak'][$run]['no_data'] .= $cs_lang['noserverview5'];
		}
	}
	else
	{
    $data['teamspeak'][$run]['if']['nodata'] = false;
		$data['teamspeak'][$run]['if']['data'] = true;
		$data['teamspeak'][$run]['name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $cs_teamspeak[$run]['teamspeak_charset']));
		$data['teamspeak'][$run]['ip'] = cs_secure($cs_teamspeak[$run]['teamspeak_ip']);
		$data['teamspeak'][$run]['udp'] = cs_secure($cs_teamspeak[$run]['teamspeak_udp']);
		$data['teamspeak'][$run]['musers'] = $sInfo['server_currentusers'] . '/' . $sInfo['server_maxusers'];
		$data['teamspeak'][$run]['view'] = cs_link($cs_lang['view'],'teamspeak','view','teamspeakid=' . $cs_teamspeak[$run]['teamspeak_id']);
	}
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','list');
?>
