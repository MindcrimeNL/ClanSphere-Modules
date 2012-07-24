<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('bets');

require_once('mods/bets/class_bet.php');

if(isset($_POST['submit'])) {
	
	$amount = str_replace(',', '.', $_POST['amount']);
	$amount = round($amount, 2);
	
	$bets_id = (int) $_POST['bets_id'];
	$myBet = new cs_bet($bets_id);
	$myBet->placeBet((int)$account['users_id'], $amount, (int) $_POST['contestant']);
	
	if(empty($myBet->error))
	{	
		$msg  = $cs_lang['create_done'];
		cs_redirect($msg, 'bets', 'view', 'id='.$bets_id.'&refresh=true');
	}
	else
		cs_redirect($cs_lang['place_failed'].cs_html_br(1).$myBet->error , 'bets', 'view', 'id='.$bets_id);	
}

?>
