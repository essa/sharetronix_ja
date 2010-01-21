<?php
	
	if( ! isset($C) ) { $C = new stdClass; }
	include_once(INCPATH.'../../system/conf_embed.php');
	$VIDSRC	= $C->NEWPOST_EMBEDVIDEO_SOURCES;
	
	function create_database($convert_version=FALSE)
	{
		global $s, $VIDSRC;
		$conn	= @mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
		$dbs	= @mysql_select_db($s['MYSQL_DBNAME'], $conn);
		if( !$conn || !$dbs ) {
			return FALSE;
		}
		@mysql_query('SET NAMES utf8', $conn);
		
		if( $convert_version=='1.0.3' || $convert_version=='1.0.2' || $convert_version=='1.0.1' || $convert_version=='1.0.0' ) {
			@mysql_query("REPLACE INTO `settings` SET `word`='USERS_EMAIL_CONFIRMATION', `value`='1' ");
			return TRUE;
		}
		
		$prefix	= '';
		if( $convert_version ) {
			$prefix	= substr(md5(rand().time()),0,rand(5,8)).'__';	
		}
		$res	= TRUE;
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."cache`;
		");
		$varchar_len	= 255;
		$ver	= @mysql_get_server_info($conn);
		$ver	= str_replace('.','',substr($ver, 0, 5));
		if( intval($ver) >= 503 ) {
			$varchar_len	= 21810;
		}
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."cache` (
			  `key` varchar(32) NOT NULL,
			  `data` varchar(".$varchar_len.") NOT NULL,
			  `expire` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`key`)
			) ENGINE=MEMORY DEFAULT CHARSET=utf8;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."crons`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."crons` (
			  `cron` varchar(10) collate utf8_unicode_ci NOT NULL,
			  `last_run` int(10) unsigned NOT NULL,
			  `next_run` int(10) unsigned NOT NULL,
			  `is_running` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`cron`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `groupname` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `avatar` varchar(200) collate utf8_unicode_ci NOT NULL,
			  `about_me` varchar(200) collate utf8_unicode_ci NOT NULL,
			  `is_public` tinyint(1) unsigned NOT NULL,
			  `num_posts` int(10) unsigned NOT NULL default '0',
			  `num_followers` int(10) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `groupname` (`groupname`),
			  KEY `is_public` (`is_public`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_admins`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_admins` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `group_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `group_id` (`group_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_deleted`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_deleted` (
			  `id` int(10) unsigned NOT NULL,
			  `groupname` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `is_public` tinyint(1) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `is_public` (`is_public`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_followed`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_followed` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `group_id` int(10) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  `group_from_postid` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`),
			  KEY `group_id` (`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_private_members`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_private_members` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `group_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  `invited_by` int(10) unsigned NOT NULL,
			  `invited_date` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `group_id` (`group_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_rssfeeds`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_rssfeeds` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `group_id` int(10) unsigned NOT NULL,
			  `feed_url` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `feed_userpwd` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `feed_title` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `filter_keywords` varchar(1000) collate utf8_unicode_ci NOT NULL,
			  `date_added` int(10) unsigned NOT NULL,
			  `date_last_post` int(10) unsigned NOT NULL,
			  `date_last_crawl` int(10) unsigned NOT NULL,
			  `date_last_item` int(10) unsigned NOT NULL,
			  `added_by_user` int(10) unsigned NOT NULL,
			  `is_deleted` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `group_id` (`is_deleted`,`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."groups_rssfeeds_posts`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."groups_rssfeeds_posts` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `rssfeed_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `rssfeed_id` (`rssfeed_id`),
			  KEY `post_id` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."invitation_codes`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."invitation_codes` (
			  `code` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `user_id` int(10) NOT NULL,
			  PRIMARY KEY  (`code`),
			  KEY `network_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `api_id` smallint(5) unsigned NOT NULL default '0',
			  `user_id` int(10) unsigned NOT NULL,
			  `group_id` int(10) unsigned NOT NULL,
			  `message` varchar(1000) collate utf8_unicode_ci NOT NULL,
			  `mentioned` tinyint(2) unsigned NOT NULL default '0',
			  `attached` tinyint(1) unsigned NOT NULL default '0',
			  `posttags` tinyint(2) unsigned NOT NULL default '0',
			  `comments` smallint(4) unsigned NOT NULL default '0',
			  `date` int(10) unsigned NOT NULL,
			  `date_lastedit` int(10) NOT NULL,
			  `date_lastcomment` int(10) NOT NULL,
			  `ip_addr` bigint(10) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`),
			  KEY `group_id` (`group_id`),
			  FULLTEXT KEY `message` (`message`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_attachments`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_attachments` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `post_id` int(10) unsigned NOT NULL,
			  `type` enum('link','image','videoembed','videoupload','text','file') collate utf8_unicode_ci NOT NULL,
			  `data` text collate utf8_unicode_ci NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `type` (`type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_comments`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_comments` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `api_id` smallint(5) unsigned NOT NULL default '0',
			  `post_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  `message` text collate utf8_unicode_ci NOT NULL,
			  `mentioned` tinyint(2) unsigned NOT NULL,
			  `posttags` tinyint(2) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  `ip_addr` bigint(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_comments_mentioned`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_comments_mentioned` (
			  `id` int(10) NOT NULL auto_increment,
			  `comment_id` int(10) NOT NULL,
			  `user_id` int(10) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `comment_id` (`comment_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_comments_watch`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_comments_watch` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  `newcomments` smallint(5) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_mentioned`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_mentioned` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `post_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `api_id` smallint(5) unsigned NOT NULL default '0',
			  `user_id` int(10) unsigned NOT NULL,
			  `to_user` int(10) unsigned NOT NULL,
			  `message` varchar(1000) collate utf8_unicode_ci NOT NULL,
			  `mentioned` tinyint(2) unsigned NOT NULL default '0',
			  `attached` tinyint(1) unsigned NOT NULL default '0',
			  `posttags` tinyint(2) unsigned NOT NULL default '0',
			  `comments` smallint(4) unsigned NOT NULL default '0',
			  `date` int(10) unsigned NOT NULL,
			  `date_lastedit` int(10) NOT NULL,
			  `date_lastcomment` int(10) NOT NULL,
			  `ip_addr` bigint(10) NOT NULL,
			  `is_recp_del` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`),
			  KEY `to_user` (`to_user`),
			  KEY `is_recp_del` (`is_recp_del`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr_attachments`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr_attachments` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `post_id` int(10) unsigned NOT NULL,
			  `type` enum('link','image','videoembed','videoupload','text','file') collate utf8_unicode_ci NOT NULL,
			  `data` text collate utf8_unicode_ci NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `type` (`type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr_comments`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr_comments` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `api_id` smallint(5) unsigned NOT NULL default '0',
			  `post_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  `message` text collate utf8_unicode_ci NOT NULL,
			  `mentioned` tinyint(2) unsigned NOT NULL,
			  `posttags` tinyint(2) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  `ip_addr` bigint(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr_comments_mentioned`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr_comments_mentioned` (
			  `id` int(10) NOT NULL auto_increment,
			  `comment_id` int(10) NOT NULL,
			  `user_id` int(10) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `comment_id` (`comment_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr_comments_watch`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr_comments_watch` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  `newcomments` smallint(5) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`),
			  KEY `post_id` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."posts_pr_mentioned`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."posts_pr_mentioned` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `post_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_id` (`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."post_api`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."post_api` (
			  `id` smallint(5) unsigned NOT NULL auto_increment,
			  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
			  `total_posts` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			INSERT INTO `".$prefix."post_api` (`id`, `name`, `total_posts`) VALUES
			(1, 'mobi', 0),
			(2, 'RSS', 0),
			(3, 'email', 0),
			(4, 'web', 0);
		");
		$res	= $res && @mysql_query("
			UPDATE `".$prefix."post_api` SET id=0 WHERE id=4 LIMIT 1;
		");
		$res	= $res && @mysql_query("
			ALTER TABLE `".$prefix."post_api` AUTO_INCREMENT=4;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."post_favs`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."post_favs` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `post_type` enum('public','private') collate utf8_unicode_ci NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `post_type` (`post_type`,`post_id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."post_userbox`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."post_userbox` (
			  `user_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  KEY `user_id` (`user_id`),
			  KEY `post_id` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."post_userbox_feeds`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."post_userbox_feeds` (
			  `user_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  KEY `user_id` (`user_id`),
			  KEY `post_id` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."searches`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."searches` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `search_key` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `search_string` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `search_url` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `added_date` int(10) unsigned NOT NULL,
			  `total_hits` mediumint(5) unsigned NOT NULL default '0',
			  `last_results` mediumint(5) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`,`search_key`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("		
			DROP TABLE IF EXISTS `".$prefix."settings`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."settings` (
			  `word` varchar(100) collate utf8_unicode_ci NOT NULL,
			  `value` text collate utf8_unicode_ci NOT NULL,
			  UNIQUE KEY `word` (`word`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			INSERT INTO `".$prefix."settings` (`word`, `value`) VALUES
			('SITE_TITLE', '".mysql_real_escape_string($s['SITE_TITLE'])."'),
			('POST_MAX_SYMBOLS', '160'),
			('LANGUAGE', '".mysql_real_escape_string($s['LANGUAGE'])."'),
			('SYSTEM_EMAIL', '".mysql_real_escape_string($s['ADMIN_EMAIL'])."'),
			('COMPANY', '".mysql_real_escape_string($s['SITE_TITLE'])."'),
			('ATTACH_LINK_DISABLED', '0'),
			('ATTACH_IMAGE_DISABLED', '0'),
			('ATTACH_VIDEO_DISABLED', '0'),
			('ATTACH_FILE_DISABLED', '0'),
			('USERS_EMAIL_CONFIRMATION', '1'),
			('MOBI_DISABLED', '0');
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."unconfirmed_registrations`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."unconfirmed_registrations` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `fullname` varchar(100) collate utf8_unicode_ci NOT NULL,
			  `confirm_key` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `invited_code` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `email` (`email`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `facebook_uid` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `username` varchar(200) collate utf8_unicode_ci NOT NULL,
			  `password` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `fullname` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `avatar` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `about_me` text collate utf8_unicode_ci NOT NULL,
			  `tags` text collate utf8_unicode_ci NOT NULL,
			  `gender` enum('','m','f') collate utf8_unicode_ci NOT NULL,
			  `birthdate` date NOT NULL,
			  `position` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `location` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
			  `timezone` varchar(100) collate utf8_unicode_ci NOT NULL,
			  `num_posts` int(10) unsigned NOT NULL,
			  `num_followers` int(10) unsigned NOT NULL,
			  `used_storage` bigint(10) unsigned NOT NULL,
			  `js_animations` tinyint(1) unsigned NOT NULL default '1',
			  `dbrd_groups_closed` tinyint(1) unsigned NOT NULL default '0',
			  `dbrd_whattodo_closed` tinyint(1) unsigned NOT NULL default '0',
			  `comments_expanded` tinyint(1) unsigned NOT NULL default '0',
			  `reg_date` int(10) unsigned NOT NULL,
			  `reg_ip` bigint(10) NOT NULL,
			  `lastlogin_date` int(10) unsigned NOT NULL,
			  `lastlogin_ip` bigint(10) NOT NULL,
			  `lastpost_date` int(10) unsigned NOT NULL,
			  `lastemail_date` int(10) unsigned NOT NULL,
			  `lastclick_date` int(10) unsigned NOT NULL,
			  `lastclick_date_newest_post` int(10) unsigned NOT NULL,
			  `pass_reset_key` varchar(32) collate utf8_unicode_ci NOT NULL,
			  `pass_reset_valid` int(10) unsigned NOT NULL,
			  `active` tinyint(1) unsigned NOT NULL default '1',
			  `is_network_admin` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `email` (`email`),
			  UNIQUE KEY `username` (`username`),
			  KEY `active` (`active`),
			  KEY `facebook_uid` (`facebook_uid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("		
			DROP TABLE IF EXISTS `".$prefix."users_dashboard_tabs`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_dashboard_tabs` (
			  `user_id` int(10) unsigned NOT NULL,
			  `tab` enum('','all','@me','private','commented','feeds') collate utf8_unicode_ci NOT NULL,
			  `state` tinyint(1) unsigned NOT NULL,
			  `newposts` smallint(4) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`user_id`,`tab`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_details`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_details` (
			  `user_id` int(10) unsigned NOT NULL,
			  `website` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `work_phone` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `personal_phone` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `personal_email` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_skype` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_icq` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_gtalk` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_msn` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_yahoo` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_aim` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `im_jabber` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_linkedin` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_facebook` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_twitter` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_flickr` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_friendfeed` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_delicious` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_digg` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_myspace` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_orcut` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_youtube` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_mixx` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_edno23` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `prof_favit` varchar(255) collate utf8_unicode_ci NOT NULL,
			  PRIMARY KEY  (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_followed`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_followed` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `who` int(10) unsigned NOT NULL,
			  `whom` int(10) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  `whom_from_postid` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `who` (`who`),
			  KEY `whom` (`whom`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_ignores`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_ignores` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `who` int(10) unsigned NOT NULL,
			  `whom` int(10) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `who` (`who`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_invitations`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_invitations` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `date` int(10) unsigned NOT NULL,
			  `recp_name` varchar(100) collate utf8_unicode_ci NOT NULL,
			  `recp_email` varchar(100) collate utf8_unicode_ci NOT NULL,
			  `recp_is_registered` tinyint(1) unsigned NOT NULL default '0',
			  `recp_user_id` int(10) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`,`recp_is_registered`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_notif_rules`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_notif_rules` (
			  `user_id` int(10) unsigned NOT NULL,
			  `ntf_them_if_i_follow_usr` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_them_if_i_comment` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_them_if_i_edt_profl` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_them_if_i_edt_pictr` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_them_if_i_create_grp` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_them_if_i_join_grp` tinyint(1) unsigned NOT NULL COMMENT '0-off, 1-on',
			  `ntf_me_if_u_follows_me` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_follows_u2` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_commments_me` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_commments_m2` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_edt_profl` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_edt_pictr` tinyint(3) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_creates_grp` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_joins_grp` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_invit_me_grp` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_posts_qme` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_posts_prvmsg` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  `ntf_me_if_u_registers` tinyint(1) unsigned NOT NULL COMMENT '0-off, 2-msg, 3-mail, 1-both',
			  PRIMARY KEY  (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_pageviews`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_pageviews` (
			  `id` bigint(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `date` varchar(13) collate utf8_unicode_ci NOT NULL COMMENT 'YY-MM-DD HH',
			  `pageviews` smallint(5) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_rssfeeds`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_rssfeeds` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `feed_url` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `feed_userpwd` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `feed_title` varchar(255) collate utf8_unicode_ci NOT NULL,
			  `filter_keywords` varchar(1000) collate utf8_unicode_ci NOT NULL,
			  `date_added` int(10) unsigned NOT NULL,
			  `date_last_post` int(10) unsigned NOT NULL,
			  `date_last_crawl` int(10) unsigned NOT NULL,
			  `date_last_item` int(10) unsigned NOT NULL,
			  `is_deleted` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `group_id` (`is_deleted`,`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		$res	= $res && @mysql_query("
			DROP TABLE IF EXISTS `".$prefix."users_rssfeeds_posts`;
		");
		$res	= $res && @mysql_query("
			CREATE TABLE `".$prefix."users_rssfeeds_posts` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `rssfeed_id` int(10) unsigned NOT NULL,
			  `post_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `rssfeed_id` (`rssfeed_id`),
			  KEY `post_id` (`post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		if( ! $res ) {
			if( ! empty($prefix) ) {
				database_drop_tables_with_prefix($prefix);
			}
			return FALSE;
		}
		if( !$convert_version && $s['ADMIN_ID']==0 ) {
			$res	= $res && @mysql_query("
				INSERT INTO `".$prefix."users` SET
				id='1',
				username='".mysql_real_escape_string($s['ADMIN_USER'])."',
				password='".mysql_real_escape_string(md5($s['ADMIN_PASS']))."',
				email='".mysql_real_escape_string($s['ADMIN_EMAIL'])."',
				fullname='".mysql_real_escape_string($s['SITE_TITLE'])."',
				reg_date='".time()."',
				reg_ip='".ip2long($_SERVER['REMOTE_ADDR'])."',
				lastpost_date='".time()."',
				language='".mysql_real_escape_string($s['LANGUAGE'])."',
				num_posts='1',
				num_followers='0',
				active='1',
				is_network_admin='1';
			");
			$res	= $res && @mysql_query("
				INSERT INTO `".$prefix."posts` SET
				id='1',
				api_id='0',
				user_id='1',
				group_id='0',
				message='Welcome to ".$s['SITE_TITLE']." :)',
				mentioned=0,
				attached=0,
				posttags=0,
				comments=0,
				date='".time()."',
				date_lastedit='',
				date_lastcomment='".time()."',
				ip_addr='".ip2long($_SERVER['REMOTE_ADDR'])."';
			");
			$res	= $res && @mysql_query("
				INSERT INTO post_userbox SET user_id='1', post_id='1';
			");
			if( ! $res ) {
				if( ! empty($prefix) ) {
					database_drop_tables_with_prefix($prefix);
				}
				return FALSE;
			}
		}
		if( $convert_version == 'unofficial' ) {
			$tables	= array();
			$tmp	= mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
			while($tbl = mysql_fetch_row($tmp)) {
				$tables[]	= $tbl[0];
			}
			if( in_array('users_watched', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users_followed` (who, whom, date, whom_from_postid) SELECT who, whom, date, whom FROM `users_watched` ORDER BY id ASC");
			}
			if( in_array('users_invitations', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users_invitations` (user_id, date, recp_name, recp_email, recp_is_registered, recp_user_id) SELECT user_id, date, recp_name, recp_email, recp_is_registered, recp_user_id FROM `users_invitations` ORDER BY id ASC");
			}
			if( in_array('users_ignores', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users_ignores` (who, whom, date) SELECT who, whom, date FROM `users_ignores` ORDER BY id ASC");
			}
			if( in_array('users_feeds', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users_rssfeeds` (id, user_id, feed_url, date_added, date_last_post, date_last_crawl, date_last_item, is_deleted) SELECT id, user_id, feed_url, date_added, date_lastpost, date_lastcrawl, date_feed_lastentry, '0' FROM `users_feeds` ORDER BY id ASC");
				@mysql_query("UPDATE `".$prefix."users_rssfeeds` SET date_date_last_post='".time()."', date_last_crawl='".time()."', date_last_item='".time()."' ");
			}
			if( in_array('users_feeds_posts', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users_rssfeeds_posts` (rssfeed_id, post_id) SELECT feed_id, post_id FROM `users_feeds_posts` ORDER BY id ASC");
			}
			if( in_array('users', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."users` (id, email, username, password, fullname, avatar, about_me, tags, gender, birthdate, location, language, num_posts, num_followers, reg_date, reg_ip, lastlogin_date, lastlogin_ip, lastpost_date, lastemail_date, lastclick_date, lastclick_date_newest_post, active, is_network_admin) SELECT id, email, username, password, fullname, avatar, about_me, tags, gender, birthdate, country, lang, '0', '0', reg_date, reg_ip, lastlogin_date, lastlogin_ip, lastpost_date, lastemail_date, lastclick_date, lastclick_date_newest_post, '1', '0' FROM `users` ORDER BY id ASC");
				if( $s['ADMIN_ID'] == 0 ) {
					@mysql_query("UPDATE `".$prefix."users` SET is_network_admin=1 WHERE id='1' LIMIT 1");
				}
				else {
					@mysql_query("UPDATE `".$prefix."users` SET is_network_admin=1 WHERE id='".intval($s['ADMIN_ID'])."' LIMIT 1");
				}
				$tmp	= @mysql_query("SELECT whom, COUNT(who) AS c FROM `".$prefix."users_followed` GROUP BY whom");
				while($obj = @mysql_fetch_object($tmp)) {
					@mysql_query("UPDATE `".$prefix."users` SET num_followers='".$obj->c."' WHERE id='".$obj->whom."' LIMIT 1");
				}
				$tmp	= @mysql_query("SELECT DISTINCT language FROM `".$prefix."users` ");
				while($obj = @mysql_fetch_object($tmp)) {
					if( empty($obj->language) ) {
						@mysql_query("UPDATE `".$prefix."users` SET language='".mysql_real_escape_string($s['LANGUAGE'])."' WHERE language='".$obj->language."' LIMIT 1");
					}
					elseif( $obj->language!=$s['LANGUAGE'] && !file_exists(INCPATH.'../../system/languages/'.$obj->language) ) {
						@mysql_query("UPDATE `".$prefix."users` SET language='".mysql_real_escape_string($s['LANGUAGE'])."' WHERE language='".$obj->language."' LIMIT 1");
					}
				}
			}
			if( in_array('posts_favs', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."post_favs` (user_id, post_type, post_id, date) SELECT user_id, 'public', post_id, date FROM `posts_favs` WHERE post_type='public' ORDER BY id ASC");
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."post_favs` (user_id, post_type, post_id, date) SELECT user_id, 'private', post_id, date FROM `posts_favs` WHERE post_type='direct' ORDER BY id ASC");
			}
			if( in_array('posts_mentioned', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_mentioned` (post_id, user_id) SELECT post_id, user_id FROM `posts_mentioned` ORDER BY id ASC");
			}
			if( in_array('posts_mentioned_d', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_pr_mentioned` (post_id, user_id) SELECT post_id, user_id FROM `posts_mentioned_d` ORDER BY id ASC");
			}
			if( in_array('posts_usertabs', $tables) ) {
				$res	= $res && @mysql_query("INSERT INTO `".$prefix."post_userbox` (user_id, post_id) SELECT user_id, post_id FROM posts_usertabs");
				$ids	= array();
				$tmp	= @mysql_query('SELECT id FROM posts WHERE is_feed=1');
				while($obj = @mysql_fetch_object($tmp)) {
					$ids[]	= $obj->id;
				}
				if( count($ids) ) {
					$ids	= implode(', ', $ids);
					@mysql_query("INSERT INTO `".$prefix."post_userbox_feeds` (user_id, post_id) SELECT user_id, post_id FROM `".$prefix."post_userbox` WHERE post_id IN(".$ids.")");
					@mysql_query("DELETE FROM `".$prefix."post_userbox` WHERE post_id IN(".$ids.")");
				}
			}
			if( in_array('posts', $tables) ) {
				$tmp	= @mysql_query("SELECT id, api_id, user_id, message, mentioned, attached_link, attachments, date, ip_address, is_feed FROM `posts` ORDER BY id ASC");
				$res	= $res && $tmp;
				while($obj = @mysql_fetch_object($tmp)) {
					$api_id	= $obj->api_id==1 ? 1 : ($obj->is_feed==1 ? 2 : 0);
					$message	= stripslashes($obj->message);
					$attached	= intval($obj->attachments);
					if( !empty($obj->attached_link) ) { $attached ++; }
					$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts` SET id='".$obj->id."', api_id='".$api_id."', user_id='".$obj->user_id."', group_id=0, message='".mysql_real_escape_string($message)."', mentioned='".$obj->mentioned."', attached='".$attached."', posttags=0, comments=0, date='".$obj->date."', date_lastcomment='".$obj->date."', ip_addr='".$obj->ip_address."' ");
					if( !empty($obj->attached_link) ) {
						$atch	= (object) array (
							'link'	=> stripslashes($obj->attached_link),
							'hits'	=> 0,
						);
						$atch	= mysql_real_escape_string(serialize($atch));
						$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_attachments` SET post_id='".$obj->id."', type='link', data='".$atch."' ");
					}
					if( $obj->attachments > 0 ) {
						$tmp2	= @mysql_query("SELECT embed_type, embed_w, embed_h, embed_thumb, if_image_filename, if_video_source, if_video_html FROM `posts_attachments` WHERE post_id='".$obj->id."' LIMIT 1");
						$res	= $res && $tmp2;
						if($atchobj = @mysql_fetch_object($tmp2)) {
							if( $atchobj->embed_type == 'video' ) {
								$src	= explode(' ', $atchobj->if_video_source);
								$src[0]	= strtolower($src[0]);
								if( isset($VIDSRC[$src[0]]) ) {
									$atch	= (object) array (
										'src_site'		=> $src[0],
										'src_id'		=> trim($src[1]),
										'title'		=> '',
										'file_thumbnail'	=> '',
										'embed_code'	=> stripslashes($atchobj->if_video_html),
										'embed_w'		=> $atchobj->embed_w,
										'embed_h'		=> $atchobj->embed_h,
										'orig_url'		=> str_replace('###ID###', trim($src[1]), $VIDSRC[$src[0]]->insite_url),
										'hits'	=> 0,
									);
									$fn	= $atchobj->embed_thumb;
									if( !empty($fn) && $fn!='_NOTHUMB.jpg' ) {
										$oldfile	= INCPATH.'../../img/attachments/thumbs/'.$fn;
										$newfn	= str_replace('.', '_thumb.', $fn);
										$newfile	= INCPATH.'../../i/attachments/1/'.$newfn;
										if( @copy($oldfile, $newfile) ) {
											@chmod($newfile, 0777);
											$atch->file_thumbnail	= $newfn;
										}
									}
									$atch	= mysql_real_escape_string(serialize($atch));
									$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_attachments` SET post_id='".$obj->id."', type='videoembed', data='".$atch."' ");
								}
							}
							elseif( $atchobj->embed_type == 'image' ) {
								$fn	= $atchobj->if_image_filename;
								$old_file	= INCPATH.'../../img/attachments/'.$fn;
								$old_thumb	= INCPATH.'../../img/attachments/thumbs/'.$atchobj->embed_thumb;
								$atch	= (object) array (
									'title'	=> $fn,
									'file_original'	=> str_replace('.', '_orig.', $fn),
									'file_preview'	=> str_replace('.', '_large.', $fn),
									'file_thumbnail'	=> str_replace('.', '_thumb.', $fn),
									'size_original'	=> array($atchobj->embed_w, $atchobj->embed_h),
									'size_preview'	=> array($atchobj->embed_w, $atchobj->embed_h),
									'filesize'	=> 0,
									'hits'	=> 0,
								);
								@copy($old_file, INCPATH.'../../i/attachments/1/'.$atch->file_original);
								@copy($old_file, INCPATH.'../../i/attachments/1/'.$atch->file_preview);
								@copy($old_thumb, INCPATH.'../../i/attachments/1/'.$atch->file_thumbnail);
								$atch->filesize	= intval( @filesize($old_file) );
								$atch	= mysql_real_escape_string(serialize($atch));
								$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_attachments` SET post_id='".$obj->id."', type='image', data='".$atch."' ");
							}
						}
					}
				}
			}
			if( in_array('posts_direct', $tables) ) {
				$tmp	= @mysql_query("SELECT id, api_id, user_id, to_user, message, mentioned, attached_link, attachments, date, ip_address FROM `posts_direct` ORDER BY id ASC");
				$res	= $res && $tmp;
				while($obj = @mysql_fetch_object($tmp)) {
					$api_id	= $obj->api_id==1 ? 1 : 0;
					$message	= stripslashes($obj->message);
					$attached	= intval($obj->attachments);
					if( !empty($obj->attached_link) ) { $attached ++; }
					$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_pr` SET id='".$obj->id."', api_id='".$api_id."', user_id='".$obj->user_id."', to_user='".$obj->to_user."', message='".mysql_real_escape_string($message)."', mentioned='".$obj->mentioned."', attached='".$attached."', posttags=0, comments=0, date='".$obj->date."', date_lastcomment='".$obj->date."', ip_addr='".$obj->ip_address."' ");
					if( !empty($obj->attached_link) ) {
						$atch	= (object) array (
							'link'	=> stripslashes($obj->attached_link),
							'hits'	=> 0,
						);
						$atch	= mysql_real_escape_string(serialize($atch));
						$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_pr_attachments` SET post_id='".$obj->id."', type='link', data='".$atch."' ");
					}
					if( $obj->attachments > 0 ) {
						$tmp2	= @mysql_query("SELECT embed_type, embed_w, embed_h, embed_thumb, if_image_filename, if_video_source, if_video_html FROM `posts_attachments_d` WHERE post_id='".$obj->id."' LIMIT 1");
						$res	= $res && $tmp2;
						if($atchobj = @mysql_fetch_object($tmp2)) {
							if( $atchobj->embed_type == 'video' ) {
								$src	= explode(' ', $atchobj->if_video_source);
								$src[0]	= strtolower($src[0]);
								if( isset($VIDSRC[$src[0]]) ) {
									$atch	= (object) array (
										'src_site'		=> $src[0],
										'src_id'		=> trim($src[1]),
										'title'		=> '',
										'file_thumbnail'	=> '',
										'embed_code'	=> stripslashes($atchobj->if_video_html),
										'embed_w'		=> $atchobj->embed_w,
										'embed_h'		=> $atchobj->embed_h,
										'orig_url'		=> str_replace('###ID###', trim($src[1]), $VIDSRC[$src[0]]->insite_url),
										'hits'	=> 0,
									);
									$fn	= $atchobj->embed_thumb;
									if( !empty($fn) && $fn!='_NOTHUMB.jpg' ) {
										$oldfile	= INCPATH.'../../img/attachments/thumbs/'.$fn;
										$newfn	= str_replace('.', '_thumb.', $fn);
										$newfile	= INCPATH.'../../i/attachments/1/'.$newfn;
										if( @copy($oldfile, $newfile) ) {
											@chmod($newfile, 0777);
											$atch->file_thumbnail	= $newfn;
										}
									}
									$atch	= mysql_real_escape_string(serialize($atch));
									$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_pr_attachments` SET post_id='".$obj->id."', type='videoembed', data='".$atch."' ");
								}
							}
							elseif( $atchobj->embed_type == 'image' ) {
								$fn	= $atchobj->if_image_filename;
								$old_file	= INCPATH.'../../img/attachments/'.$fn;
								$old_thumb	= INCPATH.'../../img/attachments/thumbs/'.$atchobj->embed_thumb;
								$atch	= (object) array (
									'title'	=> $fn,
									'file_original'	=> str_replace('.', '_orig.', $fn),
									'file_preview'	=> str_replace('.', '_large.', $fn),
									'file_thumbnail'	=> str_replace('.', '_thumb.', $fn),
									'size_original'	=> array($atchobj->embed_w, $atchobj->embed_h),
									'size_preview'	=> array($atchobj->embed_w, $atchobj->embed_h),
									'filesize'	=> 0,
									'hits'	=> 0,
								);
								@copy($old_file, INCPATH.'../../i/attachments/1/'.$atch->file_original);
								@copy($old_file, INCPATH.'../../i/attachments/1/'.$atch->file_preview);
								@copy($old_thumb, INCPATH.'../../i/attachments/1/'.$atch->file_thumbnail);
								$atch->filesize	= intval( @filesize($old_file) );
								$atch	= mysql_real_escape_string(serialize($atch));
								$res	= $res && @mysql_query("INSERT INTO `".$prefix."posts_pr_attachments` SET post_id='".$obj->id."', type='image', data='".$atch."' ");
							}
						}
					}
				}
			}
			if( ! $res ) {
				if( ! empty($prefix) ) {
					database_drop_tables_with_prefix($prefix);
				}
				return FALSE;
			}
			$tmp	= @mysql_query("SELECT id, user_id FROM `".$prefix."posts` WHERE user_id<>0 ORDER BY id ASC");
			while($obj = @mysql_fetch_object($tmp)) {
				@mysql_query("INSERT INTO `".$prefix."posts_comments_watch` SET user_id='".$obj->user_id."', post_id='".$obj->id."', newcomments=0");
			}
			$tmp	= @mysql_query("SELECT id, user_id, to_user FROM `".$prefix."posts_pr` WHERE user_id<>0 ORDER BY id ASC");
			while($obj = @mysql_fetch_object($tmp)) {
				@mysql_query("INSERT INTO `".$prefix."posts_pr_comments_watch` SET user_id='".$obj->user_id."', post_id='".$obj->id."', newcomments=0");
				@mysql_query("INSERT INTO `".$prefix."posts_pr_comments_watch` SET user_id='".$obj->to_user."', post_id='".$obj->id."', newcomments=0");
			}
			$tmp	= @mysql_query("SELECT user_id, COUNT(id) AS c FROM `".$prefix."posts` GROUP BY user_id");
			while($obj = @mysql_fetch_object($tmp)) {
				@mysql_query("UPDATE `".$prefix."users` SET num_posts='".$obj->c."' WHERE id='".$obj->user_id."' LIMIT 1");
			}
			if( ! $res ) {
				if( ! empty($prefix) ) {
					database_drop_tables_with_prefix($prefix);
				}
				return FALSE;
			}
			$res	= $res && @mysql_query("DROP TABLE IF EXISTS `badwords`, `posts_direct`, `posts_from_email`, `posts_pingbacks`, `posts_usertabs`, `users_feeds`, `users_feeds_posts`, `users_notif_rules`, `users_notif_sent`, `users_profile_hits`, `users_spammers`, `users_watched`, `users_tabs_state`, `posts_mentioned_d`, `posts_favs`, `posts_attachments_d` ;");
			foreach($tables as $tbl) {
				if( substr($tbl, 0, strlen($prefix)) == $prefix ) {
					$new	= substr($tbl,strlen($prefix));
					$res	= $res && @mysql_query("DROP TABLE IF EXISTS `".$new."`;");
					$res	= $res && @mysql_query("RENAME TABLE `".$tbl."` TO `".$new."`;");
				}
			}
			if( ! $res ) {
				if( ! empty($prefix) ) {
					database_drop_tables_with_prefix($prefix);
				}
				return FALSE;
			}
		}
		return $res;
	}
	
	function database_drop_tables_with_prefix($prefix)
	{
		if( empty($prefix) ) {
			return FALSE;
		}
		global $s;
		$conn	= @mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
		$dbs	= @mysql_select_db($s['MYSQL_DBNAME'], $conn);
		if( !$conn || !$dbs ) {
			return FALSE;
		}
		$tmp	= @mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
		while($tbl = mysql_fetch_row($tmp)) {
			$tbl	= $tbl[0];
			if( substr($tbl, 0, strlen($prefix)) == $prefix ) {
				@mysql_query("DROP TABLE IF EXISTS `".$tbl."`;");
			}
		}
	}
	
?>