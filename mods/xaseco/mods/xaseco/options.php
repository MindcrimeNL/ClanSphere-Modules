<?php
$cs_lang = cs_translate('xaseco');

if(isset($_POST['submit'])) {
  
  $save = array();
  $save['bgcolor'] = $_POST['bgcolor'];

  require 'mods/clansphere/func_options.php';
  cs_optionsave('xaseco', $save);

  cs_redirect($cs_lang['changes_done'], 'options', 'roots');
  
} else {
  
  $data = array();
  $data['com'] = cs_sql_option(__FILE__,'xaseco');

  echo cs_subtemplate(__FILE__,$data,'xaseco','options');
}

?>
