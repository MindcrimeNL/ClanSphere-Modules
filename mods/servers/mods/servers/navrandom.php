<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: navlist.php 4424 2010-08-22 12:11:49Z Fr33z3m4n $

$cs_lang = cs_translate('servers');

$id = empty($_GET['sid']) ? '' : (int) $_GET['sid'];

$data = array('servers' => array());

// Test if fsockopen active
if (fsockopen("udp://127.0.0.1", 1)) {
	include_once 'mods/servers/servers.php';

	/* Get Server SQL-Data */
	$select = 'servers_name, servers_ip, servers_port, servers_info, servers_query, servers_class, servers_stats, servers_order, servers_id, games_id, servers_rcon';
	$order = 'Rand()';
	$cs_servers = cs_sql_select(__FILE__,'servers',$select,0,$order,0,1);

	/* Settings */
	$objServers = Servers::__getInstance();

	$data['servers']['if']['live'] = false;
	$data['servers']['hostname'] = $cs_servers['servers_name'];
	$server_query_ex = explode(";",$cs_servers['servers_class']);
	$cs_servers['servers_class'] = $server_query_ex[0];
	$cs_servers['servers_game'] = $server_query_ex[1];
	if(!empty($cs_servers['servers_stats'])) {
		include_once 'mods/servers/functions.php';

		$objServers->addServer(0, $cs_servers);
		$results = $objServers->requestData();
		$server = $results[0];

		if(!empty($server['gq_online'])) {
			$data['if']['live'] = true;
			$data['servers']['map'] = '';
			$data['servers']['mappic'] = 'uploads/servers/no_response.jpg';
			$data['servers']['mapname'] = '';
			$data['servers']['max_players'] = isset($server['max_players']) ? $server['max_players'] : 0;
			$data['servers']['num_players'] = isset($server['num_players']) ? $server['num_players'] : 0;
			$data['servers']['game_descr'] = isset($server['game_descr']) ? $server['game_descr'] : '';

			if(!empty($cs_servers['servers_stats'])) {
				$data['servers']['servers_ip'] = $cs_servers['servers_ip'];
				$data['servers']['servers_port'] = $cs_servers['servers_port'];
				if(isset($server['gamename']) AND !empty($server['gamename'])) {
					$data['servers']['game_descr'] = $server['gamename'];
				}

				if(isset($server['map']) && !empty($server['map'])) {
					$data['servers']['map'] = $server['map'];
					$data['servers']['mappic'] = cs_servers_map('uploads/servers/' . $cs_servers['servers_game'], $data['servers']['map'], $cs_servers['games_id']);
				}
				elseif(isset($server['mapname']) && !empty($server['mapname'])) {
					$data['servers']['map'] = $server['mapname'];
					$data['servers']['mappic'] = cs_servers_map('uploads/servers/' . $cs_servers['servers_game'], $data['servers']['mapname'], $cs_servers['games_id']);
				}

				if(!isset($server['max_players'])) {
					if(isset($server['sv_maxclients'])) {
						$data['servers']['max_players'] = $server['sv_maxclients'];
					}
				}
				if(!isset($server['num_players'])) {
					if(isset($server['clients'])) {
						$data['servers']['num_players'] = $server['clients'];
					}
				}

				$data['servers'] = $objServers->setProtocolLink($cs_servers, $data['servers']);
				$data['servers']['pass'] = empty($data['servers']['pass']) ? $cs_lang['no'] : $cs_lang['yes'];
				$data['servers']['id'] = $cs_servers['servers_id'];
				flush();
			}
		}
	}
}

echo cs_subtemplate(__FILE__,$data,'servers','navrandom');
