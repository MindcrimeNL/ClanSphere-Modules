<?php
/*
 * Check access and then download
 */

# Overwrite global settings by using the following array
$cs_main = array('init_sql' => true, 'init_tpl' => false, 'init_mod' => true);

chdir('../../');

require_once 'system/core/functions.php';

cs_init($cs_main);

require('mods/replays/plugins/plugins.php');

$cs_lang = cs_translate('replays');

$cs_replays_id = empty($_REQUEST['where']) ? (empty($_GET['id']) ? 0 : $_GET['id']) : $_REQUEST['where'];
$cs_plugin = empty($_REQUEST['plugin']) ? (empty($_GET['plugin']) ? '' : $_GET['plugin']) : $_REQUEST['plugin'];
settype($cs_replays_id,'integer');

$select = 'users_id, replays_since, categories_id, games_id, replays_version, replays_team1, ';
$select .= 'replays_team2, replays_date, replays_map, replays_mirror_urls, replays_info, replays_id, replays_close, replays_access, replays_plugins';
$cs_replays = cs_sql_select(__FILE__,'replays',$select,'replays_id = ' . $cs_replays_id);

if ($cs_replays['replays_access'] == '0' || $account['access_replays'] < $cs_replays['replays_access'])
{
	echo $cs_lang['no_access'];
	return;
}

if (empty($cs_plugin))
{
	$mirror = explode("\n", $cs_replays['replays_mirror_urls']);
	if (count($mirror))
	{
		$useDownload = false;
 		$rpath = realpath($mirror[0]);
 		if (!empty($rpath))
			$useDownload = true;
  	if ($useDownload)
  	{
			cs_sql_query(__FILE__, 'UPDATE {pre}_replays SET replays_count_downloads = replays_count_downloads + 1 WHERE replays_id = '.$cs_replays_id);
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($mirror[0]));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
		  header('Cache-Control: public');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
    	header('Content-Length: ' . filesize($rpath));
    	if (ob_get_level()) ob_clean();
    	flush();
    	@readfile($rpath);
		  exit(0);
  	}
	}
	echo $cs_lang['no_access'];
	return;
}
else
{
	// Get optional plugin download
  $selplugins = explode(',', $cs_replays['replays_plugins']);
	if (!in_array($cs_plugin, $selplugins))	
	{
		echo $cs_lang['no_access'];
		return;
	}
	if (isset($plugins[$cs_plugin]))
	{
		require_once('mods/replays/plugins/'.$cs_plugin.'/functions.php');
		if (function_exists('replays_plugins_download_'.$cs_plugin))
		{
			if (call_user_func_array('replays_plugins_download_'.$cs_plugin, array($plugins[$cs_plugin], $cs_replays_id)))
				exit(0);
		}
	}	
	echo $cs_lang['no_access'];
	return;
}
