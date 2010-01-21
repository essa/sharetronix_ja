<?php
	
	$this->load_langfile('outside/footer.php');
	$this->load_langfile('inside/footer.php');
	
?>
				</div>
			</div>
			<?php if( $this->user->is_logged ) { ?>
			<div id="footer">
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_general') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>"><?= $this->lang('ftrlinks_sgn_dashboard') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL.$this->user->info->username ?>"><?= $this->lang('ftrlinks_sgn_profile') ?></a></div>
					<?php if( $this->user->info->is_network_admin ) { ?>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>admin"><?= $this->lang('ftrlinks_sgn_admin') ?></a></div>
					<?php } else { ?>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>settings"><?= $this->lang('ftrlinks_sgn_settings') ?></a></div>
					<?php } ?>
				</div>
				<div class="linkcol" style="width:190px;">
					<h4><?= $this->lang('ftrlinks_section_groups') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/tab:my"><?= $this->lang('ftrlinks_sgr_mygroups') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/tab:all"><?= $this->lang('ftrlinks_sgr_allgroups') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/new"><?= $this->lang('ftrlinks_sgr_newgroup') ?></a></div>
				</div>
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_findpeople') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>members"><?= $this->lang('os_ftrlinks_sf_members') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>invite"><?= $this->lang('os_ftrlinks_sf_invitemail') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>invite/personalurl"><?= $this->lang('os_ftrlinks_sf_invitelink') ?></a></div>
				</div>
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_aboutus', array('#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>contacts"><?= $this->lang('os_ftrlinks_sa_support') ?></a></div>
				</div>
			</div>
			<?php } ?>
			<div id="footercorners"><div id="footercorners2"></div></div>
			<div id="subfooter">
				<div id="sfleft">
					<b><?= htmlspecialchars($C->OUTSIDE_SITE_TITLE) ?></b>
					&middot;
					<a href="<?= $C->SITE_URL ?>contacts"><?= $this->lang('ftr_contacts') ?></a>
				</div>
				<div id="sfright">
					<span style="color:#888;">
						Powered by
						<a href="http://sharetronix.com" target="_blank" style="color:#666;">Sharetronix</a> &middot; 
						<a href="http://blogtronixmicro.com" target="_blank" style="color:#666;">BlogtronixMicro</a> &middot; 
						<a href="http://blogtronix.com" target="_blank" style="color:#666;">Blogtronix</a>
					</span> 
				</div>
			</div>
		</div>
		<div id="flybox_container" style="display:none;">
			<div class="flyboxbackgr"></div>
			<div class="flybox" id="flybox_box">
				<div class="flyboxttl">
					<div class="flyboxttl_left"><b id="flybox_title"></b></div>
					<div class="flyboxttl_right"><a href="javascript:;" title="<?= $this->lang('post_atchbox_close') ?>" onfocus="this.blur();" onclick="flybox_close();"></a></div>
				</div>
				<div class="flyboxbody"><div class="flyboxbody2" id="flybox_main"></div></div>
				<div class="flyboxftr"><div class="flyboxftr2"></div></div>
			</div>
		</div>
		<?php
			
			$lastrun	= $GLOBALS['cache']->get('cron_last_run');
			if( !$lastrun || $lastrun<time()-60 ) {
				echo '<iframe src="'.$C->OUTSIDE_SITE_URL.'cron?r='.rand(0,999999).'" width="0" height="0" border="0" frameborder="0" style="width:0px; height:0px; display:none;"></iframe>';
			}
			if( $C->DEBUG_MODE ) {
				$this->load_template('footer_debuginfo.php');
			}
			
		?>
	</body>
</html>