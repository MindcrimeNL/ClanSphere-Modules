<?php
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();

$start = empty($_REQUEST['start']) ? 0 : intval($_REQUEST['start']);
$cs_sort[1] = 'teamspeak_ip DESC';
$cs_sort[2] = 'teamspeak_ip ASC';
$cs_sort[3] = 'teamspeak_udp ASC';
$cs_sort[4] = 'teamspeak_udp DESC';
$sort = empty($_REQUEST['sort']) ? 1 : intval($_REQUEST['sort']);
$order = $cs_sort[$sort];
$teamspeak_count = cs_sql_count(__FILE__,'teamspeak');

$data['link']['teamspeak_serveradd'] = cs_link($cs_lang['addserver'],'teamspeak','serveradd');
$data['link']['teamspeak_manage'] = cs_link($cs_lang['manage'],'teamspeak','manage');
$data['head']['teamspeak_count'] = $teamspeak_count;
$data['head']['pages'] = cs_pages('teamspeak','serverlist',$teamspeak_count,$start,0,$sort);
$data['head']['message'] = cs_getmsg();

if (!empty($_REQUEST['activate']))
{
	$deactive = !empty($_REQUEST['deactivate']) ? intval($_REQUEST['deactivate']) : 0;
	$cs_teamspeak['teamspeak_active'] = 0;
	$teamspeak_cells = array_keys($cs_teamspeak);
	$teamspeak_save = array_values($cs_teamspeak);
	cs_sql_update(__FILE__,'teamspeak',$teamspeak_cells,$teamspeak_save,$deactive);
	
	$active = intval($_REQUEST['activate']);
	$cs_teamspeak['teamspeak_active'] = 1;
	$teamspeak_cells = array_keys($cs_teamspeak);
	$teamspeak_save = array_values($cs_teamspeak);
	cs_sql_update(__FILE__,'teamspeak',$teamspeak_cells,$teamspeak_save,$active);
	
	$data['head']['message'] = '<br />'.$cs_lang['changed'];
}

$select = 'teamspeak_id, teamspeak_version, teamspeak_ip, teamspeak_udp, teamspeak_tcp, teamspeak_active, teamspeak_charset';
$cs_teamspeak = cs_sql_select(__FILE__,'teamspeak',$select,0,$order,$start,$account['users_limit']);
$teamspeak_loop = count($cs_teamspeak);
$cs_teamspeak_active = cs_sql_select(__FILE__,'teamspeak',$select,'teamspeak_active = 1');

$data['sort']['teamspeak_ip'] = cs_sort('teamspeak','serverlist',$start,0,1,$sort);
$data['sort']['teamspeak_udp'] = cs_sort('teamspeak','serverlist',$start,0,3,$sort);

$data['teamspeak'] = array();

//// Classes includen
//require_once('mods/teamspeak/classes/tss.class.php'); 
//
//$tss = new tss();

for($run=0; $run<$teamspeak_loop; $run++)
{
//	$tss->version($cs_teamspeak[$run]['teamspeak_version']);
//	$tss->charset($cs_teamspeak[$run]['teamspeak_charset']);

	$data['teamspeak'][$run]['ip'] = cs_secure($cs_teamspeak[$run]['teamspeak_ip']);
	$data['teamspeak'][$run]['udp'] = intval($cs_teamspeak[$run]['teamspeak_udp']);
	$data['teamspeak'][$run]['tcp'] = intval($cs_teamspeak[$run]['teamspeak_tcp']);
	$data['teamspeak'][$run]['version'] = $cs_lang['version_'.intval($cs_teamspeak[$run]['teamspeak_version'])];
	$data['teamspeak'][$run]['link_view'] = cs_url('teamspeak','view','teamspeakid=' . $cs_teamspeak[$run]['teamspeak_id']);
	$data['teamspeak'][$run]['if']['nodata'] = false;
	$data['teamspeak'][$run]['if']['data'] = true;
//	if (!$tss->connect($cs_teamspeak[$run]['teamspeak_ip'],$cs_teamspeak[$run]['teamspeak_tcp'], $cs_teamspeak[$run]['teamspeak_udp'], $options['timeout'])) 
//	{
		$data['teamspeak'][$run]['if']['nodata'] = true;
		$data['teamspeak'][$run]['if']['data'] = false;
		$data['teamspeak'][$run]['traffic_in'] = '???';
		$data['teamspeak'][$run]['traffic_out'] = '???';
//	}
//	else
//	{
//		$sInfo = $tss->serverInfo();
//		$data['teamspeak'][$run]['traffic_in'] = cs_format_bytes($sInfo['connection_bytes_received_total']);
//		$data['teamspeak'][$run]['traffic_out'] = cs_format_bytes($sInfo['connection_bytes_sent_total']);
//	}
	
	if (intval($cs_teamspeak[$run]['teamspeak_active'] == 1))
	{
		$data['teamspeak'][$run]['active'] = cs_icon('submit','16',$cs_lang['active']);
	} else
	{
	  if (!empty($cs_teamspeak_active))
	  {
	    $data['teamspeak'][$run]['active'] = cs_link(cs_icon('cancel','16',$cs_lang['doactive']),'teamspeak','serverlist','activate=' . $cs_teamspeak[$run]['teamspeak_id'] . '&deactivate=' . $cs_teamspeak_active['teamspeak_id']);
    } else {
	    $data['teamspeak'][$run]['active'] = cs_link(cs_icon('cancel','16',$cs_lang['doactive']),'teamspeak','serverlist','activate=' . $cs_teamspeak[$run]['teamspeak_id']);
	  }
	}
	$data['teamspeak'][$run]['options'] = cs_link(cs_icon('edit','16',$cs_lang['edit']),'teamspeak','serveredit','teamspeakid=' . $cs_teamspeak[$run]['teamspeak_id']);
	$data['teamspeak'][$run]['options'] .= cs_link(cs_icon('editdelete','16',$cs_lang['del']),'teamspeak','serverdel','teamspeakid=' . $cs_teamspeak[$run]['teamspeak_id']);
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','serverlist');

?>
