<?php

$cs_lang = cs_translate('coins');

$mod_info['name']	= $cs_lang['mod'];
$mod_info['text']	= $cs_lang['mod_text'];
$mod_info['version']	= '1.0';
$mod_info['released']	= '2012-04-11';
$mod_info['creator']	= 'Mindcrime';
$mod_info['team']	= 'Geh aB Clan';
$mod_info['url']    = 'www.gab-clan.org';
$mod_info['icon']	= 'bets';
$mod_info['categories']  = FALSE;
$mod_info['comments']  = FALSE;
$mod_info['protected']  = FALSE;
$mod_info['show']    = array('clansphere/admin' => 4, 'options/roots' => 5, 'users/settings' => 2);
$mod_info['tables']     = array('coins');
$mod_info['startup'] = TRUE;

