<?php
	
	class cache_memcached
	{
		private $memcached_host;
		private $memcached_port;
		private $keys_prefix;
		private $mc;
		private $debug_mode;
		private $debug_info;
		
		public function __construct()
		{
			global $C;
			$this->memcached_host	= $C->CACHE_MEMCACHE_HOST;
			$this->memcached_port	= $C->CACHE_MEMCACHE_PORT;
			$this->keys_prefix	= $C->CACHE_KEYS_PREFIX;
			$this->mc	= FALSE;
			$this->debug_mode	= $C->DEBUG_MODE==TRUE;
			$this->debug_info	= (object) array (
				'queries'	=> array(),
				'time'	=> 0,
			);
		}
		
		private function connect()
		{
			$time	= microtime(TRUE);
			if( FALSE == $this->mc ) {
				$this->mc	= memcache_connect( $this->memcached_host, $this->memcached_port );
			}
			if($this->debug_mode) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'CONNECT',
					'key'		=> $this->memcached_host.':'.$this->memcached_port,
					'result'	=> $this->mc ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $this->mc;
		}
		
		public function get($key)
		{
			if( FALSE == $this->mc ) {
				if( FALSE == $this->connect() ) {
					return FALSE;
				}
			}
			$key	= $this->keys_prefix.$key;
			if( $this->debug_mode ) {
				$time	= microtime(TRUE);
				$res	= memcache_get( $this->mc, $key );
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'GET',
					'key'		=> $key,
					'result'	=> !$res ? 'FALSE' : gettype($res),
					'time'	=> number_format($time, 5, '.', ''),
				);
				return $res;
			}
			return memcache_get( $this->mc, $key );
		}
		
		public function set($key, $data, $expire)
		{
			if( FALSE == $this->mc ) {
				if( FALSE == $this->connect() ) {
					return FALSE;
				}
			}
			$key	= $this->keys_prefix.$key;
			if( $this->debug_mode ) {
				$time	= microtime(TRUE);
				$res	= memcache_set( $this->mc, $key, $data, NULL, $expire );
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'SET',
					'key'		=> $key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
				return $res;
			}
			return memcache_set( $this->mc, $key, $data, NULL, $expire );
		}
		
		public function del($key)
		{
			if( FALSE == $this->mc ) {
				if( FALSE == $this->connect() ) {
					return FALSE;
				}
			}
			$key	= $this->keys_prefix.$key;
			if( $this->debug_mode ) {
				$time	= microtime(TRUE);
				$res	= memcache_delete( $this->mc, $key );
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'DELETE',
					'key'		=> $key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
				return $res;
			}
			return memcache_delete( $this->mc, $key );
		}
		
		public function get_debug_info()
		{
			$debug_info	= clone($this->debug_info);
			$debug_info->time	= number_format($debug_info->time, 4, '.', '');
			$debug_info->queries	= array_reverse($debug_info->queries);
			return $debug_info;
		}
		
		public function garbage_collector()
		{
		}
	}
	
?>