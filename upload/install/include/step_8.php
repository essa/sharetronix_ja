<?php
	
	ini_set('memory_limit', -1);
	
	$PAGE_TITLE	= 'Installation - Step 8';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$error	= FALSE;
	
	if( isset($s['INSTALLED']) && $s['INSTALLED'] ) {
		$configfile	= INCPATH.'../../system/conf_main.php';
		$is_ok	= FALSE;
		if( file_exists($configfile) ) {
			$C	= new stdClass;
			$C->INCPATH	= realpath(INCPATH.'../../system/').'/';
			include($configfile);
			if( $C->INSTALLED == TRUE && $C->VERSION >= VERSION ) {
				$is_ok	= TRUE;
			}
		}
		if( ! $is_ok ) {
			unset($s['INSTALLED']);
			$_SESSION['INSTALL_STEP']	= 0;
			header('Location: ?reset');
			exit;
		}
	}
	
	$error	= FALSE;
	
	if( !isset($s['INSTALLED']) || !$s['INSTALLED'] )
	{
		$s['LANGUAGE']	= 'en';
		if( isset($OLDC->LANGUAGE) ) {
			$s['LANGUAGE']	= $OLDC->LANGUAGE;
			if( empty($s['LANGUAGE']) || !file_exists(INCPATH.'../../system/languages/'.$s['LANGUAGE']) ) {
				$s['LANGUAGE']	= 'en';
			}
		}
		if( ! file_exists( INCPATH.'../../i/attachments/1/' ) ) {
			@mkdir( INCPATH.'../../i/attachments/1/' );
		}
		@chmod( INCPATH.'../../i/attachments/1/', 0777 );
		
		$s['SITE_URL']	= rtrim($s['SITE_URL'],'/').'/';
		
		if( ! $error ) {
			$rwbase	= '/';
			$tmp	= preg_replace('/^http(s)?\:\/\//', '', $s['SITE_URL']);
			$tmp	= trim($tmp, '/');
			$pos	= strpos($tmp, '/');
			if( FALSE !== $pos ) {
				$tmp	= substr($tmp, $pos);
				$tmp	= '/'.trim($tmp,'/').'/';
				$rwbase	= $tmp;
			}
			$htaccess	= '<IfModule mod_rewrite.c>'."\n";
			$htaccess	.= '	RewriteEngine On'."\n";
			$htaccess	.= '	RewriteBase '.$rwbase."\n";
			$htaccess	.= '	RewriteCond %{REQUEST_FILENAME} !-f'."\n";
			$htaccess	.= '	RewriteCond %{REQUEST_FILENAME} !-d'."\n";
			$htaccess	.= '	RewriteRule ^(.*)$ index.php?%{QUERY_STRING} [NE,L]'."\n";
			$htaccess	.= '</IfModule>'."\n";
			$filename	= INCPATH.'../../.htaccess';
			$res	= file_put_contents($filename, $htaccess);
			if( ! $res ) {
				$error	= TRUE;
			}
			@chmod($filename, 0777);
		}
		
		$convert_version	= FALSE;
		
		if( ! $error ) {
			$conn	= @mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
			$dbs	= @mysql_select_db($s['MYSQL_DBNAME'], $conn);
			if( !$conn || !$dbs ) {
				$_SESSION['INSTALL_STEP']	= 1;
				header('Location: ?next&r='.rand(0,99999));
			}
			@mysql_query('SET NAMES utf8', $conn);
			$tables	= array();
			$res	= mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
			if( mysql_num_rows($res) ) {
				while($tbl = mysql_fetch_row($res)) {
					$tables[]	= $tbl[0];
				}
			}
			$convert_version	= FALSE;
			if( isset($OLDC->VERSION) ) {
				$convert_version	= $OLDC->VERSION; 
			}
			elseif( file_exists(INCPATH.'../../include/conf_main.php') && in_array('users_watched', $tables) ) {
				$convert_version	= 'unofficial';
			}
			require_once(INCPATH.'func_database.php');
			$res	= create_database($convert_version);
			if( ! $res ) {
				$error	= TRUE;
			}
		}
		
		if( ! $error ) {
			if( $convert_version == 'unofficial' ) {
				$resize	= array();
				$path	= INCPATH.'../../i/attachments/1/';
				$dir	= opendir($path);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( FALSE === strpos($file, '_thumb.') ) { continue; }
					list($w, $h) = getimagesize($path.$file);
					if( $w!=60 || $h!=60 ) {
						$resize[]	= $path.$file;
					}
				}
				closedir($dir);
				include_once(INCPATH.'../../system/helpers/func_images.php');
				$C->IMAGE_MANIPULATION	= 'gd';
				foreach($resize as $file) {
					copy_attachment_videoimg($file, $file, 60);
				}
			}
		}
		if( ! $error ) {
			if( $convert_version == 'unofficial' ) {
				$path1	= INCPATH.'../../img/avatars/';
				$path2	= INCPATH.'../../i/avatars/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
					copy_attachment_videoimg($path1.$file, $path2.$file, 200);
				}
				closedir($dir);
				$path1	= INCPATH.'../../img/avatars/thumbs/';
				$path2	= INCPATH.'../../i/avatars/thumbs1/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
				}
				closedir($dir);
				$path1	= INCPATH.'../../img/avatars/thumbs2/';
				$path2	= INCPATH.'../../i/avatars/thumbs2/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
				}
				closedir($dir);
				include_once(INCPATH.'../../system/helpers/func_images.php');
				$C->IMAGE_MANIPULATION	= 'gd';
				$path1	= INCPATH.'../../img/avatars/thumbs2/';
				$path2	= INCPATH.'../../i/avatars/thumbs3/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					copy_attachment_videoimg($path1.$file, $path2.$file, 30);
				}
				closedir($dir);
			}
		}
		if( ! $error ) {
			$config	= @file_get_contents(INCPATH.'conf_main_empty.php');
			if( ! $config ) {
				$error	= TRUE;
			}
			if( ! $error ) {
				$rndkey	= substr(md5(time().rand()),0,5);
				$config	= config_replace_variable( $config,	'$C->DOMAIN',		$s['DOMAIN'] );
				$config	= config_replace_variable( $config,	'$C->SITE_URL',		$s['SITE_URL'] );
				$config	= config_replace_variable( $config,	'$C->RNDKEY',	$rndkey );
				$config	= config_replace_variable( $config,	'$C->DB_HOST',	$s['MYSQL_HOST'] );
				$config	= config_replace_variable( $config,	'$C->DB_USER',	$s['MYSQL_USER'] );
				$config	= config_replace_variable( $config,	'$C->DB_PASS',	$s['MYSQL_PASS'] );
				$config	= config_replace_variable( $config,	'$C->DB_NAME',	$s['MYSQL_DBNAME'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_MECHANISM',	$s['CACHE_MECHANISM'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_EXPIRE',	$s['CACHE_EXPIRE'], FALSE );
				$config	= config_replace_variable( $config,	'$C->CACHE_MEMCACHE_HOST',	$s['CACHE_MEMCACHE_HOST'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_MEMCACHE_PORT',	$s['CACHE_MEMCACHE_PORT'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_KEYS_PREFIX',	$rndkey );
				$config	= config_replace_variable( $config,	'$C->CACHE_FILESYSTEM_PATH',	'$C->INCPATH.\'cache/\'', FALSE );
				$config	= config_replace_variable( $config,	'$C->IMAGE_MANIPULATION',	isset($OLDC->IMAGE_MANIPULATION)&&$OLDC->IMAGE_MANIPULATION=='imagemagick_cli' ? 'imagemagick_cli' : 'gd' );
				$config	= config_replace_variable( $config,	'$C->IM_CONVERT',			isset($OLDC->IM_CONVERT) ? $OLDC->IM_CONVERT : 'convert'	 );
				$config	= config_replace_variable( $config,	'$C->USERS_ARE_SUBDOMAINS',	isset($OLDC->USERS_ARE_SUBDOMAINS)&&$OLDC->USERS_ARE_SUBDOMAINS ? 'TRUE' : 'FALSE', FALSE );
				$config	= config_replace_variable( $config,	'$C->LANGUAGE',	$s['LANGUAGE'] );
				$config	= config_replace_variable( $config,	'$C->USERS_ARE_SUBDOMAINS',		isset($OLDC->USERS_ARE_SUBDOMAINS)&&$OLDC->USERS_ARE_SUBDOMAINS ? 'TRUE' : 'FALSE', FALSE );
				$config	= config_replace_variable( $config,	'$C->RPC_PINGS_ON',		isset($OLDC->RPC_PINGS_ON)&&!$OLDC->RPC_PINGS_ON ? 'FALSE' : 'TRUE', FALSE );
				$config	= config_replace_variable( $config,	'$C->RPC_PINGS_SERVERS',	isset($OLDC->RPC_PINGS_SERVERS) ? var_export($OLDC->RPC_PINGS_SERVERS,TRUE) : 'array(\'http://rpc.pingomatic.com\')', FALSE );
				$config	= config_replace_variable( $config,	'$C->INSTALLED',			'TRUE', FALSE );
				$config	= config_replace_variable( $config,	'$C->VERSION',			VERSION );
				$config	= config_replace_variable( $config,	'$C->DEBUG_USERS',		isset($OLDC->DEBUG_USERS) ? var_export($OLDC->DEBUG_USERS,TRUE) : 'array()', FALSE );
				$filename	= INCPATH.'../../system/conf_main.php';
				$res	= file_put_contents($filename, $config);
				if( ! $res ) {
					$error	= TRUE;
				}
			}
		}
		if( ! $error ) {
			@chmod( INCPATH.'../../.htaccess', 0664 );
			@chmod( INCPATH.'../../system/conf_main.php', 0755 );
			@chmod( INCPATH.'../../system/cache', 0777 );
			@chmod( INCPATH.'../../i/avatars', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs1', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs2', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs3', 0777 );
			@chmod( INCPATH.'../../i/attachments', 0777 );
			@chmod( INCPATH.'../../i/tmp', 0777 );
			@chmod( INCPATH.'../../system', 0755 );
			if( $convert_version == 'unofficial' ) {
				directory_tree_delete(INCPATH.'../../js/');
				directory_tree_delete(INCPATH.'../../css/');
				directory_tree_delete(INCPATH.'../../img/');
				directory_tree_delete(INCPATH.'../../api/');
				directory_tree_delete(INCPATH.'../../include/');
			}
			$url	= $s['SITE_URL'];
			session_unset();
			session_destroy();
			header('Location: '.$url);
		}
	}
	
	$html	.= '

							<div class="ttl">
								<div class="ttl2">
									<h3>Finishing Installation</h3>
								</div>
							</div>';
	$html	.= errorbox('Installation Failed!', 'Please <a href="?reset" style="font-size:inherit;">try again</a> or contact our team for help.', FALSE, 'margin-top:5px;');
	
?>