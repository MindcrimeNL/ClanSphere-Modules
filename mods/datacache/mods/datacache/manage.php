<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

$cs_lang = cs_translate('datacache');

$data = array();

$cs_post = cs_post('start,sort');
$cs_get = cs_get('start,sort');

$where = empty($cs_get['where']) ? 0 : $cs_get['where'];
if (!empty($cs_post['where']))  $where = $cs_post['where'];
$start = empty($cs_get['start']) ? 0 : $cs_get['start'];
if (!empty($cs_post['start']))  $start = $cs_post['start'];
$sort = empty($cs_get['sort']) ? 1 : $cs_get['sort'];
if (!empty($cs_post['sort']))  $sort = $cs_post['sort'];

$subsel = empty($where) ? 0 : 'datacache_mod = \'' . cs_sql_escape($where) . '\'';

$cs_sort[1] = 'datacache_mod DESC';
$cs_sort[2] = 'datacache_mod ASC';
$cs_sort[3] = 'datacache_action DESC';
$cs_sort[4] = 'datacache_action ASC';
$cs_sort[5] = 'datacache_key DESC';
$cs_sort[6] = 'datacache_key ASC';
$cs_sort[7] = 'datacache_time DESC';
$cs_sort[8] = 'datacache_time ASC';

/* purge the cache first */
cs_datacache_purge(null, null);

$order = $cs_sort[$sort];
$datacache_count = cs_sql_count(__FILE__,'datacache',$subsel);


$data['head']['datacache_count'] = $datacache_count;
$data['head']['message'] = cs_getmsg();
$data['head']['pages'] = cs_pages('datacache','manage',$datacache_count,$start,$where,$sort);

$cs_datacache	 = cs_sql_select(__FILE__,'datacache','*',$subsel,$order,$start,$account['users_limit']);
$datacache_loop = count($cs_datacache);

$data['sort']['mod'] = cs_sort('datacache','manage',$start,0,1,$sort,'where='.$where);
$data['sort']['action'] = cs_sort('datacache','manage',$start,0,3,$sort,'where='.$where);
$data['sort']['key'] = cs_sort('datacache','manage',$start,0,5,$sort,'where='.$where);
$data['sort']['time'] = cs_sort('datacache','manage',$start,0,7,$sort,'where='.$where);

$data['datacache'] = array();
for ($run = 0; $run < $datacache_loop; $run++)
{
		$data['datacache'][$run]['url_mod'] = cs_url('datacache','manage','where='.urlencode($cs_datacache[$run]['datacache_mod']));
	  $data['datacache'][$run]['mod'] = $cs_datacache[$run]['datacache_mod'];
	  $data['datacache'][$run]['action'] = $cs_datacache[$run]['datacache_action'];
	  $data['datacache'][$run]['key'] = cs_secure($cs_datacache[$run]['datacache_key']);
	  $data['datacache'][$run]['time'] = cs_date('date',$cs_datacache[$run]['datacache_time'], 0, 1, 'Y-m-d H:i:s');
	  $data['datacache'][$run]['timeout'] = $cs_datacache[$run]['datacache_timeout'];
	  $data['datacache'][$run]['url_view'] = cs_url('datacache','view','datacache_id='.$cs_datacache[$run]['datacache_id']);
		$data['datacache'][$run]['url_remove'] = cs_url('datacache','remove','datacache_id='.$cs_datacache[$run]['datacache_id']);
}

echo cs_subtemplate(__FILE__,$data,'datacache','manage');

?>
