<?php
/*
  SESSION ID STRUCTURE: 
      <Username><space><hash of the user name + ID + salt><space><ID>
*/
session_start();

function logged_in_session() {
	if(isset($_SESSION['USER_ID']) && !empty($_SESSION['USER_ID'])) {
		return true;
	}
	else {
		return false;
	}
}

function grab_data($head) {
	require 'database.php';
	$arr=explode(" ",$_SESSION['USER_ID']);
	if(count($arr) != 3){
		header('./logout.php');
	}
	$username = $arr[0];
	$hash = $arr[1];
	$id = $arr[2];
	$flag = false;
	if(password_verify($username.$salt_string.$id,$hash)){
		$flag = true;
	}
	if ($flag) {
		$sql = "SELECT $head FROM user_basic_data WHERE ID='".$id."'"." AND Username='".$username."'";
		if($result = $connection->query($sql)) {
			$row=$result->fetch_array();
			if($query_output=$row[$head]) {
				return $query_output;
			}
		}
	} else {
		header('./logout.php');
	}
	$connection->close();
}

?>