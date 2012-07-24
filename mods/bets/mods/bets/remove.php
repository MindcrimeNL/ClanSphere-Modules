<?php
// ClanSphere 2009 - www.clansphere.net 
// $Id: remove.php 2266 2009-03-21 10:37:39Z duRiel $

$cs_lang = cs_translate('bets');

$data = array();

require_once('mods/bets/class_bet.php');

if(empty($_REQUEST['bets_id'])) {
  $data['bets']['action'] = $cs_lang['remove'];
  $data['bets']['link'] = cs_url('bets','manage');
  echo cs_subtemplate(__FILE__,$data,'bets','no_selection');
  
} elseif (empty($_POST['agree']) AND empty($_POST['cancel'])) {
	$id = (int) $_GET['bets_id'];

  $data['bets']['action'] = cs_url('bets','remove');
  $data['bets']['bets_id'] = $id;
  $bets = cs_sql_select(__FILE__,'bets','*','bets_id = ' . $id);

	$options = '';
	if (cs_bet::STATUS_FINISHED == $bets['bets_status'])
	{
		$options .= cs_html_option($cs_lang['rollback_0'], cs_bet::ROLLBACK_ALL, true);
	}
	$options .= cs_html_option($cs_lang['rollback_1'], cs_bet::ROLLBACK_BET, $bets['bets_status'] != cs_bet::STATUS_FINISHED);
	$options .= cs_html_option($cs_lang['rollback_2'], cs_bet::ROLLBACK_NONE, false);

  $data['bets']['rollback_options'] = $options;
  $data['bets']['message'] = sprintf($cs_lang['really'],$bets['bets_title']);
  echo cs_subtemplate(__FILE__,$data,'bets','remove');
  
} else {
  if (isset($_POST['agree'])) {
  	$id = (int) $_POST['bets_id'];
  	$bets = cs_sql_select(__FILE__,'bets','*','bets_id = ' . $id);

		//decide on returning bets
		$option = (int) $_POST['rollback_option'];
		if (($option == cs_bet::ROLLBACK_ALL && $bets['bets_status'] != cs_bet::STATUS_FINISHED)
				|| ($option < cs_bet::ROLLBACK_ALL || $option > cs_bet::ROLLBACK_NONE))
		{
      cs_redirect($cs_lang['invalid_rollback_option'], 'bets');
		}
		$myBet = new cs_bet($id);
		$myBet->deleteBet($option);

		if (!empty($myBet->error))
 	   cs_redirect($myBet->error, 'bets');
 		cs_redirect($cs_lang['del_true'], 'bets');
  } 
  if (isset($_POST['cancel'])) 
      cs_redirect($cs_lang['del_false'], 'bets');
}

?>
