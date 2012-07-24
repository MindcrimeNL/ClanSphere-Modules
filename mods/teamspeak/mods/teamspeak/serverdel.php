<?php
$cs_lang = cs_translate('teamspeak');

$serverdel_form = 1;
$teamspeak_id = intval($_REQUEST['teamspeakid']);

if (isset($_GET['agree']))
{
	$serverdel_form = 0;
	cs_sql_delete(__FILE__,'teamspeak', $teamspeak_id);
	cs_redirect($cs_lang['del_true'], 'teamspeak');
}

if (isset($_GET['cancel']))
	cs_redirect($cs_lang['del_false'], 'teamspeak');

if (!empty($serverdel_form))
{
	$data = array();
  $data['head']['topline'] = sprintf($cs_lang['remove_rly'],$teamspeak_id);
  $data['teamspeak']['content'] = cs_link($cs_lang['confirm'],'teamspeak','serverdel','teamspeakid=' . $teamspeak_id . '&amp;agree');
  $data['teamspeak']['content'] .= ' - ';
  $data['teamspeak']['content'] .= cs_link($cs_lang['cancel'],'teamspeak','serverdel','teamspeakid=' . $teamspeak_id . '&amp;cancel');

	echo cs_subtemplate(__FILE__,$data,'teamspeak','serverdel');
}

?>
