<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('bets');

require_once('mods/bets/class_bet.php');

if(isset($_POST['submit'])) {
		
	$myBet = new cs_bet((int)$_POST['bets_id']);
	$myBet->removePlacedBet((int)$account['users_id']);
	
	if(empty($myBet->error)) {	
		$msg  = $cs_lang['remove_placed_done'];
		cs_redirect($msg,'bets','view', 'id='.$_POST['bets_id'].'&refresh=true');
	}
	else {
		cs_redirect($cs_lang['del_false'].cs_html_br(1).$myBet->error , 'bets', 'view', 'id='.$_POST['bets_id']);	
	}
}

?>
