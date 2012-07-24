<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('bets');

$options = cs_sql_option(__FILE__, 'bets');

require_once('mods/categories/functions.php');
require_once('mods/bets/class_bet.php');

$myBet = new cs_bet();

if(isset($_POST['submit'])) {
	$categories_id = empty($_POST['categories_name']) ? (int) $_POST['categories_id'] : cs_categories_create('bets',$_POST['categories_name']);
	
	$cs_bets['bets_auto_title'] = !empty($_POST['bets_auto_title']) ? 1 : 0;
	$cs_bets['bets_title'] = !empty($cs_bets['bets_auto_title']) ? '' : $_POST['bets_title'];

	$opponents	= array();
	foreach ($_POST['contestant'] as $key => $value) {
	 	if (empty($value))
	 	{
			$opponents[$key]['value'] = (int) $_POST['clans_id'][$key];
			$opponents[$key]['type'] = 'clan';
			if (!empty($cs_bets['bets_auto_title']))
			{
				if (!empty($cs_bets['bets_title']))
					$cs_bets['bets_title'] .= ' '.$options['auto_title_separator'].' ';
				$clan = cs_sql_select(__FILE__,'clans','clans_name,clans_id','clans_id = '.$opponents[$key]['value'],0,0,1);
				if (!empty($clan['clans_id']))
					$cs_bets['bets_title'] .= $clan['clans_name'];
			}
		}
		else
		{
			$opponents[$key]['value'] = $value;
			$opponents[$key]['type'] = 'name';
			if (!empty($cs_bets['bets_auto_title']))
			{
				if (!empty($cs_bets['bets_title']))
					$cs_bets['bets_title'] .= ' '.$options['auto_title_separator'].' ';
				$cs_bets['bets_title'] .= $value;
			}
		}
		$opponents[$key]['quote'] = (float) $_POST['quote'][$key];
		$opponents[$key]['draw'] = 0;
	}
	
	if (!empty($_POST['bets_enable_draw']))
	{
		$next_index = count($opponents);
		$opponents[$next_index]['value'] = $cs_lang['draw'];
		$opponents[$next_index]['type'] = 'name';
		$opponents[$next_index]['quote'] = (float) $_POST['draw_quote'];
		$opponents[$next_index]['draw'] = 1;
	}

	$cs_bets['bets_starts_at'] = cs_datepost('date_start','unix');
	$cs_bets['bets_closed_at'] = cs_datepost('date_end','unix');
	$cs_bets['categories_id'] = $categories_id;
	$cs_bets['bets_description'] = $_POST['bets_description'];
	$cs_bets['bets_quote_type'] = (int) $_POST['bets_quote_type'];
	$cs_bets['bets_enable_draw'] = !empty($_POST['bets_enable_draw']) ? 1 : 0;
	$cs_bets['bets_com_close'] = !empty($_POST['bets_com_close']) ? 1 : 0;
	
	$myBet->saveBet(0, $opponents, $cs_bets);
	if(empty($myBet->error)) 
	{	
		$msg  = $cs_lang['create_done'];
		cs_redirect($msg,'bets');
	}
}
else
{
	$cs_bets['bets_title'] = '';
	$cs_bets['bets_starts_at'] = cs_time();
	$cs_bets['bets_closed_at'] = cs_time()+(24*60*60*7);
	$cs_bets['categories_id'] = 0;
	$cs_bets['bets_description'] = '';
	$cs_bets['bets_quote_type'] = $options['quote_type'];
	$cs_bets['bets_enable_draw'] = 1;
	$cs_bets['bets_com_close'] = 0;
	$cs_bets['bets_auto_title'] = $options['auto_title'];
	$opponents	= array();
	$opponents[0]['value'] = '';
	$opponents[0]['type'] = 'name';
	$opponents[1]['value'] = '';
	$opponents[1]['type'] = 'name';

	if (isset($_GET['warid']))
	{
		$own_clan = cs_sql_select(__FILE__,'clans','*','clans_id = 1',0,0,1);
		$wars_id = (int) $_GET['warid'];
		$wfrom = 'wars w LEFT JOIN {pre}_squads s ON s.squads_id = w.squads_id LEFT JOIN {pre}_clans c ON c.clans_id = w.clans_id';
		$wselect = 'w.*, s.squads_name, c.clans_name';
		/* still upcoming and at least one hour before the match is played */
		$wwhere = 'w.wars_id = '.$wars_id.' AND w.wars_status = \'upcoming\' AND w.wars_date > '.(cs_time() + 3600);
		$cs_wars = cs_sql_select(__FILE__, $wfrom, $wselect, $wwhere, 0, 0, 1);
		if (!empty($cs_wars))
		{
			$opponents	= array();
			$opponents[0]['value'] = 1;
			$opponents[0]['type'] = 'clan';
			$opponents[1]['value'] = $cs_wars['clans_id'];
			$opponents[1]['type'] = 'clan';
			$cs_bets['bets_title'] = cs_secure($own_clan['clans_tag']).' - '.cs_secure($cs_wars['squads_name']).' vs. '.cs_secure($cs_wars['clans_name']);
			/* half hour before start of war */
			$cs_bets['bets_closed_at'] = $cs_wars['wars_date'] - 1800;
			$cs_bets['bets_description'] = '[url='.cs_url('wars','view','id='.$wars_id).']'.$cs_bets['bets_title'].'[/url]';
			$cs_bets['bets_auto_title'] = 0;
		}
	}
	else if (isset($_GET['cupmatchid']))
	{
		include_once('mods/cups/defines.php');
		$cupmatch_id = (int) $_GET['cupmatchid'];
		$cmfrom = 'cupmatches cm LEFT JOIN {pre}_cups c ON c.cups_id = cm.cups_id';
		$cupmatch = cs_sql_select(__FILE__, $cmfrom, 'cm.*, c.cups_system, c.cups_name, c.games_id', 'cm.cupmatches_id = '.$cupmatch_id, 0, 0, 1);
		$notallowed = array(CS_CUPS_TEAM_UNKNOWN, CS_CUPS_TEAM_BYE);
		/* we may only start this bet if:
     * - match has no winner
     * - match has no score
     * - match has not been accepted by any party
     * - match has known opponents and no byes
		 */
		if (!empty($cupmatch) && $cupmatch['cupmatches_winner'] == CS_CUPS_TEAM_UNKNOWN
				&& !in_array($cupmatch['squad1_id'], $notallowed) && !in_array($cupmatch['squad2_id'], $notallowed)
				&& empty($cupmatch['cupmatches_accepted1']) && empty($cupmatch['cupmatches_accepted2'])
				&& empty($cupmatch['cupmatches_score1']) && empty($cupmatch['cupmatches_score2']))
		{
			$opponents	= array();
			$opponents[0]['type'] = 'name';
			$opponents[1]['type'] = 'name';
			$cs_bets['bets_enable_draw'] = 0;
			switch ($cupmatch['cups_system'])
			{
			default:
			case CS_CUPS_TYPE_TEAMS:
				$sqfrom = 'squads sq LEFT JOIN {pre}_clans c ON c.clans_id = sq.clans_id';
				$squad1 = cs_sql_select(__FILE__, $sqfrom, 'sq.*, c.clans_tag', 'sq.squads_id = '.$cupmatch['squad1_id'], 0, 0, 1);
				if (!empty($squad1))
					$opponents[0]['value'] = cs_secure($squad1['clans_tag']).' - '.cs_secure($squad1['squads_name']);
				$squad2 = cs_sql_select(__FILE__, $sqfrom, 'sq.*, c.clans_tag', 'sq.squads_id = '.$cupmatch['squad2_id'], 0, 0, 1);
				if (!empty($squad2))
					$opponents[1]['value'] = cs_secure($squad2['clans_tag']).' - '.cs_secure($squad2['squads_name']);
				break;
			case CS_CUPS_TYPE_USERS:
				$squad1 = cs_sql_select(__FILE__, 'users', '*', 'users_id = '.$cupmatch['squad1_id'], 0, 0, 1);
				if (!empty($squad1))
					$opponents[0]['value'] = cs_secure($squad1['users_nick']);
				$squad2 = cs_sql_select(__FILE__, 'users', '*', 'users_id = '.$cupmatch['squad2_id'], 0, 0, 1);
				if (!empty($squad2))
					$opponents[1]['value'] = cs_secure($squad2['users_nick']);
				break;
			}
			$cs_bets['bets_title'] = $opponents[0]['value'].' vs. '.$opponents[1]['value'];
			$cs_bets['bets_auto_title'] = 0;
			$cs_bets['bets_description'] = '[url='.cs_url('cups','match','id='.$cupmatch_id).']'.$cs_bets['bets_title'].'[/url]';
		}
	}
}

if(!isset($_POST['submit'])) {
  $data['var']['message'] = $cs_lang['body_create'];
} elseif(!empty($myBet->error)) {
  $data['var']['message'] = $cs_lang['error_occured'] . cs_html_br(1) . $myBet->error;
}

if (!empty($myBet->error) OR !isset($_POST['submit']))
{
		
	$data_categories = cs_sql_select(__FILE__,'categories','categories_name, categories_id','categories_mod = \'bets\'','categories_name',0,0);

	$data_clans = cs_sql_select(__FILE__,'clans','clans_name,clans_id',0,'clans_name',0,0);
  
	switch ($opponents[0]['type'])
	{
	default:
	case 'name':
		if (!empty($opponents[0]['value']))
			$data['contestant']['value0'] = $opponents[0]['value'];
		else
			$data['contestant']['value0'] = '';
		$data['bets']['clan_sel0'] = cs_dropdown('clans_id[]','clans_name',$data_clans,0,'clans_id');
		break;
	case 'clan':
		$data['contestant']['value0'] = '';
		$data['bets']['clan_sel0'] = cs_dropdown('clans_id[]','clans_name',$data_clans,$opponents[0]['value'],'clans_id');
		break;
	}
	switch ($opponents[1]['type'])
	{
	default:
	case 'name':
		if (!empty($opponents[1]['value']))
			$data['contestant']['value1'] = $opponents[1]['value'];
		else
			$data['contestant']['value1'] = '';
		$data['bets']['clan_sel1'] = cs_dropdown('clans_id[]','clans_name',$data_clans,0,'clans_id');
		break;
	case 'clan':
		$data['contestant']['value1'] = '';
		$data['bets']['clan_sel1'] = cs_dropdown('clans_id[]','clans_name',$data_clans,$opponents[1]['value'],'clans_id');
		break;
	}
//	$data['categories'] = cs_dropdownsel($data_categories,$cs_bets['categories_id'],'categories_id');
	$choose = '';
	if (is_array($data_categories) && count($data_categories))
	foreach ($data_categories as $category)
	{
		$choose .= cs_html_option($category['categories_name'], $category['categories_id'], $category['categories_id'] == $cs_bets['categories_id']);
	}
  $data['categories']['choose'] = $choose;
  
	$data['dropdown']['date_start'] = cs_dateselect('date_start','unix',$cs_bets['bets_starts_at'],1995);
	$data['dropdown']['date_end'] = cs_dateselect('date_end','unix',$cs_bets['bets_closed_at'],1995);
	$data['value']['title'] = $cs_bets['bets_title'];
	$data['value']['report'] = $cs_bets['bets_description'];
	$data['value']['bets_enable_draw_checked'] = !empty($cs_bets['bets_enable_draw']) ? 'checked="checked"' : '';
	$data['value']['bets_auto_title_checked'] = !empty($cs_bets['bets_auto_title']) ? 'checked="checked"' : '';
	$data['value']['option_auto_title_separator'] = cs_secure($options['auto_title_separator']);
  $data['value']['quote_type_options'] = '';
  for ($i = 0; $i <= 2; $i++)
  {
  	$data['value']['quote_type_options'] .= cs_html_option($cs_lang['quote_type_'.$i], $i, $i == $cs_bets['bets_quote_type']);
  }

	$data['abcode']['smileys'] = cs_abcode_smileys('wars_report');
  $data['abcode']['features'] = cs_abcode_features('wars_report');
  
  $data['value']['bets_com_close_check'] = '';
  if (!empty($cs_bets['bets_com_close']))
		$data['value']['bets_com_close_checked'] = 'checked="checked"';
  	
	echo cs_subtemplate(__FILE__,$data,'bets','create');
}
?>
