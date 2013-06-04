<?php

if (!defined('W3G_CTS_VERSION'))
	define('W3G_CTS_VERSION', '1.4.6');

/* we need these files to be able to unserialize the classes correctly */
require_once('mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/config.php'); 
require_once('mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/tools.php');
require_once('mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/xml_parser.php');
	
/**
 * Info
 */
function replays_plugins_info_dota($plugin)
{
	$cs_lang = cs_translate('replays_dota');

	$info = $cs_lang['general_info'].$cs_lang['current_options'];
	if (!empty($plugin['options']['overwrite']))
		$info .= sprintf($cs_lang['overwrite_on'], cs_secure($plugin['options']['overwrite']));
	return $info;
} // replays_plugins_info_dota

/**
 * Navlist
 */
function replays_plugins_navlist_dota($plugin, $replays_id)
{
	return array();
} // replays_plugins_navlist_dota

/**
 * View
 */
function replays_plugins_view_dota($plugin, $replays_id) // TODO
{
	$plugin_row = cs_sql_select(__FILE__, 'replays_dota', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_dota_id']))
	{
		cs_error(__FILE__, 'plugin dota: no dota replay with replays_id #'.$replays_id.' found', 0);
		return '';
	}
	$cs_lang = cs_translate('replays_dota');
	$data = array();
	$data['if']['overwrite'] = !empty($plugin['options']['overwrite']) ? true : false;
	for ($i = 1; $i <= 2; $i++)
	{
		$data['lplugin']['team'.$i] = $cs_lang['team'].' '.$i;
		$data['plugin']['team'.$i] = replays_plugins_dota_team_name_html($plugin_row, $i);
	}
	$data['lplugin']['version'] = $cs_lang['version'];
	$data['plugin']['version'] = $plugin_row['replays_dota_version'];
	$data['plugin']['fullname'] = $plugin['name'];

	$data['lplugin']['map'] = $cs_lang['map'];
	$data['plugin']['map'] = $plugin_row['replays_dota_mapname'];
	$data['lplugin']['winner'] = $cs_lang['winner'];
	if ($plugin_row['replays_dota_winner'] >= 0)
		$winner = replays_plugins_dota_team_name_html($plugin_row, $plugin_row['replays_dota_winner'] + 1);
	else if ($plugin_row['replays_dota_winner'] == -1 || $plugin_row['replays_dota_winner'] == -10)
		$winner = $cs_lang['tie'];
	else
		$winner = replays_plugins_dota_team_name_html($plugin_row, -(20+$plugin_row['replays_dota_winner']) + 1);
	$clip = array(
		'[clip='.$cs_lang['winner_clip'].']'.$winner.'[/clip]',
		$cs_lang['winner_clip'],
		$winner
	);
	$data['plugin']['winner'] = cs_abcode_clip($clip);
	$data['lplugin']['length'] = $cs_lang['length'];
	$data['plugin']['length'] = replays_plugins_view_dota_time($plugin_row['replays_dota_length']*1000);
	$data['lplugin']['details'] = $cs_lang['details'];
	$data['plugin']['details'] = replays_plugins_view_dota_details($plugin, $plugin_row, $cs_lang);
	$data['lplugin']['chat'] = $cs_lang['chat'];
	$data['plugin']['chat'] = replays_plugins_view_dota_chat($plugin_row, unserialize(gzuncompress(base64_decode($plugin_row['replays_dota_chat_log']))), $cs_lang);
	$data['lplugin']['hoster'] = $cs_lang['hoster'];
	$data['plugin']['hoster'] = cs_secure($plugin_row['replays_dota_hoster']);
	$data['lplugin']['saver'] = $cs_lang['saver'];
	$data['plugin']['saver'] = cs_secure($plugin_row['replays_dota_saver']);
	return cs_subtemplate(__FILE__,$data,'replays','view_dota');
} // replays_plugins_view_dota

/**
 * Details
 */
function replays_plugins_view_dota_details($plugin, $plugin_row, $cs_lang)
{
	$data = array();
	$data['lang'] = array();
	$data['lang']['team'] = $cs_lang['team'];
	$data['lang']['details_name'] = $cs_lang['details_name'];
	$data['lang']['details_level'] = $cs_lang['details_level'];
	$data['lang']['details_apm'] = $cs_lang['details_apm'];
	$data['lang']['details_hk'] = $cs_lang['details_hk'];
	$data['lang']['details_hd'] = $cs_lang['details_hd'];
	$data['lang']['details_ha'] = $cs_lang['details_ha'];
	$data['lang']['details_ck'] = $cs_lang['details_ck'];
	$data['lang']['details_cd'] = $cs_lang['details_cd'];
	$data['lang']['details_n'] = $cs_lang['details_n'];
	$data['lang']['details_bans'] = $cs_lang['details_bans'];
	$data['lang']['details_picks'] = $cs_lang['details_picks'];
	
	$data['details'] = array();
	$colors = replays_plugins_view_dota_player_colors($plugin_row);

	$count = 0;

	$teamSentinal = unserialize($plugin_row['replays_dota_team01_details']);
	if (is_array($teamSentinal))
	{
		foreach ($teamSentinal as $pid => $player)
		{
			if (!isset($colors[$player['name']]))
			{
				$colors[$player['name']] = '#111111';
			}
			$data['details'][$count]['player_team'] = $cs_lang['sentinel'];
			$data['details'][$count]['player_color'] = $colors[$player['name']];
			$data['details'][$count]['player_name'] = cs_secure($player['name']);
			$hero = replays_plugins_view_dota_details_hero($player);
			$data['details'][$count]['player_hcolor'] = '#'.$plugin['options']['color_sentinel'];
			if ($hero['data'] instanceof DotaHero)
			{
				$data['details'][$count]['player_himage'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$hero['data']->getArt();
				$data['details'][$count]['player_hname'] = $hero['data']->getName();
			}
			else
			{
				$data['details'][$count]['player_himage'] = $cs_lang['n/a'];
				$data['details'][$count]['player_hname'] = $cs_lang['n/a'];
			}
			$data['details'][$count]['player_level'] = cs_secure($hero['level']);
			$data['details'][$count]['player_apm'] = round( (60 * 1000 * $player['apm']) / ($player['time']));
			if ($player['stats'] instanceof DotaPlayerStats)
			{
				$data['details'][$count]['player_hk'] = $player['stats']->HeroKills;
				$data['details'][$count]['player_hd'] = $player['stats']->Deaths;
				$data['details'][$count]['player_ha'] = $player['stats']->Assists;
				$data['details'][$count]['player_ck'] = $player['stats']->CreepKills;
				$data['details'][$count]['player_cd'] = $player['stats']->CreepDenies;
				$data['details'][$count]['player_n'] = $player['stats']->Neutrals;
			}
			else
			{
				$data['details'][$count]['player_hk'] = $cs_lang['n/a'];
				$data['details'][$count]['player_hd'] = $cs_lang['n/a'];
				$data['details'][$count]['player_ha'] = $cs_lang['n/a'];
				$data['details'][$count]['player_ck'] = $cs_lang['n/a'];
				$data['details'][$count]['player_cd'] = $cs_lang['n/a'];
				$data['details'][$count]['player_n'] = $cs_lang['n/a'];
			}
			$data['details'][$count]['player_details'] = replays_plugins_view_dota_details_player($pid, $player, $cs_lang);
			$count++;	
		}
	}
	$teamScourge = unserialize($plugin_row['replays_dota_team02_details']);
	if (is_array($teamScourge))
	{
		foreach ($teamScourge as $pid => $player)
		{
			$data['details'][$count]['player_team'] = $cs_lang['scourge'];
			$data['details'][$count]['player_color'] = $colors[$player['name']];
			$data['details'][$count]['player_name'] = cs_secure($player['name']);
			$hero = replays_plugins_view_dota_details_hero($player);
			$data['details'][$count]['player_hcolor'] = '#'.$plugin['options']['color_scourge'];
			if ($hero['data'] instanceof DotaHero)
			{
				$data['details'][$count]['player_himage'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$hero['data']->getArt();
				$data['details'][$count]['player_hname'] = cs_secure($hero['data']->getName());
			}
			else
			{
				$data['details'][$count]['player_himage'] = $cs_lang['n/a'];
				$data['details'][$count]['player_hname'] = $cs_lang['n/a'];
			}
			$data['details'][$count]['player_level'] = cs_secure($hero['level']);
			$data['details'][$count]['player_apm'] = round( (60 * 1000 * $player['apm']) / ($player['time']));
			if ($player['stats'] instanceof DotaPlayerStats)
			{
				$data['details'][$count]['player_hk'] = $player['stats']->HeroKills;
				$data['details'][$count]['player_hd'] = $player['stats']->Deaths;
				$data['details'][$count]['player_ha'] = $player['stats']->Assists;
				$data['details'][$count]['player_ck'] = $player['stats']->CreepKills;
				$data['details'][$count]['player_cd'] = $player['stats']->CreepDenies;
				$data['details'][$count]['player_n'] = $player['stats']->Neutrals;
			}
			else
			{
				$data['details'][$count]['player_hk'] = $cs_lang['n/a'];
				$data['details'][$count]['player_hd'] = $cs_lang['n/a'];
				$data['details'][$count]['player_ha'] = $cs_lang['n/a'];
				$data['details'][$count]['player_ck'] = $cs_lang['n/a'];
				$data['details'][$count]['player_cd'] = $cs_lang['n/a'];
				$data['details'][$count]['player_n'] = $cs_lang['n/a'];
			}
			$data['details'][$count]['player_details'] = replays_plugins_view_dota_details_player($pid, $player, $cs_lang);
			$count++;	
		}
	}

	$data['detail'] = array();
	// Bans
	$bans = unserialize($plugin_row['replays_dota_bans']);
	$data['detail']['bans'] = '';
	if (is_array($bans))
	{
		$ban = array();
		$ban['bans'] = array();
		$bcount = 0;
		foreach ($bans as $hero)
		{
			if ($hero instanceof DotaHero)
			{
				$ban['bans'][$bcount] = array();
				$ban['bans'][$bcount]['ban_image'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$hero->getArt();
				$ban['bans'][$bcount]['ban_name'] = cs_secure($hero->getName());
				$ban['bans'][$bcount]['ban_color'] = '#'.($hero->extra == 0 ? $plugin['options']['color_sentinel'] : $plugin['options']['color_scourge']); // sentinel / scourge
        if ($bcount < constant('DOTA_REPLAY_NUM_OF_BANS')-1)
          $ban['bans'][$bcount]['ban_extra'] = '-';
        else
          $ban['bans'][$bcount]['ban_extra'] = '';
				$bcount++;
			}
		}
		$data['detail']['bans'] = cs_subtemplate(__FILE__,$ban,'replays','view_dota_details_bans');
	}
	// Picks
	$picks = unserialize($plugin_row['replays_dota_picks']);
	$data['detail']['picks'] = '';
	if (is_array($picks))
	{
		$pick = array();
		$pick['picks'] = array();
		$pcount = 0;
		foreach ($picks as $hero)
		{
				$pick['picks'][$pcount] = array();
				$pick['picks'][$pcount]['pick_image'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$hero->getArt();
				$pick['picks'][$pcount]['pick_name'] = cs_secure($hero->getName());
				$pick['picks'][$pcount]['pick_color'] = '#'.($hero->extra == 0 ? $plugin['options']['color_sentinel'] : $plugin['options']['color_scourge']); // sentinel / scourge
        if ($pcount % 2 == 0)
          $pick['picks'][$pcount]['pick_extra'] = '-';
        else
          $pick['picks'][$pcount]['pick_extra'] = '';
				$pcount++;
		}
		$data['detail']['picks'] = cs_subtemplate(__FILE__,$pick,'replays','view_dota_details_picks');
	}
	
	$content = cs_subtemplate(__FILE__,$data,'replays','view_dota_details');
	$clip = array(
		'[clip='.$cs_lang['details_clip'].']'.$content.'[/clip]',
		$cs_lang['details_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_dota_details

/**
 * Player details
 */
function replays_plugins_view_dota_details_player($pid, $player, $cs_lang)
{
	$details = array();
	$details['details'] = array();
	$details['details']['pid'] = $pid;

	// Inventory	
	if ($player['stats'] instanceof DotaPlayerStats)
	{
		$inventory = array();
		for ($j = 0; $j < 6; $j++)
		{
			$inventory['inventory'][$j]['inventory_image'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.(isset($player['stats']->Inventory[$j]) && is_object($player['stats']->Inventory[$j]) ? $player['stats']->Inventory[$j]->getArt() : 'images/BTNEmpty.gif');
			$inventory['inventory'][$j]['inventory_name'] = (isset($player['stats']->Inventory[$j]) && is_object($player['stats']->Inventory[$j]) ? cs_secure($player['stats']->Inventory[$j]->getName()) : $cs_lang['empty']);
		}
		$icontent = cs_subtemplate(__FILE__,$inventory,'replays','view_dota_details_player_inventory');
		$iclip = array(
			'[clip='.$cs_lang['details_inventory'].']'.$icontent.'[/clip]',
			$cs_lang['details_inventory'],
			$icontent
		);
		$details['details']['inventory'] = cs_abcode_clip($iclip);
	}

	// Abilities
	$hero = replays_plugins_view_dota_details_hero($player);
	if (isset($hero['abilities']))
	{
		$abilities = array();
		$skill = 0;
		$levels = array();
		foreach ($hero['abilities'] as $time => $ability)
		{
			$skill++;
			if ($skill > 25)
				break;
			if (!isset($levels[$ability->getName()]))
				$levels[$ability->getName()] = 1;
			else
				$levels[$ability->getName()]++;
			$abilities['abilities'][($skill-1)] = array();
			$abilities['abilities'][($skill-1)]['ability_skill'] = $skill; 
			$abilities['abilities'][($skill-1)]['ability_level'] = $levels[$ability->getName()]; 
			$abilities['abilities'][($skill-1)]['ability_name'] = cs_secure($ability->getName()); 
			$abilities['abilities'][($skill-1)]['ability_image'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$ability->getArt(); 
			$abilities['abilities'][($skill-1)]['ability_time'] = replays_plugins_view_dota_time($time); 
		}
		$acontent = cs_subtemplate(__FILE__,$abilities,'replays','view_dota_details_player_abilities');
		$aclip = array(
			'[clip='.$cs_lang['details_inventory'].']'.$acontent.'[/clip]',
			$cs_lang['details_abilities'],
			$acontent
		);
		$details['details']['abilities'] = cs_abcode_clip($aclip);
	}

	// Items
	if (isset($player['items']))
	{
		$items = array();
		$itcount = 0;
		foreach ($player['items'] as $time => $item)
		{
			if (is_object($item) && $item->getName() != 'Select Hero' )
			{
				$items['items'][$itcount] = array();
				$items['items'][$itcount]['item_name'] = cs_secure($item->getName());
				$items['items'][$itcount]['item_time'] = $time;
				$items['items'][$itcount]['item_image'] = 'mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/'.$item->getArt();
				$itcount++; 
			}
		}
		$itcontent = cs_subtemplate(__FILE__,$items,'replays','view_dota_details_player_items');
		$itclip = array(
			'[clip='.$cs_lang['details_items'].']'.$itcontent.'[/clip]',
			$cs_lang['details_items'],
			$itcontent
		);
		$details['details']['items'] = cs_abcode_clip($itclip);
	}
	
	// Actions
	if (isset($player['actions_details']))
	{
		ksort($player['actions_details']);
		
		$px_per_action = 400 / $player['apm'];
		
		$actions = array();
		$account = 0;
		foreach ($player['actions_details'] as $name => $info)
		{
			$actions['actions'][$account]['action_name'] = cs_secure($name);
			$actions['actions'][$account]['action_info'] = round($info);
			$actions['actions'][$account]['action_length'] = round($info * $px_per_action);
			$account++;
		}
		$accontent = cs_subtemplate(__FILE__,$actions,'replays','view_dota_details_player_actions');
		$acclip = array(
			'[clip='.$cs_lang['details_actions'].']'.$accontent.'[/clip]',
			$cs_lang['details_actions'],
			$accontent
		);
		$details['details']['actions'] = cs_abcode_clip($acclip);
	}

	$pdetails = cs_subtemplate(__FILE__,$details,'replays','view_dota_details_player');
	$clip = array(
		'[clip='.$cs_lang['details_player'].']'.$pdetails.'[/clip]',
		$cs_lang['details_player'],
		$pdetails
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_dota_details_player

/**
 * Get the player's hero
 */
function replays_plugins_view_dota_details_hero($player)
{
	$p_hero = array('level' => 'n/a');
	// Get player's hero
	foreach ($player['heroes'] as $name => $hero)
	{
		if ($name == 'order' || !isset($hero['level']))
			continue; 
		
		if ($name != 'Common')
		{    
			// Merge common skills and atribute stats with Hero's skills
			if(isset($player['heroes']['Common']) )
			{
				$hero['level'] += $player['heroes']['Common']['level'];
				$hero['abilities'] = array_merge($hero['abilities'], $player['heroes']['Common']['abilities']);
			}
			if ( $hero['level'] > 25)
				$hero['level'] = 25;    
			ksort($hero['abilities']);
			$p_hero = $hero;
			break;
		}
	}
	return $p_hero;
} // function replays_plugins_view_dota_details_hero

/**
 * Chat log
 */
function replays_plugins_view_dota_chat($plugin_row, $chatlog, $cs_lang)
{
	if (!is_array($chatlog))
		return '';

	$data = array();
	$data['chats'] = array();
	$run = 0;
	$colors = replays_plugins_view_dota_player_colors($plugin_row);
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
		$mode = $chat['mode'];
		if (is_numeric($mode))
		{
			switch ($chat['mode'])
			{
			case 0:	$mode = $cs_lang['All']; break;
			case 1: $mode = $cs_lang['Allies']; break;
			case 2: $mode = $cs_lang['Observers']; break;
			case 3: $mode = $cs_lang['The game has been paused.']; break;
			case 4: $mode = $cs_lang['The game has been resumed.']; break;
			default: break;
			}
		}
		$data['chats'][$run]['mode'] = $mode;
		$data['chats'][$run]['time'] = replays_plugins_view_dota_time($chat['time']);
		$data['chats'][$run]['text'] = cs_secure($chat['text']);
		$run++;
	}
	$content = cs_subtemplate(__FILE__,$data,'replays','view_dota_chat');
	$clip = array(
		'[clip='.$cs_lang['chat_clip'].']'.$content.'[/clip]',
		$cs_lang['chat_clip'],
		$content
	);
	return cs_abcode_clip($clip);
} // function replays_plugins_view_dota_chat

function replays_plugins_view_dota_player_colors($plugin_row)
{
  $colors = array();
  $hex = array(0,1,2,3,4,5,6,7,8,9,'A','B','C');
  for ($i = 1; $i <= 12; $i++)
  {
    $player_info = unserialize($plugin_row['replays_dota_slot'.sprintf('%02d', $i).'_details']);
    $colors[$plugin_row['replays_dota_slot'.sprintf('%02d', $i).'_name']] = !empty($player_info['color_html']) ? $player_info['color_html'] : '#'.$hex[$i].'1'.$hex[$i].'1'.$hex[$i].'1';
  }
  return $colors;
} // replays_plugins_view_dota_player_colors

function replays_plugins_view_dota_time($time)
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
} // replays_plugins_view_dota_time

/**
 * 
 */
function replays_plugins_dota_team_name($plugin_row, $id, $substr = true)
{
	$name = '';
	$names = array();
	$team = unserialize($plugin_row['replays_dota_team0'.$id.'_details']);
	if (is_array($team))
	{
		foreach ($team as $pid => $player)
		{
			$names[] = $player['name'];
		}
		$name = implode(' / ', $names);
	}
	if ($substr)
		return substr($name, 0, 80);
	return $name;
} // function replays_plugins_dota_team_name

function replays_plugins_dota_team_name_html($plugin_row, $id)
{
	$name = '';
	$names = array();
	$team = unserialize($plugin_row['replays_dota_team0'.$id.'_details']);
	if (is_array($team))
	{
		foreach ($team as $pid => $player)
		{
			$names[] = /* replays_plugins_view_dota_hero($player['race']). */'&nbsp;'.cs_secure($player['name']); // TODO
		}
		$name = implode(' / ', $names);
	}
	return $name;
} // function replays_plugins_dota_team_name_html

/**
 * Create
 */
function replays_plugins_create_dota($plugin, $replays_id)
{
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin dota: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin dota: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin dota: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/reshine.php');
	$replay = new replay_dota($filename);
	if ($replay->hasErrors())
	{
		$errors = $replay->getErrors();
		cs_error(__FILE__, 'plugin dota: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
		return false;
	}
	// do insert
	$dotareplay = array(
		'replays_id' => $replays_id
	);
	cs_sql_insert(__FILE__, 'replays_dota', array_keys($dotareplay), array_values($dotareplay));
	$plugin_row = cs_sql_select(__FILE__, 'replays_dota', 'replays_dota_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	return replays_plugins_update_dota($plugin, $replays_id, $plugin_row['replays_dota_id'], $replay);
} // function replays_plugins_create_dota

/**
 * Extra fields for plugin during editing
 */
function replays_plugins_edit_extra_dota($plugin, $replays_id)
{
	$cs_lang = cs_translate('replays_dota');

	/* if we do not have a matching plugin row, it was not used before, skip this and determine winner automatically */
	$plugin_row = cs_sql_select(__FILE__, 'replays_dota', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_dota_id']))
	{
		return '';
	}
	$data = array();
	$winner = $plugin_row['replays_dota_winner'];
	$options = '';
	if ($winner >= -1)
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 1);
	else
		$options .= cs_html_option($cs_lang['winner_determine'], -2, 0);
	$options .= cs_html_option('-- '.$cs_lang['tie'].' --', -10, ($winner == -10));
	$team1 = replays_plugins_dota_team_name_html($plugin_row, 1);
	$options .= cs_html_option($team1, -20, ($winner == -20));
	$team2 = replays_plugins_dota_team_name_html($plugin_row, 2);
	$options .= cs_html_option($team2, -21, ($winner == -21));
	$data['lang']['winner'] = $cs_lang['winner'];
	$data['replays']['plugin_dota_winner'] = $options;
	return cs_subtemplate(__FILE__,$data,'replays','edit_dota');
} // function replays_plugins_edit_extra_dota

/**
 * Edit
 */
function replays_plugins_edit_dota($plugin, $replays_id)
{
	$cs_replay = cs_sql_select(__FILE__, 'replays', 'replays_mirror_urls', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!is_array($cs_replay))
	{
		cs_error(__FILE__, 'plugin dota: no replay with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$mirrors = explode("\n", $cs_replay['replays_mirror_urls']);
	if (empty($mirrors[0]))
	{
		cs_error(__FILE__, 'plugin dota: no mirror(0) with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	$filename = $mirrors[0];
	if (!file_exists($filename))
	{
		cs_error(__FILE__, 'plugin dota: no replay file "'.$filename.'" with replays_id #'.$replays_id.' found', 0);
		return false;
	}
	require_once('mods/replays/plugins/dota/cts/'.constant('W3G_CTS_VERSION').'/reshine.php');
	$replay = new replay_dota($filename);
	if ($replay->hasErrors())
	{
		$errors = $replay->getErrors();
		cs_error(__FILE__, 'plugin dota: errors while parsing replay with replays_id #'.$replays_id.': '.implode(', ', $errors), 0);
		return false;
	}
	$plugin_row = cs_sql_select(__FILE__, 'replays_dota', 'replays_dota_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_dota_id']))
	{
		// we did not use it before, do insert
		$dotareplay = array(
			'replays_id' => $replays_id
		);
		cs_sql_insert(__FILE__, 'replays_dota', array_keys($dotareplay), array_values($dotareplay));
		$plugin_row = cs_sql_select(__FILE__, 'replays_dota', 'replays_dota_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);	
	}
	$winner = null;
	if (isset($_POST['plugin_dota_winner']))
	{
		$winner = intval($_POST['plugin_dota_winner']);
	}
	return replays_plugins_update_dota($plugin, $replays_id, $plugin_row['replays_dota_id'], $replay, $winner);
} // function replays_plugins_edit_dota

/**
 * Update: called from Create and Edit
 */
function replays_plugins_update_dota($plugin, $replays_id, $plugin_id, $replay, $winner)
{
	global $cs_main;
	
	$replaydota = array();
	// Mapname
	$mapname = substr($replay->game['map'],5,strlen($replay->game['map'])-5);
	if (substr($mapname,0,10) == 'Downloads\\')
		$mapname = substr($mapname,15);
	else if (substr($mapname,0,9) == 'Download\\')
		$mapname = substr($mapname,14);

	$replaydota['replays_dota_w3type'] = ''.$replay->header['ident'];
	$replaydota['replays_dota_version'] = 'v1.'.sprintf('%02d', $replay->header['major_v']);
	$replaydota['replays_dota_length'] = ''.$replay->header['length'] / 1000;
	$replaydota['replays_dota_gametype'] = ''.$replay->game['type'];
	$replaydota['replays_dota_mapname'] = cs_encode($mapname);
	$replaydota['replays_dota_gateway'] = ''; // TODO
	$replaydota['replays_dota_hoster'] = ''.$replay->game['creator'];
	$replaydota['replays_dota_saver'] = ''.$replay->game['saver_name'];
	$replaydota['replays_dota_observers'] = ''.serialize($replay->observers);
	$replaydota['replays_dota_bans'] = ''.serialize($replay->bans);
	$replaydota['replays_dota_picks'] = ''.serialize($replay->picks);
	$replaydota['replays_dota_chat_log'] = ''.base64_encode(gzcompress(serialize(($replay->chat)),8));
	
	$teams = array(0 => $replay->teams[0], 1 => $replay->teams[1]);
	foreach ($teams[0] as $pid => $player)
	{	
		$player['heroes'] = array();
		// Convert 1.2 version to legacy (1.1) output
		if (isset($replay->ActivatedHeroes))
		{
    	$t_heroName = $replay->stats[$player['dota_id']]->getHero()->getName();
 
			// Set level
			$player['heroes'][$t_heroName]['level'] = $replay->stats[$player['dota_id']]->getHero()->getLevel();
 
			$t_heroSkills = $replay->stats[$player['dota_id']]->getHero()->getSkills();
 
			// Convert skill array to old format
			foreach ($t_heroSkills as $time => $skill)
			{
     		$player['heroes'][$t_heroName]['abilities'][$time] = $skill;
 			}
 
 			$player['heroes'][$t_heroName]['data'] = $replay->stats[$player['dota_id']]->getHero()->getData();
		}
    if (isset($replay->stats[$player['dota_id']]))
			$player['stats'] = $replay->stats[$player['dota_id']];
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_details'] = ''.serialize($player);
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_team'] = 0;
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_name'] = $player['name'];
		$teams[0][$pid] = $player;
	}
	foreach ($teams[1] as $pid => $player)
	{	
		$player['heroes'] = array();
		// Convert 1.2 version to legacy (1.1) output
		if (isset($replay->ActivatedHeroes))
		{
    	$t_heroName = $replay->stats[$player['dota_id']]->getHero()->getName();
 
			// Set level
			$player['heroes'][$t_heroName]['level'] = $replay->stats[$player['dota_id']]->getHero()->getLevel();
 
			$t_heroSkills = $replay->stats[$player['dota_id']]->getHero()->getSkills();
 
			// Convert skill array to old format
			foreach ($t_heroSkills as $time => $skill)
			{
     		$player['heroes'][$t_heroName]['abilities'][$time] = $skill;
 			}
 
 			$player['heroes'][$t_heroName]['data'] = $replay->stats[$player['dota_id']]->getHero()->getData();
		}
    if (isset($replay->stats[$player['dota_id']]))
			$player['stats'] = $replay->stats[$player['dota_id']];
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_details'] = ''.serialize($player);
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_team'] = 1;
		$replaydota['replays_dota_slot'.sprintf('%02d', intval($pid)).'_name'] = $player['name'];
		$teams[1][$pid] = $player;
	}
	$replaydota['replays_dota_team01_race'] = 'SE'; // Sentinel
	$replaydota['replays_dota_team01_details'] = ''.serialize($teams[0]);
	$replaydota['replays_dota_team01_name'] = replays_plugins_dota_team_name($replaydota, 1, false);
	$replaydota['replays_dota_team02_race'] = 'SC'; // Scourge
	$replaydota['replays_dota_team02_details'] = ''.serialize($teams[1]);
	$replaydota['replays_dota_team02_name'] = replays_plugins_dota_team_name($replaydota, 2, false);
	
	if (is_null($winner) || $winner == -2)
	{
		/* autodetermine */
		// Winnerteam
		$winner = -1;
		if (isset($replay->game['winner_team']))
		{
			if ($replay->game['winner_team'] != 'tie')
				$winner = intval($replay->game['winner_team']);
		}
		else if (isset($replay->game['looser_team']))
		{
			if ($replay->game['looser_team'] != 'tie')
				$winner = intval($replay->game['looser_team']);
		}
		$replaydota['replays_dota_winner'] = $winner;
	}
	else
	{
		/* fixed */
		$replaydota['replays_dota_winner'] = $winner;
	}
	

	/* save dota info */
	cs_sql_update(__FILE__, 'replays_dota', array_keys($replaydota), array_values($replaydota), $plugin_id, 0);

	if (!empty($plugin['options']['overwrite']))
	{
		/* overwrite replays table info of teams, version and map */
		$which = explode(',', $plugin['options']['overwrite']);
		$csreplay = array();
		if (in_array('map', $which))
			$csreplay['replays_map'] = $replaydota['replays_dota_mapname'];
		if (in_array('version', $which))
			$csreplay['replays_version'] = $replaydota['replays_dota_version'];
		if (in_array('team', $which))
		{
			$plugin_row = cs_sql_select(__FILE__, 'replays_dota', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
			$csreplay['replays_team1'] = replays_plugins_dota_team_name($plugin_row, 1, true);
			$csreplay['replays_team2'] = replays_plugins_dota_team_name($plugin_row, 2, true);
		}
		if (count($csreplay))
		{
			/* save replays info */
			cs_sql_update(__FILE__, 'replays', array_keys($csreplay), array_values($csreplay), $replays_id, 0);
		}
	}

	return true;
} // function replays_plugins_update_dota

/**
 * Remove
 */
function replays_plugins_remove_dota($plugin, $replays_id)
{
	cs_sql_delete(__FILE__, 'replays_dota', $replays_id, 'replays_id');
	return true;
} // function replays_plugins_remove_dota

/**
 * Show extra options as HTML 2-column rows
 */
function replays_plugins_extra_options_dota($plugin)
{
	$cs_lang = cs_translate('replays_dota');
	
	$data = array();
	$data['plugins']['fullname'] = $plugin['name'];
	$data['loption']['overwrite'] = $cs_lang['overwrite'];
	$data['option']['overwrite'] = $plugin['options']['overwrite'];
	$data['loption']['color_sentinel'] = $cs_lang['color_sentinel'];
	$data['option']['color_sentinel'] = $plugin['options']['color_sentinel'];
	$data['loption']['color_scourge'] = $cs_lang['color_scourge'];
	$data['option']['color_scourge'] = $plugin['options']['color_scourge'];
	$data['loption']['example'] = $cs_lang['example'];
	return cs_subtemplate(__FILE__,$data,'replays','options_dota');
} // function replays_plugins_extra_options_dota

/**
 * Parse extra options from POST request
 */
function replays_plugins_extra_options_parse_dota($plugin)
{
	$options = array();
	$avoptions = array('version', 'map', 'team');
	$newoptions = array();
	$overoptions = explode(',', $_POST['plugin_dota_overwrite']);
	foreach ($overoptions as $ooption)
	{
		if (in_array(trim($ooption), $avoptions))
			$newoptions[] = trim($ooption);
	}
	$options['overwrite'] = implode(',', $newoptions);
	if (replays_plugins_extra_options_parse_dota_hexcolor_check($_POST['plugin_dota_color_sentinel']))
		$options['color_sentinel'] = strtoupper($_POST['plugin_dota_color_sentinel']);
	if (replays_plugins_extra_options_parse_dota_hexcolor_check($_POST['plugin_dota_color_scourge']))
		$options['color_scourge'] = strtoupper($_POST['plugin_dota_color_scourge']);
	return $options;
} // function replays_plugins_extra_options_parse_dota

/**
 * Check hexcolor
 */
function replays_plugins_extra_options_parse_dota_hexcolor_check($color)
{
	if (strlen($color) != 6)
		return false;
	if (strspn(strtoupper($color), '0123456789ABCDEF') != 6)
		return false;
	return true;
} // function replays_plugins_extra_options_parse_dota_hexcolor_check

?>
