<?php
	
	$db1->query('DELETE FROM unconfirmed_registrations WHERE date<"'.(time()-30*24*60*60).'" ');
	
	$db2->query('UPDATE users SET pass_reset_key="", pass_reset_valid=0 WHERE pass_reset_key<>"" AND pass_reset_valid<"'.(time()-5*24*60*60).'" ');
	
?>