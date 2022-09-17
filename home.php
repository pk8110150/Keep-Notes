<?php 
if(isset($_POST['signin'])) {
	header("Location:SignIn.php");
}
if(isset($_POST['signup'])) {
	header("Location:SignUp.php");
}

?>
<head>
<title>Keep Notes</title>
<link rel="icon" type="image/ico" href="./favicon.ico">
<link href="./home.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="form_container" id="main_body">
<div class="form_container_paint" id="form_container_paint">
	<div class="logo">
	 KeepNotes
	</div>
	
	<div class="menu" id="menu"  onclick="show_menu(this);" title="Menu">
		<img src="./home_menu.png" style="width:60px;height:50px" id="menu_icon">
	</div>
	<div id="menu_container" style="display:none;">
		<div class="button_cont">
			<div class="button_cont_com">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="hidden" name="signin" value="signin">
					<input type="submit" id="submit_button_1" class="ui_button" value="SIGN IN">
				</form>
			</div>
			<div class="button_cont_com" style="float:right">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="hidden" name="signup" value="signup">
					<input type="submit" id="submit_button_2" class="ui_button" value="SIGN UP">
				</form>
			</div>
		<div>
	</div>
</div>
</div>

<script>
function show_menu(z) {
	let x=document.getElementById("menu_icon");
	let y=document.getElementById("menu_container");
	if(y.style.display=="none") {
		x.src="./home_menu_hover.png";
		z.classList.toggle("teeter");
		document.getElementById("submit_button_2").classList.add("zoomin");
		document.getElementById("submit_button_1").classList.add("zoomin");
		document.getElementById("submit_button_2").classList.remove("zoomout");
		document.getElementById("submit_button_1").classList.remove("zoomout");
		y.style.display="block";
		setTimeout(function() {z.classList.toggle("teeter");},900);
	}
	else {
		x.src="./home_menu.png";
		document.getElementById("menu").classList.toggle("teeter");
		document.getElementById("submit_button_2").classList.remove("zoomin");
		document.getElementById("submit_button_1").classList.remove("zoomin");
		document.getElementById("submit_button_2").classList.add("zoomout");
		document.getElementById("submit_button_1").classList.add("zoomout");
		setTimeout(function() {y.style.display="none";document.getElementById("menu").classList.toggle("teeter");},900);
	}
}

window.onclick = function(event) {
    if (event.target == document.getElementById("menu_container")) {
		let x=document.getElementById("menu_icon");
		let y=document.getElementById("menu_container");
        x.src="./home_menu.png";
		document.getElementById("menu").classList.toggle("teeter");
		document.getElementById("submit_button_2").classList.remove("zoomin");
		document.getElementById("submit_button_1").classList.remove("zoomin");
		document.getElementById("submit_button_2").classList.add("zoomout");
		document.getElementById("submit_button_1").classList.add("zoomout");
		setTimeout(function() {y.style.display="none";document.getElementById("menu").classList.toggle("teeter");},900);
    }	
}
</script>
</body>


