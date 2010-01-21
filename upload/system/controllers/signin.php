<?php
	
	if( $this->network->id && $this->user->is_logged ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('outside/global.php');
	$this->load_langfile('outside/signin.php');
	
	$D->page_title	= $this->lang('signin_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->email		= '';
	$D->password	= '';
	$D->rememberme	= FALSE;
	
	if( isset($_POST['email'], $_POST['password']) ) {
		$D->submit	= TRUE;
		$D->email		= trim($_POST['email']);
		$D->password	= trim($_POST['password']);
		$D->rememberme	= isset($_POST['rememberme']) && $_POST['rememberme']==1;
		if( empty($D->email) || empty($D->password) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'signin_form_errmsg';
		}
		else {
			if( $this->user->is_logged ) {
				$this->user->logout();
			}
			$res	= $this->user->login($D->email, md5($D->password), $D->rememberme);
			if( ! $res ) {
				$D->error	= TRUE;
				if( $this->network->id ) {
					$db2->query('SELECT id FROM users WHERE (email="'.$db2->e($D->email).'" OR username="'.$db2->e($D->email).'") AND password="'.$db2->e(md5($D->password)).'" AND active=0 LIMIT 1');
					if( $db2->num_rows() > 0 ) {
						$D->errmsg	= 'signin_form_errmsgsusp';
					}
				}
				if( empty($D->errmsg) ) {
					$D->errmsg	= 'signin_form_errmsg';
				}
			}
			else {
				$this->redirect($C->SITE_URL.'dashboard');
			}
		}
	}
	
	$D->num_members	= 0;
	$D->num_posts	= 0;
	if( $this->network->id ) {
		$D->num_members	= intval($db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1'));
		$D->num_posts	= intval($db2->fetch_field('SELECT COUNT(id) FROM posts WHERE user_id<>0 AND api_id<>2'));
	}
	
	$this->load_template('signin.php');
	
?>