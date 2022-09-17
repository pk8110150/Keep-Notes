<?php 

$user_username = grab_data('Username');
$no_notes_display="block";
$no_todo_display="block";
$notes_display="none";
$todo_display="none";
$altered=false;
$toast_message="";
$tab_note_display="block";
$tab_list_display="none";

function final_touch($field) {
		$field = trim($field);
		$field = stripslashes($field);
		$field = htmlspecialchars($field);
		return $field ;
}

function remove_br($field) {
	$field=htmlentities($field, null, 'utf-8');
	$field=str_replace("&amp;nbsp;", "", $field);
	$field=html_entity_decode($field);
	$field=strip_tags($field);
	return $field;
}

//For adding notes
if(isset($_POST['note_content_val'])) {
	$altered=true;
	$temp=$_POST['note_content_val'];
	$temp=remove_br($temp);
	$content=final_touch($temp);
	$title="";
	if(isset($_POST['note_title_val'])) {
		$temp=$_POST['note_title_val'];
		$temp=remove_br($temp);
		$title=final_touch($temp);
	}
	date_default_timezone_set("Asia/Kolkata");
	$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
	$query="INSERT INTO user_notes (Username,Title,Note,Last_Edited,Created,Pin_Stat) VALUES ('$user_username','$title','$content','$last_edit','$last_edit','unpinned')";
	if($result=$connection->query($query)===TRUE) {
		$toast_message="Note Successfully Added";
	}
	else {
		$toast_message="Some error occured please try again.";
	}
}

//For adding To do list
if(isset($_POST['total_list_item'])) {
	$altered=true;
	$row_existing=1;
	$sql="SELECT * FROM user_todo";
	if($res=$connection->query($sql)) {
		   if($res->num_rows>0) {
				while($row1 = $res->fetch_array()) {
					++$row_existing;	
				}
		    }
	}
	else {
		$toast_message="Some error occured please try again.";
		return;
	}
	$index=$_POST['total_list_item'];
	$title="";
	if(isset($_POST['title_list'])) {
		$temp=$_POST['title_list'];
		$temp=remove_br($temp);
		$title=final_touch($temp);
	}
	$list=$user_username.'_'.$row_existing;
	date_default_timezone_set("Asia/Kolkata");
	$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
	$query="INSERT INTO user_todo (Username,List_Id,Title,Last_Edited,Created,Pin_Stat) VALUES ('$user_username','$list','$title','$last_edit','$last_edit','unpinned')";
	if($result=$connection->query($query)===TRUE) {
		$flag=0;
			for($i=1;$i<=$index;$i++) {
				$checkbox_status=final_touch($_POST['checkbox_status_'.$i]);
				$list_content=final_touch($_POST['list_content_'.$i]);
				$add_list="INSERT INTO todo_contents (List_Id,Checkbox,Content) VALUES ('$list','$checkbox_status','$list_content')";
				if($response=$connection->query($add_list)===TRUE) {
				}
				else {
					$flag=1;
				}
			}
		if($flag==0)
			$toast_message="List Successfully Added.";
		else {
			$toast_message="Some error occured please try again.";
			return;
		}
			
	}
	else {
		$toast_message="Some error occured please try again.";
		return;
	}
	
}


//Foe editing notes
if(isset($_POST['edit_note_content_val'])) {
	$altered=true;
	$temp=$_POST['edit_note_content_val'];
	$temp=remove_br($temp);
	$content=final_touch($temp);
	$id=final_touch($_POST['id_note']);
	$title="";
	if(isset($_POST['edit_note_title_val'])) {
		$temp=$_POST['edit_note_title_val'];
		$temp=remove_br($temp);
		$title=final_touch($temp);
	}
	date_default_timezone_set("Asia/Kolkata");
	$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
	$query="UPDATE user_notes SET Title='$title',Note='$content',Last_Edited='$last_edit' WHERE ID='$id'";
	if($result=$connection->query($query)===TRUE) {
		$toast_message="Note Successfully Edited.";
	}
	else {
		$toast_message="Some error occured please try again.";
	}
}

//For editing to do list.
if(isset($_POST['edit_total_list_item'])) {
	$altered=true;
	$id=final_touch($_POST['id_to_alter']);
	$title="";
	if(isset($_POST['edit_title_list'])) {
		$temp=$_POST['edit_title_list'];
		$temp=remove_br($temp);
		$title=final_touch($temp);
	}
	date_default_timezone_set("Asia/Kolkata");
	$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
	$change_last_edit="UPDATE user_todo SET Title='$title',Last_Edited='$last_edit' WHERE List_Id='$id'";
	if($result=$connection->query($change_last_edit)===TRUE) {
	
	}
	else {
		$toast_message="Some error occured please try again.";
		return;
	}
	$sql="DELETE FROM todo_contents WHERE List_Id='$id'";
	if($result=$connection->query($sql)===TRUE) {
		$index=$_POST['edit_total_list_item'];
		$flag=0;
			for($i=1;$i<=$index;$i++) {
				$checkbox_status=final_touch($_POST['edit_checkbox_status_'.$i]);
				$list_content=final_touch($_POST['edit_list_content_'.$i]);
				$add_list="INSERT INTO todo_contents (List_Id,Checkbox,Content) VALUES ('$id','$checkbox_status','$list_content')";
				if($response=$connection->query($add_list)===TRUE) {
				}
				else {
					$flag=1;
				}
			}
		if($flag==0)
			$toast_message="List Successfully Edited.";
		else {
			$toast_message="Some error occured please try again.";
			return;
		}
	}
	else {
		$toast_message="Some error occured please try again.";
	}
}

//For pin/unpin notes
if(isset($_POST['pin_stat'])) {
	$altered=true;
	$id=$_POST['pin_stat'];
	$sql="SELECT Pin_Stat FROM user_notes WHERE ID='$id'";
	if($result=$connection->query($sql)) {
		$row=$result->fetch_array();
		$current_stat=$row['Pin_Stat'];
		$query="";
		date_default_timezone_set("Asia/Kolkata");
		$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
		if($current_stat=="unpinned") {
			$query="UPDATE user_notes SET Last_Edited='$last_edit',Pin_Stat='pinned' WHERE ID='$id'";
			$toast_message="Note Successfully Pinned.";
		}
		else {
			$query="UPDATE user_notes SET Last_Edited='$last_edit',Pin_Stat='unpinned' WHERE ID='$id'";
			$toast_message="Note Successfully Unpinned.";
		}
		if($res=$connection->query($query)===TRUE) {
			
		}
		else {
			$toast_message="Some error occured please try again.";
			return;
		}	
			
	}
	else {
		$toast_message="Some Error occured.";
	}
}


//For pin/unpin lists
if(isset($_POST['pin_stat_list'])) {
	$altered=true;
	$id=$_POST['pin_stat_list'];
	$sql="SELECT Pin_Stat FROM user_todo WHERE ID='$id'";
	if($result=$connection->query($sql)) {
		$row=$result->fetch_array();
		$current_stat=$row['Pin_Stat'];
		$query="";
		date_default_timezone_set("Asia/Kolkata");
		$last_edit=date("h:i:s A")."  ".date("l")."  ".date("d-m-y");
		if($current_stat=="unpinned") {
			$query="UPDATE user_todo SET Last_Edited='$last_edit',Pin_Stat='pinned' WHERE ID='$id'";
			$toast_message="List Successfully Pinned.";
		}
		else {
			$query="UPDATE user_todo SET Last_Edited='$last_edit',Pin_Stat='unpinned' WHERE ID='$id'";
			$toast_message="List Successfully Unpinned.";
		}
		if($res=$connection->query($query)===TRUE) {
			
		}
		else {
			$toast_message="Some error occured please try again.";
			return;
		}	
			
	}
	else {
		$toast_message="Some Error occured.";
	}
}

//For deleting note
if(isset($_POST['deleting_id'])) {
	$altered=true;
	$id=final_touch($_POST['deleting_id']);
	$query="DELETE FROM user_notes WHERE ID='$id'";
	if($result=$connection->query($query)===TRUE) {
		$toast_message="Successfully Deleted.";
	}
	else {
		$toast_message="Some error occured please try again.";
	}
}

//For deleting To do
if(isset($_POST['deleting_id_list'])) {
	$altered=true;
	$id=final_touch($_POST['deleting_id_list']);
	$query="DELETE FROM todo_contents WHERE List_Id='$id'";
	if($result=$connection->query($query)===TRUE) {
		$toast_message="Successfully Deleted.";
		$del="DELETE FROM user_todo WHERE List_Id='$id'";
			if($res=$connection->query($del)===TRUE) {
				$toast_message="Successfully Deleted.";
			}
			else {
				$toast_message="Some error occured please try again.";
				return;
			}
	}
	else {
		$toast_message="Some error occured please try again.";
	}
}

//For selecting navigation tab
if(isset($_POST['nav_location'])) {
	$nav_value=$_POST['nav_location'];
	if($nav_value=="note") {
		$tab_note_display="block";
		$tab_list_display="none";
	}
	else if($nav_value=="todo") {
		$tab_note_display="none";
		$tab_list_display="block";
	}
}

//For notes
$query="SELECT * FROM user_notes WHERE USername='$user_username'";

if($result=$connection->query($query)) {
	if($result->num_rows==0) {
					$no_notes_display="block";
					$notes_display="none";
				}
	else if($result->num_rows>0) {
		$no_notes_display="none";
		$notes_display="flex";
	}
}
else {
	$no_notes_display="block";
    $notes_display="none";
}

//For todo	
$query="SELECT * FROM user_todo WHERE Username='$user_username'";

if($result=$connection->query($query)) {
	if($result->num_rows==0) {
					$no_todo_display="block";
					$todo_display="none";
				}
	else if($result->num_rows>0) {
		$no_todo_display="none";
		$todo_display="flex";
	}
}
else {
	$no_todo_display="block";
    $todo_display="none";
}
?>
<head>
<title>Keep Notes</title>
<link rel="icon" type="image/ico" href="./favicon.ico">
<link href="./account.css" rel="stylesheet">
<link href="./header.css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="form_container" id="main_body">
<div class="form_container_paint" id="form_container_paint">
	<div class="header_container">
		<div class="header_bar">
			<a href="./index.php">
				<div class="main_logo">KeepNotes</div>
			</a>
			<div class="account_control" id="account_control" onClick="show_drop_menu()">
				<div class="user_name">
					<?php echo $user_username;?>
				</div>
				<div id="control_panel">
					<div class="bar1 bar_com" id="bar1"></div>
					<div class="bar2 bar_com" id="bar2"></div>
					<div class="bar3 bar_com" id="bar3"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="hidden_menu">
				<div id="hidden_menu_block">
				  <div class="beep"></div>
				  <div class="block_container">
					<div class="mobile_user">
					Signed In as <br> <?php echo $user_username;?>
					<hr>
					</div>
					<div class="h_main_menu" style="padding-bottom:10px">
							<a href=""><div class="li">My Account</div></a>
							<a href=""><div class="li">Settings</div></a>
							<a href="./logout.php"><div class="li">Logout</div></a>
					</div>
				  </div>
				</div>
				</div>
	
	<div class="body_container" id="body_container">
		<div class="navigate_page">
		
			<div class="navigation_index">
				<div class="header_" id="note_nav" onClick="toogle_panel_to_note(this.id);">Notes</div>
				<div class="header_" id="todo_nav" onClick="toogle_panel_to_todo(this.id);">To Do List</div>
				<form name="" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:none;">
					<input type="hidden" value="" name="nav_location" id="nav_location">
				</form>
			</div>
			
			<div class="notes_container com_container" id="note_panel" style="display:<?php echo $tab_note_display ?>">
				<div class="add_bar" id="add_bar">
						<div class="input_container" id="title_bar" style="display:none;">
							<div class="legend ui_title" onClick='document.getElementById("note_title").focus()' id="note_title_caption">Title</div>
							<div class="title_bar ui_text ui_title" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="note_title" oninput="detect_change(this.id);"></div>
						</div>
						<div class="input_container" id="note_bar">
							<div class="legend " onClick='document.getElementById("note_content").focus()' id="note_content_caption">Add Note....</div>
							<div class="title_bar ui_text" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="note_content" oninput="detect_change(this.id);" onFocus="show_note_content();"></div>
						</div>
						<div class="input_container" id="settings_bar" style="padding-bottom:10px;padding-top:10px;display:none;">
							<div class="button_container">
							  <form name="" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
								<input type="hidden" name="note_title_val" id="note_title_val" value="">
								<input type="hidden" name="note_content_val" id="note_content_val" value="">
								<input type="submit" class="ui_button" value="Create Note" id="note_button" disabled>
							  </form>
							</div>
							<div class="button_container">
								<input type="button" class="ui_button button_cancel" value="Cancel" onClick="hide_note_content();">
							</div>
						</div>	
				</div>
				<div class="notes_show_bar">
				<center>
					<div class="no_notes_found" id="no_notes" style="display:<?php echo $no_notes_display ?>">
						<img src="./notes.png" class="no_img">
						<div class="no_footer">
							Add a Note to display Here
						</div>
					</div>
				</center>
					<div class="_row" id="notes_disp" style="display:<?php echo $notes_display ?>">
						<?php 
							echo "<form style='display:none;' method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
									echo "<input type='hidden' name='pin_stat' id='pin_stat' value=''>";
									echo "<input type='submit' id='pin_stat_button'>";
							echo "</form>";
							$no_pin="no_pin";
							$no_pin_pic="none";
							if($notes_display=="flex") {
								$query="SELECT * FROM user_notes WHERE Username='$user_username' AND Pin_Stat='pinned'";
								if($result=$connection->query($query)) {
									if($result->num_rows>0) {
										while($row = $result->fetch_array()) {
											echo "<div class='_column'>";
												echo "<div class='_note_datepanel'>";
													 echo "<label class='l_date'>Created On:</label><br>".$row['Created'];
												echo "</div>";
												echo "<div class='note_pad' id='note_".$row['ID']."'>";
													if($row['Pin_Stat']=="unpinned") {
														$no_pin="no_pin";
														$no_pin_pic="none";
													}
													else {
														$no_pin="";
														$no_pin_pic="block";
													}
													echo "<div class='pin_container ".$no_pin."' title='Pin/Unpin this note' onClick='pin_this_note(\"".$row['ID']."\");'>";
														echo "<img src='./pin.png' style='width:20px;height:22px;display:".$no_pin_pic."'>";
													echo "</div>";
													echo "<div class='show_title' aria-multiline='true' id='title_".$row['ID']."'>".$row['Title']."</div>";
													echo "<div class='show_note' aria-multiline='true' id='content_".$row['ID']."'>".$row['Note']."</div>";
													echo "<div class='_note_controlpanel'>";
														echo "<div class='note_button_panel'>";
															echo "<input type='button' class='panel_button' value='Edit' onClick='edit_note(".$row['ID'].")'>";
														echo "</div>";
														echo "<div class='note_button_panel' style='float:right'>";
															echo "<form method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
															echo "<input type='hidden' name='deleting_id' value='".$row['ID']."'>";
															echo "<input type='submit' class='panel_button del_but' value='Delete' title='Delete this note.'>";
															echo "</form>";
														echo "</div>";
													echo "</div>";
												echo "</div>";
												echo "<div class='_note_datepanel' style='margin-top:0px'>";
													 echo "<label class='l_date'>Last Edited :</label><br>".$row['Last_Edited'];
												echo "</div>";
											echo "</div>";											
										}
									}
								}
								
								$query="SELECT * FROM user_notes WHERE Username='$user_username' AND Pin_Stat='unpinned'";
								if($result=$connection->query($query)) {
									if($result->num_rows>0) {
										while($row = $result->fetch_array()) {
											echo "<div class='_column'>";
												echo "<div class='_note_datepanel'>";
													 echo "<label class='l_date'>Created On:</label><br>".$row['Created'];
												echo "</div>";
												echo "<div class='note_pad' id='note_".$row['ID']."'>";
													if($row['Pin_Stat']=="unpinned") {
														$no_pin="no_pin";
														$no_pin_pic="none";
													}
													else {
														$no_pin="";
														$no_pin_pic="block";
													}
													echo "<div class='pin_container ".$no_pin."' title='Pin/Unpin this note' onClick='pin_this_note(\"".$row['ID']."\");'>";
														echo "<img src='./pin.png' style='width:20px;height:22px;display:".$no_pin_pic."'>";
													echo "</div>";
													echo "<div class='show_title' aria-multiline='true' id='title_".$row['ID']."'>".$row['Title']."</div>";
													echo "<div class='show_note' aria-multiline='true' id='content_".$row['ID']."'>".$row['Note']."</div>";
													echo "<div class='_note_controlpanel'>";
														echo "<div class='note_button_panel'>";
															echo "<input type='button' class='panel_button' value='Edit' onClick='edit_note(".$row['ID'].")'>";
														echo "</div>";
														echo "<div class='note_button_panel' style='float:right'>";
															echo "<form method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
															echo "<input type='hidden' name='deleting_id' value='".$row['ID']."'>";
															echo "<input type='submit' class='panel_button del_but' value='Delete' title='Delete this note.'>";
															echo "</form>";
														echo "</div>";
													echo "</div>";
												echo "</div>";
												echo "<div class='_note_datepanel' style='margin-top:0px'>";
													 echo "<label class='l_date'>Last Edited :</label><br>".$row['Last_Edited'];
												echo "</div>";
											echo "</div>";											
										}
									}
								}
								
						
							}				
						?>
					</div>
				</div>
			</div>
			
			<div class="todo_container com_container" id="todo_panel" style="display:<?php echo $tab_list_display ?>">
				<div class="add_bar" >
				
						<div class="input_container" id="todo_title_bar" style="display:none;">
							<div class="legend ui_title" onClick='document.getElementById("todo_title").focus()' id="todo_title_caption">Title</div>
							<div class="title_bar ui_text ui_title" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="todo_title" oninput="detect_change(this.id);"></div>
						</div>
						<div class="input_container" id="list_bar">
							<div class="legend " onClick="show_todo_content();" style="width:100%" id="todo_content_caption">Add List....</div>
							<div class="title_bar ui_text" style="padding:0px" id="todo_content">
								<div class="add_list_container" id="add_list_container" style="display:none;">
									<table style="width:100%" id="check_list_table">
										<tr>
											<td colspan="2" class="list_head" onclick='add_list_item("check_list_table");'>+ List Item</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="input_container" id="todo_settings_bar" style="padding-bottom:10px;padding-top:20px;display:none;">
							<div class="button_container">
								<input type="button" class="ui_button" value="Create Note" onclick='validate_list("check_list_table");'>
								<form id="list_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:none">
							
								</form>
							</div>
							<div class="button_container">
								<input type="button" class="ui_button button_cancel" value="Cancel" onClick="hide_todo_content();">
							</div>
						</div>	
				</div>
				<div class="notes_show_bar">
				<center>
					<div class="no_notes_found" style="display:<?php echo $no_todo_display ?>">
						<img src="./todo.png" class="no_img">
						<div class="no_footer">
							Add a List to display Here
						</div>
					</div>
				</center>
					<div class="_row" id="notes_disp" style="display:<?php echo $todo_display ?>">
						<?php
							echo "<form style='display:none;' method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
								    echo "<input type='hidden' name='pin_stat_list' id='pin_stat_list' value=''>";
									echo "<input type=\"hidden\" value=\"todo\" name=\"nav_location\" id=\"nav_location\">";
									echo "<input type='submit' id='pin_stat_button_list'>";
							echo "</form>";
							$no_pin="no_pin";
							$no_pin_pic="none";
							if($todo_display=="flex") {
								$query="SELECT * FROM user_todo WHERE Username='$user_username' AND Pin_Stat='pinned'";
								if($result=$connection->query($query)) {
									if($result->num_rows>0) {
										while($row = $result->fetch_array()) {
											echo "<div class='_column'>";
												echo "<div class='_note_datepanel'>";
													 echo "<label class='l_date'>Created On:</label><br>".$row['Created'];
												echo "</div>";
												if($row['Pin_Stat']=="unpinned") {
														$no_pin="no_pin";
														$no_pin_pic="none";
													}
													else {
														$no_pin="";
														$no_pin_pic="block";
													}
												echo "<div class='todo_list' id='list_".$row['List_Id']."'>";
													echo "<div class='pin_container ".$no_pin."' title='Pin/Unpin this note' onClick='pin_this_list(\"".$row['ID']."\");'>";
														echo "<img src='./pin.png' style='width:20px;height:22px;display:".$no_pin_pic."'>";
													echo "</div>";
													echo "<div class='show_title' aria-multiline='true' id='title_".$row['List_Id']."'>".$row['Title']."</div>";
													echo "<div class='show_note' aria-multiline='true' id='content_".$row['ID']."' style='padding-top:0px'>";
													$sql="SELECT * FROM todo_contents WHERE List_Id='".$row['List_Id']."'";
													if($res=$connection->query($sql)) {
														$i=0;
														if($res->num_rows>0) {
															while($row1 = $res->fetch_array()) {
																++$i;
																$strike="";
																echo "<div class='list_frame'>";
																	if($row1['Checkbox']=="checked") {
																		$strike="strike";
																	}
																		echo "<label class='form_label custom_checkbox ".$strike."' for='checkbox_".$row1['ID']."'> ".$row1['Content'];
																			echo "<input type='checkbox' id='checkbox_".$row1['ID']."' ".$row1['Checkbox']." disabled>";
																				echo "<span class='checkmark'></span>";
																		echo "</label>";
																		echo "<div id='content_at_".$row['List_Id']."_".$i."' style='display:none;'>";
																			echo "<input type='hidden' id='checkbox_stat_".$row1['ID']."' value='".$row1['Checkbox']."'>";
																			echo "<input type='hidden' id='list_content_stat".$row1['ID']."' value='".$row1['Content']."'>";
																		echo "</div>";
																echo "</div>";
															}
														}
														echo "<input type='hidden' id='no_list_".$row['List_Id']."' value='".$i."'>";
													}		
													echo "</div>";
													echo "<div class='_note_controlpanel'>";
														echo "<div class='note_button_panel'>";
															echo "<input type='button' class='panel_button list_but' value='Edit' onClick='edit_list(\"".$row['List_Id']."\");'>";
														echo "</div>";
														echo "<div class='note_button_panel' style='float:right'>";
															echo "<form method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
															echo "<input type='hidden' name='deleting_id_list' value='".$row['List_Id']."'>";
															echo "<input type=\"hidden\" value=\"todo\" name=\"nav_location\" id=\"nav_location\">";
															echo "<input type='submit' class='panel_button del_but list_but' value='Delete' title='Delete this To-do List.'>";
															echo "</form>";
														echo "</div>";
													echo "</div>";
													
												echo "</div>";
												echo "<div class='_note_datepanel' style='margin-top:0px'>";
													 echo "<label class='l_date'>Last Edited :</label><br>".$row['Last_Edited'];
													 echo "</label>";
												echo "</div>";
											echo "</div>";									
										}
									}
								}
								
								
								$query="SELECT * FROM user_todo WHERE Username='$user_username' AND Pin_Stat='unpinned'";
								if($result=$connection->query($query)) {
									if($result->num_rows>0) {
										while($row = $result->fetch_array()) {
											echo "<div class='_column'>";
												echo "<div class='_note_datepanel'>";
													 echo "<label class='l_date'>Created On:</label><br>".$row['Created'];
												echo "</div>";
												if($row['Pin_Stat']=="unpinned") {
														$no_pin="no_pin";
														$no_pin_pic="none";
													}
													else {
														$no_pin="";
														$no_pin_pic="block";
													}
												echo "<div class='todo_list' id='list_".$row['List_Id']."'>";
													echo "<div class='pin_container ".$no_pin."' title='Pin/Unpin this note' onClick='pin_this_list(\"".$row['ID']."\");'>";
														echo "<img src='./pin.png' style='width:20px;height:22px;display:".$no_pin_pic."'>";
													echo "</div>";
													echo "<div class='show_title' aria-multiline='true' id='title_".$row['List_Id']."'>".$row['Title']."</div>";
													echo "<div class='show_note' aria-multiline='true' id='content_".$row['ID']."' style='padding-top:0px'>";
													$sql="SELECT * FROM todo_contents WHERE List_Id='".$row['List_Id']."'";
													if($res=$connection->query($sql)) {
														$i=0;
														if($res->num_rows>0) {
															while($row1 = $res->fetch_array()) {
																++$i;
																$strike="";
																echo "<div class='list_frame'>";
																	if($row1['Checkbox']=="checked") {
																		$strike="strike";
																	}
																		echo "<label class='form_label custom_checkbox ".$strike."' for='checkbox_".$row1['ID']."'> ".$row1['Content'];
																			echo "<input type='checkbox' id='checkbox_".$row1['ID']."' ".$row1['Checkbox']." disabled>";
																				echo "<span class='checkmark'></span>";
																		echo "</label>";
																		echo "<div id='content_at_".$row['List_Id']."_".$i."' style='display:none;'>";
																			echo "<input type='hidden' id='checkbox_stat_".$row1['ID']."' value='".$row1['Checkbox']."'>";
																			echo "<input type='hidden' id='list_content_stat".$row1['ID']."' value='".$row1['Content']."'>";
																		echo "</div>";
																echo "</div>";
															}
														}
														echo "<input type='hidden' id='no_list_".$row['List_Id']."' value='".$i."'>";
													}		
													echo "</div>";
													echo "<div class='_note_controlpanel'>";
														echo "<div class='note_button_panel'>";
															echo "<input type='button' class='panel_button list_but' value='Edit' onClick='edit_list(\"".$row['List_Id']."\");'>";
														echo "</div>";
														echo "<div class='note_button_panel' style='float:right'>";
															echo "<form method='post' action='".htmlspecialchars($_SERVER["PHP_SELF"])."'>";
															echo "<input type='hidden' name='deleting_id_list' value='".$row['List_Id']."'>";
															echo "<input type=\"hidden\" value=\"todo\" name=\"nav_location\" id=\"nav_location\">";
															echo "<input type='submit' class='panel_button del_but list_but' value='Delete' title='Delete this To-do List.'>";
															echo "</form>";
														echo "</div>";
													echo "</div>";
													
												echo "</div>";
												echo "<div class='_note_datepanel' style='margin-top:0px'>";
													 echo "<label class='l_date'>Last Edited :</label><br>".$row['Last_Edited'];
													 echo "</label>";
												echo "</div>";
											echo "</div>";									
										}
									}
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br><br><br>
	<div class="footer">
		<div class="navigate_page _foot">
			<div class="_foot_notes">
			<a href="./index.php">KeepNotes.<span class="com_">com</span></a>
			</div>
			<div class="_foot_notes copyright" style="">
			&copy 2017-<?php echo date("Y"); ?>
			</div>
		</div>
	</div>
</div>
</div>


<div id="content_editable" style="display:none;">
		<div class="edit_container" id="edit_container">
					<div class="cross_" onclick="hide_edit_content();">
						<div class="bar1 bar_com" id="close1"></div>
						<div class="bar2 bar_com" id="close2"></div>
					</div>
					<div class="add_bar" id="note_edit" style="display:none;">
						<div class="input_container" id="title_bar" >
							<div class="legend ui_title" onClick='document.getElementById("edit_note_title").focus()' id="edit_note_title_caption">Title</div>
							<div class="title_bar ui_text ui_title" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="edit_note_title" oninput="detect_change(this.id);"></div>
						</div>
						<div class="input_container" id="note_bar">
							<div class="legend " onClick='document.getElementById("edit_note_content").focus()' id="edit_note_content_caption">Add Note....</div>
							<div class="title_bar ui_text" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="edit_note_content" oninput="detect_change(this.id);"></div>
						</div>
						<div class="input_container" id="settings_bar" style="padding-bottom:10px;padding-top:10px;">
							<div class="button_container">
							 <form name="" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
								<input type="hidden" name="id_note" id="id_note" value="">
								<input type="hidden" name="edit_note_title_val" id="edit_note_title_val" value="">
								<input type="hidden" name="edit_note_content_val" id="edit_note_content_val" value="">
								<input type="submit" class="ui_button" value="Update Note" id="edit_button" disabled>
							</form>
							</div>
							<div class="button_container">
								<input type="button" class="ui_button button_cancel" value="Cancel" onClick="hide_edit_content();">
							</div>
						</div>	
				    </div>
					
					<div class="add_bar" id="todo_edit" style="display:none;">
						<div class="input_container" id="title_bar" >
							<div class="legend ui_title" onClick='document.getElementById("edit_list_title").focus()' id="edit_list_title_caption">Title</div>
							<div class="title_bar ui_text ui_title" contenteditable="true" aria-multiline="true" aria-label="Title" role="textbox" id="edit_list_title" oninput="detect_change(this.id);"></div>
						</div>
						<div class="input_container" id="note_bar">
							<div class="title_bar ui_text" id="edit_list_content" style="padding:0px">
								<div class="add_list_container" id="edit_list_container" style="">
									<table style="width:100%" id="edit_check_list_table">
										<tr>
											<td colspan="2" class="list_head" onclick='add_list_item("edit_check_list_table");'>+ List Item</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="input_container" id="settings_bar" style="padding-bottom:10px;padding-top:10px;">
							<div class="button_container">
								<input type="button" class="ui_button" value="Update List" id="edit_button" onclick='validate_list("edit_check_list_table");'>
								<form id="e_list_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:none">
							
								</form>
							</div>
							<div class="button_container">
								<input type="button" class="ui_button button_cancel" value="Cancel" onClick="hide_edit_content();">
							</div>
						</div>	
				    </div>
		
		</div>
</div>
<div id="popup" class="">
<?php	echo $toast_message ?>
</div>


<script>
let x=document.getElementById("popup");
<?php 
	if($altered==true) {
		$altered=false;
		echo "x.classList.toggle('show');"."\n";
		echo "setTimeout(function(){x.classList.toggle('show');}, 3000);"."\n";
	}
?>
</script>
<script>
let val=1;
function show_drop_menu() {
	if(val==1) {
		document.getElementById("account_control").classList.toggle("change");
		document.getElementById("hidden_menu").style.display="block";
		document.getElementById("hidden_menu_block").classList.toggle("zoomin");
		setTimeout(function() {document.getElementById("hidden_menu_block").classList.toggle("zoomin");},600);
		val=2;
	}
	else {
		hide_drop_menu();
	}
}

function hide_drop_menu() {
	if(val==2) {
		document.getElementById("account_control").classList.toggle("change");
		document.getElementById("hidden_menu_block").classList.toggle("zoomout");
		setTimeout(function() {document.getElementById("hidden_menu_block").classList.toggle("zoomout");
								document.getElementById("hidden_menu").style.display="none";},500);
		val=1;
	}
}

function detect_change(id) {
	let value=document.getElementById(id).innerHTML;
	let button_id=id.charAt(0)+id.charAt(1)+id.charAt(2)+id.charAt(3);
	if(value!="") {
		document.getElementById(id+"_caption").style.display="none";
		document.getElementById(id+"_val").value=value;
		if(id=="note_content"||id=="edit_note_content") {
			document.getElementById(button_id+"_button").disabled=false;
		}
	}
	if(value==""||value=="<br>"){
		document.getElementById(id+"_caption").style.display="block";
		document.getElementById(id+"_val").value="";
		if(id=="note_content"||id=="edit_note_content") {
			document.getElementById(button_id+"_button").disabled=true;
		}
	}
}

function show_note_content() {
	document.getElementById("title_bar").style.display="block";
	document.getElementById("settings_bar").style.display="block";
}

function hide_note_content() {
	document.getElementById("title_bar").style.display="none";
	document.getElementById("settings_bar").style.display="none";
	document.getElementById("note_title").innerHTML="";
	document.getElementById("note_content").innerHTML="";
	document.getElementById("note_content_caption").style.display="block";
	document.getElementById("note_title_caption").style.display="block";
}


function show_todo_content() {
	document.getElementById("todo_title_bar").style.display="block";
	document.getElementById("todo_settings_bar").style.display="block";
	document.getElementById("todo_content_caption").style.display="none";
	document.getElementById("add_list_container").style.display="block";
}

function hide_todo_content() {
	document.getElementById("todo_title_bar").style.display="none";
	document.getElementById("todo_settings_bar").style.display="none";
	document.getElementById("add_list_container").style.display="none";
	document.getElementById("todo_title").innerHTML="";
	document.getElementById("todo_content_caption").style.display="block";
	document.getElementById("todo_title_caption").style.display="block";
	document.getElementById("list_form").innerHTML="";
	let table=document.getElementById("check_list_table");
	let row=table.rows.length;
	if(row>1) {
		for(i=(row-1);i>=1;i--) {
			table.deleteRow(i);
		}
	}
}

function toogle_panel_to_todo(id) {
	id=document.getElementById(id);
	if(id.className=="header_") {
		document.getElementById("note_panel").style.display="none";
		document.getElementById("todo_panel").style.display="block";
		hide_note_content();
		id.classList.toggle("h_active");
		document.getElementById("note_nav").classList.toggle("h_active");
		document.getElementById("nav_location").value="todo";
	}
}

function toogle_panel_to_note(id) {
	id=document.getElementById(id);
	if(id.className=="header_") {
		document.getElementById("note_panel").style.display="block";
		document.getElementById("todo_panel").style.display="none";
		hide_todo_content();
		id.classList.toggle("h_active");
		document.getElementById("todo_nav").classList.toggle("h_active");
		document.getElementById("nav_location").value="note";
	}
}

function edit_note(note_id) {
	document.getElementById("content_editable").style.display="block";
	document.getElementById("note_edit").style.display="block";
	document.getElementById("id_note").value=note_id;
	let title=document.getElementById("title_"+note_id).innerHTML;
	let content=document.getElementById("content_"+note_id).innerHTML;
	document.getElementById("edit_note_content_caption").style.display="none";
	document.getElementById("edit_note_content").innerHTML=content;
	document.getElementById("edit_note_content_val").value=content;
	if(title!="") {
		document.getElementById("edit_note_title_caption").style.display="none";
		document.getElementById("edit_note_title").innerHTML=title;
		document.getElementById("edit_note_title_val").value=title;
	}
	
	
}

function edit_list(list_id) {
	document.getElementById("content_editable").style.display="block";
	document.getElementById("todo_edit").style.display="block";
	let index=document.getElementById("no_list_"+list_id).value;
	document.getElementById("edit_list_title").innerHTML="";
	let title="";
	if(document.getElementById("title_"+list_id).innerHTML!="") {
		title=document.getElementById("title_"+list_id).innerHTML;
		document.getElementById("edit_list_title_caption").style.display="none";
		document.getElementById("edit_list_title").innerHTML=title;
	}
	let table=document.getElementById("edit_check_list_table");
	let checklist="e_checklist";
	let row=table.rows.length;
	let c_box_stat="";
	let l_content="";
	let x;
	let striked="";
	for(i=1;i<=index;i++) {
		x=document.getElementById("content_at_"+list_id+"_"+i).querySelectorAll("input");
		for(j=0;j<x.length;j=j+2) {
			c_box_stat=x[j].value;
			l_content=x[j+1].value;
			if(c_box_stat=="checked")
				striked="strike";
			else
				striked="";
			row=table.rows.length;
			table.insertRow(row).outerHTML="<tr><td style='width:93%'><label class='form_label custom_checkbox' for='"+checklist+"_"+i+"' style='margin-top:5px'><input type='text' value=\""+l_content+"\" id='"+checklist+"_"+i+"_caption' class='list_text "+striked+"' required autofocus='true'><input type='checkbox' value='Remember Me' "+c_box_stat+" id='"+checklist+"_"+i+"' onclick='toggle_check(this.id);' ><span class='checkmark _blue'></span></label></td><td style='width:7%'><div class='cross_delete' onclick='delete_row(\"edit_check_list_table\",this);' title='Delete'> X</div></td></tr>";
	    }
	}
	document.getElementById("e_list_form").innerHTML="<input type='hidden' name='id_to_alter' value='"+list_id+"'>";
}

function hide_edit_content() {
	document.getElementById("edit_container").classList.toggle("edit_container_zoomout");
	setTimeout(function() {
						document.getElementById("edit_container").classList.toggle("edit_container_zoomout");
						document.getElementById("content_editable").style.display="none";
						document.getElementById("note_edit").style.display="none";
						document.getElementById("todo_edit").style.display="none";
						let table=document.getElementById("edit_check_list_table");
						let row=table.rows.length;
						if(row>1) {
							for(i=(row-1);i>=1;i--) {
							table.deleteRow(i);
							}
						}
						document.getElementById("e_list_form").innerHTML="";
						},500);
}

function pin_this_note(x) {
	document.getElementById("pin_stat").value=x;
	document.getElementById("pin_stat_button").click();
}

function pin_this_list(x) {
	document.getElementById("pin_stat_list").value=x;
	document.getElementById("pin_stat_button_list").click();
}

window.onclick = function(event) {
    if (event.target == document.getElementById("content_editable")) {
        hide_edit_content();
    }
	if (event.target == document.getElementById("hidden_menu")) {
        hide_drop_menu();
    }
	if(event.target == document.getElementsByClassName("header_container")[0]||event.target == document.getElementsByClassName("header_bar")[0]) {
		if(val==2) {
			hide_drop_menu();
		}
	}
}

if(document.getElementById("note_panel").style.display=="block") {
		id=document.getElementById("note_nav");
		if(id.className=="header_") {
			document.getElementById("note_panel").style.display="block";
			document.getElementById("todo_panel").style.display="none";
			hide_todo_content();
			id.classList.toggle("h_active");
			document.getElementById("nav_location").value="note";
		}
	}
if(document.getElementById("todo_panel").style.display=="block") {
	id=document.getElementById("todo_nav");
	if(id.className=="header_") {
		document.getElementById("note_panel").style.display="none";
		document.getElementById("todo_panel").style.display="block";
		hide_note_content();
		id.classList.toggle("h_active");
		document.getElementById("nav_location").value="todo";
	}
}

</script>
<script src="./todo.js"></script>
</body>


