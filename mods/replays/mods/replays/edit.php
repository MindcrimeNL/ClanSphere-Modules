<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

require('mods/replays/plugins/plugins.php');

$cs_lang = cs_translate('replays');

$files_gl = cs_files();

$cs_post = cs_post('id');
$cs_get = cs_get('id');

$replays_id = empty($cs_get['id']) ? 0 : $cs_get['id'];
if (!empty($cs_post['id']))  $replays_id = $cs_post['id'];

$op_replays = cs_sql_option(__FILE__,'replays');
$rep_max['size'] = $op_replays['file_size'];
$rep_filetypes = explode(",", $op_replays['file_type']);

$replay = cs_sql_select(__FILE__,'replays','replays_id, users_id',"replays_id = '" . $replays_id . "'");
if (empty($replay) || ($replay['users_id'] != $account['users_id'] && $account['access_replays'] < 4))
{
	echo $cs_lang['no_access'];
	return;
}


require_once('mods/categories/functions.php');

if(isset($_POST['submit'])) {

  $cs_replays['categories_id'] = empty($_POST['categories_name']) ? $_POST['categories_id'] : 
  cs_categories_create('replays',$_POST['categories_name']);

  $cs_replays['games_id'] = $_POST['games_id'];
  $cs_replays['replays_version'] = $_POST['replays_version'];
  $cs_replays['replays_team1'] = $_POST['replays_team1'];
  $cs_replays['replays_team2'] = $_POST['replays_team2'];
  $cs_replays['replays_date'] = cs_datepost('date','date');
  $cs_replays['replays_map'] = $_POST['replays_map'];
  $cs_replays['replays_mirror_urls'] = $_POST['replays_mirror_urls'];
  $cs_replays['replays_mirror_names'] = $_POST['replays_mirror_names'];
  $cs_replays['replays_info'] = $_POST['replays_info'];
  $cs_replays['replays_close'] = isset($_POST['replays_close']) ? $_POST['replays_close'] : 0;
  $cs_replays['replays_access'] = intval($_POST['replays_access']);

	$cs_replays['replays_plugins'] = '';
	if (count($plugins))
	{
		$selplugins = array();
		foreach ($plugins as $pname => $pinfo) 
		{
			if (!empty($_POST['plugin_'.$pname]))
			{
				$selplugins[] = $pname;
			}
		}
		$cs_replays['replays_plugins'] = implode(',', $selplugins); 
	}
	
  $error = 0;
  $error = '';

  if(!empty($files_gl['replay']['tmp_name'])) {
    $rep_size = filesize($files_gl['replay']['tmp_name']);
    $rep_ext = explode('.',$files_gl['replay']['name']);
    $who_ext = count($rep_ext) < 1 ? 0 : count($rep_ext) - 1;
    $extension = in_array(strtolower($rep_ext[$who_ext]),$rep_filetypes) ? $rep_ext[$who_ext] : 0;
    if(empty($extension)) {
      $error .= $cs_lang['ext_error'] . cs_html_br(1);
    }
    if($files_gl['replay']['size']>$rep_max['size']) { 
    $error .= $cs_lang['too_big'] . cs_html_br(1);
    }

    $oldmirror = explode("\n", $cs_replays['replays_mirror_urls']);
		$rpath = null;
    if (count($oldmirror))
    {
      $rpath = realpath($oldmirror[0]);
      if (empty($rpath) || !is_file($rpath))
				$rpath = null;
		}

    $filename = 'replay-' . $replays_id . '-' . cs_time() . '.' . $extension;
    if(empty($error) AND cs_upload('replays', $filename, $files_gl['replay']['tmp_name'])) {
    	$replay_file = 'uploads/replays/' . $filename;
    	$cs_replays['replays_mirror_urls'] = empty($cs_replays['replays_mirror_urls']) ? $replay_file : 
     			 $replay_file . "\n" . $cs_replays['replays_mirror_urls'];
			if (!empty($rpath) && $oldmirror[0] != $replay_file)
				@unlink($rpath);
    }
    else {
      $error .= $cs_lang['up_error'];
    }
  }

  if(empty($cs_replays['games_id'])) {
    $error .= $cs_lang['no_game'] . cs_html_br(1);
  }
  if(empty($cs_replays['categories_id'])) {
    $error .= $cs_lang['no_cat'] . cs_html_br(1);
  }
  if(empty($cs_replays['replays_version'])) {
    $error .= $cs_lang['no_version'] . cs_html_br(1);
  }
  if(empty($cs_replays['replays_team2'])) {
    $error .= $cs_lang['no_team1'] . cs_html_br(1);
  }
  if(empty($cs_replays['replays_team2'])) {
    $error .= $cs_lang['no_team2'] . cs_html_br(1);
  }
  if(empty($cs_replays['replays_date'])) {
    $error .= $cs_lang['no_date'] . cs_html_br(1);
  }
}
else {

  $cells = 'categories_id, games_id, replays_version, replays_team1, replays_team2, replays_date, replays_map, replays_mirror_urls, replays_mirror_names, replays_info, replays_close, replays_access, replays_plugins';
  $cs_replays = cs_sql_select(__FILE__,'replays',$cells,"replays_id = '" . $replays_id . "'");
  $selplugins = explode(',', $cs_replays['replays_plugins']);
}
if(!isset($_POST['submit'])) {
   $data['head']['body'] = $cs_lang['body_edit'];
}
elseif(!empty($error)) {
   $data['head']['body'] = $error;
}

if(!empty($error) OR !isset($_POST['submit'])) {

  $data['replays'] = $cs_replays;
  $data['replays']['cat_dropdown'] = cs_categories_dropdown('replays',$cs_replays['categories_id']);

  $data['games'] = array();
  $el_id = 'game_1';
  $cs_games = cs_sql_select(__FILE__,'games','games_name,games_id',0,'games_name',0,0);
  $games_count = count($cs_games);
  for($run = 0; $run < $games_count; $run++) {
    $sel = $cs_games[$run]['games_id'] == $cs_replays['games_id'] ? 1 : 0;
    $data['games'][$run]['op'] = cs_html_option($cs_games[$run]['games_name'],$cs_games[$run]['games_id'],$sel);
  }
  $url = 'uploads/games/' . $cs_replays['games_id'] . '.gif';
  $data['replays']['games_img'] = cs_html_img($url,0,0,'id="' . $el_id . '"');

  $data['replays']['date_sel'] = cs_dateselect('date','date',$cs_replays['replays_date'],1995);

  $matches[1] = $cs_lang['rep_infos'];
  $return_types = '';
  foreach($rep_filetypes AS $add) {
    $return_types .= empty($return_types) ? $add : ', ' . $add;
  }
  $matches[2] = $cs_lang['max_size'] . cs_filesize($rep_max['size']) . cs_html_br(1);
  $matches[2] .= $cs_lang['filetypes'] . $return_types;
  $data['replays']['upload_clip'] = cs_abcode_clip($matches);

  $data['replays']['abcode_smileys'] = cs_abcode_smileys('replays_info');
  $data['replays']['abcode_features'] = cs_abcode_features('replays_info');

  $data['replays']['close_check'] = empty($cs_replays['replays_close']) ? '' : 'checked="checked"';
  
  $data['replays']['id'] = $replays_id;

  $levels = 0;
  $sel = 0;
  while($levels < 6) {
    $cs_replays['replays_access'] == $levels ? $sel = 1 : $sel = 0;
    $data['access'][$levels]['sel'] = cs_html_option($levels . ' - ' . $cs_lang['lev_' . $levels],$levels,$sel);
    $levels++;
  }

	$data['if']['plugin'] = false;
	if (count($plugins))
	{
		$data['if']['plugin'] = true;
		$data['plugins'] = array();
		$run = 0;
		$data['plugin']['javascript'] = '';
		foreach ($plugins as $pname => $pinfo) 
		{
			$data['plugins'][$run]['name'] = $pname;
			$data['plugins'][$run]['fullname'] = $pinfo['name'];
			require_once('mods/replays/plugins/'.$pname.'/functions.php');
			$data['plugins'][$run]['info'] = call_user_func('replays_plugins_info_'.$pname, $pinfo);
			$data['plugins'][$run]['checked'] = in_array($pname, $selplugins) ? ' checked="checked"' : '';
			$data['plugins'][$run]['extra'] = in_array($pname, $selplugins) ? call_user_func_array('replays_plugins_edit_extra_'.$pname, array($pinfo, $replays_id)) : '';
			$games_ids = explode(',', $pinfo['options']['games_ids']);
			$data['plugins'][$run]['disabled'] = (in_array($cs_replays['games_id'], $games_ids) || empty($pinfo['options']['games_ids']) ? '' : ' disabled');
			foreach ($games_ids as $game_id)
			{
				$data['plugin']['javascript'] .= '							if (games_id == '.$game_id.') { document.getElementById(\'plugin_'.$pname.'\').disabled = false; document.getElementById(\'plugin_'.$pname.'\').checked = true; }
					else  { document.getElementById(\'plugin_'.$pname.'\').disabled = true; document.getElementById(\'plugin_'.$pname.'\').checked = false; }';
			}
			$run++;
		}
		
	}
  echo cs_subtemplate(__FILE__,$data,'replays','edit');
}
else {

  $replays_cells = array_keys($cs_replays);
  $replays_save = array_values($cs_replays);
  cs_sql_update(__FILE__,'replays',$replays_cells,$replays_save,$replays_id);
  
  if (!empty($cs_replays['replays_plugins']))
  {
  	$execplugins = explode(',', $cs_replays['replays_plugins']);
  	replays_plugins_edit($plugins, $execplugins, $replays_id);
  }
  cs_redirect($cs_lang['changes_done'], 'replays') ;
}
