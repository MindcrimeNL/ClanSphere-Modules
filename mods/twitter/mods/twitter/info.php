<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('twitter');

$mod_info['name']    = $cs_lang['mod_name'];
$mod_info['version']  = '201x.x.x (oAuth)';
$mod_info['released']  = '2011-08-30';
$mod_info['creator']  = 'Mindcrime';
$mod_info['team']    = 'GaB e.V.';
$mod_info['url']    = 'www.gab-clan.org';
$mod_info['text']    = $cs_lang['modtext'];
$mod_info['icon']    = 'twitter';
$mod_info['show']    = array('clansphere/admin' => 4, 'options/roots' => 5, 'users/settings' => 2);
$mod_info['categories']  = FALSE;
$mod_info['comments']  = FALSE;
$mod_info['protected']  = FALSE;
$mod_info['tables']    = array('twitter');
$mod_info['navlist'] = array('navlist' => 'max_navlist',
                             'navlist_headline' => 'max_headline');
