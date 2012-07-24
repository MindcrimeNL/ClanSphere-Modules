<?php

require_once('mods/replays/plugins/functions.php');

$op_plugin_replays = cs_sql_option(__FILE__,'replays');

$plugins = array();
if (!empty($op_plugin_replays['plugins']))
{
	$plugin_names = explode(',', $op_plugin_replays['plugins']);
	if (count($plugin_names))
		$plugin_names = array_map('trim', $plugin_names);
	
	foreach ($plugin_names as $key => $plugin)
	{
		if (empty($plugin))
		{
			unset($plugins[$key]);
			continue;
		}
		if (!is_dir('mods/replays/plugins/'.$plugin))
		{
			unset($plugins[$key]);
			continue;
		}
		if (!is_file('mods/replays/plugins/'.$plugin.'/info.php'))
		{
			unset($plugins[$key]);
			continue;
		}
		include('mods/replays/plugins/'.$plugin.'/info.php');
		$plugins[$plugin] = $plugin_info;
		/* load plugin options */
		$op_plugin = cs_sql_option(__FILE__,'replays_'.$plugin);
		$plugins[$plugin]['options'] = $op_plugin;
	}
	/* now all plugin info is loaded */
}

?>
