<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('comments');

if (isset($_POST['submit'])) {
  
	require_once 'mods/clansphere/func_options.php';
	
  $save['show_avatar'] = $_POST['show_avatar'];
  $save['allow_unreg'] = $_POST['allow_unreg'];
  $save['coins_receive'] = (float) str_replace(',', '.', $_POST['coins_receive']);
  $save['coins_min_length'] = (int) $_POST['coins_min_length'];
  
  cs_optionsave('comments', $save);
  
  cs_redirect($cs_lang['success'],'options','roots');
}

$data = array();

$options = cs_sql_option(__FILE__,'comments');

$checked = ' checked="checked"';
$data['checked']['show_avatar'] = empty($options['show_avatar']) ? '' : $checked;
$data['checked']['show_avatar_no'] = !empty($options['show_avatar']) ? '' : $checked;

$data['checked']['allow_unreg'] = empty($options['allow_unreg']) ? '' : $checked;
$data['checked']['allow_unreg_no'] = !empty($options['allow_unreg']) ? '' : $checked;

$coins_lang = cs_translate('coins');
$data['op']['coins_receive_text'] = $coins_lang['coins_receive'];
$data['op']['coins_receive'] = $options['coins_receive'];
$data['op']['coins_min_length_text'] = $coins_lang['coins_min_length_text'];
$data['op']['coins_min_length'] = $options['coins_min_length'];

echo cs_subtemplate(__FILE__, $data, 'comments','options');
