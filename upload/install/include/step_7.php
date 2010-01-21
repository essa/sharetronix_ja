<?php
	
	$PAGE_TITLE	= 'Installation - Step 7';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$conn	= @mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
	$dbs	= @mysql_select_db($s['MYSQL_DBNAME'], $conn);
	if( !$conn || !$dbs ) {
		$_SESSION['INSTALL_STEP']	= 1;
		header('Location: ?next&r='.rand(0,99999));
	}
	$res	= @mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
	if( @mysql_num_rows($res) ) {
		$tables	= array();
		while($tbl = @mysql_fetch_row($res)) {
			$tables[]	= $tbl[0];
		}
		sort($tables);
		$tables	= implode(',', $tables);
		if( $tables == 'cache,crons,groups,groups_admins,groups_deleted,groups_followed,groups_private_members,groups_rssfeeds,groups_rssfeeds_posts,invitation_codes,post_api,post_favs,post_userbox,post_userbox_feeds,posts,posts_attachments,posts_comments,posts_comments_mentioned,posts_comments_watch,posts_mentioned,posts_pr,posts_pr_attachments,posts_pr_comments,posts_pr_comments_mentioned,posts_pr_comments_watch,posts_pr_mentioned,searches,settings,unconfirmed_registrations,users,users_dashboard_tabs,users_details,users_followed,users_ignores,users_invitations,users_notif_rules,users_pageviews,users_rssfeeds,users_rssfeeds_posts' ) {
			$res	= @mysql_query('SELECT id, username, email FROM users WHERE is_network_admin=1 ORDER BY active=1 DESC, id ASC LIMIT 1');
		}
		elseif( $tables == 'badwords,cache,crons,post_api,posts,posts_attachments,posts_attachments_d,posts_direct,posts_favs,posts_from_email,posts_mentioned,posts_mentioned_d,posts_pingbacks,posts_usertabs,users,users_feeds,users_feeds_posts,users_ignores,users_invitations,users_notif_rules,users_notif_sent,users_profile_hits,users_spammers,users_tabs_state,users_watched' ) {
			$res	= @mysql_query('SELECT id, username, email FROM users WHERE id=1 LIMIT 1');
		}
		if($adm = @mysql_fetch_object($res)) {
			$s['ADMIN_ID']	= $adm->id;
			$s['ADMIN_USER']	= $adm->username;
			$s['ADMIN_EMAIL']	= $adm->email;
			$_SESSION['INSTALL_STEP']	= 7;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	
	if( ! isset($s['ADMIN_ID']) ) {
		$s['ADMIN_ID']	= FALSE;
	}
	if( ! isset($s['ADMIN_USER']) ) {
		$s['ADMIN_USER']	= '';
	}
	if( ! isset($s['ADMIN_PASS']) ) {
		$s['ADMIN_PASS']	= '';
	}
	if( ! isset($s['ADMIN_PASS2']) ) {
		$s['ADMIN_PASS2']	= '';
	}
	if( ! isset($s['ADMIN_EMAIL']) ) {
		$s['ADMIN_EMAIL']	= '';
	}
	
	$submit	= FALSE;
	$error	= FALSE;
	$errmsg	= '';
	if( isset($_POST['ADMIN_USER'], $_POST['ADMIN_PASS'], $_POST['ADMIN_PASS2'], $_POST['ADMIN_EMAIL']) ) {
		$submit	= TRUE;
		$_SESSION['INSTALL_STEP']	= 6;
		$s['ADMIN_ID']	= 0;
		$s['ADMIN_USER']	= trim($_POST['ADMIN_USER']);
		$s['ADMIN_PASS']	= trim($_POST['ADMIN_PASS']);
		$s['ADMIN_PASS2']	= trim($_POST['ADMIN_PASS2']);
		$s['ADMIN_EMAIL']	= trim($_POST['ADMIN_EMAIL']);
		if( empty($s['ADMIN_USER']) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter Username.';
		}
		if( !$error && ! is_valid_username($s['ADMIN_USER'], TRUE) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter valid Username.';
		}
		if( !$error ) {
			$res	= @mysql_query('SELECT id FROM users WHERE username="'.addslashes($s['ADMIN_USER']).'" LIMIT 1');
			if( $res ) {
				if( @mysql_num_rows($res) > 0 ) {
					$error	= TRUE;
					$errmsg	= 'This username is already registered.';
				}
			}
		}
		if( !$error && strlen($s['ADMIN_PASS'])<5 ) {
			$error	= TRUE;
			$errmsg	= 'Password must be at least 5 characters long.';
		}
		if( !$error && $s['ADMIN_PASS']!=$s['ADMIN_PASS2'] ) {
			$error	= TRUE;
			$errmsg	= 'Passwords don`t match.';
		}
		if( !$error && !is_valid_email($s['ADMIN_EMAIL']) ) {
			$error	= TRUE;
			$errmsg	= 'Invalid E-mail address.';
		}
		if( !$error ) {
			$res	= @mysql_query('SELECT id FROM users WHERE email="'.addslashes($s['ADMIN_EMAIL']).'" LIMIT 1');
			if( $res ) {
				if( @mysql_num_rows($res) > 0 ) {
					$error	= TRUE;
					$errmsg	= 'This e-mail is already registered.';
				}
			}
		}
		if( ! $error ) {
			unset($s['ADMIN_PASS2']);
			$_SESSION['INSTALL_STEP']	= 7;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Administrative Account</h3>
								</div>
							</div>
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<p>Create an Administrative account for accessing the Administration center.</p>';
	if( $error ) {
		$html	.= errorbox('Error', $errmsg);
	}
	$html	.= '
										<form method="post" action="">
										<table id="setform" cellpadding="5">
											<tr>
												<td class="setparam" width="120">Admin Username:</td>
												<td style="padding-bottom:2px;"><input type="text" autocomplete="off" class="setinp" name="ADMIN_USER" value="'.htmlspecialchars($s['ADMIN_USER']).'" /></td>
											</tr>
											<tr>
												<td class="setparam">Admin Password:</td>
												<td style="padding-bottom:2px;"><input type="password" autocomplete="off" class="setinp" name="ADMIN_PASS" value="'.htmlspecialchars($s['ADMIN_PASS']).'" /></td>
											</tr>
											<tr>
												<td class="setparam">Password Again:</td>
												<td style="padding-bottom:2px;"><input type="password" autocomplete="off" class="setinp" name="ADMIN_PASS2" value="'.htmlspecialchars($s['ADMIN_PASS2']).'" /></td>
											</tr>
											<tr>
												<td class="setparam" width="120">E-Mail Address:</td>
												<td style="padding-bottom:2px;"><input type="text" class="setinp" name="ADMIN_EMAIL" value="'.htmlspecialchars($s['ADMIN_EMAIL']).'" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" name="submit" value="Continue" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
										</form>
									</div>
								</div>
							</div>';
	
?>