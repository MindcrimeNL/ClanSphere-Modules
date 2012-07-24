<?php

if (!defined('W3G_JULAS_VERSION'))
	define('W3G_JULAS_VERSION', '2.4');

/**
 * Info
 */
function replays_plugins_info_wc3($plugin)
{
	$cs_lang = cs_translate('replays_wc3');

	$info = $cs_lang['general_info'].$cs_lang['current_options'];
	if (!empty($plugin['options']['apmdiagram']))
		$info .= sprintf($cs_lang['apm_on'], $plugin['options']['apmx'], $plugin['options']['apmy']);
	if (!empty($plugin['options']['overwrite']))
		$info .= sprintf($cs_lang['overwrite_on'], cs_secure($plugin['options']['overwrite']));
	return $info;
} // replays_plugins_info_wc3

/**
 * Navlist
 * 
 * Example
 * {if:wc3}{replays:team1_race}v{replays:team2_race}{if:team3}v{replays:team3_race}{stop:team3}{if:team4}v{replays:team4_race}{stop:team4}: {stop:wc3}
 */
function replays_plugins_navlist_wc3($plugin, $replays_id)
{
	$data = array();
	$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	$data['team1_race'] = '';
	$data['team2_race'] = '';
	$data['team3_race'] = '';
	$data['team4_race'] = '';
	$data['if']['team3'] = false;
	$data['if']['team4'] = false;
	if (isset($plugin_row['replays_wc3_id']))
	{
		$teams = replays_plugins_view_wc3_teams($plugin_row);
		$count = 1;
		foreach ($teams as $team)
		{
			switch ($count)
			{
			default:
				break;
			case 4:
				$data['if']['team4'] = true;
				break;
			case 3:
				$data['if']['team3'] = true;
				break;
			}
			foreach ($team as $playerinfo)
			{
				if (!empty($data['team'.$count.'_race']))
					$data['team'.$count.'_race'] .= '/';
				$data['team'.$count.'_race'] .= $playerinfo['race'];
			}
			$count++;	
		}
		
	}
	else
		cs_error(__FILE__, 'plugin wc3: no wc3 replay with replays_id #'.$replays_id.' found', 0);
	
	return $data;
} // replays_plugins_navlist_wc3

/**
 * View
 */
function replays_plugins_view_wc3($plugin, $replays_id)
{
	$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_wc3_id']))
	{
		cs_error(__FILE__, 'plugin wc3: no wc3 replay with replays_id #'.$replays_id.' found', 0);
		return '';
	}

	$cs_lang = cs_translate('replays_wc3');
	
	$data = array();
	$data['if']['overwrite'] = !empty($plugin['options']['overwrite']) ? true : false;
	$teams = replays_plugins_view_wc3_teams($plugin_row);
	$data['if']['team3'] = false;
	$data['if']['team4'] = false;
	$count = 1;
	foreach ($teams as $team)
	{
		switch ($count)
		{
		default:
			break;
		case 4:
			$data['if']['team4'] = true;
			break;
		case 3:
			$data['if']['team3'] = true;
			break;
		}
		$data['lplugin']['team'.$count] = $cs_lang['team'].' '.$count;			
		$data['plugin']['team'.$count] = replays_plugins_wc3_team_name_html($team, $plugin_row['replays_wc3_w3type']);
		$count++;	
	}
	$data['lplugin']['version'] = $cs_lang['version'];
	$data['plugin']['version'] = $plugin_row['replays_wc3_version'];
	$data['if']['apmdiagram'] = false;
	$data['plugin']['fullname'] = $plugin['name'];
	$data['lplugin']['apmdiagram'] = $cs_lang['apmdiagram'];
	$data['plugin']['apmdiagram'] = '';
	$file = 'uploads/replays/wc3/'.$replays_id.'.png';
	if (!empty($plugin['options']['apmdiagram']) && file_exists($file))
	{
		$data['if']['apmdiagram'] = true;
		$data['lplugin']['apm'] = $cs_lang['apm'];
		$data['plugin']['apmdiagram'] = cs_html_img('mods/replays/download.php?id='.$replays_id.'&plugin=wc3', 0,0,0, $cs_lang['apmdiagram']);
		$count = 0;
		$data['apmplayers'] = array();
		foreach ($teams as $team)
		{
			foreach ($team as $player)
			{
				$data['apmplayers'][$count] = array();
				$data['apmplayers'][$count]['player'] = cs_secure($player['name']);
				$data['apmplayers'][$count]['player_color'] = $player['color_html'];
				$data['apmplayers'][$count]['player_raceicon'] = replays_plugins_view_wc3_race($plugin_row['replays_wc3_w3type'], $player['race'], false);
				$data['apmplayers'][$count]['player_race'] = replays_plugins_view_wc3_race($plugin_row['replays_wc3_w3type'], $player['race'], true);
				$data['apmplayers'][$count]['player_apm'] = intval($player['apm']);
				$count++;
			}
		}
	}
	$data['lplugin']['map'] = $cs_lang['map'];
	$data['plugin']['map'] = $plugin_row['replays_wc3_mapname'];
	$data['plugin']['mapimage'] = replays_plugins_view_wc3_mapimage($plugin_row['replays_wc3_mapname']);
	$data['lplugin']['mode'] = $cs_lang['mode'];
	$data['plugin']['mode'] = $plugin_row['replays_wc3_mode'];
	$data['lplugin']['winner'] = $cs_lang['winner'];
	if ($plugin_row['replays_wc3_winner'] >= 0)
		$winner = replays_plugins_wc3_team_name_html($teams[$plugin_row['replays_wc3_winner']], $plugin_row['replays_wc3_w3type']);
	else if ($plugin_row['replays_wc3_winner'] == -1 || $plugin_row['replays_wc3_winner'] == -10)
		$winner = $cs_lang['tie'];
	else
		$winner = replays_plugins_wc3_team_name_html($teams[-($plugin_row['replays_wc3_winner'] + 20)], $plugin_row['replays_wc3_w3type']);
	$clip = array(
		'[clip='.$cs_lang['winner_clip'].']'.$winner.'[/clip]',
		$cs_lang['winner_clip'],
		$winner
	);
	$data['plugin']['winner'] = cs_abcode_clip($clip);
	$data['lplugin']['length'] = $cs_lang['length'];
	$data['plugin']['length'] = replays_plugins_view_wc3_time($plugin_row['replays_wc3_length']*1000);
	$data['lplugin']['details'] = $cs_lang['details'];
	$data['plugin']['details'] = replays_plugins_view_wc3_details($plugin_row, $teams, $cs_lang);
	$data['lplugin']['chat'] = $cs_lang['chat'];
	$data['plugin']['chat'] = replays_plugins_view_wc3_chat($plugin_row, unserialize(gzuncompress(base64_decode($plugin_row['replays_wc3_chat_log']))), $cs_lang);
	return cs_subtemplate(__FILE__,$data,'replays','view_wc3');
} // replays_plugins_view_wc3

/**
 * Get the race
 */
function replays_plugins_view_wc3_race($w3type, $race, $text = false)
{
	$cs_lang = cs_translate('replays_wc3');

	$races = array('WAR3' => array(), 'W3XP' => array());
	// Classic-Icons
	$races['WAR3']['O'] = array(
								'text' => $cs_lang['orc'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/icon_orc.gif" alt="['.$cs_lang['orc'].']" title="['.$cs_lang['orc'].']" />');
	$races['WAR3']['H'] = array(
								'text' => $cs_lang['human'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/icon_hu.gif" alt="['.$cs_lang['human'].']" title="['.$cs_lang['human'].']" />');
	$races['WAR3']['N'] = array(
								'text' => $cs_lang['nightelf'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/icon_ne.gif" alt="['.$cs_lang['nightelf'].']" title="['.$cs_lang['nightelf'].']" />');
	$races['WAR3']['U'] = array(
								'text' => $cs_lang['undead'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/icon_ud.gif" alt="['.$cs_lang['undead'].']" title="['.$cs_lang['undead'].']" />');
	// TFT-Icons
	$races['W3XP']['O'] = array(
								'text' => $cs_lang['orc'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/tft_orc.gif" alt="['.$cs_lang['orc'].']" title="['.$cs_lang['orc'].']" />');
	$races['W3XP']['H'] = array(
								'text' => $cs_lang['human'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/tft_hu.gif" alt="['.$cs_lang['human'].']" title="['.$cs_lang['human'].']" />');
	$races['W3XP']['N'] = array(
								'text' => $cs_lang['nightelf'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/tft_ne.gif" alt="['.$cs_lang['nightelf'].']" title="['.$cs_lang['nightelf'].']" />');
	$races['W3XP']['U'] = array(
								'text' => $cs_lang['undead'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/tft_ud.gif" alt="['.$cs_lang['undead'].']" title="['.$cs_lang['undead'].']" />');
	$races['W3XP']['R'] = array(
								'text' => $cs_lang['random'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/tft_rdm.gif" alt="['.$cs_lang['random'].']" title="['.$cs_lang['random'].']" />');
	$races['W3XP']['RO'] = array(
								'text' => $cs_lang['orc'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/rnd_tft_orc.gif" alt="['.$cs_lang['orc'].']" title="['.$cs_lang['orc'].']" />');
	$races['W3XP']['RH'] = array(
								'text' => $cs_lang['human'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/rnd_tft_hu.gif" alt="['.$cs_lang['human'].']" title="['.$cs_lang['human'].']" />');
	$races['W3XP']['RN'] = array(
								'text' => $cs_lang['nightelf'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/rnd_tft_ne.gif" alt="['.$cs_lang['nightelf'].']" title="['.$cs_lang['nightelf'].']" />');
	$races['W3XP']['RU'] = array(
								'text' => $cs_lang['undead'],
								'icon' => '<img src="mods/replays/plugins/wc3/izireplays/images/icons/rnd_tft_ud.gif" alt="['.$cs_lang['undead'].']" title="['.$cs_lang['undead'].']" />');
	if (!isset($races[$w3type][$race]))
		return ($text ? '???' : '[???]');
	return ($text ? $races[$w3type][$race]['text'] : $races[$w3type][$race]['icon']);
} // function replays_plugins_view_wc3_race

/**
 * Map image
 */
function replays_plugins_view_wc3_mapimage($map)
{
	$smap = strtolower($map);
	$bspos = strrpos($smap, '\\');
	if ($bspos !== false)
	{
		$smap = substr($smap, $bspos+1);
	}
	if (in_array(substr($smap, 0, 3), array('(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)')))
	{
		$smap = substr($smap, 3);
	}
	else if (in_array(substr($smap, 0, 3), array('(10)', '(11)', '(12)')))
	{
		$smap = substr($smap, 4);
	}
	$ext = substr($smap, -4);
	if (in_array($ext, array('.w3m','.w3x')))
	{
		$smap = substr($smap, 0, -4);
	}
	if (file_exists('mods/replays/plugins/wc3/izireplays/images/maps/'.$smap.'.jpg'))
	{
		return cs_html_img('mods/replays/plugins/wc3/izireplays/images/maps/'.$smap.'.jpg',0,0,0,$map);
	}
	return '';
} // replays_plugins_view_wc3_mapimage

/**
 * Gather all players into their corresponding teams
 */
function replays_plugins_view_wc3_teams($plugin_row)
{
	$teams = array();
	for ($i = 1; $i <= 12; $i++)
	{
		$player_info = unserialize($plugin_row['replays_wc3_slot'.sprintf('%02d', $i).'_details']);
		if (!is_array($player_info))
			continue;
		if (!isset($teams[intval($player_info['team'])]))
			$teams[intval($player_info['team'])] = array();
		$player_info['id'] = $i;
		$player_info['name'] = $plugin_row['replays_wc3_slot'.sprintf('%02d', $i).'_name'];
		$player_info['race'] = $plugin_row['replays_wc3_slot'.sprintf('%02d', $i).'_race'];
		$teams[intval($player_info['team'])][] = $player_info;
	}
	return $teams;
} // replays_plugins_view_wc3_teams


function replays_plugins_view_wc3_player_colors($plugin_row)
{
	$colors = array();
	$hex = array(0,1,2,3,4,5,6,7,8,9,'A','B','C'); 
	for ($i = 1; $i <= 12; $i++)
	{
		$player_info = unserialize($plugin_row['replays_wc3_slot'.sprintf('%02d', $i).'_details']);
		$colors[$plugin_row['replays_wc3_slot'.sprintf('%02d', $i).'_name']] = !empty($player_info['color_html']) ? $player_info['color_html'] : '#'.$hex[$i].'1'.$hex[$i].'1'.$hex[$i].'1';
	}
	return $colors;
} // replays_plugins_view_wc3_player_colors

function replays_plugins_view_wc3_details($plugin_row, $teams, $cs_lang)
{
	include_once('mods/replays/plugins/wc3/izireplays/costs.php');
	$unitmap = array(
		'trollheadhunter/berserker' => 'trollheadhunter',
		'destroyer' => 'obsidianstatue'
	);
	$data = array();
	$data['details'] = array();	
	
//	$details = '<div class="replays_plugin_wc3_details">';
	$count = 0;
	foreach ($teams as $team)
	{
		foreach ($team as $player)
		{
			$pid = sprintf('%02d', $player['id']);
			$advanced_details = unserialize($plugin_row['replays_wc3_slot'.$pid.'_advanced_details']);

 			if (!is_array($advanced_details))
				continue;

			$playerdetails = '<div class="replays_plugin_wc3_details_player" id="replays_plugin_wc3_details_player_'.$pid.'">';
			// Heroes
			if (isset($advanced_details['heroes']))
			{
				$hcount = 0;
				$heroes = array();
				foreach ($advanced_details['heroes'] as $name => $info)
				{
		  		// don't display info for heroes whose summoning was aborted
     			if ($name != 'order' && isset($info['level']))
     			{
          	$hero_file = strtolower(str_replace(' ', '', $name));
					  $hero_details = array();
					  $hdcount = 0;
 						$hero_details['hero_details'] = array();
      			foreach ($info['abilities'] as $ability => $ainfo)
      			{
         			if ($ability !== 'order')
         			{
         				foreach ($ainfo as $aname => $alevel)
         				{
									$ability_file = strtolower(str_replace(' ', '', $aname));
									$hero_details['hero_details'][$hdcount]['detail_image'] = 'mods/replays/plugins/wc3/izireplays/images/skills/'.$ability_file.'.gif';
									$hero_details['hero_details'][$hdcount]['detail_name'] = $cs_lang[$aname];
									$hero_details['hero_details'][$hdcount]['detail_level'] = $alevel;
									$hdcount++;
         				}
         			}
      			}
      			$hdcontent = cs_subtemplate(__FILE__,$hero_details,'replays','view_wc3_details_heroes_details');
						$hdclip = array(
							'[clip='.$cs_lang['hero_capabilities'].']'.$hdcontent.'[/clip]',
							$cs_lang[$name],
							$hdcontent
						);
						unset($hero_details);
						$heroes['heroes'][$hcount]['hero_details'] = cs_abcode_clip($hdclip);
						$heroes['heroes'][$hcount]['hero_image'] = 'mods/replays/plugins/wc3/izireplays/images/heroes/'.$hero_file.'.gif';
						$heroes['heroes'][$hcount]['hero_name'] = $cs_lang[$name];
						$heroes['heroes'][$hcount]['hero_level'] = $info['level'];
						$hcount++;
     			}
				}
				$hcontent = cs_subtemplate(__FILE__,$heroes,'replays','view_wc3_details_heroes');
				$clip = array(
					'[clip='.$cs_lang['heroes'].']'.$hcontent.'[/clip]',
					$cs_lang['heroes'],
					$hcontent
				);
				unset($heroes);
				$playerdetails .= cs_abcode_clip($clip);

			}

			// Actions
			if (isset($advanced_details['actions_details']))
			{
				$actions = array();
				$acount = 0;
				arsort($advanced_details['actions_details']);
				foreach ($advanced_details['actions_details'] as $aname => $ainfo)
				{
					$actions['actions'][$acount] = array();
					$actions['actions'][$acount]['action_name'] = $cs_lang[$aname];
					$actions['actions'][$acount]['action_info'] = $ainfo;
					$actions['actions'][$acount]['action_length'] = round($ainfo/10);
					$acount++;
				}
				$content = cs_subtemplate(__FILE__,$actions,'replays','view_wc3_details_actions');
				$clip = array(
					'[clip='.$cs_lang['actions_details'].']'.$content.'[/clip]',
					$cs_lang['actions_details'],
					$content
				);
				unset($actions);
				$playerdetails .= cs_abcode_clip($clip);
			}

			// Units
			if (isset($advanced_details['units']))
			{
				$units = array();
				$ucount = 0;
				$num_units = 0;
				$gold_units = 0;
				$wood_units = 0;
				arsort($advanced_details['units']);
				foreach ($advanced_details['units'] as $uname => $uinfo)
				{
      		if ($uname != 'order' && $uinfo)
      		{ 
         		// don't show units which were cancelled and finally never made by player
         		$unit_file = strtolower(str_replace(' ', '', $uname));
         		/* convert some combi's */
         		if (isset($unitmap[$unit_file]))
         			$unit_file = $unitmap[$unit_file];
         		if (!isset($cost[$unit_file]))
         		{
         			cs_error(__FILE__, 'plugin wc3: cost not found for unit:'.$unit_file, 0);
         			$gold = 0;
         			$wood = 0;
         		}
         		else
         		{
	         		$gold = $cost[$unit_file][0]*$uinfo;
	         		$wood = $cost[$unit_file][1]*$uinfo;
         		}
						$units['units'][$ucount] = array();
						$units['units'][$ucount]['unit_wood'] = $wood;
						$units['units'][$ucount]['unit_gold'] = $gold;
						$units['units'][$ucount]['unit_name'] = $cs_lang[$unit_file];
						$units['units'][$ucount]['unit_image'] = 'mods/replays/plugins/wc3/izireplays/images/units/'.$unit_file.'.gif';
						$units['units'][$ucount]['unit_info'] = $uinfo;
						$units['units'][$ucount]['unit_length'] = round($uinfo*5);
						$gold_units += $gold;
						$wood_units += $wood;
						$num_units += $uinfo;
						$ucount++;
      		}
				}
				$units['unit'] = array();
				$units['unit']['unit_woodimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/lumber-sm.gif';
				$units['unit']['unit_goldimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/gold-sm.gif';
				$units['unit']['total'] = $num_units;
				$units['unit']['total_gold'] = $gold_units;
				$units['unit']['total_wood'] = $wood_units;
				$content = cs_subtemplate(__FILE__,$units,'replays','view_wc3_details_units');
				$clip = array(
					'[clip='.$cs_lang['units'].']'.$content.'[/clip]',
					$cs_lang['units'],
					$content
				);
				unset($units);
				$playerdetails .= cs_abcode_clip($clip);
			}
			
			// Upgrades
			if (isset($advanced_details['upgrades']))
			{
				$upgrades = array();
				$upcount = 0;
   			$num_upgrades = 0;
				$gold_upgrades = 0;
				$wood_upgrades = 0;
				foreach ($advanced_details['upgrades'] as $upname => $upinfo)
				{
      		if ($upname != 'order')
      		{ 
         		// don't show units which were cancelled and finally never made by player
         		$upgrade_file = strtolower(str_replace(' ', '', $upname));
         		if (!isset($cost[$upgrade_file]))
         		{
         			cs_error(__FILE__, 'plugin wc3: cost not found for upgrade:'.$upgrade_file, 0);
         			$gold = 0;
         			$wood = 0;
         			$upinfo = 0;
         		}
         		else
         		{
							while ((!isset($cost[$upgrade_file][$upinfo]) OR !$cost[$upgrade_file][$upinfo]) AND $upinfo > 1)
								$upinfo--;
	         		$gold = $cost[$upgrade_file][$upinfo][0];
	         		$wood = $cost[$upgrade_file][$upinfo][1];
         		}
						$upgrades['upgrades'][$upcount] = array();
						$upgrades['upgrades'][$upcount]['upgrade_wood'] = $wood;
						$upgrades['upgrades'][$upcount]['upgrade_gold'] = $gold;
						$upgrades['upgrades'][$upcount]['upgrade_name'] = $cs_lang[$upgrade_file];
						$upgrades['upgrades'][$upcount]['upgrade_image'] = 'mods/replays/plugins/wc3/izireplays/images/upgrades/'.str_replace('\'', '', $upgrade_file).'.gif';
						$upgrades['upgrades'][$upcount]['upgrade_info'] = $upinfo;
						$upgrades['upgrades'][$upcount]['upgrade_length'] = round($upinfo*20);
						$gold_upgrades += $gold;
						$wood_upgrades += $wood;
						$num_upgrades += $upinfo;
						$upcount++;
      		}
				}
				$upgrades['upgrade'] = array();
				$upgrades['upgrade']['upgrade_woodimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/lumber-sm.gif';
				$upgrades['upgrade']['upgrade_goldimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/gold-sm.gif';
				$upgrades['upgrade']['total'] = $num_upgrades;
				$upgrades['upgrade']['total_gold'] = $gold_upgrades;
				$upgrades['upgrade']['total_wood'] = $wood_upgrades;
				$content = cs_subtemplate(__FILE__,$upgrades,'replays','view_wc3_details_upgrades');
				$clip = array(
					'[clip='.$cs_lang['upgrades'].']'.$content.'[/clip]',
					$cs_lang['upgrades'],
					$content
				);
				unset($upgrades);
				$playerdetails .= cs_abcode_clip($clip);
			}

			
			// Upgrades
			if (isset($advanced_details['buildings']))
			{
				$buildings = array();
				$bcount = 0;
   			$num_buildings = 0;
				$gold_buildings = 0;
				$wood_buildings = 0;
				foreach ($advanced_details['buildings'] as $bname => $binfo)
				{
      		if ($bname != 'order')
      		{ 
         		// don't show units which were cancelled and finally never made by player
         		$building_file = strtolower(str_replace(' ', '', $bname));
         		if (!isset($cost[$building_file]))
         		{
         			cs_error(__FILE__, 'plugin wc3: cost not found for building:'.$building_file, 0);
         			$gold = 0;
         			$wood = 0;
         			$binfo = 0;
         		}
         		else
         		{
	         		$gold = $cost[$building_file][0]*$binfo;
	         		$wood = $cost[$building_file][1]*$binfo;
         		}
         		$binfox = ($binfo > 15) ? 15 : $binfo;
						$buildings['buildings'][$bcount] = array();
						$buildings['buildings'][$bcount]['building_wood'] = $wood;
						$buildings['buildings'][$bcount]['building_gold'] = $gold;
						$buildings['buildings'][$bcount]['building_name'] = $cs_lang[$building_file];
						$buildings['buildings'][$bcount]['building_image'] = 'mods/replays/plugins/wc3/izireplays/images/buildings/'.$building_file.'.gif';
						$buildings['buildings'][$bcount]['building_info'] = $binfo;
						$buildings['buildings'][$bcount]['building_length'] = round($binfox*10);
						$gold_buildings += $gold;
						$wood_buildings += $wood;
						$num_buildings += $binfo;
						$bcount++;
      		}
					else
					{
						$bocount = 0;
						foreach ($advanced_details['buildings']['order'] as $btime => $bname)
						{
							$building_file = strtolower(str_replace(' ', '', $bname));
							$buildings['buildingsorder'][$bocount]['building_time'] = replays_plugins_view_wc3_time($btime);
							$buildings['buildingsorder'][$bocount]['building_image'] = 'mods/replays/plugins/wc3/izireplays/images/buildings/'.$building_file.'.gif';
							$buildings['buildingsorder'][$bocount]['building_name'] = $cs_lang[$building_file];
							$bocount++;
						}
					}
				}
				$buildings['building'] = array();
				$buildings['building']['building_woodimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/lumber-sm.gif';
				$buildings['building']['building_goldimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/gold-sm.gif';
				$buildings['building']['order'] = $cs_lang['buildingorder'];
				$buildings['building']['total'] = $num_buildings;
				$buildings['building']['total_gold'] = $gold_buildings;
				$buildings['building']['total_wood'] = $wood_buildings;
				
				$content = cs_subtemplate(__FILE__,$buildings,'replays','view_wc3_details_buildings');
				$clip = array(
					'[clip='.$cs_lang['buildings'].']'.$content.'[/clip]',
					$cs_lang['buildings'],
					$content
				);
				unset($buildings);
				$playerdetails .= cs_abcode_clip($clip);
			}

			// Items
			if (isset($advanced_details['items']))
			{
				$items = array();
				$icount = 0;
   			$num_items = 0;
				$gold_items = 0;
				$wood_items = 0;
				foreach ($advanced_details['items'] as $iname => $iinfo)
				{
      		if ($iname != 'order')
      		{ 
         		// don't show units which were cancelled and finally never made by player
         		$item_file = strtolower(str_replace(' ', '', $iname));
         		if (!isset($cost[$item_file]))
         		{
         			cs_error(__FILE__, 'plugin wc3: cost not found for upgrade:'.$item_file, 0);
         			$gold = 0;
         			$wood = 0;
         			$iinfo = 0;
         		}
         		else
         		{
	         		$gold = $cost[$item_file][0]*$iinfo;
	         		$wood = $cost[$item_file][1]*$iinfo;
         		}
						$items['items'][$icount] = array();
						$items['items'][$icount]['item_wood'] = $wood;
						$items['items'][$icount]['item_gold'] = $gold;
						$items['items'][$icount]['item_name'] = $cs_lang[$item_file];
						$items['items'][$icount]['item_image'] = 'mods/replays/plugins/wc3/izireplays/images/items/'.$item_file.'.gif';
						$items['items'][$icount]['item_info'] = $iinfo;
						$items['items'][$icount]['item_length'] = round($iinfo*20);
						$gold_items += $gold;
						$wood_items += $wood;
						$num_items += $iinfo;
						$icount++;
      		}
				}
				$items['item'] = array();
				$items['item']['item_woodimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/lumber-sm.gif';
				$items['item']['item_goldimage'] = 'mods/replays/plugins/wc3/izireplays/images/icons/gold-sm.gif';
				$items['item']['total'] = $num_items;
				$items['item']['total_gold'] = $gold_items;
				$items['item']['total_wood'] = $wood_items;
				$content = cs_subtemplate(__FILE__,$items,'replays','view_wc3_details_items');
				$clip = array(
					'[clip='.$cs_lang['items'].']'.$content.'[/clip]',
					$cs_lang['items'],
					$content
				);
				unset($items);
				$playerdetails .= cs_abcode_clip($clip);
			}

			$playerdetails .= '</div>';
			$data['details'][$count]['player_details'] = $playerdetails;
			$data['details'][$count]['player_name'] = $player['name'];
			$data['details'][$count]['player_color'] = $player['color_html'];
			$data['details'][$count]['player_raceicon'] = replays_plugins_view_wc3_race($plugin_row['replays_wc3_w3type'], $player['race'], false);
			$data['details'][$count]['player_race'] = replays_plugins_view_wc3_race($plugin_row['replays_wc3_w3type'], $player['race'], true);
			$count++;

		}
	}
	
	$content = cs_subtemplate(__FILE__,$data,'replays','view_wc3_details');
	$clip = array(
		'[clip='.$cs_lang['details_clip'].']'.$content.'[/clip]',
		$cs_lang['details_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_wc3_details

/**
 * Chat log
 */
function replays_plugins_view_wc3_chat($plugin_row, $chatlog, $cs_lang)
{
	if (!is_array($chatlog))
		return '';

	$data = array();
	$data['chats'] = array();
	$run = 0;
	$colors = replays_plugins_view_wc3_player_colors($plugin_row);
	foreach ($chatlog as $chat)
	{
		$data['chats'][$run] = array();
		if (!isset($colors[$chat['player_name']]))
		{
			$colors[$chat['player_name']] = '#111111';
		}
		// mode = "Observers" / "All"
		$data['chats'][$run]['player_color'] = $colors[$chat['player_name']];
		$data['chats'][$run]['player'] = cs_secure($chat['player_name']);
		$data['chats'][$run]['mode'] = cs_secure($chat['mode']);
		$data['chats'][$run]['time'] = replays_plugins_view_wc3_time($chat['time']);
		$data['chats'][$run]['text'] = cs_secure($chat['text']);
		$run++;
	}
	$content = cs_subtemplate(__FILE__,$data,'replays','view_wc3_chat');
	$clip = array(
		'[clip='.$cs_lang['chat_clip'].']'.$content.'[/clip]',
		$cs_lang['chat_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_wc3_chat

function replays_plugins_view_wc3_time($time)
{
	$time = floor($time / 1000);
	if (intval($time) < 0)
		return '';

	$hours = 0;
	if ($time > 3600)
	{
		$hours = floor($time / 3600);
		$time -= $hours * 3600;
	}
	$minutes = 0;
	if ($time > 60)
	{
		$minutes = floor($time / 60);
		$time -= $minutes * 60;
	}
	if ($hours > 0)
		return sprintf('%02d:%02d:%02d', $hours, $minutes, $time);
	else
		return sprintf('%02d:%02d', $minutes, $time);
} // replays_plugins_view_wc3_time

/**
 * Create
 */
function replays_plugins_create_wc3($plugin, $replays_id)
{
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin wc3: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin wc3: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin wc3: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/wc3/w3g-julas/'.constant('W3G_JULAS_VERSION').'/w3g-julas.php');
	$replay = new replay_wc3($filename);
	if ($replay->hasErrors())
	{
		$errors = $replay->getErrors();
		cs_error(__FILE__, 'plugin wc3: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
		return false;
	}
	// do insert
	$wc3replay = array(
		'replays_id' => $replays_id
	);
	cs_sql_insert(__FILE__, 'replays_wc3', array_keys($wc3replay), array_values($wc3replay));
	$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', 'replays_wc3_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	return replays_plugins_update_wc3($plugin, $replays_id, $plugin_row['replays_wc3_id'], $replay);
} // function replays_plugins_create_wc3

/**
 * Extra fields for plugin during editing
 */
function replays_plugins_edit_extra_wc3($plugin, $replays_id)
{
	$cs_lang = cs_translate('replays_wc3');

	/* if we do not have a matching plugin row, it was not used before, skip this and determine winner automatically */
	$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_wc3_id']))
	{
		return '';
	}
	$data = array();
	$winner = $plugin_row['replays_wc3_winner'];
	$teams = replays_plugins_view_wc3_teams($plugin_row);
	$options = '';
	if ($winner >= -1)
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 1);
	else
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 0);
	$options .= cs_html_option('-- '.$cs_lang['tie'].' --', -10, ($winner == -10));
	foreach ($teams as $id => $team)
	{
		$options .= cs_html_option(replays_plugins_wc3_team_name_html($team, $plugin_row['replays_wc3_w3type']), -20 - $id, ($winner == -20 - $id));
	}
	$data['lang']['winner'] = $cs_lang['winner'];
	$data['replays']['plugin_wc3_winner'] = $options;
	return cs_subtemplate(__FILE__,$data,'replays','edit_wc3');
} // replays_plugins_info_wc3

/**
 * Edit
 */
function replays_plugins_edit_wc3($plugin, $replays_id)
{
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin wc3: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin wc3: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin wc3: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/wc3/w3g-julas/'.constant('W3G_JULAS_VERSION').'/w3g-julas.php');
	$replay = new replay_wc3($filename);
	if ($replay->hasErrors())
	{
		$errors = $replay->getErrors();
		cs_error(__FILE__, 'plugin wc3: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
		return false;
	}
	$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', 'replays_wc3_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_wc3_id']))
	{
		// we did not use it before, do insert
		$wc3replay = array(
			'replays_id' => $replays_id
		);
		cs_sql_insert(__FILE__, 'replays_wc3', array_keys($wc3replay), array_values($wc3replay));
		$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', 'replays_wc3_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);	
	}
	$winner = null;
	if (isset($_POST['plugin_wc3_winner']))
	{
		$winner = intval($_POST['plugin_wc3_winner']);
	}
	return replays_plugins_update_wc3($plugin, $replays_id, $plugin_row['replays_wc3_id'], $replay, $winner);
} // function replays_plugins_edit_wc3

/**
 * Remove
 */
function replays_plugins_remove_wc3($plugin, $replays_id)
{
	cs_sql_delete(__FILE__, 'replays_wc3', $replays_id, 'replays_id');
	/* remove optional APM diagram */
	$remove_file = 'uploads/replays/wc3/'.$replays_id.'.png';
	if (file_exists($remove_file))
	{
		if (@unlink($remove_file))
			return true;
		return false;
	}
	return true;
} // function replays_plugins_remove_wc3

/**
 * Update
 *
 * The real stuff is done here...
 *
 * Based on iZiReplays.
 */
function replays_plugins_update_wc3($plugin, $replays_id, $plugin_id, $replay, $winner = null)
{
	global $cs_main;
	
	$replaywc3 = array();
	// Mapname
	$mapname = substr($replay->game['map'],5,strlen($replay->game['map'])-5);
	if (substr($mapname,0,13) == 'FrozenThrone\\')
		$mapname = substr($mapname,13);
	$replaywc3['replays_wc3_mapname'] = cs_encode($mapname);

	// All Teams (0-11) im Replay durchgehen und die einzelnen Spieler extrahieren
	//Spielmodi: 1on1 2on2 3on3 4on4 FFA und TeamFFA werden erkannt, Spiele wie z.B. 3on2on2 werden als 'Custom'
	//gespeichert
	$slot = array();
	$team_array = array();
	for ($i=0; $i<12; $i++)
	{
		$slot[$i] = array();
		$slot[$i]['name'] = '';
		$slot[$i]['race'] = '';
  	$slot[$i]['details'] = '';
  	$slot[$i]['advanced_details'] = '';
  	$team_array[$i] = 0;
	}

	// Slot-Variablen fuellen (Name/Rasse/Details)
	$playerapm = array();
	$playercolor = array();
	for ($i = 0, $j = 0; $i < 12; $i++)
	{
  	if (isset($replay->teams[$i]) && is_array($replay->teams[$i]))
		{
  		foreach ($replay->teams[$i] as $player)
   		{
    		$team_array[$i]++;
    		$slot[$j]['name'] = $player['name'];
    		$slot[$j]['race'] = substr($player['race'], 0, 1);
    		if($slot[$j]['race'] == 'R')
     			$slot[$j]['race'].= substr($player['race_detected'], 0, 1);
    
				$detail_array = array();
    		$detail_array['team'] = $player['team'];
      	$detail_array['color_html'] = $player['color_html'];
      	$detail_array['color'] = $player['color'];
	    	$detail_array['apm'] = $player['apm'];
    		$detail_array['actions'] = $player['actions'];
    
    		// actions_details = Selects/Assigns/Hotkeys ...
      	// wird extra gespeichert zur schnelleren Anzeige der Hauptseite
				$advanced_detail_array = array();
      	$advanced_detail_array['actions_details'] = $player['actions_details'];
	    	$advanced_detail_array['units'] = $player['units'];
    		$advanced_detail_array['buildings'] = $player['buildings'];
    		$advanced_detail_array['heroes'] = $player['heroes'];
		  	$advanced_detail_array['upgrades'] = $player['upgrades'];
   	 		$advanced_detail_array['items'] = $player['items'];
    		$playerapm[$j] = $player['apmcount'];
    		$playercolor[$j] = $player['color_html'];

    		$slot[$j]['details'] = $detail_array;
    		$slot[$j]['advanced_details'] = $advanced_detail_array;
    		$j++;
				$replaywc3['replays_wc3_slot'.sprintf('%02d', $j).'_name'] = $slot[$j-1]['name'];
				$replaywc3['replays_wc3_slot'.sprintf('%02d', $j).'_race'] = $slot[$j-1]['race'];
				$replaywc3['replays_wc3_slot'.sprintf('%02d', $j).'_details'] = serialize($slot[$j-1]['details']);
				$replaywc3['replays_wc3_slot'.sprintf('%02d', $j).'_advanced_details'] = serialize($slot[$j-1]['advanced_details']);
   		}
		}
	}
		
 	// Observers
	$observercount = 0;
	$observers = array();
	if (isset($replay->teams[12]) && is_array($replay->teams[12]))
	{
		foreach($replay->teams[12] as $observer)
 	 		$observers[$observercount++] = $observer['name'];
	}

  /* Determing game mode
   * (benoetigt fuer das spaetere Datenbank-Sortieren)
   * count = number of teams / mode = 1,2,3,4 when 1,2,3,4 players per team
	 */
	$replaywc3['replays_wc3_mode'] = 'Unknown';
	for ($i=0, $mode=0, $count=0; $i<12; $i++)
 	{
  	if($mode > 0 && $team_array[$i] > 0 && ($mode != $team_array[$i])) $mode = 6;
  	if($team_array[$i] > 0 && $team_array[$i] < 5)
  	{ 
  		if($mode < $team_array[$i])
  		{
  			$mode = $team_array[$i];
  		}
  		$count++;
  	}
   	elseif($team_array[$i] > 4) $mode = 6;
 	}
 	if($count > 2 && $mode == 1)
  	$modus = 'FFA';
 	else if($count > 2 && $mode != 6)
  	$modus = 'TeamFFA';
 	else if($count == 2 && $mode != 6)
  	$modus = $mode.'on'.$mode;
 	else
  	$modus = 'Custom';
	$replaywc3['replays_wc3_mode'] = $modus;
	
	$replaywc3['replays_wc3_w3type'] = ''.$replay->header['ident'];
	$replaywc3['replays_wc3_version'] = 'v1.'.sprintf('%02d', $replay->header['major_v']);
	$replaywc3['replays_wc3_length'] = ''.$replay->header['length'] / 1000;
	$replaywc3['replays_wc3_gametype'] = ''.$replay->game['type'];
	$replaywc3['replays_wc3_chat_log'] = ''.base64_encode(gzcompress(serialize(($replay->chat)),8));
	$replaywc3['replays_wc3_observers'] = ''.serialize($observers);

	if (is_null($winner) || $winner == -2)
	{
		/* autodetermine */
		// Winnerteam
		$replaywc3['replays_wc3_winner'] = ($replay->game['winner_team'] == 'tie' ? -1 : intval($replay->game['winner_team']));
	}
	else
	{
		/* fixed */
		$replaywc3['replays_wc3_winner'] = $winner;
	}
	
	/* save wc3 info */
	cs_sql_update(__FILE__, 'replays_wc3', array_keys($replaywc3), array_values($replaywc3), $plugin_id, 0);

	if (!empty($plugin['options']['overwrite']))
	{
		/* overwrite replays table info of teams, version and map */
		$which = explode(',', $plugin['options']['overwrite']);
		$csreplay = array();
		if (in_array('map', $which))
			$csreplay['replays_map'] = $replaywc3['replays_wc3_mapname'];
		if (in_array('version', $which))
			$csreplay['replays_version'] = $replaywc3['replays_wc3_version'];
		if (in_array('team', $which))
		{
			$plugin_row = cs_sql_select(__FILE__, 'replays_wc3', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
			$teams = replays_plugins_view_wc3_teams($plugin_row);
			if (count($teams))
			{
				$count = 1;
				foreach ($teams as $id => $players)
				{
					if ($count == 3)
						break;
					$csreplay['replays_team'.$count] = replays_plugins_wc3_team_name($players);
					$count++;
				}
			}
		}
		if (count($csreplay))
		{
			/* save replays info */
			cs_sql_update(__FILE__, 'replays', array_keys($csreplay), array_values($csreplay), $replays_id, 0);
		}
	}
	$apmx = $plugin['options']['apmx'];
	$apmy = $plugin['options']['apmy'];
	/* create apm diagram */
	if (isset($playerapm[0]) && !empty($plugin['options']['apmdiagram']) && $apmx > 0 && $apmy > 0)
	{
		if (!extension_loaded('gd'))
		{
			cs_error(__FILE__, 'plugin wc3: unable to create optional apldiagram replays_id #'.$replays_id.': extension gd not available', 0);
			return true;
		}

		/* check for PNG support */
	  $gd_info = gd_info();
		if (empty($gd_info['PNG Support']))
		{
			cs_error(__FILE__, 'plugin wc3: unable to create optional apldiagram replays_id #'.$replays_id.': PNG support not available in gd extension', 0);
			return true;
		}

		// Create base image
		$apm_img = imagecreatetruecolor($apmx, $apmy);
		$bgcolor = replays_plugins_extra_options_parse_wc3_hex2int($plugin['options']['bgcolor']);
		$cBackground = imagecolorallocate($apm_img, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		$gridcolor = replays_plugins_extra_options_parse_wc3_hex2int($plugin['options']['gridcolor']);
		$cBackgroundLines = imagecolorallocate($apm_img, $gridcolor[0], $gridcolor[1], $gridcolor[2]);
		$labelcolor = replays_plugins_extra_options_parse_wc3_hex2int($plugin['options']['labelcolor']);
		$cText = imagecolorallocate($apm_img, $labelcolor[0], $labelcolor[1], $labelcolor[2]);
		// fill with background color
    imagefilledrectangle($apm_img, 0, 0, $apmx+1, $apmy+1, $cBackground);

    // Calculate maximum APM for all
    $overallmaxapm = 0;
    for($i=0; $i<12; $i++)
    {
    	if(isset($playerapm[$i]['maxmedianapm']))
     	{
    		if($playerapm[$i]['maxmedianapm'] > $overallmaxapm)
          $overallmaxapm = floor($playerapm[$i]['maxmedianapm']);
      }
    }

    // Grid lines every minute, from 20 minutes every 5 minutes and for every 50 APM
    if ($replaywc3['replays_wc3_length'] > 1200)
			$linedivision = 300;
		else
			$linedivision = 60;
		$add = ($apmx/($replaywc3['replays_wc3_length']/$linedivision));
    for ($i = 0; $i < $apmx; $i += $add)
    	imageline($apm_img, $i, 0, $i, $apmy+1,$cBackgroundLines);

		$sub = floor($apmy*50/$overallmaxapm);
    for ($i = $apmy; $i > 0; $i -= $sub)
      imageline($apm_img, 0, $i, $apmx, $i,$cBackgroundLines);

    // Calculate individual curves
    for($i = 0; $i < 12; $i++)
    {
    	if (isset($playerapm[$i]))
    	{
    		$lastx = $lastapm = 0;
     		$cPlayer = imagecolorallocate($apm_img, hexdec(substr($playercolor[$i],1,2)), hexdec(substr($playercolor[$i],3,2)), hexdec(substr($playercolor[$i],5,2)));
     		foreach ($playerapm[$i] as $time => $apm)
     		{
     			$currentx = floor(($apmx*($time/1000))/($replaywc3['replays_wc3_length']-25));
     	 		$currentapm = floor($apmy - ( ($apmy*$apm)/$overallmaxapm));
     	 		imagefilledpolygon($apm_img, array($lastx, $lastapm, $currentx, $currentapm, $currentx+1, $currentapm+1, $lastx+1, $lastapm+1), 4, $cPlayer);
     	 	//imagefilledellipse($apm_img, $currentx, $currentapm, 2,2, $cPlayer);
     	 		$lastx = $currentx; $lastapm = $currentapm;
      	}
    	}
  	}

    // X-label
		$add = ($apmx/($replaywc3['replays_wc3_length']/300));
    for ($i = 0, $j = 0; $i < $apmx; $i += $add)
    {
    	if($j > 0)
    	imagestring($apm_img, 2, $i-9, $apmy-20, $j.'min', $cText);
    	$j += 5;
    }

		// Y-label
   	for ($i = 0; $i < $overallmaxapm; $i += 50)
    {
    	if($i > 0)
    		imagestring($apm_img, 2, 14, $apmy-((($apmy*$i)/$overallmaxapm) + 9), $i.'apm', $cText);
   	}

    // Border and info text
    imagerectangle($apm_img, 0, 0, $apmx-1, $apmy-1, $cText);
    //imagefilledrectangle($apm_img, $apmx-185, $apmy-39, $apmx-35, $apmy-21, $cBackgroundLines);
    //imagestring($apm_img, 3, $apmx-180, $apmy-36, 'Spielzeit: '.$length, $cText);
    imagestring($apm_img, 3, $apmx-(imagefontwidth(3)*strlen('WC3 plugin APM-Diagram'))-10, $apmy-55, 'WC3 plugin APM-Diagram', $cBackgroundLines);
    // Set copyright to this website
    imagestring($apm_img, 3, $apmx-(imagefontwidth(3)*(9+strlen($cs_main['def_title'])))-10, $apmy-35, '(c) '.date('Y').' '.$cs_main['def_title'], $cBackgroundLines);
    // Save as PNG
		$savefile = 'uploads/replays/wc3/'.$replays_id.'.png';
    imagepng($apm_img, $savefile, 7);
    imagedestroy($apm_img);
		chmod($savefile, 0666);
  }

	return true;
} // function replays_plugins_update_wc3

/**
 * Show extra options as HTML 2-column rows
 */
function replays_plugins_extra_options_wc3($plugin)
{
	$cs_lang = cs_translate('replays_wc3');
	$data = array();
	$data['plugins']['fullname'] = $plugin['name'];
	$data['option']['apmx'] = $plugin['options']['apmx'];
	$data['option']['apmy'] = $plugin['options']['apmy'];
	$data['option']['bgcolor'] = $plugin['options']['bgcolor'];
	$data['option']['labelcolor'] = $plugin['options']['labelcolor'];
	$data['option']['gridcolor'] = $plugin['options']['gridcolor'];
	$data['option']['selapmyes'] = !empty($plugin['options']['apmdiagram']) ? ' selected' : '';
	$data['option']['selapmno'] = empty($plugin['options']['apmdiagram']) ? ' selected' : '';
	$data['option']['overwrite'] = $plugin['options']['overwrite'];
	$data['loption']['apmx'] = $cs_lang['apmx'];
	$data['loption']['apmy'] = $cs_lang['apmy'];
	$data['loption']['create_apmdiagram'] = $cs_lang['create_apmdiagram'];
	$data['loption']['overwrite'] = $cs_lang['overwrite'];
	$data['loption']['bgcolor'] = $cs_lang['bgcolor'];
	$data['loption']['labelcolor'] = $cs_lang['labelcolor'];
	$data['loption']['gridcolor'] = $cs_lang['gridcolor'];
	$data['loption']['example'] = $cs_lang['example'];
	return cs_subtemplate(__FILE__,$data,'replays','options_wc3');
} // function replays_plugins_extra_options_wc3

/**
 * Check hexcolor
 */
function replays_plugins_extra_options_parse_wc3_hexcolor_check($color)
{
	if (strlen($color) != 6)
		return false;
	if (strspn(strtoupper($color), '0123456789ABCDEF') != 6)
		return false;
	return true;
} // function replays_plugins_extra_options_parse_wc3_hexcolor_check

/**
 * Hex to int
 */
function replays_plugins_extra_options_parse_wc3_hex2int($color)
{
	$hexstring = '0123456789ABCDEF';
	
	if (!replays_plugins_extra_options_parse_wc3_hexcolor_check($color))
		return array(0,0,0);
	
	$intcolor = array(0,0,0);
	for ($j = 0; $j < 3; $j++)
	{
		$fcolor = strtoupper(substr($color, 0 + $j*2, 2));
		$val = 0;
		for ($i = 0; $i < strlen($fcolor); $i++)
		{
			$ipos = strpos($hexstring, substr($fcolor, $i, 1));
			if ($ipos !== false)
			{
				$val = 16*$val + $ipos;
			}
		}
		$intcolor[$j] = $val;
	}
	return $intcolor;
} // function replays_plugins_extra_options_parse_wc3_hexcolor_check

/**
 * Parse extra options from POST request
 */
function replays_plugins_extra_options_parse_wc3($plugin)
{
	$options = array();
	$options['apmx'] = (intval($_POST['plugin_wc3_apmx']) > 0 ? intval($_POST['plugin_wc3_apmx']) : 0);
	$options['apmy'] = (intval($_POST['plugin_wc3_apmy']) > 0 ? intval($_POST['plugin_wc3_apmy']) : 0);
	$options['apmdiagram'] = !empty($_POST['plugin_wc3_apmdiagram']) ? 1 : 0;
	$avoptions = array('version', 'map', 'team');
	$newoptions = array();
	$overoptions = explode(',', $_POST['plugin_wc3_overwrite']);
	foreach ($overoptions as $ooption)
	{
		if (in_array(trim($ooption), $avoptions))
			$newoptions[] = trim($ooption);
	}
	$options['overwrite'] = implode(',', $newoptions);
	if (replays_plugins_extra_options_parse_wc3_hexcolor_check($_POST['plugin_wc3_bgcolor']))
		$options['bgcolor'] = strtoupper($_POST['plugin_wc3_bgcolor']);
	if (replays_plugins_extra_options_parse_wc3_hexcolor_check($_POST['plugin_wc3_labelcolor']))
		$options['labelcolor'] = strtoupper($_POST['plugin_wc3_labelcolor']);
	if (replays_plugins_extra_options_parse_wc3_hexcolor_check($_POST['plugin_wc3_gridcolor']))
		$options['gridcolor'] = strtoupper($_POST['plugin_wc3_gridcolor']);
	return $options;
} // function replays_plugins_extra_options_parse_wc3

function replays_plugins_wc3_team_name($players)
{
	$name = '';
	$names = array();
	foreach ($players as $player_info)
	{
		$names[] = $player_info['name'];
	}
	$name = implode(' / ', $names);
	return substr($name, 0, 80);
} // function replays_plugins_wc3_team_name

function replays_plugins_wc3_team_name_html($players, $w3type = 'W3XP')
{
	$name = '';
	$names = array();
	foreach ($players as $player_info)
	{
		$names[] = replays_plugins_view_wc3_race($w3type, $player_info['race']).'&nbsp;'.cs_secure($player_info['name']);
	}
	$name = implode(' / ', $names);
	return $name;
} // function replays_plugins_wc3_team_name_html

/**
 * Download APM diagram
 */
function replays_plugins_download_wc3($plugin, $replays_id)
{
	if (empty($plugin['options']['apmdiagram']))
		return false;
	$file = 'uploads/replays/wc3/'.$replays_id.'.png';
	if (!file_exists($file))
		return false;
	$stat = stat($file);
  header('Content-Type: image/png');
  header('Expires: '.date('r', time()+30*86400));
  header('Last-Modified: '. date('r', $stat['mtime']));
  header('Cache-Control: public');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');
	header('Content-Length: ' . $stat['size']);
	if (ob_get_level()) ob_clean();
	flush();
	@readfile($file);
	
	return true;
} // function replays_plugins_download_wc3
?>
