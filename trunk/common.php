<?php
Class common {

	protected $DB;

	function __consturct() {
		if(DB_ENABLE) $this->db_init();
	}

	function __deconstruct() {
		if(DB_ENABLE) $this->db_close();
	}

	function db_init()
	{
		mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
		mysql_select_db(DB_DATABASE, $this->DB);
		mysql_query("SET NAMES '" . DB_CHARSET ."' COLLATE '" . DB_COLLATION ."'");
	}

	function db_close()
	{
		mysql_close($this->DB);
	}

	function db_query($statement)
	{
		return mysql_query($statement);
	}

	/**
	 * function get_permalink
	 * transfer plurk_id to permalink
	 *
	 * @param $plurk_id
	 * @return string.
	 */
	function get_permalink($plurk_id)
	{
		return "http://www.plurk.com/p/" . base_convert($plurk_id, 10, 36);
	}

	/**
	 * function get_plurk_id
	 * transfer permalink to plurk_id
	 *
	 * @param $permalink
	 * @return int.
	 */
	function get_plurk_id($permalink)
	{
		return base_convert(str_replace('http://www.plurk.com/p/', '', $permalink), 36, 10);
	}

	/**
	 * funciton log
	 * message log
	 *
	 * @param $message
	 */
	function log($message = '')
	{
		$source = file_get_contents(PLURK_LOG_PATH);
		$source .= date("Y-m-d H:i:s - ") . $message . "\n";
		file_put_contents(PLURK_LOG_PATH, $source);
	}
}