<?php

/**
 * Info
 */
function replays_plugins_info_example($plugin)
{
	return 'This is just an example plugin.';
} // replays_plugins_info_example

/**
 * Navlist
 */
function replays_plugins_navlist_example($plugin, $replays_id)
{
	return array();
} // replays_plugins_navlist_example

/**
 * View
 */
function replays_plugins_view_example($plugin, $replays_id)
{
	return '';
} // replays_plugins_view_example

/**
 * Create
 */
function replays_plugins_create_example($plugin, $replays_id)
{
	// do insert
	$ereplay = array(
		'replays_id' => $replays_id,
		// add your other stuff here
		'replay_example_other' => 'hello (insert)'
	);
	cs_sql_insert(__FILE__, 'replays_example', array_keys($ereplay), array_values($ereplay));
	$plugin_row = cs_sql_select(__FILE__, 'replays_example', 'replays_example_id', 'replays_id = '.$replays_id, 0, 0, 1, 0);
} // function replays_plugins_create_example

/**
 * Edit extra
 */
function replays_plugins_edit_extra_example($plugin, $replays_id)
{
	return '';
} // function replays_plugins_edit_example

/**
 * Edit
 */
function replays_plugins_edit_example($plugin, $replays_id)
{
	$plugin_row = cs_sql_select(__FILE__, 'replays_example', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);
	if (!isset($plugin_row['replays_example_id']))
	{
		// we did not use it before, do insert
		$ereplay = array(
			'replays_id' => $replays_id
		);
		cs_sql_insert(__FILE__, 'replays_example', array_keys($ereplay), array_values($ereplay));
		$plugin_row = cs_sql_select(__FILE__, 'replays_example', '*', 'replays_id = '.$replays_id, 0, 0, 1, 0);	
	}
	// do your edit stuff here
	$plugin_row['replay_example_other'] = 'hello (after edit)';
	
	cs_sql_update(__FILE__, 'replays_example', array_keys($plugin_row), array_values($plugin_row), $plugin_row['replays_example_id'], 0);
} // function replays_plugins_edit_example

/**
 * Remove
 */
function replays_plugins_remove_example($plugin, $replays_id)
{
	cs_sql_delete(__FILE__, 'replays_example', $replays_id, 'replays_id');
	return true;
} // function replays_plugins_remove_example

/**
 * Show extra options as HTML 2-column rows
 */
function replays_plugins_extra_options_example($plugin)
{
	$cs_lang = cs_translate('replays_example');
	
	$data = array();
	$data['plugins']['fullname'] = $plugin['name'];
	$data['option']['option1string'] = $plugin['options']['option1string'];
	$data['option']['option2int'] = $plugin['options']['option1string'];
	$data['loption']['option1string'] = $cs_lang['option1string'];
	$data['loption']['option2int'] = $cs_lang['option1string'];
	return cs_subtemplate(__FILE__,$data,'replays','options_example');
} // function replays_plugins_extra_options_example

/**
 * Parse extra options from POST request
 */
function replays_plugins_extra_options_parse_example($plugin)
{
	$options = array();
	$options['option1string'] = $_POST['plugin_example_option1string'];
	$options['option2int'] = (intval($_POST['plugin_example_option2int']) > 0 ? intval($_POST['plugin_example_option2int']) : 0);
	return $options;
} // function replays_plugins_extra_options_parse_example
?>
