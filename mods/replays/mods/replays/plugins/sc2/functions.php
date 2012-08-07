<?php
// Geh aB Clan 2009 - www.gab-clan.org
// StarCraft2 Replay Parser plugin
// $Id$

if (!defined('SC2_PARSER_VERSION'))
	define('SC2_PARSER_VERSION', '1.61');

/**
 * Build to Version
 */
function replays_plugins_version_sc2($version, $build)
{
	/* from http://wiki.teamliquid.net/starcraft2/Patch_Notes */
	$builds = array(
		14093 => '0.3.0 beta',
		14133 => '0.4.0 beta',
		14219 => '0.5.0 beta',
		14259 => '0.6.0 beta',
		14356 => '0.7.0 beta',
		14593 => '0.8.0 beta',
		14621 => '0.9.0 beta',
		14803 => '0.10.0 beta',
		15097 => '0.11.0 beta',
		15133 => '0.12.0 beta',
		15250 => '0.13.0 beta',
		15343 => '0.14.0 beta',
		15392 => '0.14.1 beta',
		15449 => '0.15.0 beta',
		15580 => '0.16.0 beta',
		15623 => '0.17.0 beta',
		15655 => '0.17.1 beta',
		15976 => '0.19.0 beta',
		16036 => '0.20.0 beta', // beta end
		16195 => '1.0.1',
		16223 => '1.0.2',
		16291 => '1.0.3',
		16561 => '1.1.0',
		16605 => '1.1.1',
		16755 => '1.1.2',
		16939 => '1.1.3',
		17326 => '1.2.0',
		17682 => '1.2.1',
		17811 => '1.2.2',
		18092 => '1.3.0',
		18221 => '1.3.1',
		18317 => '1.3.2',
		18574 => '1.3.3',
		18701 => '1.3.4',
		19132 => '1.3.5',
		19269 => '1.3.6',
		19679 => '1.4.0', // according to wiki page 19678
		19776 => '1.4.1',
		20141 => '1.4.2',
	);
	if (array_key_exists(intval($build), $builds))
		return $builds[intval($build)];
	return '?.?.?';
} // function replays_plugins_version_sc2

/**
 * Info
 */
function replays_plugins_info_sc2($plugin)
{
	$cs_lang = cs_translate('replays_sc2');

	$info = $cs_lang['general_info'].$cs_lang['current_options'];
	if (!empty($plugin['options']['apmdiagram']))
		$info .= sprintf($cs_lang['apm_on'], $plugin['options']['apmx'], $plugin['options']['apmy']);
	if (!empty($plugin['options']['overwrite']))
		$info .= sprintf($cs_lang['overwrite_on'], cs_secure($plugin['options']['overwrite']));
	return $info;
} // replays_plugins_info_sc2

/**
 * Navlist
 * 
 * Example:
 * {if:sc2}{replays:team1_race}v{replays:team2_race}{if:team3}v{replays:team3_race}{stop:team3}{if:team4}{replays:eam4_race}{stop:team4}: {stop:sc2}
 */
function replays_plugins_navlist_sc2($plugin, $replays_id)
{
	$data = array();
	$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	$data['team1_race'] = '';
	$data['team2_race'] = '';
	$data['team3_race'] = '';
	$data['team4_race'] = '';
	$data['if']['team3'] = false;
	$data['if']['team4'] = false;
	if (isset($plugin_row['replays_sc2_id']))
	{
		$teams = replays_plugins_view_sc2_teams($plugin_row);
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
		cs_error(__FILE__, 'plugin sc2: no sc2 replay with replays_id #'.$replays_id.' found', 0);
	
	return $data;
} // replays_plugins_navlist_sc2

/**
 * View
 */
function replays_plugins_view_sc2($plugin, $replays_id)
{
	global $cs_main, $account;

	$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_sc2_id']))
	{
		cs_error(__FILE__, 'plugin sc2: no sc2 replay with replays_id #'.$replays_id.' found', 0);
		return '';
	}

	$cs_lang = cs_translate('replays_sc2');
	
	$data = array();
	$data['if']['overwrite'] = !empty($plugin['options']['overwrite']) ? true : false;
	$teams = replays_plugins_view_sc2_teams($plugin_row);
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
		$data['plugin']['team'.$count] = replays_plugins_sc2_team_name_html($team, $plugin_row['replays_sc2_s2type']);
		$count++;	
	}
	$data['lplugin']['version'] = $cs_lang['version'];
	$data['plugin']['version'] = replays_plugins_version_sc2($plugin_row['replays_sc2_version'], $plugin_row['replays_sc2_build']);
	$data['if']['apmdiagram'] = false;
	$data['plugin']['fullname'] = $plugin['name'];
	$data['lplugin']['apmdiagram'] = $cs_lang['apmdiagram'];
	$data['plugin']['apmdiagram'] = '';
	$file = 'uploads/replays/sc2/'.$replays_id.'.png';
	if (!empty($plugin['options']['apmdiagram']) && file_exists($file))
	{
		$data['if']['apmdiagram'] = true;
		$data['lplugin']['apm'] = $cs_lang['apm'];
		$data['plugin']['apmdiagram'] = cs_html_img('mods/replays/download.php?id='.$replays_id.'&plugin=sc2', 0,0,0, $cs_lang['apmdiagram']);
		$count = 0;
		$data['apmplayers'] = array();
		foreach ($teams as $team)
		{
			foreach ($team as $player)
			{
				if ($player['is_observer'] == false && $player['is_computer'] == false)
				{
					$data['apmplayers'][$count] = array();
					$data['apmplayers'][$count]['player'] = cs_secure($player['name']);
					$data['apmplayers'][$count]['player_color'] = cs_secure($player['color_html']);
					$data['apmplayers'][$count]['player_raceicon'] = replays_plugins_view_sc2_race($plugin_row['replays_sc2_s2type'], $player['race'], false);
					$data['apmplayers'][$count]['player_race'] = replays_plugins_view_sc2_race($plugin_row['replays_sc2_s2type'], $player['race'], true);
					$data['apmplayers'][$count]['player_apm'] = intval($player['apm']);
					$count++;
				}
			}
		}
	}
	$data['lplugin']['map'] = $cs_lang['map'];
	/* try to get the translated map name */
	$mapname = $plugin_row['replays_sc2_mapname'];
	$maphash = $plugin_row['replays_sc2_maphash'];
	if (!empty($maphash))
	{
		$lang = empty($account['users_lang']) ? $cs_main['def_lang'] : $account['users_lang'];
		$maplangs = array('English' => 'enUS', 'German' => 'deDE', 'Spanish' => 'esES', 'French' => 'frFR', 'Polish' => 'plPL', 'Italian' => 'itIT', 'Korean' => 'koKR', 'Portugese' => 'ptPT', 'Russian' => 'ruRU', 'Chinese' => 'zhCN', 'Taiwanese' => 'zhTW', 'Mexican' => 'esMX');
		if (array_key_exists($lang, $maplangs))
		{
			require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/sc2replayutils.php');
			if (isset(SC2ReplayUtils::$depHashes[$maphash]))
			{
				$maphashname = SC2ReplayUtils::$depHashes[$maphash];
				/* since 1.20 the locales are in a different array */
				if (isset(SC2ReplayUtils::$mapLocales[$maphashname]))
				{
					$mapinfo = SC2ReplayUtils::$mapLocales[$maphashname];
					if (isset($mapinfo[$maplangs[$lang]]))
					{
						$mapname = cs_encode($mapinfo[$maplangs[$lang]], 'UTF-8');
					}
				}
			}
		}
	}

	$data['plugin']['map'] = cs_secure($mapname);
	$data['plugin']['mapimage'] = replays_plugins_view_sc2_mapimage($plugin_row['replays_sc2_mapname']);
	$data['lplugin']['mode'] = $cs_lang['mode'];
	$data['plugin']['mode'] = cs_secure($plugin_row['replays_sc2_mode']);
	$data['lplugin']['winner'] = $cs_lang['winner'];
	if ($plugin_row['replays_sc2_winner'] >= 0)
		$winner = replays_plugins_sc2_team_name_html($teams[$plugin_row['replays_sc2_winner']], $plugin_row['replays_sc2_s2type']);
	else if ($plugin_row['replays_sc2_winner'] == -1 || $plugin_row['replays_sc2_winner'] == -10)
		$winner = $cs_lang['tie'];
	else
		$winner = replays_plugins_sc2_team_name_html($teams[-($plugin_row['replays_sc2_winner'] + 20)], $plugin_row['replays_sc2_s2type']);
	$clip = array(
		'[clip='.$cs_lang['winner_clip'].']'.$winner.'[/clip]',
		$cs_lang['winner_clip'],
		$winner
	);
	$data['plugin']['winner'] = cs_abcode_clip($clip);
	$data['lplugin']['length'] = $cs_lang['length'];
	$data['plugin']['length'] = replays_plugins_view_sc2_time($plugin_row['replays_sc2_length']*1000);
	$data['lplugin']['details'] = $cs_lang['details'];
	$data['plugin']['details'] = replays_plugins_view_sc2_details($plugin_row, $teams, $cs_lang);
	$data['lplugin']['chat'] = $cs_lang['chat'];
	$data['plugin']['chat'] = replays_plugins_view_sc2_chat($plugin_row, unserialize(gzuncompress(base64_decode($plugin_row['replays_sc2_chat_log']))), $cs_lang);
	return cs_subtemplate(__FILE__,$data,'replays','view_sc2');
} // replays_plugins_view_sc2

/**
 * Get the race
 *
 * s2type = for future expansion packs
 */
function replays_plugins_view_sc2_race($s2type, $race, $text = false)
{
	$cs_lang = cs_translate('replays_sc2');

	$races = array('SC2' => array());
	// SC2-Icons
	$races['SC2']['T'] = array(
								'text' => $cs_lang['terran'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/terran_logo_64.gif" alt="['.$cs_lang['terran'].']" title="['.$cs_lang['terran'].']" />');
	$races['SC2']['P'] = array(
								'text' => $cs_lang['protoss'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/protoss_logo_64.gif" alt="['.$cs_lang['protoss'].']" title="['.$cs_lang['protoss'].']" />');
	$races['SC2']['Z'] = array(
								'text' => $cs_lang['zerg'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/zerg_logo_64.gif" alt="['.$cs_lang['zerg'].']" title="['.$cs_lang['zerg'].']" />');
	$races['SC2']['R'] = array(
								'text' => $cs_lang['random'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/random_logo_64.gif" alt="['.$cs_lang['random'].']" title="['.$cs_lang['random'].']" />');
	$races['SC2']['RT'] = array(
								'text' => $cs_lang['random'].' '.$cs_lang['terran'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/random_terran_logo_64.gif" alt="['.$cs_lang['random'].' '.$cs_lang['terran'].']" title="['.$cs_lang['random'].' '.$cs_lang['terran'].']" />');
	$races['SC2']['RP'] = array(
								'text' => $cs_lang['random'].' '.$cs_lang['protoss'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/random_protoss_logo_64.gif" alt="['.$cs_lang['random'].' '.$cs_lang['protoss'].']" title="['.$cs_lang['random'].' '.$cs_lang['protoss'].']" />');
	$races['SC2']['RZ'] = array(
								'text' => $cs_lang['random'].' '.$cs_lang['zerg'],
								'icon' => '<img width="19" height="19" src="mods/replays/plugins/sc2/images/icons/random_zerg_logo_64.gif" alt="['.$cs_lang['random'].' '.$cs_lang['zerg'].']" title="['.$cs_lang['random'].' '.$cs_lang['zerg'].']" />');
	if (!isset($races[$s2type][$race]))
		return ($text ? '???' : '[???]');
	return ($text ? $races[$s2type][$race]['text'] : $races[$s2type][$race]['icon']);
} // function replays_plugins_view_sc2_race

/**
 * Map image
 */
function replays_plugins_view_sc2_mapimage($map)
{
	global $cs_main;

	if (function_exists('mb_strtolower'))
		$smap = mb_strtolower($map, $cs_main['charset']);
	else
		$smap = strtolower($map);
	if (in_array(substr($smap, 0, 3), array('(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)')))
	{
		$smap = substr($smap, 3);
	}
	else if (in_array(substr($smap, 0, 3), array('(10)', '(11)', '(12)')))
	{
		$smap = substr($smap, 4);
	}
	$ext = substr($smap, -7);
	if (in_array($ext, array('.SC2Map')))
	{
		$smap = substr($smap, 0, -7);
	}
	$smap = ucwords($smap);
	$smap = str_replace(array('\'','/','.','\\',' '), '', $smap);
	if (file_exists('mods/replays/plugins/sc2/images/maps/'.$smap.'.png'))
	{
		return cs_html_img('mods/replays/plugins/sc2/images/maps/'.$smap.'.png',0,0,0,$map);
	}
	return '';
} // replays_plugins_view_sc2_mapimage

/**
 * Gather all players into their corresponding teams
 */
function replays_plugins_view_sc2_teams($plugin_row)
{
	$teams = array();
	for ($i = 1; $i <= 12; $i++)
	{
		$player_info = unserialize($plugin_row['replays_sc2_slot'.sprintf('%02d', $i).'_details']);
		if (!is_array($player_info))
			continue;
		/* no observer teams */
		if ($player_info['is_observer'] == true)
			continue;
		if (!isset($teams[intval($player_info['team'])]))
			$teams[intval($player_info['team'])] = array();
		$player_info['id'] = $i;
		$player_info['name'] = $plugin_row['replays_sc2_slot'.sprintf('%02d', $i).'_name'];
		$player_info['race'] = $plugin_row['replays_sc2_slot'.sprintf('%02d', $i).'_race'];
		$teams[intval($player_info['team'])][] = $player_info;
	}
	return $teams;
} // replays_plugins_view_sc2_teams


function replays_plugins_view_sc2_player_colors($plugin_row)
{
	$colors = array();
	$hex = array(0,1,2,3,4,5,6,7,8,9,'A','B','C'); 
	for ($i = 1; $i <= 12; $i++)
	{
		$player_info = unserialize($plugin_row['replays_sc2_slot'.sprintf('%02d', $i).'_details']);
		$colors[$plugin_row['replays_sc2_slot'.sprintf('%02d', $i).'_name']] = !empty($player_info['color_html']) ? $player_info['color_html'] : '#'.$hex[$i].'1'.$hex[$i].'1'.$hex[$i].'1';
	}
	return $colors;
} // replays_plugins_view_sc2_player_colors

function replays_plugins_view_sc2_details($plugin_row, $teams, $cs_lang)
{
	$data = array();
	$data['details'] = array();	
	
//	$details = '<div class="replays_plugin_sc2_details">';
	$count = 0;
	foreach ($teams as $team)
	{
		foreach ($team as $player)
		{
			if ($player['is_observer'] || $player['is_computer'])
				continue;
			$pid = sprintf('%02d', $player['id']);
			$advanced_details = unserialize($plugin_row['replays_sc2_slot'.$pid.'_advanced_details']);

 			if (!is_array($advanced_details))
				continue;

			$playerdetails = '<div class="replays_plugin_sc2_details_player" id="replays_plugin_sc2_details_player_'.$pid.'">';

			// Actions
			if (isset($advanced_details['all_events']))
			{
				$actions = array();
				$actions['actions'] = array();
				$acount = 0;
				foreach ($advanced_details['all_events'] as $event)
				{
					$actions['actions'][$acount] = array();
					$actions['actions'][$acount]['time'] = replays_plugins_view_sc2_time($event['time'] * 1000);
					$actions['actions'][$acount]['message'] = cs_secure($event['message']);
					$acount++;
				}
				$content = cs_subtemplate(__FILE__,$actions,'replays','view_sc2_details_events');
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
				$units['units'] = array();
				$ucount = 0;
				foreach ($advanced_details['units'] as $unit)
				{
					$units['units'][$ucount] = array();
					$units['units'][$ucount]['time'] = replays_plugins_view_sc2_time($unit['time'] * 1000);
					$units['units'][$ucount]['img'] = cs_html_img('mods/replays/plugins/sc2/images/units/'.$player['race_full'].'/'.replays_plugin_view_sc2_convert_unit($unit['name']).'.jpg',14,14,0,$unit['name'],$unit['name']);
					$units['units'][$ucount]['name'] = $unit['name'];
					$units['units'][$ucount]['count'] = $unit['count'];
					$units['units'][$ucount]['minerals'] = $unit['minerals'];
					$units['units'][$ucount]['gas'] = $unit['gas'];
					$units['units'][$ucount]['supply'] = $unit['supply'];
					$ucount++;
				}
				$units['unit']['race'] = strtolower($player['race_full']);
				$content = cs_subtemplate(__FILE__,$units,'replays','view_sc2_details_units');
				$clip = array(
					'[clip='.$cs_lang['units'].']'.$content.'[/clip]',
					$cs_lang['units'],
					$content
				);
				unset($units);
				$playerdetails .= cs_abcode_clip($clip);
			}
			
			// Buildings
			if (isset($advanced_details['buildings']))
			{
				$buildings = array();
				$buildings['buildings'] = array();
				$bucount = 0;
				foreach ($advanced_details['buildings'] as $building)
				{
					$buildings['buildings'][$bucount] = array();
					$buildings['buildings'][$bucount]['time'] = replays_plugins_view_sc2_time($building['time'] * 1000);
					$buildings['buildings'][$bucount]['img'] = cs_html_img('mods/replays/plugins/sc2/images/buildings/'.$player['race_full'].'/'.replays_plugin_view_sc2_convert_building($building['name']).'.jpg',14,14,0,$building['name'],$building['name']);
					$buildings['buildings'][$bucount]['name'] = $building['name'];
					$buildings['buildings'][$bucount]['count'] = $building['count'];
					$buildings['buildings'][$bucount]['minerals'] = $building['minerals'];
					$buildings['buildings'][$bucount]['gas'] = $building['gas'];
					$bucount++;
				}
				$content = cs_subtemplate(__FILE__,$buildings,'replays','view_sc2_details_buildings');
				$clip = array(
					'[clip='.$cs_lang['buildings'].']'.$content.'[/clip]',
					$cs_lang['buildings'],
					$content
				);
				unset($buildings);
				$playerdetails .= cs_abcode_clip($clip);
			}

			// Upgrades
			if (isset($advanced_details['upgrades']))
			{
				$upgrades = array();
				$upgrades['upgrades'] = array();
				$upcount = 0;
				foreach ($advanced_details['upgrades'] as $upgrade)
				{
					$upgrades['upgrades'][$upcount] = array();
					$upgrades['upgrades'][$upcount]['time'] = replays_plugins_view_sc2_time($upgrade['time'] * 1000);
					$upgrades['upgrades'][$upcount]['img'] = cs_html_img('mods/replays/plugins/sc2/images/upgrades/'.$player['race_full'].'/'.replays_plugin_view_sc2_convert_upgrade($upgrade['name']).'.jpg',14,14,0,$upgrade['name'],$upgrade['name']);
					$upgrades['upgrades'][$upcount]['name'] = $upgrade['name'];
					$upgrades['upgrades'][$upcount]['count'] = $upgrade['count'];
					$upgrades['upgrades'][$upcount]['minerals'] = $upgrade['minerals'];
					$upgrades['upgrades'][$upcount]['gas'] = $upgrade['gas'];
					$upcount++;
				}
				$content = cs_subtemplate(__FILE__,$upgrades,'replays','view_sc2_details_upgrades');
				$clip = array(
					'[clip='.$cs_lang['upgrades'].']'.$content.'[/clip]',
					$cs_lang['upgrades'],
					$content
				);
				unset($upgrades);
				$playerdetails .= cs_abcode_clip($clip);
			}

			$playerdetails .= '</div>';
			$data['details'][$count]['player_details'] = $playerdetails;
			$data['details'][$count]['player_name'] = $player['name'];
			$data['details'][$count]['player_color'] = $player['color_html'];
			$data['details'][$count]['player_raceicon'] = replays_plugins_view_sc2_race($plugin_row['replays_sc2_s2type'], $player['race'], false);
			$data['details'][$count]['player_race'] = replays_plugins_view_sc2_race($plugin_row['replays_sc2_s2type'], $player['race'], true);
			$count++;

		}
	}
	
	$content = cs_subtemplate(__FILE__,$data,'replays','view_sc2_details');
	$clip = array(
		'[clip='.$cs_lang['details_clip'].']'.$content.'[/clip]',
		$cs_lang['details_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_sc2_details

/**
 * Convert some unit names
 * 
 * Try to stay compatible with the phpsc2replay parser
 */
function replays_plugin_view_sc2_convert_unit($name)
{
	$newname = ucwords($name);
	$newname = str_replace(' ', '', $newname);
	switch ($newname)
	{
	case 'ZerglingX2':
		return 'Zergling';
		break;
	}
	return $newname;
} // function replays_plugin_view_sc2_convert_unit

/**
 * Convert some upgrade names
 * 
 * Try to stay compatible with the phpsc2replay parser
 */
function replays_plugin_view_sc2_convert_upgrade($name)
{
	$newname = ucwords($name);
	$newname = str_replace(' ', '', $newname);
	$ss = substr($newname, -2, 2);
	if (in_array($ss, array('L1', 'L2', 'L3')))
		$newname = substr($newname, 0, -2);
	switch ($newname)
	{
	default: break;
	case 'StructureArmor':
		return 'BuildingArmor';
	case 'InfernalPre-igniter':
		return 'InfernalPre-Igniter';
		break;
	case 'Hi-secAutoTracking':
		return 'Hi-SecAutoTracking';
		break;
	case '250mmStrikeCannons':
		return '250mmCannons';
		break;
	}
	return $newname;
} // function replays_plugin_view_sc2_convert_upgrade

/**
 * Convert some building names
 * 
 * Try to stay compatible with the phpsc2replay parser
 */
function replays_plugin_view_sc2_convert_building($name)
{
	$newname = ucwords($name);
	$newname = str_replace(' ', '', $newname);
	switch ($newname)
	{
	default: break;
	// Protoss
	case 'DarkShrine':
		return 'DarkObelisk';
	case 'RoboticsBay':
		return 'RoboticsSupportBay';
	case 'TransformToWarpGate(Gateway)':
		return 'WarpGate';
	case 'TransformToGateway(WarpGate)':
		return 'WarpGate';
	// Terran
	case 'OrbitalCommand':
		return 'SurveillanceStation';
	// Zerg
	case 'MutateIntoGreaterSpire(Spire)':
		return 'GreaterSpire';
	case 'MutateIntoLair(Hatchery)':
		return 'Lair';
	case 'MutateIntoHive(Lair)':
		return 'Hive';
	case 'InfestationPit':
		return 'InfestorPit';
	}
	return $newname;
} // function replays_plugin_view_sc2_convert_building

/**
 * Chat log
 */
function replays_plugins_view_sc2_chat($plugin_row, $chatlog, $cs_lang)
{
	if (!is_array($chatlog))
		return '';

	$data = array();
	$data['chats'] = array();
	$run = 0;
	$colors = replays_plugins_view_sc2_player_colors($plugin_row);
	foreach ($chatlog as $chat)
	{
		$data['chats'][$run] = array();
		if (!isset($colors[$chat['name']]))
		{
			$colors[$chat['name']] = '#111111';
		}
		$mode = '???';
		switch (intval($chat['target']))
		{
		case 0:	$mode = 'All'; break;
		case 1: $mode = '1'; break;
		case 2: $mode = 'Alliance'; break;
		case 3: $mode = '3'; break;
		}
		$data['chats'][$run]['player_color'] = $colors[$chat['name']];
		$data['chats'][$run]['player'] = cs_secure($chat['name']);
		$data['chats'][$run]['mode'] = $mode;
		$data['chats'][$run]['time'] = replays_plugins_view_sc2_time($chat['time']*1000);
		$data['chats'][$run]['text'] = cs_secure($chat['message']);
		$run++;
	}
	$content = cs_subtemplate(__FILE__,$data,'replays','view_sc2_chat');
	$clip = array(
		'[clip='.$cs_lang['chat_clip'].']'.$content.'[/clip]',
		$cs_lang['chat_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_sc2_chat

/**
 * Return batlle.net URL
 */ 
function replays_plugins_view_sc2_battlenet_url($player_info)
{
	// TODO
	return '';
} // function replays_plugins_view_sc2_battlenet_url($player_info)

function replays_plugins_view_sc2_time($time)
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
} // replays_plugins_view_sc2_time

/**
 * Create
 */
function replays_plugins_create_sc2($plugin, $replays_id)
{
	global $cs_main, $account;
	
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin sc2: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin sc2: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin sc2: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/mpqfile.php');
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/sc2replay.php');
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/sc2replayutils.php');
	$debug = false && (!empty($cs_main['developer']) OR $account['access_clansphere'] > 4);
	$mpq = new MPQFile($filename, true, $debug);
	$init = $mpq->getState();
	if (constant('SC2_PARSER_VERSION') < '1.30')
	{
		if (!defined('MPQ_ERR_NOTMPQFILE'))
			define('MPQ_ERR_NOTMPQFILE', -1);
		if (!defined('MPQ_SC2REPLAYFILE'))
			define('MPQ_SC2REPLAYFILE', 1);
		if ($init == constant('MPQ_ERR_NOTMPQFILE') || $mpq->getFileType() != constant('MPQ_SC2REPLAYFILE'))
		{
			echo $init;
			echo constant('SC2_PARSER_VERSION');
			$errors = array(($init == constant('MPQ_ERR_NOTMPQFILE') ? 'Not an MPQ file' : 'Not a SC2 replay'));
			cs_error(__FILE__, 'plugin sc2: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
			return false;
		}
	}
	else
	{
		if (!$init)
		{
			cs_error(__FILE__, 'plugin sc2: errors occurred while parsing replay with replays_id #'.$replays_id.'.', 0);
			return false;
		}
	}
	$replay = $mpq->parseReplay();
	// do insert
	$sc2replay = array(
		'replays_id' => $replays_id
	);
	cs_sql_insert(__FILE__, 'replays_sc2', array_keys($sc2replay), array_values($sc2replay));
	$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', 'replays_sc2_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	return replays_plugins_update_sc2($plugin, $replays_id, $plugin_row['replays_sc2_id'], $replay);
} // function replays_plugins_create_sc2

/**
 * Extra fields for plugin during editing
 */
function replays_plugins_edit_extra_sc2($plugin, $replays_id)
{
	$cs_lang = cs_translate('replays_sc2');

	/* if we do not have a matching plugin row, it was not used before, skip this and determine winner automatically */
	$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_sc2_id']))
	{
		return '';
	}
	$data = array();
	$winner = $plugin_row['replays_sc2_winner'];
	$teams = replays_plugins_view_sc2_teams($plugin_row);
	$options = '';
	if ($winner >= -1)
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 1);
	else
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 0);
	$options .= cs_html_option('-- '.$cs_lang['tie'].' --', -10, ($winner == -10));
	foreach ($teams as $id => $team)
	{
		$options .= cs_html_option(replays_plugins_sc2_team_name_html($team, $plugin_row['replays_sc2_s2type']), -20 - $id, ($winner == -20 - $id));
	}
	$data['lang']['winner'] = $cs_lang['winner'];
	$data['replays']['plugin_sc2_winner'] = $options;
	return cs_subtemplate(__FILE__,$data,'replays','edit_sc2');
} // function replays_plugins_edit_extra_sc2

/**
 * Edit
 */
function replays_plugins_edit_sc2($plugin, $replays_id)
{
	global $cs_main, $account;
	
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin sc2: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin sc2: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin sc2: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/mpqfile.php');
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/sc2replay.php');
	require_once('mods/replays/plugins/sc2/phpsc2replay/'.constant('SC2_PARSER_VERSION').'/sc2replayutils.php');
	$debug = false && (!empty($cs_main['developer']) OR $account['access_clansphere'] > 4);
	$mpq = new MPQFile($filename, true, $debug);
	$init = $mpq->getState();
	if (constant('SC2_PARSER_VERSION') < '1.30')
	{
		if (!defined('MPQ_ERR_NOTMPQFILE'))
			define('MPQ_ERR_NOTMPQFILE', -1);
		if (!defined('MPQ_SC2REPLAYFILE'))
			define('MPQ_SC2REPLAYFILE', 1);
		if ($init == constant('MPQ_ERR_NOTMPQFILE') || $mpq->getFileType() != constant('MPQ_SC2REPLAYFILE'))
		{
			$errors = array(($init == constant('MPQ_ERR_NOTMPQFILE') ? 'Not an MPQ file' : 'Not a SC2 replay'));
			cs_error(__FILE__, 'plugin sc2: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
			return false;
		}
	}
	else
	{
		if (!$init)
		{
			cs_error(__FILE__, 'plugin sc2: errors occurred while parsing replay with replays_id #'.$replays_id.'.', 0);
			return false;
		}
	}
	$replay = $mpq->parseReplay();
	$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', 'replays_sc2_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_sc2_id']))
	{
		// we did not use it before, do insert
		$sc2replay = array(
			'replays_id' => $replays_id
		);
		cs_sql_insert(__FILE__, 'replays_sc2', array_keys($sc2replay), array_values($sc2replay));
		$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', 'replays_sc2_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);	
	}
	$winner = null;
	if (isset($_POST['plugin_sc2_winner']))
	{
		$winner = intval($_POST['plugin_sc2_winner']);
	}
	return replays_plugins_update_sc2($plugin, $replays_id, $plugin_row['replays_sc2_id'], $replay, $winner);
} // function replays_plugins_edit_sc2

/**
 * Remove
 */
function replays_plugins_remove_sc2($plugin, $replays_id)
{
	cs_sql_delete(__FILE__, 'replays_sc2', $replays_id, 'replays_id');
	/* remove optional APM diagram */
	$remove_file = 'uploads/replays/sc2/'.$replays_id.'.png';
	if (file_exists($remove_file))
	{
		if (@unlink($remove_file))
			return true;
		return false;
	}
	return true;
} // function replays_plugins_remove_sc2

/**
 * Update
 *
 * The real stuff is done here...
 */
function replays_plugins_update_sc2($plugin, $replays_id, $plugin_id, $replay, $winner = null)
{
	global $cs_main;
	
	$limited = false;
	/* check if we have only limited functionality available */
	if (!function_exists('gzinflate') || !function_exists('bzdecompress'))
	{
		$limited = true;
	}

	$replaysc2 = array();
	// Mapname
	$replaysc2['replays_sc2_mapname'] = substr($replay->getMapName(), 0, 80);
	// Maphash, we can use this later on to translate the mapname to German/Italian/etc.
	$replaysc2['replays_sc2_maphash'] = substr($replay->getMapHash(), 0, 80);
	$replaysc2['replays_sc2_date'] = (int) $replay->getCtime();

	$slot = array();
	for ($i=0; $i<12; $i++)
	{
		$slot[$i] = array();
		$slot[$i]['name'] = '';
		$slot[$i]['race'] = '';
  	$slot[$i]['details'] = '';
  	$slot[$i]['advanced_details'] = '';
	}

	// Slot-Variablen fuellen (Name/Rasse/Details)
	$playerapm = array();
	$playercolor = array();
	$playertype = array();
		
	$players = $replay->getPlayers();
	$events = $replay->getEvents();
	$j = 0;
  $winner_team = -1;
	foreach ($players as $playerId => $player)
	{
  	if (is_array($player))
		{
			$detail_array = array();
   		$slot[$j]['name'] = $player['name'];
   		if ($player['isObs'] == false)
   		{
	   		$slot[$j]['race'] = substr($player['srace'], 0, 1);
	   		if($slot[$j]['race'] == 'R')
	   			$slot[$j]['race'] .= substr($player['race'], 0, 1);

				$detail_array['race_full'] = $player['race'];
	  		$detail_array['ptype'] = $player['ptype']; // Humn or Comp
	    	$detail_array['color_html'] = '#'.$player['color'];
	    	$detail_array['color'] = $player['sColor'];
	    	$detail_array['apm'] = ($player['apmtotal'] * 60.0) / ($replay->getGameLength() * 1.0); /* apm */
	  		$detail_array['handicap'] = $player['handicap'];
	  		$detail_array['difficulty'] = $player['difficulty'];
	  		$detail_array['start_race'] = $player['srace'];
	  		$detail_array['locale_race'] = $player['lrace'];
	  		$detail_array['uid'] = $player['uid']; // 0 = Computer
	  		$detail_array['uidIndex'] = $player['uidIndex']; // Realm EU: 1 on EU and 2 on RU, Realm US: 1 on US and 2 on LA
		 		$detail_array['actions'] = $player['apmtotal'];
	  		$playerapm[$j] = $player['apm'];
	  		$playercolor[$j] = $detail_array['color_html'];
	  		$playertype[$j] = ($player['isComp'] ? false : true);
   		}
   		else
   		{
   			$slot[$j]['race'] = '';
   			
	  		$detail_array['ptype'] = ''; // Humn or Comp
	    	$detail_array['color_html'] = '';
	    	$detail_array['color'] = '';
	    	$detail_array['apm'] = 0; /* apm */
	  		$detail_array['handicap'] = '';
	  		$detail_array['difficulty'] = 0;
	  		$detail_array['start_race'] = '';
	  		$detail_array['locale_race'] = '';
	  		$detail_array['uid'] = -1; // 0 = Computer
		 		$detail_array['actions'] = 0;
	  		$playerapm[$j] = array();
	  		$playercolor[$j] = '';
	  		$playertype[$j] = false;
   		}
    
    	// player details
  		$detail_array['team'] = (int) $player['team'];
   		$detail_array['is_computer'] = $player['isComp'];
  		$detail_array['is_observer'] = $player['isObs'];
  		$detail_array['id'] = $playerId;
  		  
  		// actions_details = Selects/Assigns/Hotkeys ...
    	// wird extra gespeichert zur schnelleren Anzeige der Hauptseite
			$advanced_detail_array = array();
			$allevents = array();
			$units = array();
			$buildings = array();
			$upgrades = array();
			if (count($events) > 0 && !$player['isObs'] && !$player['isComp'])
			{
				foreach ($events as $event) // array('p' => $playerId, 't' => $time, 'a' => $ability);
				{
					if ($event['p'] == $playerId)
					{
						$ability = $replay->getAbilityArray($event['a']);
						if ($ability['type'] == SC2_TYPEGEN)
							continue;
						$allevents[] = array('time' => floor($event['t'] / 16), 'message' => $ability['desc']);
					}
				}
			}
			
			if (!$player['isObs'] && !$player['isComp'])
			{
				$firstevents = $player['firstevents'];
				$numevents = $player['numevents'];
				if (count($firstevents) > 0)
				{
					foreach ($firstevents as $eventid => $time)
					{
						$ability = $replay->getAbilityArray($eventid);
						switch ($ability['type'])
						{
						default: // ???
							break;
						case SC2_TYPEBUILDING:
						case SC2_TYPEBUILDINGUPGRADE:
							$buildings[] = array('time' => $time, 'name' => $ability['name'], 'count' => $numevents[$eventid],
																	'minerals' => (isset($ability['min']) ? $ability['min'] : '-'),
																	'gas' => (isset($ability['gas']) ? $ability['gas'] : '-'));
							break;
						case SC2_TYPEUNIT:
						case SC2_TYPEWORKER:
							$units[] = array('time' => $time, 'name' => $ability['name'], 'count' => $numevents[$eventid],
																	'minerals' => (isset($ability['min']) ? $ability['min'] : '-'),
																	'gas' => (isset($ability['gas']) ? $ability['gas'] : '-'),
																	'supply' => (isset($ability['sup']) ? $ability['sup'] : '-'));
							break;
						case SC2_TYPEUPGRADE:
							$upgrades[] = array('time' => $time, 'name' => $ability['name'], 'count' => $numevents[$eventid],
																	'minerals' => (isset($ability['min']) ? $ability['min'] : '-'),
																	'gas' => (isset($ability['gas']) ? $ability['gas'] : '-'));
							break;
						}
					}
				}
			}
    	$advanced_detail_array['all_events'] = $allevents;
    	$advanced_detail_array['units'] = $units;
  		$advanced_detail_array['buildings'] = $buildings;
	  	$advanced_detail_array['upgrades'] = $upgrades;

  		$slot[$j]['details'] = $detail_array;
  		$slot[$j]['advanced_details'] = $advanced_detail_array;
  		$j++;
  		if ($replay->isWinnerKnown() && isset($player['won']) && $player['won'] == 1)
  			$winner_team = (int) $player['team'];
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $j).'_name'] = $slot[$j-1]['name'];
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $j).'_race'] = $slot[$j-1]['race'];
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $j).'_details'] = serialize($slot[$j-1]['details']);
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $j).'_advanced_details'] = serialize($slot[$j-1]['advanced_details']);
		}
	}

	if ($j < 12)
	{
		for ($k = $j+1; $k <= 12; $k++)
		{
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $k).'_name'] = '';
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $k).'_race'] = '';
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $k).'_details'] = '';
			$replaysc2['replays_sc2_slot'.sprintf('%02d', $k).'_advanced_details'] = '';
		}
	}
		
 	// Observers
	$observercount = 0;
	$observers = array();
  for ($i = 0; $i < $j; $i++)
	{
  	if ($slot[$i]['details']['is_observer'])
		{
 	 		$observers[$observercount++] = $slot[$i]['name'];
		}
	}

	/* game mode */
	$replaysc2['replays_sc2_mode'] = 'Unknown';
	switch ($replay->getTeamSize())
	{
	default:
  	$modus = 'Custom';
		break;
	case '1v1':
		$modus = '1on1';
		break;
	case '2v2':
		$modus = '2on2';
		break;
	case '3v3':
		$modus = '3on3';
		break;
	case '4v4':
		$modus = '4on4';
		break;
	case 'FFA':
		$modus = 'FFA';
		break;
	}
	$replaysc2['replays_sc2_mode'] = $modus;
	
	$replaysc2['replays_sc2_s2type'] = 'SC2';
	$replaysc2['replays_sc2_version'] = ''.substr($replay->getVersion(), 0, 20);
	$replaysc2['replays_sc2_build'] = ''.substr($replay->getBuild(), 0, 20);
	$replaysc2['replays_sc2_length'] = ''.$replay->getGameLength(); // in seconds
	$replaysc2['replays_sc2_gateway'] = ''.substr($replay->getRealm(), 0, 12);
	$replaysc2['replays_sc2_gametype'] = ''.($replay->isGamePublic() ? 'Public' : 'Private');
	$replaysc2['replays_sc2_chat_log'] = ''.base64_encode(gzcompress(serialize(($replay->getMessages())),8));
	$replaysc2['replays_sc2_observers'] = ''.serialize($observers);

	if (is_null($winner) || $winner == -2)
	{
		/* autodetermine */
		// Winnerteam = -1 tie? / >= 1 other
		$replaysc2['replays_sc2_winner'] = $winner_team;
	}
	else
	{
		/* fixed */
		$replaysc2['replays_sc2_winner'] = $winner;
	}
	
	/* save sc2 info */
	cs_sql_update(__FILE__, 'replays_sc2', array_keys($replaysc2), array_values($replaysc2), $plugin_id, 0);

	if (!empty($plugin['options']['overwrite']))
	{
		/* overwrite replays table info of teams, version and map */
		$which = explode(',', $plugin['options']['overwrite']);
		$csreplay = array();
		if (in_array('date', $which))
			$csreplay['replays_date'] = cs_datereal('Y-m-d', $replaysc2['replays_sc2_date']);
		if (in_array('map', $which))
			$csreplay['replays_map'] = $replaysc2['replays_sc2_mapname'];
		if (in_array('version', $which))
			$csreplay['replays_version'] = $replaysc2['replays_sc2_version'].' ('.$replaysc2['replays_sc2_build'].')';
		if (in_array('team', $which))
		{
			$plugin_row = cs_sql_select(__FILE__, 'replays_sc2', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
			$teams = replays_plugins_view_sc2_teams($plugin_row);
			if (count($teams))
			{
				$count = 1;
				foreach ($teams as $id => $players)
				{
					if ($count == 3)
						break;
					// no need to secure, cs_secure is done on view from replays itself
					$csreplay['replays_team'.$count] = replays_plugins_sc2_team_name($players);
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
			cs_error(__FILE__, 'plugin sc2: unable to create optional apldiagram replays_id #'.$replays_id.': extension gd not available', 0);
			return true;
		}

		/* check for PNG support */
	  $gd_info = gd_info();
		if (empty($gd_info['PNG Support']))
		{
			cs_error(__FILE__, 'plugin sc2: unable to create optional apldiagram replays_id #'.$replays_id.': PNG support not available in gd extension', 0);
			return true;
		}

		// Create base image
		$apm_img = imagecreatetruecolor($apmx, $apmy);
		$bgcolor = replays_plugins_extra_options_parse_sc2_hex2int($plugin['options']['bgcolor']);
		$cBackground = imagecolorallocate($apm_img, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		$gridcolor = replays_plugins_extra_options_parse_sc2_hex2int($plugin['options']['gridcolor']);
		$cBackgroundLines = imagecolorallocate($apm_img, $gridcolor[0], $gridcolor[1], $gridcolor[2]);
		$labelcolor = replays_plugins_extra_options_parse_sc2_hex2int($plugin['options']['labelcolor']);
		$cText = imagecolorallocate($apm_img, $labelcolor[0], $labelcolor[1], $labelcolor[2]);
		// fill with background color
    imagefilledrectangle($apm_img, 0, 0, $apmx+1, $apmy+1, $cBackground);

    // Calculate maximum APM for all
    $overallmaxapm = 0;
    for($i=0; $i<12; $i++)
    {
    	if (isset($playertype[$i]) && $playertype[$i] && isset($playerapm[$i]) && is_array($playerapm[$i]) && count($playerapm[$i]) > 0)
    	{
    		$apmr = 0;
    		$cs = array();
    		$c = 0;
    		foreach ($playerapm[$i] as $key => $apm)
    		{
					$cs[$c] = $apm;
    			if ($c >= 60)
    				$apmr += $apm - $cs[$c-60];
    			else
    				$apmr += $apm;
    			if($apmr > $overallmaxapm)
          	$overallmaxapm = $apmr;
          $c++;
    		}
      }
    }

    // Grid lines every minute, from 20 minutes every 5 minutes and for every 50 APM
    if ($replaysc2['replays_sc2_length'] > 1200)
			$linedivision = 300;
		else
			$linedivision = 60;
		$add = ($apmx/($replaysc2['replays_sc2_length']/$linedivision));
		if ($add > 0.0)
		{
    	for ($i = 0; $i < $apmx; $i += $add)
    		imageline($apm_img, $i, 0, $i, $apmy+1,$cBackgroundLines);
		}

		if ($overallmaxapm > 0.0)
		{
			$sub = floor($apmy*50/$overallmaxapm);
			if ($sub > 0.0)
			{
   		 	for ($i = $apmy; $i > 0; $i -= $sub)
   	  	 imageline($apm_img, 0, $i, $apmx, $i,$cBackgroundLines);
			}

	    // Calculate individual curves
			$pixelsPerSecond = $apmx / $replaysc2['replays_sc2_length'];
 	   	for($i = 0; $i < 12; $i++)
 	   	{
 	   		if (isset($playertype[$i]) && $playertype[$i] && isset($playerapm[$i]) && is_array($playerapm[$i]) && count($playerapm[$i]) > 0)
 	   		{
					// first create x/y pairs
					// do this by adding up the actions of the 60 seconds before the pixel
					// if there are less than 60 seconds, extrapolate by multiplying with 60/$secs
					// the time index corresponding to a pixel can be calculated using the $pixelsPerSecond variable,
					// it should always be 0 < $pixelsPerSecond < 1
					$xypair = array();
					$maxapm = 0;
					for ($x = 1; $x <= $apmx; $x++) {
						$secs = ceil($x / $pixelsPerSecond);
						$apm = 0;
						if ($secs < 60) {
							for ($tmp = 0;$tmp < $secs;$tmp++)
								if (isset($playerapm[$i][$tmp]))
									$apm += $playerapm[$i][$tmp];
							$apm = $apm / $secs * 60;
						} else {
							for ($tmp = $secs - 60;$tmp < $secs;$tmp++)
								if (isset($playerapm[$i][$tmp]))
									$apm += $playerapm[$i][$tmp];
							$apm = $apm;
						}
						if ($apm > $overallmaxapm)
							$overallmaxapm = $apm;
						$xypair[$x] = $apm;
				
					}
	
 	    		$cPlayer = imagecolorallocate($apm_img, hexdec(substr($playercolor[$i],1,2)), hexdec(substr($playercolor[$i],3,2)), hexdec(substr($playercolor[$i],5,2)));
					for ($j = 2; $j <= $apmx; $j++) {
						imageline($apm_img, $j - 1, $apmy - $xypair[$j - 1] / $overallmaxapm * $apmy, $j, $apmy - $xypair[$j] / $overallmaxapm * $apmy, $cPlayer);
					}
 	   		}
 	 		}

 	   	// X-label
			$add = ($apmx/($replaysc2['replays_sc2_length']/300));
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
			
		}
	
    // Border and info text
    imagerectangle($apm_img, 0, 0, $apmx-1, $apmy-1, $cText);
    //imagefilledrectangle($apm_img, $apmx-185, $apmy-39, $apmx-35, $apmy-21, $cBackgroundLines);
    //imagestring($apm_img, 3, $apmx-180, $apmy-36, 'Spielzeit: '.$length, $cText);
    imagestring($apm_img, 3, $apmx-(imagefontwidth(3)*strlen('SC2 plugin APM-Diagram'))-10, $apmy-55, 'SC2 plugin APM-Diagram', $cBackgroundLines);
    // Set copyright to this website
    imagestring($apm_img, 3, $apmx-(imagefontwidth(3)*(9+strlen($cs_main['def_title'])))-10, $apmy-35, '(c) '.date('Y').' '.$cs_main['def_title'], $cBackgroundLines);
    // Save as PNG
		$savefile = 'uploads/replays/sc2/'.$replays_id.'.png';
    imagepng($apm_img, $savefile, 7);
    imagedestroy($apm_img);
		chmod($savefile, 0666);
  }

	return true;
} // function replays_plugins_update_sc2

/**
 * Show extra options as HTML 2-column rows
 */
function replays_plugins_extra_options_sc2($plugin)
{
	$cs_lang = cs_translate('replays_sc2');
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
	return cs_subtemplate(__FILE__,$data,'replays','options_sc2');
} // function replays_plugins_extra_options_sc2

/**
 * Check hexcolor
 */
function replays_plugins_extra_options_parse_sc2_hexcolor_check($color)
{
	if (strlen($color) != 6)
		return false;
	if (strspn(strtoupper($color), '0123456789ABCDEF') != 6)
		return false;
	return true;
} // function replays_plugins_extra_options_parse_sc2_hexcolor_check

/**
 * Hex to int
 */
function replays_plugins_extra_options_parse_sc2_hex2int($color)
{
	$hexstring = '0123456789ABCDEF';
	
	if (!replays_plugins_extra_options_parse_sc2_hexcolor_check($color))
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
} // function replays_plugins_extra_options_parse_sc2_hexcolor_check

/**
 * Parse extra options from POST request
 */
function replays_plugins_extra_options_parse_sc2($plugin)
{
	$options = array();
	$options['apmx'] = (intval($_POST['plugin_sc2_apmx']) > 0 ? intval($_POST['plugin_sc2_apmx']) : 0);
	$options['apmy'] = (intval($_POST['plugin_sc2_apmy']) > 0 ? intval($_POST['plugin_sc2_apmy']) : 0);
	$options['apmdiagram'] = !empty($_POST['plugin_sc2_apmdiagram']) ? 1 : 0;
	$avoptions = array('date', 'version', 'map', 'team');
	$newoptions = array();
	$overoptions = explode(',', $_POST['plugin_sc2_overwrite']);
	foreach ($overoptions as $ooption)
	{
		if (in_array(trim($ooption), $avoptions))
			$newoptions[] = trim($ooption);
	}
	$options['overwrite'] = implode(',', $newoptions);
	if (replays_plugins_extra_options_parse_sc2_hexcolor_check($_POST['plugin_sc2_bgcolor']))
		$options['bgcolor'] = strtoupper($_POST['plugin_sc2_bgcolor']);
	if (replays_plugins_extra_options_parse_sc2_hexcolor_check($_POST['plugin_sc2_labelcolor']))
		$options['labelcolor'] = strtoupper($_POST['plugin_sc2_labelcolor']);
	if (replays_plugins_extra_options_parse_sc2_hexcolor_check($_POST['plugin_sc2_gridcolor']))
		$options['gridcolor'] = strtoupper($_POST['plugin_sc2_gridcolor']);
	return $options;
} // function replays_plugins_extra_options_parse_sc2

function replays_plugins_sc2_team_name($players)
{
	$name = '';
	$names = array();
	foreach ($players as $player_info)
	{
		$name = $player_info['name'];
		if ($player_info['is_computer'] == true)
			$name .= ' (Comp. '.SC2Replay::$difficultyLevels[$player_info['difficulty']].')';
		$names[] = $name;
	}
	$name = implode(' / ', $names);
	return substr($name, 0, 80);
} // function replays_plugins_sc2_team_name

function replays_plugins_sc2_team_name_html($players, $s2type = 'SC2')
{
	$name = '';
	$names = array();
	foreach ($players as $player_info)
	{
		$names[] = replays_plugins_view_sc2_race($s2type, $player_info['race']).'&nbsp;'.cs_secure($player_info['name']);
	}
	$name = implode(' / ', $names);
	return $name;
} // function replays_plugins_sc2_team_name_html

/**
 * Download APM diagram
 */
function replays_plugins_download_sc2($plugin, $replays_id)
{
	if (empty($plugin['options']['apmdiagram']))
		return false;
	$file = 'uploads/replays/sc2/'.$replays_id.'.png';
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
} // function replays_plugins_download_sc2

?>
