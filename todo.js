function toggle_check(id) {
		document.getElementById(id+"_caption").classList.toggle("strike");	
}

function add_list_item(table_id) {
	let table=document.getElementById(table_id);
	let checklist="checklist";
	if(table_id.charAt(0)=='e') {
		checklist="e_checklist";
	}
	let row=table.rows.length;
	table.insertRow(row).outerHTML="<tr><td style='width:93%'><label class='form_label custom_checkbox' for='"+checklist+"_"+row+"' style='margin-top:5px'><input type='text' value='' id='"+checklist+"_"+row+"_caption' class='list_text' required autofocus='true'><input type='checkbox' value='Remember Me' id='"+checklist+"_"+row+"' onclick='toggle_check(this.id);'><span class='checkmark _blue'></span></label></td><td style='width:7%'><div class='cross_delete' onclick='delete_row(\""+table_id+"\",this);' title='Delete'> X</div></td></tr>";
}

function delete_row(table_id,id) {
	let x=id.parentNode.parentNode.rowIndex;
	document.getElementById(table_id).deleteRow(x);
}

function validate_list(table_id) {
	let table=document.getElementById(table_id);
	let row=table.rows.length;
	let checklist="checklist";
	let form_id="list_form";
	if(table_id.charAt(0)=='e') {
		checklist="e_checklist";
		form_id="e_list_form";
	}
	let checked="unchecked";
	let f=document.getElementById(form_id);
	if(row>1) {
		for(i=1;i<=row;i++) {
			checked="unchecked";
			if(document.getElementById(checklist+"_"+i+"_caption")) {
					if(document.getElementById(checklist+"_"+i+"_caption").value=="") {
						document.getElementById(checklist+"_"+i+"_caption").focus();
						alert("One of the list item is empty. Either delete it or fill it.");
						f.innerHTML="";
						return;
					}
					else {
						if(document.getElementById(checklist+"_"+i).checked==true)
							checked="checked";
						if(table_id.charAt(0)!='e') {
							f.innerHTML+="<input type='hidden' name='checkbox_status_"+i+"' value='"+checked+"'>";
							f.innerHTML+="<input type='hidden' name='list_content_"+i+"' value='"+document.getElementById(checklist+"_"+i+"_caption").value+"'>";						
						}
						else {
							f.innerHTML+="<input type='hidden' name='edit_checkbox_status_"+i+"' value='"+checked+"'>";
							f.innerHTML+="<input type='hidden' name='edit_list_content_"+i+"' value='"+document.getElementById(checklist+"_"+i+"_caption").value+"'>";						
						}
					}
			}				
		}
	if(table_id.charAt(0)!='e') {
		f.innerHTML+="<input type='hidden' name='title_list' value='"+document.getElementById("todo_title").innerHTML+"'>";
		f.innerHTML+="<input type='hidden' name='total_list_item' value='"+(row-1)+"'>";
		f.innerHTML+="<input type=\"hidden\" value=\"todo\" name=\"nav_location\" id=\"nav_location\">";
		f.innerHTML+="<input type='submit' id='final_list_submit'>";
		document.getElementById("final_list_submit").click();
	}
	else {
		f.innerHTML+="<input type='hidden' name='edit_title_list' value='"+document.getElementById("edit_list_title").innerHTML+"'>";
		f.innerHTML+="<input type='hidden' name='edit_total_list_item' value='"+(row-1)+"'>";
		f.innerHTML+="<input type=\"hidden\" value=\"todo\" name=\"nav_location\" id=\"nav_location\">";
		f.innerHTML+="<input type='submit' id='final_edit_list_submit'>";
		document.getElementById("final_edit_list_submit").click();
	}
	}
	
}