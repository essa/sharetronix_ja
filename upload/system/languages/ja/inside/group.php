<?php
	
	$lang	= array
	(
		'group_pagetitle_members'	=> 'Members of #GROUP# - #SITE_TITLE#',
		'group_pagetitle_admins'	=> 'Administrators of #GROUP# - #SITE_TITLE#',
		'group_pagetitle_settings'	=> 'Settings - #GROUP# - #SITE_TITLE#',
		'group_pagetitle_settings_admins'	=> 'Administrators - #GROUP# - #SITE_TITLE#',
		'group_pagetitle_settings_rssfeeds'	=> 'RSS Feeds - #GROUP# - #SITE_TITLE#',
		'group_pagetitle_settings_delgroup'	=> 'Delete Group - #GROUP# - #SITE_TITLE#',
		'group_pagetitle_settings_privmembers'	=> 'Private members - #GROUP# - #SITE_TITLE#',
		
		'group_left_members'	=> 'グループのメンバー',
		'group_left_admins'	=> 'グループの管理者',
		'group_left_viewall'	=> '全員見る',
		'group_left_invite_txt'	=> ' #GROUP# に招待する',
		'group_left_invite_btn'	=> '招待',
		'group_left_posttags'	=> 'よく使われているタグ',
		
		'group_subtitle_type_public'	=> 'パブリックグループ',
		'group_subtitle_type_private'	=> 'プライベートグループ',
		'group_subtitle_nm_posts'	=> '#NUM# posts',
		'group_subtitle_nm_posts1'	=> '1 post',
		'group_subtitle_nm_members'	=> '#NUM# members',
		'group_subtitle_nm_members1'	=> '1 member',
		
		'grp_toplnks_follow'	=> 'グループに参加',
		'grp_toplnks_unfollow'	=> 'グループから離れる',
		'grp_toplnks_post'	=> 'グループへ投稿',
		
		'grp_tab_updates'		=> 'Updates',
		'grp_tab_members'		=> 'Members',
		'grp_tab_settings'	=> 'グループの設定',
		
		'group_title_updates'	=> 'Posts in #GROUP#',
		'group_tab_members_all'		=> '全メンバー',
		'group_tab_members_admins'	=> '管理者',
		'group_updates_rss'		=> 'RSS',
		'group_updates_rss_dsc'		=> 'Subscribe for #GROUP# via RSS',
		
		'group_del_ttl'			=> 'グループを削除',
		'group_del_f_posts'		=> 'Posts action:',
		'group_del_f_posts_keep'	=> 'Delete this group but <b>keep</b> the posts',
		'group_del_f_posts_del'		=> 'Delete this group <b>including</b> the posts',
		'group_del_f_password'		=> 'Your password:',
		'group_del_f_btn'			=> 'Delete Group',
		'group_del_f_btn_cnfrm'		=> 'Are you sure you want to delete this group?',
		'group_del_f_err'			=> 'Error',
		'group_del_f_err_posts'		=> 'Please choose what to do with the posts.',
		'group_del_f_err_passwd'	=> 'Password is incorrect.',
		
		'group_sett_subtabs_main'	=> '全体設定',
		'group_sett_subtabs_rssfeeds'	=> 'RSS feeds',
		'group_sett_subtabs_admins'	=> '管理者',
		'group_sett_subtabs_delgroup'	=> 'Delete group',
		'group_sett_subtabs_privmembers'	=> 'Private members',
		'group_settings_f_title'	=> 'グループの名前:',
		'group_settings_f_url'		=> 'グループのURL:',
		'group_settings_f_descr'	=> '概略:',
		'group_settings_f_type'		=> 'タイプ:',
		'group_settings_f_tp_public'	=> '<b>パブリック</b> - 誰でも参加できて、誰でもグループ宛ての投稿を読める',
		'group_settings_f_tp_private'	=> '<b>Private group</b> - 招待した人のみ',
		'group_settings_f_avatar'	=> 'Picture:',
		'group_settings_f_btn'		=> '保存',
		'group_settings_f_ok'		=> '完了',
		'group_settings_f_oktxt'	=> 'Information was saved.',
		'group_settings_f_err'		=> 'Error',
		'group_setterr_avatar_invalidfile'		=> 'Uploaded file is not a valid image.',
		'group_setterr_avatar_invalidformat'	=> 'Image must be JPEG, GIF, PNG or BMP format.',
		'group_setterr_avatar_toosmall'		=> 'Image resolution must be at least 200x200px.',
		'group_setterr_avatar_cantcopy'		=> 'Picture cannot be processed, please try again later.',
		'group_setterr_title_length'			=> 'Title must be from 3 to 30 characters long.',
		'group_setterr_title_chars'			=> 'Title can contain only letters, digits, dashes, dots and spaces.',
		'group_setterr_title_exists'			=> 'Group with this title already exists.',
		'group_setterr_name_length'			=> 'URL slug must be from 3 to 30 characters long.',
		'group_setterr_name_chars'			=> 'URL slug can contain only latin letters, digits, dash or underscore.',
		'group_setterr_name_exists'			=> 'URL slug is already in use by another group.',
		'group_setterr_name_existsu'			=> 'URL slug is already in use by a network member.',
		'group_setterr_name_existss'			=> 'URL slug is already in use by the system.',
		'group_admsett_ttl'			=> 'グループの管理者',
		'group_admsett_f_adm'			=> '現在の管理者:',
		'group_admsett_f_adm_you'		=> 'あなた',
		'group_admsett_f_add'			=> '管理者を追加:',
		'group_admsett_f_add_btn'		=> '追加',
		'group_admsett_f_btn'			=> '保存',
		'group_admsett_jserr_user1'		=> 'No such user #USERNAME#.',
		'group_admsett_jserr_user2'		=> '#USERNAME# is not a member of #GROUP#.',
		'group_admsett_jscnf_del'		=> 'Remove #USERNAME# from admins - are you sure?',
		'group_admsett_f_ok'			=> '完了',
		'group_admsett_f_ok_txt'		=> 'Information was saved.',
		'group_feedsett_feedslist'	=> 'Current feeds:',
		'group_feedsett_feed_filter'	=> 'Filtered by:',
		'group_feedsett_feed_delete'	=> 'Remove this feed',
		'group_feedsett_feed_delcnf'	=> 'Remove feed - Are you sure?',
		'group_feedsett_f_title'	=> 'Add new RSS/Atom feed',
		'group_feedsett_f_url'		=> 'Feed url:',
		'group_feedsett_f_usr'		=> 'Username:',
		'group_feedsett_f_pwd'		=> 'Password:',
		'group_feedsett_f_filter'	=> 'Filter:',
		'group_feedsett_f_filtertxt'	=> 'Optional: If you prefer not all the feed items to be posted, but only ones containing some keywords, fill in the keywords (comma separated).',
		'group_feedsett_f_submit'	=> 'Add feed',
		'group_feedsett_err'		=> 'Error',
		'group_feedsett_pwdreq_ttl'	=> 'Authentication required',
		'group_feedsett_pwdreq_txt'	=> 'This feed requires username/password authentication.',
		'group_feedsett_err_feed'	=> 'Invalid RSS/Atom feed.',
		'group_feedsett_err_auth'	=> 'Invalid feed username/password.',
		'group_feedsett_ok'		=> 'Done',
		'group_feedsett_ok_txt'		=> 'Feed was added successfully. All future feed entries will be posted.',
		'group_feedsett_okdel_txt'	=> 'Feed was deleted.',
		'group_privmmb_title'			=> 'This group is private. The following users (the group members and the users invited to become members) have permissions to access the group. When you remove someone\'s access, he will no longer be able to browse, join and leave the group (unless he is invited again), he will also be removed from group members.',
		'group_privmmb_f_curr'			=> '現在のユーザ:',
		'group_privmmb_f_curr_you'		=> 'あなた',
		'group_privmmb_f_btn'			=> '保存',
		'group_privmmb_jscnf_del'		=> ' #USERNAME# をこのグループからはずします。よろしいですか?',
		'group_privmmb_f_ok'			=> '完了',
		'group_privmmb_f_ok_txt'		=> 'Information was saved.',
		
		'noposts_group_ttl'		=> 'No posts yet',
		'noposts_group_txt'		=> 'Click #A1#here#A2# to post the first message in #GROUP#.',
		'noposts_group_ttl_filter'	=> 'No posts',
		'noposts_group_txt_filter'	=> 'There are no posts matching your search criteria.',
		
		'group_justcreated_box_ttl'	=> 'グループを作成しました。',
		'group_justcreated_box_txt'	=> 'これで #A1#メンバーを招待する#A2# ことや #A3#グループの設定を変更する#A4# ことができます.',
		'group_invited_box_ttl'	=> '完了',
		'group_invited_box_txt'	=> '招待は正常に送信されました.',
	);
	
?>