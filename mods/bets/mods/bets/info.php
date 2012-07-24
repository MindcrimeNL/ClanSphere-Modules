<?php

$cs_lang = cs_translate('bets');

$mod_info['name']	= $cs_lang['mod'];
$mod_info['text']	= $cs_lang['mod_text'];
$mod_info['version']	= '1.0';
$mod_info['released']	= '2012-03-13';
$mod_info['creator']	= 'Mindcrime (based on Nenti\'s)';
$mod_info['team']	= 'Geh aB Clan';
$mod_info['url']    = 'www.gab-clan.org';
$mod_info['icon']	= 'bets';
$mod_info['categories']  = TRUE;
$mod_info['comments']  = TRUE;
$mod_info['protected']  = FALSE;
$mod_info['show']    = array('clansphere/admin' => 4, 'options/roots' => 5, 'users/settings' => 2);
$mod_info['tables']     = array('bets','bets_contestants','bets_users');

