<?php

$cs_lang = cs_translate('coins');

$coins_form = 1;
$coins_id = (int) $_REQUEST['id'];

if(isset($_POST['agree'])) {
	$coins_form = 0;
  cs_sql_delete(__FILE__,'coins',$coins_id);
  cs_redirect($cs_lang['del_true'], 'coins');
}

if(isset($_POST['cancel'])) {
  cs_redirect($cs_lang['del_false'], 'coins');
}

if(!empty($coins_form)) {
  $data['lang']['body'] = sprintf($cs_lang['del_rly'],$coins_id);
  $data['action']['form'] = cs_url('coins','remove');
  $data['coins']['id'] = $coins_id;
  
  echo cs_subtemplate(__FILE__,$data,'coins','remove');
}
?>
