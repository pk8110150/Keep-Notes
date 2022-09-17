<?php
/*
  COOKIE STRUCTURE: 
      <Username><space><hash of the user name + ID + salt><space><ID>
*/
function logged_in_cookie() {
	if(isset($_COOKIE['USER_ID']) && !empty($_COOKIE['USER_ID'])) {
		return true;
	}
	else {
		return false;
	}
}

function check_cookie(){
	require 'database.php';
	$arr=explode(" ",$_COOKIE['USER_ID']);
	if(count($arr) != 3){
		return false;
	}
	$username = $arr[0];
	$hash = $arr[1];
	$id = $arr[2];
	$flag=false;
	if(password_verify($username.$salt_string.$id,$hash)){
		$flag=true;
	}
	return $flag;
}

function extract_cookie(){
	$flag=check_cookie();
	if($flag){
		$_SESSION['USER_ID']=$_COOKIE['USER_ID'];
		header("Location: index.php");
	}
	else{
		if(session_id() != '')
		{
			session_regenerate_id(true);
			session_destroy();
			session_unset();
			session_write_close();
		}
		setcookie('USER_ID',$_COOKIE['USER_ID'],time()-3600,"/");
	}
}
?>