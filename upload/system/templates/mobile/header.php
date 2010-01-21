<?php
	
	if( $this->user->is_logged ) {
		$this->user->write_pageview();
	}
	
	$this->load_langfile('mobile/header.php');
	
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?= $D->page_title ?></title>
		<link href="<?= $C->IMG_URL ?>css/mobile.css" media="handheld" rel="stylesheet" type="text/css" />
		<link href="<?= $C->IMG_URL ?>css/mobile.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?= $C->IMG_URL ?>js/mobile.js"></script>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<?php if( isset($C->HDR_SHOW_LOGO) && $C->HDR_SHOW_LOGO==0 ) { ?>
		<style type="text/css"> #hdr { background-image: none; } </style>
		<?php } elseif( isset($C->HDR_SHOW_LOGO) && $C->HDR_SHOW_LOGO==2 && !empty($C->HDR_CUSTOM_LOGO) ) { ?>
		<style type="text/css"> #hdr { padding:0px; height:38px; background-image: url('<?= $C->IMG_URL.'attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_LOGO ?>'); } </style>
		<?php } ?>
	</head>
	<body>
		
		<?php if( $this->network->id ) { ?>
		<h1 id="hdr"><?= $C->HDR_SHOW_COMPANY ? htmlspecialchars($C->COMPANY) : '&nbsp;' ?></h1>
		<hr />
		<?php } else { ?>
		<h1 id="hdr"><span style="display:none"><?= htmlspecialchars($C->SITE_TITLE) ?></span>&nbsp;</h1>
		<hr />
		<?php } ?>
		
		<?php if( $this->user->is_logged ) { ?>
		<div id="nav">
			<?php if( $this->request[0] == 'dashboard' ) { ?>
			<a href="<?= $C->SITE_URL ?>dashboard" accesskey="0" class="on"><b><?= $this->lang('header_nav_home') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>dashboard" accesskey="0"><?= $this->lang('header_nav_home') ?></a>
			<?php }  ?>
			<span>|</span>
			<?php if( $this->request[0] == 'newpost' ) { ?>
			<a href="<?= $C->SITE_URL ?>newpost" accesskey="1" class="on"><b><?= $this->lang('header_nav_newpost') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>newpost" accesskey="1"><?= $this->lang('header_nav_newpost') ?></a>
			<?php }  ?>
			<span>|</span>
			<?php if( $this->request[0] == 'search' ) { ?>
			<a href="<?= $C->SITE_URL ?>search" accesskey="2" class="on"><b><?= $this->lang('header_nav_search') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>search" accesskey="2"><?= $this->lang('header_nav_search') ?></a>
			<?php }  ?>
			<div class="klear"></div>
		</div>
		<hr />
		<?php } ?>
