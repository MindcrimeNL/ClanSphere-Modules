<?php
// Geh aB Clan 2009 - www.gab-clan.org
// $Id$

/*
 * A data caching system:
 *
 * It's main purpose is to prevent the generation of extensive queries for data,
 * which does not change a lot, but which is used a lot.
 *
 * If you have, for example, a finished cup, why retrieve and generate the
 * resulting brackets every time, if it will never change (spare for changes in
 * the template)?
 */

/**
 * Clear/Remove the cache (for a particular mod and action)
 *
 * @param	mod	$mod the identifier mod
 * @param	action	$action the identifier action
 *
 * @return true on success or false on failure
 */
function cs_datacache_clear($mod = null, $action = null)
{
	$where = '1=1';
	if (!is_null($mod))
		$where .= (empty($where) ? '' : ' AND ').'datacache_mod = \''.cs_sql_escape($mod).'\'';
	if (!is_null($action))
		$where .= (empty($where) ? '' : ' AND ').'datacache_action = \''.cs_sql_escape($action).'\'';

	$query = 'DELETE FROM {pre}_datacache WHERE '.$where;
	$result = cs_sql_query(__FILE__, $query);
	return $result;
} // function cs_datacache_clear

/**
 * Purge (all expired data from) the cache (for a particular mod and/or action)
 *
 * @param	mod	$mod the identifier mod
 * @param	action	$action the identifier action
 *
 * @return the number of cache records purged
 */
function cs_datacache_purge($mod = null, $action = null)
{
	$where = 'datacache_timeout <> 0 AND datacache_time + datacache_timeout < '.time();
	if (!is_null($mod))
		$where .= ' AND datacache_mod = \''.cs_sql_escape($mod).'\'';
	if (!is_null($action))
		$where .= ' AND datacache_action = \''.cs_sql_escape($action).'\'';

	$query = 'DELETE FROM {pre}_datacache WHERE '.$where;
	$result = cs_sql_query(__FILE__, $query);
	return $result;
} // function cs_datacache_purge

/**
 * Load data from cache
 *
 * @param	string	$mod the identifier mod
 * @param	string	$action the identifier action
 * @param	string	$key the identifier action
 * @param	
 *
 * @return clob on success or false on failure
 */
function cs_datacache_load($mod, $action, $key, $remove = false)
{
	global $cs_main;
	
	// do not use datacache during debug
	if (!empty($cs_main['debug']) || !empty($cs_main['no_datacache']))
		return false;
		
	$where = 'datacache_mod = \''.cs_sql_escape($mod)
				.'\' AND datacache_action = \''.cs_sql_escape($action)
				.'\' AND datacache_key = \''.cs_sql_escape($key)
				.'\'';
	$data = cs_sql_select(__FILE__, 'datacache', 'datacache_data, datacache_time, datacache_timeout', $where, 0, 0, 1, 0);
	if (!is_null($data) && $data !== false)
	{
		if (intval($data['datacache_timeout']) == 0 || intval($data['datacache_timeout']) + intval($data['datacache_time']) >= time())
		{
			return $data['datacache_data'];
		}
		/* cache data expired, remove? */
		if ($remove)
			cs_datacache_remove($mod, $action, $key);
	}
	return false;
} // function cs_datacache_load

/**
 * Save data in cache, the cache must have been created before.
 * If not, use cs_datacache_create()
 *
 * @param	string	$mod the identifier mod
 * @param	string	$action the identifier action
 * @param	string	$key the identifier action
 * @param	blob	$content the content
 * @param	int	$timeout timeout in seconds, 0 is no timeout, always ok
 *
 * @return true on success or false on failure
 */
function cs_datacache_save($mod, $action, $key, $content, $timeout = null)
{
	global $cs_main;
	
	// do not use datacache during debug
	if (!empty($cs_main['debug']) || !empty($cs_main['no_datacache']))
		return false;

	$where = 'datacache_mod = \''.cs_sql_escape($mod)
					.'\' AND datacache_action = \''.cs_sql_escape($action)
					.'\' AND datacache_key = \''.cs_sql_escape($key).'\'';
	$cells = array();
	$values = array();
	$cells[0] = 'datacache_data';
	$values[0] = $content;
	$cells[1] = 'datacache_time';
	$values[1] = time();
	if (!is_null($timeout))
	{
		$cells[2] = 'datacache_timeout';
		$values[2] = intval($timeout);
	}
	cs_sql_update(__FILE__, 'datacache', $cells, $values, 0, $where); 
	return true;
} // function cs_datacache_save

/**
 * Create (or update) data in cache.
 * This is somewhat slower, but is more safe.
 *
 * @param	string	$mod the identifier mod
 * @param	string	$action the identifier action
 * @param	string	$key the identifier action
 * @param	blob	$content the content
 * @param	int	$timeout timeout in seconds, 0 is no timeout, always ok
 *
 * @return true on success or false on failure
 */
function cs_datacache_create($mod, $action, $key, $content, $timeout = null)
{
	global $cs_main;
	
	// do not use datacache during debug
	if (!empty($cs_main['debug']) || !empty($cs_main['no_datacache']))
		return false;

	$where = 'datacache_mod = \''.cs_sql_escape($mod)
				.'\' AND datacache_action = \''.cs_sql_escape($action)
				.'\' AND datacache_key = \''.cs_sql_escape($key)
				.'\'';
	$count = cs_sql_count(__FILE__, 'datacache', $where);
	if ($count > 0)
  {
  	/* update */
		$cells = array();
		$values = array();
		$cells[0] = 'datacache_data';
		$values[0] = $content;
		$cells[1] = 'datacache_time';
		$values[1] = time();
		if (!is_null($timeout))
		{
			$cells[2] = 'datacache_timeout';
			$values[2] = intval($timeout);
		}
		cs_sql_update(__FILE__, 'datacache', $cells, $values, 0, $where);
  }
  else
  {
		$options = cs_sql_option(__FILE__,'datacache');
  	/* insert */ 
		$cells = array();
		$values = array();
		$cells[0] = 'datacache_mod';
		$values[0] = $mod;
		$cells[1] = 'datacache_action';
		$values[1] = $action;
		$cells[2] = 'datacache_key';
		$values[2] = $key;
		$cells[3] = 'datacache_time';
		$values[3] = time();
		$cells[4] = 'datacache_data';
		$values[4] = $content;
		$cells[5] = 'datacache_timeout';
		if (!is_null($timeout))
			$values[5] = intval($timeout);
		else
			$values[5] = intval($options['timeout']);
		cs_sql_insert(__FILE__, 'datacache', $cells, $values);
  }
  return true;
} // function cs_datacache_create


/**
 * Remove data from cache
 *
 * @param	string	$mod the identifier mod
 * @param	string	$action the identifier action
 * @param	string	$key the identifier action
 *
 * @return true on success or false on failure
 */
function cs_datacache_remove($mod, $action, $key)
{
	$query = 'DELETE FROM {pre}_datacache WHERE '
					.'datacache_mod = \''.cs_sql_escape($mod)
					.'\' AND datacache_action = \''.cs_sql_escape($action)
					.'\' AND datacache_key = \''.cs_sql_escape($key).'\''; 
	$result = cs_sql_query(__FILE__, $query);
	return ($result >= 1);
} // function cs_datacache_remove
?>
