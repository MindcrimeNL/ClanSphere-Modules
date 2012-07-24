<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('datacache');

$mod_info['name']    = $cs_lang['mod_name'];
$mod_info['version']  = '2010.0.0';
$mod_info['released']  = '2010-11-24';
$mod_info['creator']  = 'Mindcrime';
$mod_info['team']    = 'GaB e.V.';
$mod_info['url']    = 'www.gab-clan.org';
$mod_info['text']    = $cs_lang['modtext'];
$mod_info['icon']    = 'kexi';
$mod_info['show']    = array('clansphere/admin' => 5, 'options/roots' => 5);
$mod_info['categories']  = FALSE;
$mod_info['comments']  = FALSE;
$mod_info['protected']  = TRUE;
$mod_info['tables']    = array('datacache');
$mod_info['startup'] = TRUE;
