<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('datacache');

$datacache_form = 1;
$datacache_id = intval($_REQUEST['datacache_id']);

if (isset($_GET['agree']))
{
	$datacache_form = 0;
	cs_sql_delete(__FILE__,'datacache',$datacache_id);
	cs_redirect($cs_lang['del_true'], 'datacache');
}

if (isset($_GET['cancel']))
	cs_redirect($cs_lang['del_false'], 'datacache');

if (!empty($datacache_form))
{
	$data = array();
	$data['head']['topline'] = sprintf($cs_lang['remove_rly'],$datacache_id);
  $data['datacache']['content'] = cs_link($cs_lang['confirm'],'datacache','remove','datacache_id=' . $datacache_id . '&amp;agree');
  $data['datacache']['content'] .= ' - ';
  $data['datacache']['content'] .= cs_link($cs_lang['cancel'],'datacache','remove','datacache_id=' . $datacache_id . '&amp;cancel');

	echo cs_subtemplate(__FILE__,$data,'datacache','remove');
}

?>
