<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('datacache');

$data = array();

$cs_get = cs_get('datacache_id');

$datacache_id = $cs_get['datacache_id'];

$cs_datacache	 = cs_sql_select(__FILE__,'datacache','*','datacache_id = '.$datacache_id,0,0,1,0);

$data['datacache'] = array();
$data['datacache']['mod'] = cs_secure($cs_datacache['datacache_mod']);
$data['datacache']['action'] = cs_secure($cs_datacache['datacache_action']);
$data['datacache']['key'] = cs_secure($cs_datacache['datacache_key']);
$data['datacache']['time'] = cs_date('date',$cs_datacache['datacache_time'], 0, 1, 'Y-m-d H:i:s');
$data['datacache']['timeout'] = $cs_datacache['datacache_timeout'];
if ($cs_datacache['datacache_timeout'] <> 0)
	$data['datacache']['expires'] = cs_date('date',$cs_datacache['datacache_time'] + $cs_datacache['datacache_timeout'], 0, 1, 'Y-m-d H:i:s');
else
	$data['datacache']['expires'] = $cs_lang['never'];
$data['datacache']['data'] = cs_secure($cs_datacache['datacache_data']);
$data['datacache']['raw_data'] = $cs_datacache['datacache_data'];

echo cs_subtemplate(__FILE__,$data,'datacache','view');

?>
