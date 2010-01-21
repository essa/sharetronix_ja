<?php
	
	class mysql
	{
		private $connection	= FALSE;
		private $dbhost;
		private $dbuser;
		private $dbpass;
		private $dbname;
		private $last_result	= FALSE;
		public $fatal_error	= FALSE;
		private $debug_mode	= FALSE;
		private $debug_info	= FALSE;
		
		public function __construct($host, $user, $pass, $db)
		{
			$this->dbhost	= $host;
			$this->dbuser	= $user;
			$this->dbpass	= $pass;
			$this->dbname	= $db;
			$this->debug_mode	= $GLOBALS['C']->DEBUG_MODE;
			if( ! isset($GLOBALS['MYSQL_DEBUG_INFO']) ) {
				$GLOBALS['MYSQL_DEBUG_INFO']	= (object) array('queries'=>array(), 'time'=>0);
			}
			$this->debug_info	= & $GLOBALS['MYSQL_DEBUG_INFO'];
		}
		
		public function connect()
		{
			$time	= microtime(TRUE);
			$this->connection	= mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
			if(FALSE == $this->connection) {
				return $this->fatal_error('Connect');
			}
			$db	= mysql_select_db($this->dbname, $this->connection);
			if(FALSE == $db) {
				return $this->fatal_error('Select DB');
			}
			mysql_query('SET NAMES utf8', $this->connection);
			if($this->debug_mode) {
				$time	= microtime(TRUE) - $time;
				$this->debug_info->queries[]	= (object) array (
					'query'	=> 'CONNECT '.$this->dbhost,
					'time'	=> number_format($time, 5, '.', ''),
				);
				$this->debug_info->time		+= $time;
			}
			return $this->connection;
		}
		
		public function query($query, $remember_result=TRUE)
		{
			if(FALSE == $this->connection) {
				$this->connect();
			}
			$time	= microtime(TRUE);
			$result	= mysql_query($query, $this->connection);
			if($this->debug_mode) {
				$time	= microtime(TRUE) - $time;
				$this->debug_info->queries[]	= (object) array (
					'query'	=> $query,
					'time'	=> number_format($time, 5, '.', ''),
				);
				$this->debug_info->time	+= $time;
			}
			if(FALSE == $result) {
				return $this->fatal_error($query);
			}
			if($remember_result) {
				$this->last_result	= $result;
			}
			return $result;
		}
		
		public function fetch_object($res=FALSE) {
			$res	= FALSE!==$res ? $res : $this->last_result;
			if(FALSE == $res) {
				return FALSE;
			}
			return mysql_fetch_object($res);
		}
		
		public function fetch($query) {
			$res	= $this->query($query, FALSE);
			if(FALSE == $res) {
				return FALSE;
			}
			return $this->fetch_object($res);
		}
		
		public function fetch_all($query) {
			$res	= $this->query($query, FALSE);
			if(FALSE == $res) {
				return FALSE;
			}
			$data	= array();
			while( $obj = $this->fetch_object($res) ) {
				$data[]	= $obj;
			}
			$this->free_result($res);
			return $data;
		}
		
		public function fetch_field($query) {
			$res	= $this->query($query, FALSE);
			if(FALSE == $res) {
				return FALSE;
			}
			if( ! $row = mysql_fetch_row($res) ) {
				return FALSE;
			}
			$this->free_result($res);
			return $row[0];
		}
		
		public function num_rows($res=FALSE) {
			$res    = FALSE!==$res ? $res : $this->last_result;
			if(FALSE == $res) {
				return FALSE;
			}
			return mysql_num_rows($res);
		}
		
		public function insert_id() {
			if(FALSE == $this->connection) {
				$this->connect();
			}
			return intval( mysql_insert_id($this->connection) );
		}
		
		public function affected_rows() {
			if(FALSE == $this->connection) {
				$this->connect();
			}
			return mysql_affected_rows($this->connection);
		}
		
		public function data_seek($row=0, $res=FALSE) {
			$res    = FALSE!==$res ? $res : $this->last_result;
			if(FALSE == $res) {
				return FALSE;
			}
			return mysql_data_seek($res, $row);
		}
		
		public function free_result($res=FALSE) {
			$res    = FALSE!==$res ? $res : $this->last_result;
			if(FALSE == $res) {
				return FALSE;
			}
			return mysql_free_result($res);
		}
		
		public function escape($string) {
			if(FALSE == $this->connection) {
				$this->connect();
			}
			return mysql_real_escape_string($string, $this->connection);
		}
		
		public function e($str) {
			return $this->escape($str);
		}
		
		private function fatal_error($query) {
			$this->fatal_error	= TRUE;
			$error	= mysql_error($this->connection);
			if($this->debug_mode) {
				echo 'MySQL Query: '.$query.'<br />';
				echo 'MySQL Error: '.$error.'<br />';
				exit;
			}
			exit;
			//return FALSE;
		}
		
		public function get_debug_info()
		{
			$debug_info	= clone($this->debug_info);
			$debug_info->time	= number_format($debug_info->time, 4, '.', '');
			$debug_info->queries	= array_reverse($debug_info->queries);
			return $debug_info;
		}
		
		public function delete_debug_info()
		{
			$this->debug_info->queries	= array();
		}
		
		public function __destruct()
		{
			if(FALSE !== $this->connection) {
				@mysql_close($this->connection);
				$this->connection	= FALSE;
			}
		}
	}
	
?>