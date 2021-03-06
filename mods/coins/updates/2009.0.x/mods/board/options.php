<?php
// ClanSphere 2009 - www.clansphere.net
// $Id: options.php 3009 2009-05-03 14:57:11Z hajo $

$cs_lang = cs_translate('board');

$cs_board = cs_sql_option(__FILE__,'board');
$board_form = 1;

$data['lang']['getmsg'] = cs_getmsg();

if(isset($_POST['submit'])) {  
  
  $board_form = 0;
  
  $save = array();
  $save['max_text'] = $_POST['max_text'] < 65000 ? (int) $_POST['max_text'] : 65000;
  $save['max_signatur'] = $_POST['max_signatur'];
  $save['avatar_width'] = $_POST['avatar_width'];
  $save['avatar_height'] = $_POST['avatar_height'];
  $save['avatar_size'] = $_POST['avatar_size'] * 1024;
  $save['file_size'] = $_POST['file_size'] * 1024;
  $save['file_types'] = $_POST['file_types'];
  $save['sort'] = $_POST['sort'];
  $save['doubleposts'] = empty($_POST['doublep_allowed']) ? -1 : (int) (86400 * str_replace(',','.',$_POST['doubleposts']));
  $save['list_subforums'] = empty($_POST['list_subforums']) ? 0 : 1;
  
  $save['coins_receive'] = (float) str_replace(',', '.', $_POST['coins_receive']);
  $save['coins_receive_thread'] = (float) str_replace(',', '.', $_POST['coins_receive_thread']);
  $save['coins_min_length'] = (int) $_POST['coins_min_length'];

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
  $data['options']['max_high'] = $cs_board['avatar_height'];
  $data['options']['max_avatar_width'] = $cs_board['avatar_width'];
  $data['options']['max_avatar_size'] = $size;
  $data['options']['max_filesize'] = $size2;
  $data['options']['filetypes'] = $cs_board['file_types'];

  
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
    $visibility = 'hidden';
    $checked = '';
  }
  else {
    $visibility = 'visible';
    $doubleposts = $cs_board['doubleposts'] / 86400;
    $checked = 'checked="checked"';
  }
  
  $data['options']['double_posts'] = $checked;
  $data['options']['visible'] = $visibility;
  $data['options']['doubleposts'] = $doubleposts;
  
  $data['options']['list_subforums'] = empty($cs_board['list_subforums']) ? '' : ' checked="checked"';

	$coins_lang = cs_translate('coins');
	$data['options']['coins_receive_text'] = $coins_lang['coins_receive'];
	$data['options']['coins_receive'] = $cs_board['coins_receive'];
	$data['options']['coins_receive_thread_text'] = $coins_lang['coins_receive_thread'];
	$data['options']['coins_receive_thread'] = $cs_board['coins_receive_thread'];
	$data['options']['coins_min_length_text'] = $coins_lang['coins_min_length_text'];
	$data['options']['coins_min_length'] = $cs_board['coins_min_length'];
}

echo cs_subtemplate(__FILE__,$data,'board','options');
