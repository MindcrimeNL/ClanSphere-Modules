<?php
/*
 * ClanSphere Converter Db abstract class
 * 
 * Current support for the following DB types:
 * - mysql
 */

abstract class ClanSphere_Convert_Db
{
	/*
   * db resource
	 */
	protected $_db = null;
	/*
	 * the db settings
	 */
	protected $_settings = null;
 	/*
   * error messages
   */
  protected $_errors = array();
  protected $_errornum = 0;
  /*
   * connected?
   */
  protected $_connected = false;

	public function __construct($settings)	
	{
		$this->_settings = $settings;
	} // constructor	
	
	/* these are currently only needed */
	
	abstract public function db_query($query);

	abstract public function db_fetch_assoc($result);
	
	abstract public function db_free_result($result);
	
	abstract public function db_connect();

	abstract public function db_disconnect();
	
	public function connected()
	{
		return $this->_connected;
	} // function connected
	
	/* 
   * @return void
	 */
	public function error($text)
	{
		$this->_errors[$this->_errornum++]['message'] = $text;
	} // function error

	/* 
   * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	} // function getErrors
	
	public function clearErrors()
	{
		$this->_errors = array();
		$this->_errornum = 0;
	} // function clearErrors
} // class ClanSphere_Convert_Db
?>
