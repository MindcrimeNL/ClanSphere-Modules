<?php
function cs_servers_dns($host) {
	return gethostbyname($host); 
} // function cs_servers_dns

function cs_servers_mapname($map_name)
{
	global $cs_main;

	/* full path to the map picture */
	$map_name = str_replace(array(' ', '\'', '&#039;', '"'), '', $map_name);
	if (function_exists('mb_strtolower'))
		$map_name = mb_strtolower($map_name, $cs_main['charset']);
	else
		$map_name = strtolower($map_name);

	return $map_name;
} // function cs_servers_mapname

function cs_servers_map($map_path, $map_name, $games_id)
{
	$map_name = cs_servers_mapname($map_name);

	$mname = $map_path . '/' . $map_name . '.jpg';
	if (file_exists($mname))
		return $mname;

	/* fetch games_id and server_name */
	$map = cs_sql_select(__FILE__,'maps','*','games_id = '.intval($games_id).' AND server_name = \''.cs_sql_escape($map_name).'\'', 0, 0, 1);
	if (isset($map['maps_id']) && !empty($map['maps_picture']))
	{
		$mname = 'uploads/maps/'.$map['maps_picture'];
		if (file_exists($mname))
			return $mname;
	}

 	return $map_path . '/default.jpg';
} // function cs_servers_map
