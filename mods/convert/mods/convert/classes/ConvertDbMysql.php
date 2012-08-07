<?php
/*
 * ClanSphere Converter DB Mysql
 */

require_once('mods/convert/classes/ConvertDb.php');

class ClanSphere_Convert_Db_Mysql extends ClanSphere_Convert_Db
{
	public function db_query($query)
	{
		if (is_resource($this->_db))
		{
			return mysql_query($query, $this->_db);
		}
		return false;
	} // function db_query
	
	public function db_fetch_assoc($result)
	{
		if (is_resource($this->_db))
		{
			return mysql_fetch_assoc($result);
		}
		return false;
	} // function db_fetch_assoc
	
	public function db_free_result($result)
	{
		return mysql_free_result($result);
	} // function db_free_result
	
	public function db_connect()
	{
		$this->_db = mysql_connect($this->_settings['db_host'].':'.$this->_settings['db_port'],
															 $this->_settings['db_user'], $this->_settings['db_pass'], true);
		if (is_resource($this->_db))
		{
			if (!mysql_select_db($this->_settings['db_name'], $this->_db))
			{
				$this->error('Could not select db with name: "'.$this->_settings['db_name'].'"');
				$this->_connected = false;
				return false;
			}

			/* try to set the charset of the WS DB for communication */
	    if (function_exists('mysql_set_charset'))
				if (!mysql_set_charset($this->_settings['db_charset'], $this->_db))
					$this->error('Could not set charset to "'.$this->_settings['db_charset'].'" for DB server');
	    else
				if (!mysql_unbuffered_query("SET NAMES '".$this->_settings['db_charset']."'", $this->_db))
					$this->error('Could not set charset to "'.$this->_settings['db_charset'].'" for DB server');
			$this->_connected = true;
			return true;
		}
		else
			$this->error('Could not establish connection to the DB server');

		$this->_connected = false;
		return false;
	} // function connect
	
	public function db_disconnect()
	{
		$this->_connected = false;
		if (is_resource($this->_db))
		{
			return mysql_close($this->_db);
		}
		else
			$this->error('Could not close connection to the DB server');
		return false;
	} // function disconnect

} // class ClanSphere_Convert_Db_Mysql
?>
