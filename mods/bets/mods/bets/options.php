<?php
// ClanSphere 2009 - www.clansphere.net
// $Id: options.php
$cs_lang = cs_translate('bets');

if(isset($_POST['submit'])) {
  
  $save = array();
  $save['base_fee'] = (float) $_POST['base_fee'];
	/* do not let base_fee drop below zero */
	if ($save['base_fee'] < 0.0)
		$save['base_fee'] = 0.0;
  $save['pointsname'] = $_POST['pointsname'];
  $save['auto_title'] = !empty($_POST['auto_title']) ? '1' : '0';
  $save['auto_title_separator'] = trim($_POST['auto_title_separator']);
  $save['quote_type'] = (int) $_POST['quote_type'];
  $save['max_navlist'] = (int) $_POST['max_navlist'];
  $save['max_navlist_title'] = (int) $_POST['max_navlist_title'];
  $save['remove_quote'] = (int) $_POST['remove_quote'];
  $save['max_quote'] = (float) str_replace(',', '.', $_POST['max_quote']);
  $save['min_quote'] = (float) str_replace(',', '.', $_POST['min_quote']);
  $save['win_quote'] = (float) str_replace(',', '.', $_POST['super_quote']);
  $save['coins_receive'] = (float) str_replace(',', '.', $_POST['coins_receive']);
  $save['coins_min_length'] = (int) $_POST['coins_min_length'];
  $save['date_format'] = $_POST['date_format'];
  
  require_once 'mods/clansphere/func_options.php';
  cs_optionsave('bets', $save);

  cs_redirect($cs_lang['changes_done'], 'options', 'roots');
  
} else {
  
  $data = array();
  $data['com'] = cs_sql_option(__FILE__,'bets');
  $data['com']['quote_type_options'] = '';
  for ($i = 0; $i <= 2; $i++)
  {
  	$data['com']['quote_type_options'] .= cs_html_option($cs_lang['quote_type_'.$i], $i, $i == $data['com']['quote_type']);
  }

  $data['com']['date_format_example'] = date($data['com']['date_format']);

  $coins_lang = cs_translate('coins');
	$data['com']['coins_receive_text'] = $coins_lang['coins_receive'];
	$data['com']['coins_min_length_text'] = $coins_lang['coins_min_length_text'];

	if (!empty($data['com']['auto_title']))
	{
		$data['com']['auto_title_enable'] = ' checked="checked"';
		$data['com']['auto_title_disable'] = '';
	}
	else
	{
		$data['com']['auto_title_enable'] = '';
		$data['com']['auto_title_disable'] = ' checked="checked"';
	}

  echo cs_subtemplate(__FILE__,$data,'bets','options');

}

?>
