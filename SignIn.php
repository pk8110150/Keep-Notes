<?php

require 'database.php';

require 'session.php';

require 'cookies.php';

$username=$password="";
$encrypt_password="";
$error_head="";
$error_display_head="none";
$keep_me_logged_in=false;

function final_touch($field) {
		$field = trim($field);
		$field = stripslashes($field);
		$field = htmlspecialchars($field);
		return $field ;
}

if(logged_in_cookie()){
	$sql="SELECT ID FROM user_basic_data WHERE ID='".$_COOKIE['USER_ID']."'";
	if($result = $connection->query($sql)) {
		$row=$result->fetch_array();
		$user_ID=$row['ID'];
		$_SESSION['USER_ID']=$user_ID;
		header("Location: index.php");
	}
}
else if(logged_in_session()){
	header("Location: index.php");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(empty($_POST["email_input"])) {
		$error_head="Error: Username is required";
		$error_display_head="block";
	}
	else {
		 if(ctype_space($_POST["email_input"])) {
			$error_head="Error: Invalid Email";
			$error_display_head="block";
		 }
		 else
			$username = final_touch($_POST["email_input"]);
	}
	
	if(empty($_POST["password_input"])) {
		$error_password="Error: Password is required";
		$error_display_head="block";
	}
	else {
		$password = final_touch($_POST["password_input"]);
		$encrypt_password=md5($password);
		}
		
	if(isset($_POST["remember_me"])) {
		$keep_me_logged_in=final_touch($_POST["remember_me"]);
	}
}

if(!empty($username) && !empty($encrypt_password) ) {
			$sql="SELECT ID FROM user_basic_data WHERE Username='$username' AND Password='$encrypt_password'";
			if($result = $connection->query($sql)) {
				if($result->num_rows==0) {
					$error_head="Invalid Username or Password.";
					$error_display_head="block";
				}
				else if($result->num_rows==1) {
					$row=$result->fetch_array();
					$user_ID=$row['ID'];
					$_SESSION['USER_ID']=$username." ".password_hash($username.$salt_string.$user_ID, PASSWORD_BCRYPT)." ".$user_ID;
					if($keep_me_logged_in!=false) {
						setcookie('USER_ID',$username." ".password_hash($username.$salt_string.$user_ID, PASSWORD_BCRYPT)." ".$user_ID,time()+(86400*30),"/");
					}
					header("Location: index.php");
				}
			}
			
 }	
?>
<head>
<title>Sign In | Keep Notes</title>
<link rel="icon" type="image/ico" href="./favicon.ico">
<link href="./sign_main.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<div>
		<div class="login_screen_container">
			<center>
			<div class="logo_container">
			<a href="./index.php"><img src="./logo.png" class="logo"></a>
			</div>
			<div class="heading_container">
			Sign In to Keep Notes
			</div>
			</center>
			<div class="form_container">
				<div class="form_container_paint">
					<div class="form_">
						<div class="header_">
						Sign In
						</div>
						<div class="input_container">
						  <form id="signin_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_head ?>">
							<?php echo $error_head ?>
							</div>
							<div class="user_box">
							<label class="form_label" for="email_input">Username</label>
							<div>
							<input type="text" id="email_input" name="email_input" class="ui_text" autofocus="on" autocomplete="off" required>
							</div>
							</div>
							
							<div class="user_box">
							<label class="form_label" for="password_input">Password</label>
							<div>
							<input type="password" id="password_input" name="password_input" class="ui_text" required>
							<div class="toggle_psw" onclick="psw_toggle();">
							<img src="./eye.png" id="toggle_psw" style="width:30px;height:20px">
							</div>
							</div>
							</div>
							
							<div class="user_box" style="margin-top:5px">
							<label class="form_label custom_checkbox" for="logged_in" style="text-transform:none;transition:all 0.5s;font-weight:bold" id="keep_caption">Keep Me Logged In
							<input type="checkbox" name="remember_me" value="Remember Me" id="logged_in" checked onClick="check_loggedin();">
							<span class="checkmark"></span>
							</label>
							<div class="forgot_psw">
							<a href="">Forgot password ?</a>
							</div>
							</div>
							
							<div class="user_box" style="margin-top:30px">
								<input type="submit" id="submit_button" class="ui_button" value="SIGN IN">
							</div>
							<br>
							<hr>
							<center>
							<div class="new_registered_user_help">
							New User ? <b><a href="./SignUp.php">Sign Up</a></b> here
							</div>
							</center>
						</div>
						</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
document.getElementById("keep_caption").style.color="#fff";
function check_loggedin() {
	if(document.getElementById("logged_in").checked) {
		document.getElementById("keep_caption").style.color="#fff";
	}
	else  {
		document.getElementById("keep_caption").style.color="#aaa";

	}
}

function psw_toggle() {
	var psw=document.getElementById("password_input");
	var img=document.getElementById("toggle_psw");
	if (psw.type === "password") {
        psw.type = "text";
		img.src="./eye_check.png";
    } else {
        psw.type = "password";
		img.src="./eye.png";
    }
}
</script>
</body>