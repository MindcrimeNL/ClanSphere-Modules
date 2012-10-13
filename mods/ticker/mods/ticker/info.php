<?php
// Clansphere 2009
// ticker - info.php - flow
// 2007-08-13
// based on the Tickermodule from Mr.AndersoN

$cs_lang = cs_translate('ticker','translate');

$mod_info['name']	  = $cs_lang['mod_name'];
$mod_info['version']  = '2011.x.x';
$mod_info['released'] = '2012-05-10';
$mod_info['creator']  = 'Mindcrime (original by flow)';
$mod_info['team']	  = 'GaB e.V.';
$mod_info['url']	  = 'http://www.gab-clan.org/';
$mod_info['text']	  = $cs_lang['mod_info'];
$mod_info['icon']	  = 'view_text';
$mod_info['show']	  = array('clansphere/admin' => 4, 'options/roots' => 5);
$mod_info['categories'] = FALSE;
$mod_info['comments']  = FALSE;
$mod_info['protected']	= FALSE;
$mod_info['tables']		= array('ticker');
?>
