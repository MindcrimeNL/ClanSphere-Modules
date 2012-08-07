<?php 
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('datacache');

$data['if']['view_1'] = 1;
$data['if']['view_2'] = 0;

$options = cs_sql_option(__FILE__,'datacache', true);

if (!empty($_POST['submit']))
{
  $save = array();
	$save['timeout'] = intval($_POST['timeout']);
	
  require_once 'mods/clansphere/func_options.php';
  
  cs_optionsave('datacache', $save);
  
  cs_redirect($cs_lang['success'], 'options', 'roots');

} else {
	$data['options']['timeout'] = $options['timeout'];
}

echo cs_subtemplate(__FILE__,$data,'datacache','options');
?> 
