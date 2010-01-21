<?php
	
	if( $this->network->id && $this->user->is_logged ) {
		$this->redirect('dashboard');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/home.php');
	
	$D->page_title	= $this->lang('home_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->is_network	= $this->network->id ? TRUE : FALSE;
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->email		= '';
	$D->password	= '';
	$D->rememberme	= TRUE;
	
	if( isset($_POST['email'], $_POST['password']) ) {
		$D->submit	= TRUE;
		$D->email		= trim($_POST['email']);
		$D->password	= trim($_POST['password']);
		$D->rememberme	= isset($_POST['rememberme']) && $_POST['rememberme']==1;
		if( empty($D->email) || empty($D->password) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'home_form_errmsg';
		}
		else {
			if( $D->is_network ) {
				if( $this->user->is_logged ) {
					$this->user->logout();
				}
				$res	= $this->user->login($D->email, md5($D->password), $D->rememberme);
			}
			else {
				$networks	= array();
				if( $n = $this->network->find_network_by_email($D->email) ) {
					$networks[$n->id]	= $n;
				}
				$r	= $db1->query('SELECT networks FROM emails_multinetworks WHERE email="'.$db1->e($D->email).'" LIMIT 1');
				if($tmp = $db1->fetch_object($r)) {
					$tmp	= explode(',', stripslashes($tmp->networks));
					foreach($tmp as $sdf) {
						if( $sdf = $this->network->find_network_by_id($sdf) ) {
							$ndb	= new mysql($sdf->db_host, $sdf->db_user, $sdf->db_pass, $sdf->db_name);
							$ndb->query('SELECT id FROM users WHERE email="'.$ndb->e($D->email).'" LIMIT 1');
							if( $ndb->num_rows() > 0 ) {
								$networks[$sdf->id]	= $sdf;
							}
						}
					}
				}
				if( 0 == count($networks) ) {
					$res	= FALSE;
				}
				elseif( 1 == count($networks) ) {
					$this->network->LOAD_by_id( key($networks) );
					$this->user	= new user();
					$this->user->LOAD();
					if( $this->user->is_logged ) {
						$this->user->logout();
					}
					$res	= $this->user->login($D->email, md5($D->password), $D->rememberme);
				}
				elseif( isset($_POST['networkid']) && isset($networks[$_POST['networkid']]) ) {
					$this->network->LOAD_by_id( $_POST['networkid'] );
					$this->user	= new user();
					$this->user->LOAD();
					if( $this->user->is_logged ) {
						$this->user->logout();
					}
					$res	= $this->user->login($D->email, md5($D->password), $D->rememberme);
				}
				else {
					$this->network->LOAD_by_id( key($networks) );
					$u	= $this->network->get_user_by_email($D->email, TRUE);
					if( $u && $u->password == md5($D->password) ) {
						$D->networks	= $networks;
						$this->load_template('mobile/home_choosenetwork.php');
						return;
					}
					$res	= FALSE;
				}
			}
			if( ! $res ) {
				$D->error	= TRUE;
				$D->errmsg	= 'home_form_errmsg';
			}
			else {
				$this->redirect($C->SITE_URL.'dashboard');
			}
		}
	}
	
	$this->load_template('mobile/home.php');
	
?>