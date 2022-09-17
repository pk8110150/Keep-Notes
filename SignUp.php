<?php

require_once('database.php');

require 'session.php';

require 'cookies.php';

function final_touch($field) {
		$field = trim($field);
		$field = stripslashes($field);
		$field = htmlspecialchars($field);
		return $field ;
}

if(logged_in_cookie()){
	extract_cookie();
}
else if(logged_in_session()){
	header("Location: index.php");
}

$name=$username=$password=$check_psw=$gender=$dob="";
$encrypt_password="";
$flag_error=0;
$error_name=$error_username=$error_password=$error_gender =$error_dob=$error_head="";
$error_display_name=$error_display_username=$error_display_password=$error_display_gender =$error_display_dob=$error_display_head="none";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(empty($_POST["new_name_input"])) {
		$error_display_name="block";
		$error_name="Error: Name is required";
		$flag_error=1;
	}
	else {
		$name = final_touch($_POST["new_name_input"]);
		if(ctype_space($_POST["new_name_input"])) {
		$error_display_name="block";
		$error_name="Error: Name can't start with a SPACE";
		$flag_error=1;
		}
	}
	
	if(empty($_POST["new_username_input"])) {
		$error_display_username="block";
		$error_username="Error: Username is required";
		$flag_error=1;
	}
	else {
		$username = final_touch($_POST["new_username_input"]);
		if(ctype_space($_POST["new_username_input"])) {
		$error_display_username="block";
		$error_username="Error: Username can't start with a SPACE";
		$flag_error=1;
		}
	}
	
	if(empty($_POST["new_password_input"])) {
		$error_display_password="block";
		$error_password="Error: Password is required";
		$flag_error=1;
	}
	else {
		$password = final_touch($_POST["new_password_input"]);
		if(strlen($password)<8) {
		$error_display_password="block";
		$error_password="Error: Password must be 8 characters long";
		$check_psw="";
		$flag_error=1;
		}
		else {
			if(empty($_POST["new_repassword_input"])) {
				$error_display_password="block";
				$error_password="Error: Password doesn't match.";
				$flag_error=1;
			}
			else {
				$check_psw = final_touch($_POST["new_repassword_input"]);
				if(strcmp($_POST["new_repassword_input"],$_POST["new_password_input"])==0) {
					$encrypt_password=md5($password);
				}
				else {
					$error_display_password="block";
					$error_password="Error: Password doesn't match.";
					$flag_error=1;
				}
		
			}
		}
	}
	
	if(empty($_POST["new_dob_input"])) {
		$error_display_dob="block";
		$error_dob="Error: Date fo Birth is required";
		$flag_error=1;
	}
	else {
		$dob = final_touch($_POST["new_dob_input"]);
	}
	
	if(empty($_POST["gender"])) {
		$error_display_gender="block";
		$error_gender="Error: Select your gender";
		$flag_error=1;
	}
	else {
		$gender = final_touch($_POST["gender"]);
	}
}
 
 if($flag_error==0){
	 $error_display_name=$error_display_username=$error_display_password=$error_display_gender =$error_display_dob=$error_display_head="none";
	 if(isset($_POST['new_name_input']) && isset($_POST['new_username_input']) && isset($_POST['new_password_input']) && isset($_POST['new_repassword_input']) && isset($_POST['gender']) && isset($_POST['new_dob_input'])) {
			$sql="SELECT Username FROM user_basic_data WHERE Username='$username'";
			if($result = $connection->query($sql)) {
				if($result->num_rows==1) {
					$flag_error=1;
					$error_display_username="block";
					$error_username="This username already exists.";
				}
			}
			if($flag_error==0) {
				$query = "INSERT INTO user_basic_data (Name, Username, Password, DOB, Gender) VALUES ('$name','$username','$encrypt_password','$dob','$gender')";
				if($connection->query($query)===TRUE) {
					$sql="SELECT ID FROM user_basic_data WHERE Username='$username' AND Password='$encrypt_password'";
					if($result = $connection->query($sql)) {
							$row=$result->fetch_array();
							$user_ID=$row['ID'];
							$_SESSION['USER_ID']=$username." ".password_hash($username.$salt_string.$user_ID, PASSWORD_BCRYPT)." ".$user_ID;
							header('Location: index.php');	
					}
				}
				else {
				echo "Error: " . $query. "<br>" . $connection->error;
				}
			}
	 }	
 }
 if($flag_error==1) {
	 $error_display_head="block";
	 $error_head="There are one or more errors in your form. Correct them and register again !";
 }
?>
<head>
<title>Sign Up | Keep Notes</title>
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
			Sign Up to Keep Notes
			</div>
			</center>
			<div class="form_container">
				<div class="form_container_paint">
					<div class="form_">
						<div class="header_">
						Sign Up
						</div>
						<div class="input_container">
						  <form id="new_signup_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_head ?>">
							<?php echo $error_head ?>
							</div>
							<div class="user_box">
							<label class="form_label" for="new_name_input">Name</label>
							<div>
							<input type="text" id="new_name_input" autocomplete="off" name="new_name_input" class="ui_text" autofocus="on" value="<?php echo $name ?>" required>
							</div>
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_name ?>;border:0px;">
							<?php echo $error_name ?>
							</div>
							</div>
							
							<div class="user_box">
							<label class="form_label" for="new_username_input">Username</label>
							<div>
							<input type="text" id="new_username_input" autocomplete="off" name="new_username_input" class="ui_text" value="<?php echo $username ?>" required>
							</div>
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_username ?>;border:0px;">
							<?php echo $error_username ?>
							</div>
							</div>
							
							<div class="user_box">
							<label class="form_label" for="new_password_input">Password</label>
							<div>
							<input type="password" id="new_password_input" name="new_password_input" class="ui_text" value="<?php echo $password ?>" required>
							</div>
							</div>
							
							<div class="user_box">
							<label class="form_label" for="new_repassword_input">Re-Enter Password</label>
							<div>
							<input type="password" id="new_repassword_input" name="new_repassword_input" class="ui_text" value="" required>
							</div>
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_password ?>;border:0px;">
							<?php echo $error_password ?>
							</div>
							</div>
							
							
							<div class="user_box">
							<label class="form_label" for="new_dob_input">Date of Birth</label>
							<input type="date" class="ui_text" id="new_dob_input" name="new_dob_input" value="<?php echo $dob ?>" onblur="cal_age();" style="text-transform:uppercase">
							<input type="hidden" value="" id="new_age">
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_dob ?>;border:0px;">
							<?php echo $error_dob ?>
							</div>
							</div>
							
							
							<div class="user_box">
							<label class="form_label" >Gender</label>
								<div class="gender_sec">
									<label class="form_label custom_checkbox" for="gender_male" style="text-transform:none;transition:all 0.5s;font-weight:bold" id="gender_caption_m">
										Male
									<input type="radio" id="gender_male" name="gender" onClick="check_loggedin(this.id);" value="male" <?php if (isset($gender) && $gender=="male") echo "checked";?>>
									<span class="radiomark"></span>
								</div>
								<div class="gender_sec">
									<label class="form_label custom_checkbox" for="gender_female" style="text-transform:none;transition:all 0.5s;font-weight:bold" id="gender_caption_f">
										Female
									<input type="radio" id="gender_female" name="gender" onClick="check_loggedin(this.id);" value="female" <?php if (isset($gender) && $gender=="female") echo "checked";?>>
									<span class="radiomark"></span>
								</div>
							<div class="user_box error" style="text-align:center;display:<?php echo $error_display_gender ?>;border:0px;">
							<?php echo $error_gender ?>
							</div>
							</div>
							
							
							<div class="user_box" style="margin-top:30px">
								<input type="submit" id="submit_button" class="ui_button" value="SIGN UP">
							</div>
						  </form>
							<br>
							<hr>
							<center>
							<div class="new_registered_user_help">
							Already have an account ? <b><a href="./SignIn.php">Sign In</a></b> here
							</div>
							</center>
						</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
	let date=new Date();
	let day=date.getDate();				
	let month=date.getMonth()+1;
	if(day<10)
		day="0"+day;
	if(month<10)
		month="0"+month;		
	let yr=date.getFullYear()-10;
	date=[day,month,yr].join('-');
	document.getElementById("new_dob_input").setAttribute("max",date);
	
function check_loggedin(id) {
	let gender=id.charAt(7);
	console.log(id);
	console.log(gender);
	if(document.getElementById(id).checked) {
		document.getElementById("gender_caption_m").style.color="#aaa";
		document.getElementById("gender_caption_f").style.color="#aaa";
		document.getElementById("gender_caption_"+gender).style.color="#fff";
	}
	else  {
		document.getElementById("gender_caption_"+gender).style.color="#aaa";

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
function cal_age() {
	let dob=new Date(document.getElementById("new_dob_input").value);
		let curr_date=new Date();
		
		let day=dob.getDate();				//Extract the given date
		let mon=dob.getMonth()+1;			//Extract the given month
		let yr=dob.getFullYear();			//Extract the given year
		
		let c_day=curr_date.getDate();		//Extract current date
		let c_mon=curr_date.getMonth()+1;	//Extract current month
		let c_yr=curr_date.getFullYear();	//Extract current ear
		if((yr>=c_yr)&&((mon>=c_mon)||(day>=c_day))) {
			alert("Invalid Date of Birth");
			return;
		}
		let age=c_yr-yr;
		if(c_mon<mon)
			--age;
		else if(c_mon==mon){
			   if(c_day<day)
				   age--;
		     }
		document.getElementById("new_age").value=age;		//Display the age in the textbox.
}
</script>
</body>