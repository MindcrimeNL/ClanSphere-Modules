<?php
global $cs_main;
$cs_lang = cs_translate('teamspeak');
$options = cs_sql_option(__FILE__,'teamspeak');

$data = array();
$data['head']['message'] = cs_getmsg();

$select = 'teamspeak_id, teamspeak_version, teamspeak_ip, teamspeak_udp, teamspeak_tcp, teamspeak_admin, teamspeak_adminpw, teamspeak_register, teamspeak_charset';
$where = 'teamspeak_access <= '.$account['access_teamspeak'];
$cs_tss = cs_sql_select(__FILE__,'teamspeak',$select,$where,0,0,0);
$tss_loop = count($cs_tss);

require_once('mods/teamspeak/classes/tss.class.php');
$tss = new tss(); 

$data['tss'] = array();
for($run=0; $run<$tss_loop; $run++) {
	$tss->version($cs_tss[$run]['teamspeak_version']);
	$tss->charset($cs_tss[$run]['teamspeak_charset']);
	
	if (!$tss->connect($cs_tss[$run]['teamspeak_ip'],$cs_tss[$run]['teamspeak_tcp'], $cs_tss[$run]['teamspeak_udp'], $options['timeout'])) 
	{
		$data['tss'][$run]['if']['nodata'] = true;
		$data['tss'][$run]['if']['data'] = false;
		$data['tss'][$run]['no_data'] = $cs_lang['noserverview1'] . $cs_tss[$run]['teamspeak_ip']. ':' . $cs_tss[$run]['teamspeak_udp'];
		$data['tss'][$run]['no_data'] .= $cs_lang['noserverview2'];
		continue;
	}
	/* try login, because of possible ban for anonymous connections */
	if (!$tss->login($cs_tss[$run]['teamspeak_admin'], cs_crypt(base64_decode($cs_tss[$run]['teamspeak_adminpw']), $cs_main['crypt_key'])))
	{
		/* we can try for TS2, but for TS3 stop */
		if ($cs_tss[$run]['teamspeak_admin'] == VERSION_TS3)
		{
			$data['tss'][$run]['if']['nodata'] = true;
			$data['tss'][$run]['if']['data'] = false;
			$data['tss'][$run]['no_data'] = $cs_lang['noserverview1'] . $cs_tss[$run]['teamspeak_ip']. ':' . $cs_tss[$run]['teamspeak_udp'];
			$data['tss'][$run]['no_data'] .= $cs_lang['noserverview2'];
			$tss->disconnect();
			continue;
		}
	}
  $sInfo = $tss->serverInfo();
  if (empty($sInfo))
  { 
    $data['tss'][$run]['if']['nodata'] = true;
		$data['tss'][$run]['if']['data'] = false;
		$data['tss'][$run]['no_data'] = $cs_lang['noserverview1'] . $cs_tss[$run]['teamspeak_ip']. ':' . $cs_tss[$run]['teamspeak_udp'];
		$data['tss'][$run]['no_data'] .= $cs_lang['noserverview2'] . cs_html_br(1) . $cs_lang['noserverview3'];
		if (@fsockopen('udp://'.$cs_tss[$run]['teamspeak_ip'], 1)) {
		  $data['tss'][$run]['no_data'] .= $cs_lang['noserverview4'];
		} else {
		  $data['tss'][$run]['no_data'] .= $cs_lang['noserverview5'];
		}
  } else {
    $data['tss'][$run]['if']['nodata'] = false;
		$data['tss'][$run]['if']['data'] = true;
		$data['tss'][$run]['name'] = cs_secure(cs_encode($sInfo['virtualserver_name'], $cs_tss[$run]['teamspeak_charset']));
		$data['tss'][$run]['ip'] = cs_secure($cs_tss[$run]['teamspeak_ip']);
		$data['tss'][$run]['view'] = cs_link($cs_lang['view'],'teamspeak','view','teamspeakid=' . $cs_tss[$run]['teamspeak_id']);
		if ($cs_tss[$run]['teamspeak_register'] != '0' && $cs_tss[$run]['teamspeak_register'] <= $account['access_teamspeak'])
		{
		  $data['tss'][$run]['view'] .= ' - ' . cs_link($cs_lang['create_account'],'teamspeak','create_uaccount','teamspeakid=' . $cs_tss[$run]['teamspeak_id']);
		}
		$tss->disconnect();
  }
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','center');
?>
