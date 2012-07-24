<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: navlist.php 4633 2010-11-10 19:25:06Z hajo $
require('mods/replays/plugins/plugins.php');

$cs_lang = cs_translate('replays');

$cs_get = cs_get('catid');
$cs_option = cs_sql_option(__FILE__,'replays');
$data = array();

$tables = 'replays re INNER JOIN {pre}_categories cat ON re.categories_id = cat.categories_id';
$tables .= ' LEFT JOIN {pre}_games g ON g.games_id = re.games_id';
$select = 're.replays_id AS replays_id, re.games_id AS games_id, g.games_name AS games_name, re.replays_date AS replays_date, re.replays_team1 AS replays_team1, re.replays_team2 AS replays_team2, re.replays_plugins AS replays_plugins';
$check = 'cat.categories_access <= ' . $account['access_replays'];
$check .= ' AND re.replays_access <> 0 AND re.replays_access <= '.$account['access_replays'];
if(!empty($cs_get['catid'])) {
  $check .= ' AND cat.categories_id = ' . $cs_get['catid'];
}
$order = 're.replays_date DESC';
$cs_replays = cs_sql_select(__FILE__,$tables,$select,$check,$order,0,$cs_option['max_navlist']);

if(empty($cs_replays)) {
  $data['if']['replay'] = false;
  $data['noreplay']['nodata'] = $cs_lang['no_data'];
	$cachedata = cs_subtemplate(__FILE__,$data,'replays','navlist');
}
else {
  $data['if']['replay'] = true;
  $run = 0;
  $count = count($cs_replays);
  foreach ($cs_replays AS $replays) {
	  $data['replays'][$run]['if']['empty'] = false;
	  $data['replays'][$run]['date'] = cs_date('date',$replays['replays_date'],0,1);
	  $data['replays'][$run]['game_icon'] = cs_html_img('uploads/games/' . $replays['games_id'] . '.gif', 0, 0, 0, $replays['games_name'], $replays['games_name'].' ('.$data['replays'][$run]['date'].')');
	  $data['replays'][$run]['view_url'] = cs_url('replays','view','id=' . $replays['replays_id']);
	  $short_team1 = cs_textcut($replays['replays_team1'], $cs_option['max_headline_team1'], '.', 1);
	  $short_team2 = cs_textcut($replays['replays_team2'], $cs_option['max_headline_team2'], '.', 1);
	  $data['replays'][$run]['team1_short'] = cs_secure($short_team1);
	  $data['replays'][$run]['team2_short'] = cs_secure($short_team2);
	  $data['replays'][$run]['title_short'] = cs_secure($short_team1).' vs. '.cs_secure($short_team2);
	  $data['replays'][$run]['team1'] = cs_secure($replays['replays_team1']);
	  $data['replays'][$run]['team2'] = cs_secure($replays['replays_team2']);
	  $data['replays'][$run]['title'] = cs_secure($replays['replays_team1']).' vs. '.cs_secure($replays['replays_team2']);
	  /* add some specific plugin stuff */
		if (count($plugins))
		{
			$selplugins = empty($replays['replays_plugins']) ? array() : explode(',', $replays['replays_plugins']);
			foreach ($plugins as $pname => $pinfo) 
			{
				/* set all if's for the plugins default to false */
				$data['replays'][$run]['if'][$pname] = false;
				if (in_array($pname, $selplugins))
				{
					$data['replays'][$run]['if'][$pname] = true;
					require_once('mods/replays/plugins/'.$pname.'/functions.php');
					$extra = call_user_func_array('replays_plugins_navlist_'.$pname, array($pinfo, $replays['replays_id']));
					foreach ($extra as $ekey => $eval)
					{
						/* multiple if's might exist */
						if (array_key_exists($ekey, $data['replays'][$run]))
						{
							if (is_array($data['replays'][$run][$ekey]) && is_array($eval))
							{
								foreach ($eval as $ekey2 => $eval2)
								{
									if (!array_key_exists($ekey2, $data['replays'][$run][$ekey]))
										$data['replays'][$run][$ekey][$ekey2] = $eval2;
								}
							}
						}
						else
							$data['replays'][$run][$ekey] = $eval;
					}
				}
			}
		}
 	  $run++;
  }
  for ($run = $count; $run < $cs_option['max_navlist']; $run++)
  {
  	$data['replays'][$run]['if']['empty'] = true;
	  $data['replays'][$run]['game_icon'] = '';
	  $data['replays'][$run]['date'] = '';
	  $data['replays'][$run]['view_url'] = '';
	  $data['replays'][$run]['team1_short'] = '';
	  $data['replays'][$run]['team2_short'] = '';
	  $data['replays'][$run]['title_short'] = '';
	  $data['replays'][$run]['team1'] = '';
	  $data['replays'][$run]['team2'] = '';
	  $data['replays'][$run]['title'] = '';
		if (count($plugins))
		{
			$selplugins = empty($replays['replays_plugins']) ? array() : explode(',', $replays['replays_plugins']);
			foreach ($plugins as $pname => $pinfo) 
			{
				/* set all if's for the plugins default to false */
				$data['replays'][$run]['if'][$pname] = false;
			}
		}
  }
	$cachedata = cs_subtemplate(__FILE__,$data,'replays','navlist');
}
echo $cachedata;
