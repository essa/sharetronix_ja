<?php
	
	if( $this->user->is_logged ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('outside/global.php');
	$this->load_langfile('outside/signup.php');
	$this->load_langfile('email/signup.php');
	
	$D->page_title	= $this->lang('signup_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->network_members	= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1');
	
	require_once( $C->INCPATH.'helpers/func_signup.php' );
	require_once( $C->INCPATH.'helpers/func_images.php' );
	require_once( $C->INCPATH.'helpers/func_captcha.php' );
	
	if( $C->USERS_EMAIL_CONFIRMATION && isset($_POST['email']) && !empty($_POST['email']) )
	{
		$D->submit	= TRUE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$D->errmsg_lngkeys	= array();
		
		$email	= strtolower(trim($_POST['email']));
		if( ! is_valid_email($email) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'signup_err_email_invalid';
		}
		if( ! $D->error ) {
			$db2->query('SELECT id, active FROM users WHERE email="'.$db2->e($email).'" LIMIT 1');
			if($obj = $db2->fetch_object()) {
				$D->error	= TRUE;
				$D->errmsg	= $obj->active==1 ? 'signup_err_email_exists' : 'signup_err_email_disabled';
			}
		}
		if( ! $D->error ) {
			$invited_code	= isset($_SESSION['invite_code']) ? trim($_SESSION['invite_code']) : '';
			$D->reg_key	= md5(rand().time().rand());
			$db1->query('REPLACE INTO unconfirmed_registrations SET email="'.$db1->e($email).'", confirm_key="'.$db1->e($D->reg_key).'", invited_code="'.$db1->e($invited_code).'", date="'.time().'" ');
			$D->reg_id	= intval($db1->insert_id());
			$D->activation_link	= $C->SITE_URL.'signup/regid:'.$D->reg_id.'/regkey:'.$D->reg_key;
			$subject	= $this->lang('signup_email_subject', array('#SITE_TITLE#'=>$C->SITE_TITLE));
			$msgtxt	= $this->load_template('email/signup_txt.php', FALSE);
			$msghtml	= $this->load_template('email/signup_html.php', FALSE);
			do_send_mail_html($email, $subject, $msgtxt, $msghtml);
		}
		$D->email	= $email;
		$D->steps	= $D->network_members==0 ? 2 : 3;
		$this->load_template('signup-step1.php');
	}
	elseif( ($C->USERS_EMAIL_CONFIRMATION && $this->param('regid') && $this->param('regkey')) || !$C->USERS_EMAIL_CONFIRMATION )
	{
		$D->email		= '';
		$D->fullname	= '';
		$invited_code	= '';
		if( $C->USERS_EMAIL_CONFIRMATION ) {
			$reg_id	= intval($this->param('regid'));
			$reg_key	= $db1->e($this->param('regkey'));
			$db1->query('SELECT email, fullname, invited_code FROM unconfirmed_registrations WHERE id="'.$reg_id.'" AND confirm_key="'.$reg_key.'" LIMIT 1');
			if( ! $obj = $db1->fetch_object() ) {
				$D->submit	= FALSE;
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_confirmlink';
				$D->errmsg_lngkeys	= array();
				$D->steps	= 3;
				$this->load_template('signup-step1.php');
				return;
			}
			$invited_code	= trim(stripslashes($obj->invited_code));
			$D->email		= stripslashes($obj->email);
			$D->fullname	= stripslashes($obj->fullname);
		}
		else {
			$D->captcha_key	= '';
			$D->captcha_word	= '';
			$D->captcha_html	= '';
			list($D->captcha_word, $D->captcha_html)	= generate_captcha(5);
			$D->captcha_key	= md5($D->captcha_word.time().rand());
			$_SESSION['captcha_'.$D->captcha_key]	= $D->captcha_word;
		}
		$D->steps	= $C->USERS_EMAIL_CONFIRMATION ? ($D->network_members>0 ? 3 : 2) : ($D->network_members>0 ? 2 : 1);
		$D->submit	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$D->errmsg_lngkeys	= array();
		$D->username	= '';
		$D->password	= '';
		$D->password2	= '';
		if( isset($_POST['fullname'], $_POST['username']) ) {
			$D->submit	= TRUE;
			$D->fullname	= trim($_POST['fullname']);
			$D->username	= trim($_POST['username']);
			$D->password	= isset($_POST['password']) ? trim($_POST['password']) : '';
			$D->password2	= isset($_POST['password2']) ? trim($_POST['password2']) : '';
			if( ! $C->USERS_EMAIL_CONFIRMATION ) {
				$D->email	= isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
				if( ! is_valid_email($D->email) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signup_err_email_invalid';
				}
				else {
					$db2->query('SELECT id, active FROM users WHERE email="'.$db2->e($D->email).'" LIMIT 1');
					if($obj = $db2->fetch_object()) {
						$D->error	= TRUE;
						$D->errmsg	= $obj->active==1 ? 'signup_err_email_exists' : 'signup_err_email_disabled';
					}
				}
			}
			if( !$D->error && empty($D->fullname) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_fullname';
			}
			if( !$D->error && empty($D->username) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_username';
			}
			if( !$D->error && (strlen($D->username)<3 || strlen($D->username)>30) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_usernmlen';
			}
			if( !$D->error && preg_match('/[^a-z0-9-_]/i', $D->username) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_usernmlet';
			}
			if( !$D->error ) {
				$db2->query('SELECT id, active FROM users WHERE username="'.$db2->e($D->username).'" LIMIT 1');
				if($obj = $db2->fetch_object()) {
					$D->error	= TRUE;
					$D->errmsg	= $obj->active==1 ? 'signup_err_usernm_exists' : 'signup_err_usernm_disabled';
				}
			}
			if( !$D->error ) {
				$db2->query('SELECT id FROM groups WHERE groupname="'.$db2->e($D->username).'" LIMIT 1');
				if($obj = $db2->fetch_object()) {
					$D->error	= TRUE;
					$D->errmsg	= 'signup_err_usernm_exists';
				}
			}
			if( !$D->error && file_exists($C->INCPATH.'controllers/'.strtolower($D->username).'.php') ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_usernm_existss';
			}
			if( !$D->error && file_exists($C->INCPATH.'controllers/mobile/'.strtolower($D->username).'.php') ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_usernm_existss';
			}
			if( !$D->error && file_exists($C->INCPATH.'../'.strtolower($D->username)) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_usernm_existss';
			}
			if( !$D->error && (empty($D->password) || empty($D->password2)) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_password';
				$D->password	= '';
				$D->password2	= '';
			}
			if( !$D->error && strlen($D->password)<5 ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_passwdlen';
			}
			if( !$D->error && $D->password!=$D->password2 ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signup_err_passwddiff';
				$D->password	= '';
				$D->password2	= '';
			}
			if( !$D->error && !$C->USERS_EMAIL_CONFIRMATION ) {
				if( !isset($_POST['captcha_key'],$_POST['captcha_word']) || !isset($_SESSION['captcha_'.$_POST['captcha_key']]) || $_SESSION['captcha_'.$_POST['captcha_key']]!=strtolower($_POST['captcha_word']) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signup_err_captcha';
				}
			}
			if( !$D->error ) {
				$tmplang	= $db2->fetch_field('SELECT value FROM settings WHERE word="LANGUAGE" LIMIT 1');
				$tmpzone	= $db2->fetch_field('SELECT value FROM settings WHERE word="DEF_TIMEZONE" LIMIT 1');
				$tmppass	= md5($D->password);
				$db2->query('INSERT INTO users SET email="'.$db2->e($D->email).'", username="'.$db2->e($D->username).'", password="'.$db2->e($tmppass).'", fullname="'.$db2->e($D->fullname).'", language="'.$tmplang.'", timezone="'.$tmpzone.'", reg_date="'.time().'", reg_ip="'.ip2long($_SERVER['REMOTE_ADDR']).'", active=1');
				$user_id	= intval($db2->insert_id());
				$db1->query('DELETE FROM unconfirmed_registrations WHERE email="'.$db1->e($D->email).'" ');
				$this->user->login($D->email, md5($D->password), FALSE);
				
				$gravatar_url	= 'http://www.gravatar.com/avatar/'.md5($D->email).'?s='.$C->AVATAR_SIZE.'&d=404';
				$gravatar_local	= $C->TMP_DIR.'grvtr'.time().rand(0,9999).'.jpg';
				if( @copy($gravatar_url, $gravatar_local) ) {
					list($w, $h, $tp) = @getimagesize($gravatar_local);
					if( $w && $h && $tp && $w==$C->AVATAR_SIZE && $h>=$C->AVATAR_SIZE && ($tp==IMAGETYPE_JPEG || $tp==IMAGETYPE_GIF || $tp==IMAGETYPE_PNG) ) {
						$fn	= time().rand(100000,999999).'.png';
						$res	= copy_avatar($gravatar_local, $fn);
						if( $res ) {
							$db2->query('UPDATE users SET avatar="'.$db2->escape($fn).'" WHERE id="'.$user_id.'" LIMIT 1');
							$this->network->get_user_by_id($user_id, TRUE);
						}
					}
					rm($gravatar_local);
				}
				
				$invited_from	= array();
				$r	= $db2->query('SELECT DISTINCT user_id FROM users_invitations WHERE recp_email="'.$db2->e($D->email).'" LIMIT 1');
				if( $db2->num_rows($r) > 0 ) {
					while($tmpu = $db2->fetch_object($r)) {
						$db2->query('INSERT INTO users_followed SET who="'.$tmpu->user_id.'", whom="'.$user_id.'", date="'.time().'", whom_from_postid="'.$this->network->get_last_post_id().'" ');
						$db2->query('UPDATE users SET num_followers=num_followers+1 WHERE id="'.$user_id.'" LIMIT 1');
						$this->network->get_user_follows($tmpu->user_id, TRUE);
						$invited_from[$tmpu->user_id]	= TRUE;
					}
					$this->network->get_user_by_id($user_id, TRUE);
					$this->network->get_user_follows($user_id, TRUE);
					$db2->query('UPDATE users_invitations SET recp_is_registered=1, recp_user_id="'.$user_id.'" WHERE recp_email="'.$db2->e($D->email).'" ');
				}
				if( ! empty($invited_code) ) {
					$db1->query('SELECT user_id FROM invitation_codes WHERE code="'.$db1->e($invited_code).'" AND network_id="'.$this->network->id.'" LIMIT 1');
					if($tmpu = $db1->fetch_object()) {
						if( ! isset($invited_from[$tmpu->user_id]) ) {
							$db2->query('INSERT INTO users_followed SET who="'.$tmpu->user_id.'", whom="'.$user_id.'", date="'.time().'", whom_from_postid="'.$this->network->get_last_post_id().'" ');
							$db2->query('UPDATE users SET num_followers=num_followers+1 WHERE id="'.$user_id.'" LIMIT 1');
							$this->network->get_user_follows($tmpu->user_id, TRUE);
							$invited_from[$tmpu->user_id]	= TRUE;
							$this->network->get_user_by_id($user_id, TRUE);
							$this->network->get_user_follows($user_id, TRUE);
							$db2->query('INSERT INTO users_invitations SET user_id="'.$tmpu->user_id.'", date="'.time().'", recp_name="'.$db2->e($D->fullname).'", recp_email="'.$db2->e($D->email).'", recp_is_registered=1, recp_user_id="'.$user_id.'" ');
						}
					}
				}
				if( $D->network_members > 0 ) {
					$key	= md5(time().rand(0,999999));
					$_SESSION['reg_'.$key]	= (object) array (
						'network_id'	=> $this->network->id,
						'user_id'		=> $user_id,
					);
					
					$this->load_langfile('inside/notifications.php');
					$this->load_langfile('email/notifications.php');
					$r	= $db2->query('SELECT id FROM users WHERE active=1', FALSE);
					while($sdf = $db2->fetch_object($r)) {
						$uid	= intval($sdf->id);
						$send_post	= FALSE;
						$send_mail	= FALSE;
						$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_registers );
						if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
						if( $send_post ) {
							$lng	= array('#COMPANY#'=>$C->COMPANY, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->user->info->username.'</a>');
							$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_registers', $lng, 'replace');
						}
						if( $send_mail ) {
							$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#COMPANY#'=>$C->COMPANY, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#A0#'=>$C->SITE_URL.$this->user->info->username);
							$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#COMPANY#'=>$C->COMPANY, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#A0#'=>'');
							$subject		= $this->lang('emlsubj_ntf_me_if_u_registers', $lng_txt);
							$message_txt	= $this->lang('emltxt_ntf_me_if_u_registers', $lng_txt);
							$message_htm	= $this->lang('emlhtml_ntf_me_if_u_registers', $lng_htm);
							$this->network->send_notification_email($uid, 'u_edt_profl', $subject, $message_txt, $message_htm);
						}
					}
					
					$this->redirect( $C->SITE_URL.'signup/follow/regid:'.$key);
				}
				else {
					$this->redirect($C->SITE_URL.'dashboard');
				}
			}
		}
		$this->load_template('signup-step2.php');
	}
	elseif( $C->USERS_EMAIL_CONFIRMATION )
	{
		$D->submit	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$D->email	= '';
		$D->steps	= $D->network_members==0 ? 2 : 3;
		$this->load_template('signup-step1.php');
	}
	
?>