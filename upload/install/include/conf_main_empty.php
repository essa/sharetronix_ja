<?php
	
	// Site Address Here:
	// 
		$C->DOMAIN		= '';
		$C->SITE_URL	= '';
	// 
	
	// Random identifier for this installation on this server
	// 
		$C->RNDKEY	= '';
	// 
	
	// MySQL SETTINGS
	// 
		$C->DB_HOST	= '';
		$C->DB_USER	= '';
		$C->DB_PASS	= '';
		$C->DB_NAME	= '';
	// 
	
	// CACHE SETTINGS
	// 
		$C->CACHE_MECHANISM	= '';	// 'apc' or 'memcached' or 'mysqlheap' or 'filesystem'
		$C->CACHE_EXPIRE		= '';
		$C->CACHE_KEYS_PREFIX	= '';
		
		// If 'memcached':
		$C->CACHE_MEMCACHE_HOST	= '';
		$C->CACHE_MEMCACHE_PORT	= '';
		
		// If 'filesystem':
		$C->CACHE_FILESYSTEM_PATH	= '';
	// 
	
	// IMAGE MANIPULATION SETTINGS
	// 
		$C->IMAGE_MANIPULATION	= '';	// 'imagemagick_cli' or 'gd'
		
		// if 'imagemagick_cli' - /path/to/convert
		$C->IM_CONVERT	= '';
	// 
	
	// DEFAULT LANGUAGE
	// 
		$C->LANGUAGE	= '';
	// 
	
	// USERS ACCOUNTS SETTINGS
	// 
		$C->USERS_ARE_SUBDOMAINS	= '';	// if urls are user.site.com or site.com/user
	// 
	
	// RPC PING SETTINGS
	// 
		$C->RPC_PINGS_ON		= '';
		$C->RPC_PINGS_SERVERS	= '';
		$C->DEBUG_USERS		= '';
	// 
	
	// DO NOT REMOVE THIS
	// 
		$C->INSTALLED	= '';
		$C->VERSION		= '';
	// 
	
?>