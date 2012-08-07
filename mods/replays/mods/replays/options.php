<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

require('mods/replays/plugins/plugins.php');

$cs_lang = cs_translate('replays');

if(isset($_POST['submit'])) {  
  
  $save = array();
  $save['file_size'] = (int) $_POST['file_size'] * 1024;
  $save['file_type'] = $_POST['file_type'];
  $save['max_navlist'] = (int) $_POST['max_navlist'];
  $save['plugins'] = $_POST['plugins'];
  $save['max_headline_team1'] = (int) $_POST['max_headline_team1'];
  $save['max_headline_team2'] = (int) $_POST['max_headline_team2'];
  
  require_once 'mods/clansphere/func_options.php';
  
  cs_optionsave('replays', $save);

	if (count($plugins))
	{
		foreach ($plugins as $pname => $pinfo) 
		{
			$options = $pinfo['options'];
			$new_games_ids = '';
			if (isset($_POST['games_ids_'.$pname]))
			{
				if (is_array($_POST['games_ids_'.$pname]))
				{
					$new_games_ids = implode(',', $_POST['games_ids_'.$pname]);
				}
			}
			$options['games_ids'] = $new_games_ids;
			require_once('mods/replays/plugins/'.$pname.'/functions.php');
			$return_options = call_user_func('replays_plugins_extra_options_parse_'.$pname, $pinfo);
			foreach ($options as $oname => $ovalue)
			{
				if ($oname == 'games_ids')
					continue;
				if (isset($return_options[$oname]))
				{
					$options[$oname] = $return_options[$oname];
				}
			}
		  cs_optionsave('replays_'.$pname, $options);
		}
	}

  cs_redirect($cs_lang['changes_done'],'options','roots');

} else {
  
  $data = array();
  $data['op'] = cs_sql_option(__FILE__,'replays');
  
  $data['op']['filesize'] = round($data['op']['file_size'] / 1024);

	$data['if']['plugin'] = false;
	if (count($plugins))
	{
		$data['if']['plugin'] = true;
		$data['plugins'] = array();
		$run = 0;
		$games = cs_sql_select(__FILE__, 'games', 'games_id, games_name',0,'games_name',0,0);
		foreach ($plugins as $pname => $pinfo) 
		{
			$data['plugins'][$run]['name'] = $pname;
			$data['plugins'][$run]['fullname'] = $pinfo['name'];
			
			$seloptions = explode(',', $pinfo['options']['games_ids']);
			$goptions = '';
			reset($games);
			foreach($games as $game) 
			{
				$goptions .= '<option value="'.$game['games_id'].'"'.(in_array($game['games_id'], $seloptions) ? ' selected': '').'>'.$game['games_name'].'</option>';
			}
			$data['plugins'][$run]['games_options'] = $goptions;
			require_once('mods/replays/plugins/'.$pname.'/functions.php');
			$data['plugins'][$run]['extra_options'] = call_user_func('replays_plugins_extra_options_'.$pname, $pinfo);
			$run++;
		}
	}
  echo cs_subtemplate(__FILE__,$data,'replays','options');
}
