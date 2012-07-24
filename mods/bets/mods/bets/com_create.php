<?php
// ClanSphere 2009 - www.clansphere.net
// Id: com_create.php

$cs_lang = cs_translate('bets');
$cs_post = cs_post('fid');
$cs_get = cs_get('id');

$fid = empty($cs_post['fid']) ? 0 : $cs_post['fid'];
$quote_id = empty($cs_get['id']) ? 0 : $cs_get['id'];

$cs_bets = cs_sql_select(__FILE__,'bets','bets_com_close',"bets_id = '" . $fid . "'");

require_once('mods/comments/functions.php');
cs_commments_create($fid,'bets','view',$quote_id,$cs_lang['mod'],$cs_bets['bets_com_close']);

?>