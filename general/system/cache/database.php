<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

function cs_cache_clear() {

	// delete from {pre}_cache where 1 = 1
	//
  // I really would like to have a cs_sql_truncate('cache');
  // SQLite: DELETE FROM {pre}_cache
  // All other db: TRUNCATE TABLE {pre}_cache
  //
  // Reason: it will reset the autoincrement ID back to 1
	$query = 'DELETE FROM {pre}_cache WHERE 1 = 1';
	cs_sql_query(__FILE__, $query);

  $unicode = extension_loaded('unicode') ? 1 : 0;
  $where = "options_mod = 'clansphere' AND options_name = 'cache_unicode'";
  cs_sql_update(__FILE__, 'options', array('options_value'), array($unicode), 0, $where); 
 }

function cs_cache_delete($name, $ttl = 0) {
	$token = empty($ttl) ? $name : 'ttl_' . $name;
	$query = 'DELETE FROM {pre}_cache WHERE cache_md5 = \''.md5($token).'\'';
	cs_sql_query(__FILE__, $query);
}

function cs_cache_info() {

	$form = array();
	$select = cs_sql_select(__FILE__, 'cache', 'cache_id, cache_key, cache_time, '.cs_sql_length('cache_content').' AS cache_length', 0, 'cache_key ASC', 0, 0);

	if (count($select))
	{
  	foreach ($select as $row)
   	 $form[] = array('name' => $row['cache_key'], 'time' => $row['cache_time'], 'size' => $row['cache_length']);
	}

  return $form;
}

function cs_cache_load($name, $ttl = 0) {
	$token = empty($ttl) ? $name : 'ttl_' . $name;
	$select = cs_sql_select(__FILE__, 'cache', '*', 'cache_md5 = \''.md5($token).'\'', 0, 0, 1);
  if(empty($select['cache_id']))
    return false;
	if ($select['cache_timeout'] > 0 && cs_time() > $select['cache_time'] + $select['cache_timeout'])
	{
		/* do not delete old content on save, just let it overwrite, prevents autoincrement of ID */
//		cs_cache_delete($token);
    return false;
	}
  return unserialize($select['cache_content']);
}

function cs_cache_save($name, $content, $ttl = 0) {

	$token = empty($ttl) ? $name : 'ttl_' . $name;
  if(is_bool($content))
    cs_error($token, 'cs_cache_save - It is not allowed to just store a boolean');

  $store = serialize($content);

	/* do not delete old content on save, just overwrite, prevents autoincrement of ID */
	$select = cs_sql_select(__FILE__, 'cache', 'cache_id', 'cache_md5 = \''.md5($token).'\'', 0, 0, 1);
	if (!empty($select['cache_id']))
	{
	  $cells = array('cache_time', 'cache_timeout', 'cache_content');
	  $values = array(cs_time(), $ttl, $store);
	  cs_sql_update(__FILE__, 'cache', $cells, $values, $select['cache_id']);
	}
	else
	{
	  $cells = array('cache_key', 'cache_md5', 'cache_time', 'cache_timeout', 'cache_content');
	  $values = array($token, md5($token), cs_time(), $ttl, $store);
	  cs_sql_insert(__FILE__, 'cache', $cells, $values);
	}

  return $content;
}
