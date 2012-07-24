<?php 
$cs_lang = cs_translate('teamspeak');

$data = array();
$data['if']['view_1'] = 1;
$data['if']['view_2'] = 0;
$data['link']['teamspeak_manage'] = cs_link($cs_lang['head_options'],'teamspeak','manage');

if(!empty($_POST['submit'])) {
	$data['if']['view_1'] = 0;
	$data['if']['view_2'] = 1;
	$opt_where = "options_mod = 'teamspeak' AND options_name = ";
	$def_cell = array('options_value');
	$def_cont = array($_POST);
	$save = array();
	$save['timeout'] = intval($_POST['timeout']);
	$save['player_flags'] = empty($_POST['player_flags']) ? 0 : 1;
	$save['channel_flags'] = empty($_POST['channel_flags']) ? 0 : 1;
	$save['show_empty'] = empty($_POST['show_empty']) ? 0 : 1;
	$save['show_empty_navlist'] = empty($_POST['show_empty_navlist']) ? 0 : 1;

  require_once 'mods/clansphere/func_options.php';
	
	cs_optionsave('teamspeak', $save);

} else {
	$options = cs_sql_option(__FILE__,'teamspeak');
	$sel = 'selected="selected"';
	$data['options']['player_flags_0'] = $options['player_flags'] == '0' ? $sel : '';
	$data['options']['player_flags_1'] = $options['player_flags'] == '1' ? $sel : '';
	$data['options']['channel_flags_0'] = $options['channel_flags'] == '0' ? $sel : '';
	$data['options']['channel_flags_1'] = $options['channel_flags'] == '1' ? $sel : '';
	$sel = 'checked';
	$data['options']['show_empty_no'] = (intval($options['show_empty']) == 0 ? $sel : '');
	$data['options']['show_empty_yes'] = (intval($options['show_empty']) == 1 ? $sel : '');
	$sel = 'checked';
	$data['options']['show_empty_navlist_no'] = (intval($options['show_empty_navlist']) == 0 ? $sel : '');
	$data['options']['show_empty_navlist_yes'] = (intval($options['show_empty_navlist']) == 1 ? $sel : '');
	$data['options']['timeout'] = $options['timeout'];
}

echo cs_subtemplate(__FILE__,$data,'teamspeak','options');
?> 
