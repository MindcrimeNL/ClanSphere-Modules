<?php
$cs_lang = cs_translate('xaseco');

$mod_info['name']	    = $cs_lang['mod'];
$mod_info['version']  = 'v0.1 for XAseco v1.11';
$mod_info['released'] = '2010-10-01';
$mod_info['creator']	= 'Mindcrime';
$mod_info['team']	    = 'Geh aB Clan';
$mod_info['url']      = 'www.gab-clan.org';
$mod_info['text']	    = $cs_lang['mod_text'];
$mod_info['icon']	    = 'games';
$mod_info['show']     = array('options/roots' => 5);
$mod_info['categories'] = FALSE;
$mod_info['comments']	= FALSE;
$mod_info['protected']  = FALSE;
$mod_info['tables'] = array('xaseco_challenges', 'xaseco_players', 'xaseco_records', 'xaseco_votes', 
															'xaseco_players_extra', // plugin.localdatabase
															'xaseco_rs_rank', 'xaseco_rs_times', 'xaseco_rs_karma', // plugin.rasp
															'xaseco_match_main', 'xaseco_match_details'); // plugin.matchsave
$mod_info['startup'] = FALSE;

?>
