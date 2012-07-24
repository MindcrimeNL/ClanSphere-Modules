<?php
// Geh aB Clan 2011 - www.gab-clan.org
// $Id$

/* support for which phpsc2replay version */
if (!defined('SC2_PARSER_VERSION'))
  define('SC2_PARSER_VERSION', '1.61');

$cs_lang = cs_translate('replays_sc2');

$plugin_info['name']    = $cs_lang['plugin_name'];
$plugin_info['version']  = $cs_main['version_name'];
$plugin_info['released']  = $cs_main['version_date'];
$plugin_info['creator']  = 'Mindcrime';
$plugin_info['team']    = 'GaB e.V.';
$plugin_info['url']    = 'www.gab-clan.org';
$plugin_info['text']    = sprintf($cs_lang['plugin_text'], SC2_PARSER_VERSION);
$plugin_info['icon']    = 'cam_unmount';
$plugin_info['show']    = array('clansphere/admin' => 3, 'options/roots' => 5);
$plugin_info['categories']  = FALSE;
$plugin_info['comments']  = FALSE;
$plugin_info['protected']  = FALSE;
$plugin_info['tables']    = array('replays_sc2');
