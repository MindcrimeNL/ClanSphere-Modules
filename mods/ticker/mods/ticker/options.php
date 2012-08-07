<?php
// Clansphere 2009
// ticker - options.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker');

if(isset($_POST['submit'])) {

  $save = array();
  $save['separator'] = $_POST['separator'];
  $save['stop_mo'] = intval($_POST['stop_mo']) == 1 ? 1 : 0;
  $save['max_news'] = intval($_POST['max_news']);
  $save['max_user'] = intval($_POST['max_user']);
  $save['max_dls'] = intval($_POST['max_dls']);
  $save['max_online'] = intval($_POST['max_online']);
  $save['max_threads'] = intval($_POST['max_threads']);
  $save['max_wars'] = intval($_POST['max_wars']);

  require_once 'mods/clansphere/func_options.php';
  
  cs_optionsave('ticker', $save);

  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear(null, 'ticker');
  
  cs_redirect($cs_lang['changes_done'],'options','roots');

}
else {

  $data = array();
  $data['op'] = cs_sql_option(__FILE__,'ticker');

	if (intval($data['op']['stop_mo']) == 1)
	{
		$data['op']['stopmo_yes'] = ' checked="checked"';
		$data['op']['stopmo_no'] = '';
	}
	else
	{
		$data['op']['stopmo_yes'] = '';
		$data['op']['stopmo_no'] = ' checked="checked"';
	}
	echo cs_subtemplate(__FILE__,$data,'ticker','options');
}

?>
