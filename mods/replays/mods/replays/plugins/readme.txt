

Every plugin must be in a directory XXX which contains:
- info.php file with a $plugin_info array which contains at least the following information:
	$plugin_info['name'] = name of the plugin
- function.php file with at least the following functions:
	replays_plugins_info_XXX(): provides some short extra info about the current settings of the plugin
	replays_plugins_create_XXX(): this function will be called when creating a replay
	replays_plugins_edit_XXX(): this function will be called when editing a replay
	replays_plugins_edit_extra_XXX(): this function will be called when editing a replay, it will show possible extra options you can edit
	replays_plugins_remove_XXX(): this function will be called when removing a replay
	replays_plugins_extra_options_XXX(): this function will be called to show a couple of html rows of extra options
	replays_plugins_extra_options_parse_XXX(): this function will be called to parse the posted extra options
	replays_plugins_navlist_XXX(): this function will be called to show extra data for the navlist
	replays_plugins_view_XXX(): this function will be called to show a couple of html rows for the view
	// replays_plugins_search_XXX(): this function will be called to show a couple of html rows for search purposes
	// replays_plugins_search_parse_XXX(): this function will be called to parse the posted search options
	
To be able to use the plugin you must also perform the following query (where XXX should be substituted for your XXX directory name):

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('replays_XXX', 'games_ids', '');

To be able to use the plugin within the replays you must add 'XXX' it to the 'plugins' in System -> Options -> Replays.

