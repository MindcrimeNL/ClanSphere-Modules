<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('board');

$cs_board = cs_sql_option(__FILE__,'board');
$board_form = 1;

$data['lang']['getmsg'] = cs_getmsg();

if(isset($_POST['submit'])) {  
  
  $board_form = 0;
  
  $save = array();
  $save['max_text'] = $_POST['max_text'] < 65000 ? (int) $_POST['max_text'] : 65000;
  $save['max_signatur'] = (int) $_POST['max_signatur'];
  $save['avatar_width'] = (int) $_POST['avatar_width'];
  $save['avatar_height'] = (int) $_POST['avatar_height'];
  $save['avatar_size'] = (int) $_POST['avatar_size'] * 1024;
  $save['file_size'] = (int) $_POST['file_size'] * 1024;
  $save['file_types'] = $_POST['file_types'];
  $save['sort'] = $_POST['sort'];
  $save['doubleposts'] = empty($_POST['doublep_allowed']) ? -1 : (int) (86400 * str_replace(',','.',$_POST['doubleposts']));
  $save['list_subforums'] = empty($_POST['list_subforums']) ? 0 : 1;
  $save['max_navlist'] = (int) $_POST['max_navlist'];
  $save['max_headline'] = (int) $_POST['max_headline'];
  $save['max_navtop'] = (int) $_POST['max_navtop'];
  $save['max_navtop2'] = (int) $_POST['max_navtop2'];
  
  $save['coins_receive'] = (float) str_replace(',', '.', $_POST['coins_receive']);
  $save['coins_receive_thread'] = (float) str_replace(',', '.', $_POST['coins_receive_thread']);
  $save['coins_min_length'] = (int) $_POST['coins_min_length'];
	$save['coins_checkbox_thread'] = empty($_POST['coins_checkbox_thread']) ? 0 : 1;

  require_once 'mods/clansphere/func_options.php';
  
  cs_optionsave('board', $save);

  cs_redirect($cs_lang['success'], 'options','roots');
  
}

if(!empty($board_form)) {  
  $data['action']['form'] = cs_url('board','options');
  
  $size = $cs_board['avatar_size'] / 1024;
  $size2 = $cs_board['file_size'] / 1024;
  
  $data['options']['max_text'] = $cs_board['max_text'];
  $data['options']['max_signatur'] = $cs_board['max_signatur'];
  $data['options']['max_navlist'] = $cs_board['max_navlist'];
  $data['options']['max_high'] = $cs_board['avatar_height'];
  $data['options']['max_avatar_width'] = $cs_board['avatar_width'];
  $data['options']['max_avatar_size'] = $size;
  $data['options']['max_filesize'] = $size2;
  $data['options']['filetypes'] = $cs_board['file_types'];
  $data['options']['max_navlist'] = $cs_board['max_navlist'];
  $data['options']['max_headline'] = $cs_board['max_headline'];
  $data['options']['max_navtop'] = $cs_board['max_navtop'];
  $data['options']['max_navtop2'] = $cs_board['max_navtop2'];

  
  if($cs_board['sort'] == 'DESC') {
    $data['check']['desc'] = 'selected="selected"';
  }
  else {
    $data['check']['desc'] = '';
  }
  
  if($cs_board['sort'] == 'ASC') {
    $data['check']['asc'] = 'selected="selected"';
  }
  else {
    $data['check']['asc'] = '';
  }
  
  if($cs_board['doubleposts'] == -1) {
    $doubleposts = 0;
    $display = 'none';
    $checked = '';
  }
  else {
    $display = 'block';
    $doubleposts = $cs_board['doubleposts'] / 86400;
    $checked = 'checked="checked"';
  }
  
  $data['options']['double_posts'] = $checked;
  $data['options']['display'] = $display;
  $data['options']['doubleposts'] = $doubleposts;
  
  $data['options']['list_subforums'] = empty($cs_board['list_subforums']) ? '' : ' checked="checked"';

	$coins_lang = cs_translate('coins');
	$data['options']['coins_receive_text'] = $coins_lang['coins_receive'];
	$data['options']['coins_receive'] = $cs_board['coins_receive'];
	$data['options']['coins_receive_thread_text'] = $coins_lang['coins_receive_thread'];
	$data['options']['coins_receive_thread'] = $cs_board['coins_receive_thread'];
	$data['options']['coins_min_length_text'] = $coins_lang['coins_min_length_text'];
	$data['options']['coins_min_length'] = $cs_board['coins_min_length'];
	$data['options']['coins_checkbox_thread'] = empty($cs_board['coins_checkbox_thread']) ? '' : ' checked="checked"';
	$data['options']['coins_checkbox_thread_text'] = $coins_lang['coins_checkbox_thread'];
}

echo cs_subtemplate(__FILE__,$data,'board','options');
