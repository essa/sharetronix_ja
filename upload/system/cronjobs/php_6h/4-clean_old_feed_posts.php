<?php
	
	$date		= time() - 62*24*60*60;
	$faved	= array();
	$r	= $db2->query('SELECT DISTINCT post_id FROM post_favs WHERE post_type="public" ');
	while($tmp = $db2->fetch_object($r)) {
		$faved[]	= intval($tmp->post_id);
	}
	$posts	= array();
	$r	= $db2->query('SELECT * FROM posts WHERE api_id=2 AND date<"'.$date.'" AND date_lastcomment<="'.$date.'" ');
	while($tmp = $db2->fetch_object($r)) {
		$tmp->id	= intval($tmp->id);
		if( in_array($tmp->id, $faved) ) {
			continue;
		}
		$posts[]	= $tmp;
	}
	$user	= (object) array (
		'is_logged'	=> TRUE,
		'id'		=> 0,
		'info'	=> (object) array('is_network_admin' => 1),
	);
	foreach($posts as $obj) {
		$p	= new post('public', FALSE, $obj);
		$p->delete_this_post();
	}
	
?>