<?php
	
	function __autoload($class_name)
	{
		global $C;
		require_once( $C->INCPATH.'classes/class_'.$class_name.'.php' );
	}
	
	function my_session_name($domain)
	{
		global $C;
		return $C->RNDKEY.str_replace(array('.','-'), '', $domain);
	}
	
	function cookie_domain()
	{
		global $C;
		$tmp	= $GLOBALS['C']->DOMAIN;
		if( substr($tmp,0,2) == 'm.' ) {
			$tmp	= substr($tmp,2);
		}
		$pos	= strpos($tmp, '.');
		if( FALSE === $pos ) {
			return '';
		}
		if( preg_match('/^[0-9\.]+$/', $tmp) ) {
			return $tmp;
		}
		return '.'.$tmp;
	}
	
	function userlink($username)
	{
		global $C;
		if( $C->USERS_ARE_SUBDOMAINS ) {
			return 'http://'.$username.'.'.$C->DOMAIN;
		}
		return $C->SITE_URL.$username;
	}
	
	function rm()
	{
		$files = func_get_args();
		foreach($files as $filename)
			if( is_file($filename) && is_writable($filename) )
				unlink($filename);
	}
	
	function is_valid_email($email)
	{
		return preg_match('/^[a-zA-Z0-9._%-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z]{2,4}$/u', $email);
	}
	
	function do_send_mail($email, $subject, $message, $from=FALSE)
	{
		global $C;
		if( ! $from ) {
			$from	= $C->SITE_TITLE.' <'.$C->SYSTEM_EMAIL.'>';
		}
		$crlf	= "\n";
		$headers	= NULL;
		$headers	.= 'From: '.$from.$crlf;
		$headers	.= 'Reply-To: '.$from.$crlf;
		$headers	.= 'Return-Path: '.$from.$crlf;
		$headers	.= 'Message-ID: <'.time().rand(1000,9999).'@'.$C->DOMAIN.'>'.$crlf;
		$headers	.= 'X-Mailer: PHP/'.PHP_VERSION.$crlf;
		$headers	.= 'MIME-Version: 1.0'.$crlf;
		$headers	.= 'Content-Type: text/plain; charset=UTF-8'.$crlf;
		$headers	.= 'Content-Transfer-Encoding: 8bit'.$crlf;
		//$message	= wordwrap($message, 70);
		$subject	= '=?UTF-8?B?'.base64_encode($subject).'?='.$crlf;
		return mail( $email, $subject, $message, $headers, '-f'.$C->SYSTEM_EMAIL );
	}
	
	function do_send_mail_html($email, $subject, $message_txt, $message_html, $from=FALSE)
	{
		global $C;
		if( ! $from ) {
			$from	= $C->SITE_TITLE.' <'.$C->SYSTEM_EMAIL.'>';
		}
		$crlf	= "\n";
		$boundary	= '=_Part_'.md5(time().rand(0,9999999999));
		$headers	= '';
		$headers	.= 'From: '.$from.$crlf;
		$headers	.= 'Reply-To: '.$from.$crlf;
		$headers	.= 'Return-Path: '.$from.$crlf;
		$headers	.= 'Message-ID: <'.time().rand(1000,9999).'@'.$C->DOMAIN.'>'.$crlf;
		$headers	.= 'X-Mailer: PHP/'.PHP_VERSION.$crlf;
		$headers	.= 'MIME-Version: 1.0'.$crlf;
		$headers	.= 'Content-Type: multipart/alternative; boundary="'.$boundary.'"'.$crlf;
		$headers	.= $crlf;
		$headers	.= '--'.$boundary.$crlf;
		$headers	.= 'Content-Type: text/plain; charset=UTF-8'.$crlf;
		$headers	.= 'Content-Transfer-Encoding: base64'.$crlf;
		$headers	.= 'Content-Disposition: inline'.$crlf;
		$headers	.= $crlf;
		$headers	.= chunk_split(base64_encode($message_txt)).$crlf;
		$headers	.= '--'.$boundary.$crlf;
		$headers	.= 'Content-Type: text/html; charset=UTF-8'.$crlf;
		$headers	.= 'Content-Transfer-Encoding: base64'.$crlf;
		$headers	.= 'Content-Disposition: inline'.$crlf;
		$headers	.= $crlf;
		$headers	.= chunk_split(base64_encode($message_html));
		$subject	= '=?UTF-8?B?'.base64_encode($subject).'?='.$crlf;
		return mail( $email, $subject, '', $headers, '-f'.$C->SYSTEM_EMAIL );
	}
	
	function generate_password($len=8, $let='abcdefghkmnpqrstuvwxyzABCDEFGHKLMNPRSTUVWXYZ23456789')
	{
		$word	= '';
		for($i=0; $i<$len; $i++) {
			$word	.= $let{ rand(0,strlen($let)-1) };
		}
		return $word;
	}
	
	function msgbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="msgbox_close(\''.$div_id.'\'); this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function okbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert green" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="msgbox_close(\''.$div_id.'\'); this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function errorbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert red" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="msgbox_close(\''.$div_id.'\'); this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function show_filesize($bytes)
	{
		$kb	= ceil($bytes/1024);
		if( $kb < 1024 ) {
			return $kb.'KB';
		}
		$mb	= round($kb/1024,1);
		return $mb.'MB';
	}
	
	function str_cut($str, $mx)
	{
		return mb_strlen($str)>$mx ? mb_substr($str, 0, $mx-1).'..' : $str;
	}
	
	function str_cut_link($str, $mx)
	{
		return mb_strlen($str)>$mx ? ( mb_substr($str,0,$mx-6).'...'.mb_substr($str,-4) ) : $str;
	}
	
	function nowrap($string)
	{
		return str_replace(' ', '&nbsp;', $string);
	}
	
	function br2nl($string)
	{
		return str_replace(array('<br />', '<br/>', '<br>'), "\r\n", $string);
	}
	
	function strip_url($url)
	{
		$url	= preg_replace('/^(http|https):\/\/(www\.)?/u', '', trim($url));
		$url	= preg_replace('/\/$/u', '', $url);
		return trim($url);
	}
	
	function my_ucwords($str)
	{
		return mb_convert_case($str, MB_CASE_TITLE);
	}
	
	function my_ucfirst($str)
	{
		return mb_strtoupper(mb_substr($str,0,1)).mb_substr($str,1);
	}
	
?>