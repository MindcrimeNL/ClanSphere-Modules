<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

	$cs_lang = cs_translate('bets');
	
	require_once('mods/categories/functions.php');
	require_once('mods/bets/class_bet.php');
	
	$bets_id = (int) $_REQUEST['bets_id'];
	$myBet = new cs_bet($bets_id);
	$myBet->calcQuote();
	
	if(isset($_POST['submit'])) {
		
		
		$myBet->enterResult($_POST['result']);
		$msg  = $cs_lang['result_booked'];
		cs_redirect($msg,'bets','view', 'id='.$bets_id.'&refresh=true');
		
	}
	
	if(!empty($error) OR !isset($_POST['submit'])) {
	
		$cs_bets = $myBet->bet_data;
		$cs_contestants = $myBet->contestant_data;
		$numCons = (is_array($cs_contestants) ? count($cs_contestants) : 0);
		
		$data['bets']['title'] = $cs_bets['bets_title'];
		$data['bets']['id'] = $cs_bets['bets_id'];
		
		for ($run = 0; $run < $numCons; $run++) {
			
			if(empty($cs_contestants[$run]['bets_name'])){ 
				$data_clan = cs_sql_select(__FILE__,'clans','clans_name','clans_id ='.$cs_contestants[$run]['clans_id'] ,0,0,1);
				$data['contestants'][$run]['name'] = cs_secure($data_clan['clans_name']);
			}
			else {
				$data['contestants'][$run]['name'] = (!empty($cs_contestants[$run]['bets_draw']) ? $cs_lang['draw'] : cs_secure($cs_contestants[$run]['bets_name']));
			}
			
			$data['contestants'][$run]['bets_quote'] = $cs_contestants[$run]['bets_quote'];
			$data['contestants'][$run]['id'] = $cs_contestants[$run]['contestants_id'];
		}
		$data['bets']['title'] = cs_secure($cs_bets['bets_title']);
		
		$data['contestants_list'] = $data['contestants'];
		
		echo cs_subtemplate(__FILE__,$data,'bets','result');
	
	}
?>
