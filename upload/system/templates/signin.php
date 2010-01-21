<?php
	
	$this->load_template('header.php');
	
?>		
			<?php if($D->error) { ?>
				<?= errorbox($this->lang('signin_form_error'), $this->lang($D->errmsg)); ?>
			<?php } elseif($this->param('pass')=='changed') { ?>
				<?= okbox($this->lang('signinforg_alldone_ttl'), $this->lang('signinforg_alldone_txt')) ?>
			<?php } ?>
			<div id="poblicpage_login">
				<form method="post" action="">
					<table id="regform" cellspacing="5">
						<tr>
							<td></td>
							<td>
								<b><?= $this->lang('signin_form_caption') ?></b>
								<a id="forgotpass" href="<?= $C->SITE_URL ?>signin/forgotten"><?= $this->lang('signin_form_forgotten') ?></a>
							</td>
						</tr>
						<tr>
							<td class="regparam"><?= $this->lang('signin_form_email') ?></td>
							<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" tabindex="1" maxlength="100" class="reginp" /></td>
						</tr>
						<tr>
							<td class="regparam"><?= $this->lang('signin_form_password') ?></td>
							<td><input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" tabindex="2" maxlength="100" class="reginp" /></td>
						</tr>
						<tr>
							<td></td>
							<td valign="middle">
								<input type="submit" value="<?= $this->lang('signin_form_submit') ?>" tabindex="4" style="float:left; padding:4px; font-weight:bold;" />
								<label style="margin:0px; padding:0px; margin-left:10px; margin-top:7px; float:left; clear:none;">
									<input type="checkbox" name="rememberme" value="1" <?= $D->rememberme==1?'checked="checked"':'' ?> tabindex="3" />
									<span style="padding:2px; padding-left:5px;"><?= $this->lang('signin_form_rem') ?></span>
								</label>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="poblicpage_info">
				<h2><?= $this->lang('signin_reg_title') ?></h2>
				<?= $this->lang('os_signin_reg_txt_comp', array('#COMPANY#'=>$C->COMPANY, '#NUM_MEMBERS#'=>$D->num_members, '#NUM_POSTS#'=>$D->num_posts)) ?>
				<div id="joinnow">
					<a href="<?= $C->SITE_URL ?>signup" class="bluebtn1" style="margin-top:0px;"><b><?= $this->lang('signin_reg_button') ?></b></a>
				</div>
			</div>
			<div class="klear"></div>
<?php
	
	$this->load_template('footer.php');
	
?>