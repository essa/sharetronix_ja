<?php
		
	$this->load_template('header.php');
	
?>
		<div class="ttl" style="margin-bottom:10px;">
			<div class="ttl2">
				<h3><?= $this->lang('signup_subtitle', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></h3>
				<?php if( $D->steps > 1 ) { ?>
				<div id="postfilter"><span><?= $this->lang('signup_step') ?> <?= $C->USERS_EMAIL_CONFIRMATION ? 2 : 1 ?> / <?= $D->steps ?></span></div>
				<?php } ?>
			</div>
		</div>
		
		<?php if($D->error) { ?>
			<?= errorbox($this->lang('signup_step2_error'), $this->lang($D->errmsg,$D->errmsg_lngkeys)); ?>
		<?php } ?>
		<form method="post" action="">
			<table id="regform" cellspacing="5">
				<?php if( $C->USERS_EMAIL_CONFIRMATION ) { ?>
				<tr>
					<td class="regparam" style="padding:5px; padding-top:7px;"><?= $this->lang('signup_step2_form_email') ?></td>
					<td class="confirmedmail">
						<b><?= $D->email ?> <img src="<?= $C->IMG_URL ?>design/greencheck.gif"/></b>
						<?= $this->lang('signup_step2_email_confirmed') ?>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_email') ?></td>
					<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_fullname') ?></td>
					<td><input type="text" name="fullname" value="<?= htmlspecialchars($D->fullname) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_username') ?></td>
					<td><input type="text" name="username" value="<?= htmlspecialchars($D->username) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_password') ?></td>
					<td><input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_password2') ?></td>
					<td><input type="password" name="password2" value="<?= htmlspecialchars($D->password2) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<?php if( ! $C->USERS_EMAIL_CONFIRMATION ) { ?>
				<tr>
					<td class="regparam" style="padding-top:13px;"><?= $this->lang('signup_step2_form_captcha') ?></td>
					<td>
						<input type="hidden" name="captcha_key" value="<?= $D->captcha_key ?>" />
						<?= $D->captcha_html ?><br />
						<input type="text" maxlength="20" name="captcha_word" value="" autocomplete="off" class="reginp" style="width:168px; margin-top:5px;" />
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td></td>
					<td><input type="submit" value="<?= $this->lang('signup_step2_form_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
				</tr>
			</table>
		</form>
		
<?php
	
	$this->load_template('footer.php');
	
?>