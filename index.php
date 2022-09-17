<?php 

require 'database.php';
require 'session.php';
require 'cookies.php';

if(logged_in_cookie()){
	if(check_cookie()){
		$_SESSION['USER_ID']=$_COOKIE['USER_ID'];
		include 'account.php';
	}
	else{
		if(session_id() != ''){
			session_regenerate_id(true);
			session_destroy();
			session_unset();
			session_write_close();
		}
		setcookie('USER_ID',$_COOKIE['USER_ID'],time()-3600,"/");
		include 'home.php';
	}
}
else if(logged_in_session()){
	include 'account.php';
}
else {
    include_once 'home.php';
}

?>
