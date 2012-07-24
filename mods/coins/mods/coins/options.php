<?php
// ClanSphere 2009 - www.clansphere.net
// $Id: options.php
$cs_lang = cs_translate('coins');

if(isset($_POST['submit'])) {
  
  $save = array();
  $save['startcoins'] = (int) $_POST['startcoins'];
  $save['coin_mods'] = $_POST['coin_mods'];
  $save['coin_decimals'] = (int) $_POST['coin_decimals'];
	if ($save['coin_decimals'] < 0)
  	$save['coin_decimals'] = 0;
  require_once 'mods/clansphere/func_options.php';
  cs_optionsave('coins', $save);

  cs_redirect($cs_lang['changes_done'], 'options', 'roots');
  
} else {
  
  $data = array();
  $data['com'] = cs_sql_option(__FILE__,'coins');

  echo cs_subtemplate(__FILE__,$data,'coins','options');

}

?>
