<?php
	/**
   * Get all plugins that can handle this type of game
	 *
	 * @param array $plugins
	 * @param int $game_id
	 *
	 * @return array of capable plugins
   */
	function replays_plugins_get($plugins, $game_id)
	{
		$sup_plugins = array();
		foreach ($plugins as $plugin => $plugindata)
		{
			if (isset($plugindata['options']['games_ids']))
			{
				$games = explode(',', $plugindata['options']['games_ids']);
				if (in_array($game_id, $games))
				{
					$sup_plugins[$plugin] = $plugindata;
				}
			}
		}
		return $sup_plugins;
	} // function replays_plugins_get
	
	/**
	 * 
	 */
	function replays_plugins_create($plugins, $useplugins, $replays_id)
	{
		/* call each active plugin after creation of the replay */
		foreach ($useplugins as $plugin)
		{
			if (isset($plugins[$plugin]) && file_exists('mods/replays/plugins/'.$plugin.'/functions.php'))
			{
				require_once('mods/replays/plugins/'.$plugin.'/functions.php');
				$function_name = 'replays_plugins_create_'.$plugin;
				if (function_exists($function_name))
				{
					/* do your stuff for this plugin */
					call_user_func_array($function_name, array($plugins[$plugin], $replays_id));
				}
			}
		}
	} // function replays_plugins_create
	
	/**
	 * 
	 */
	function replays_plugins_edit($plugins, $useplugins, $replays_id)
	{
		/* call each active plugin after edit of the replay */
		foreach ($useplugins as $plugin)
		{
			if (isset($plugins[$plugin]) && file_exists('mods/replays/plugins/'.$plugin.'/functions.php'))
			{
				require_once('mods/replays/plugins/'.$plugin.'/functions.php');
				$function_name = 'replays_plugins_edit_'.$plugin;
				if (function_exists($function_name))
				{
					/* do your stuff for this plugin */
					call_user_func_array($function_name, array($plugins[$plugin], $replays_id));
				}
			}
		}
	} // function replays_plugins_edit

	/**
	 * 
	 */
	function replays_plugins_remove($plugins, $useplugins, $replays_id)
	{
		/* call each active plugin after removal of the replay */
		foreach ($useplugins as $plugin)
		{
			if (isset($plugins[$plugin]) && file_exists('mods/replays/plugins/'.$plugin.'/functions.php'))
			{
				require_once('mods/replays/plugins/'.$plugin.'/functions.php');
				$function_name = 'replays_plugins_remove_'.$plugin;
				if (function_exists($function_name))
				{
					/* do your stuff for this plugin */
					call_user_func_array($function_name, array($plugins[$plugin], $replays_id));
				}
			}
		}
	} // function replays_plugins_remove

?>
