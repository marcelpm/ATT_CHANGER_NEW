<?php



/*
	Goal:

		When processing an input csv file after deciding which columns relate to which in the database
			-> collect all the unique values within the csv file for each file 

				-> if any of these values are not found within the list of allowed values :
					1. -> create a page to give the option to add these new values

					2. -> process the added values (ie. sql insert and all that)

					3. -> continue as usual 

	Functionality now changes on File Parsing:

		Build 2 arrays for each processed user:

				1. For the currently allowed values that will be processed (modify/new entry tables)

				2. For the not currently allowed values that will be processed by this script
					-> consider breaking this into two arrays (modify/new)
					->call it NEW/MODIFY_ENTRY_PENDING_VALUES

			Once the decision of which new atributes to add has been made, loop each PENDING_VALUES array
			--> if the user has any of the newly allowed values, add it to the NEW/MODIFY_ENTRY_LISTS





-> this requires a little more processing time but is conceptually simpler due to its logical flow (load first, process later)
		ALTERNATIVELY:

			build 1 array containing all entries in the csv file and ALL the values in the csv file

			for each column, keep a list of all unique values

			after column matching is submitted, process the allowed_values

				-> extract the allowed attribute values from the database

				-> for each attribute-> create a pending_new_value_list[$att_id] => array('new_val_1', 'new_val_2', ..)

					-> if one of the unique values is not in the array, add it to the pending_new_value_list

				-> this list is te main focus of the next view to select new allowed values

				--> process the new allowed values:
						-> insert into db
						-> add to attribute_list[$attribute_id] 


				--> loop the entire entry array to insert csv rows into new/modify entry lists (same as before)

*/
	/*


	 additionly again:

	 	when doing TEST_ENTRY() can collect the unique new values list, then keep thatas the separate arrayss
	 		--> then if there are new values, display the new_value_selector page

	 			-> then go back over the values

	*/

/*


GOING wITH The THIRD OPTION FOR NOW

*/


// $new_entry_pending_attribute_values = array();

// $modify_entry_pending_attribute_values = array();



// WILL HAVE TO CALL THIS FUNCTION IF ATT IS NOT ALLOWED VALUE
function Process_New_Attribute_Value($email_key, $attribute_id, $value_string, $is_new_entry) {

	//CONSIDER USING USER ID INSTEAD OF EMAIL KEY TO SAVE TIME AND EVEN SPACE

    $Session = $GLOBALS['AttributeChangerPlugin']->Current_Session;
	
	//print("<br>$email_key, $attribute_id, $value_string, $is_new_entry<br>");

	//should check if the value matches the requirements of the attribute
		// for now just giver

	if(!isset($Session->all_pending_attributes_and_emails[$attribute_id])) {
		$Session->all_pending_attributes_and_emails[$attribute_id] = array();
		//print('<br>HERHERHER<br>');
	}
	if(!isset($Session->all_pending_attributes_and_emails[$attribute_id][$value_string])) {
		$Session->all_pending_attributes_and_emails[$attribute_id][$value_string] = array();
		$Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['new_entry'] = array();
		$Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['modify_entry'] = array();
	}
	if($is_new_entry == true) {
		if(!in_array($email_key, $Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['new_entry'])) {
			array_push($Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['new_entry'], $email_key);
		}
	}
	else if($is_new_entry == false) {
		if(!in_array($email_key, $Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['modify_entry'])) {
			array_push($Session->all_pending_attributes_and_emails[$attribute_id][$value_string]['modify_entry'], $email_key);
		}
	}
}


// if(count($all_pending_attributes_and_emails) > 0) {}

function Get_Pending_Attributes_Selection_HTML() {


    $Session = $GLOBALS['AttributeChangerPlugin']->Current_Session;

	$all_pending_attributes_and_emails = $Session->all_pending_attributes_and_emails;

	$return_string = '<form action="" method="post" name="pending_attributes_selection_f">';
	$return_string.= '<table id="pending_attributes_selection_table">';

	$return_string .= '<tr>';

	foreach ($all_pending_attributes_and_emails as $attribute_id => $values_and_email_arrays) {
		$return_string .= '<td>'.$Session->attribute_list[$attribute_id]['name'].'</td>';
	}
	$return_string .= '</tr><tr>';
	foreach ($all_pending_attributes_and_emails as $attribute_id => $values_and_email_arrays) {

		$return_string .= '<td>';

		foreach ($values_and_email_arrays as $value => $email_arrays) {
			$return_string .= '<input type="checkbox" name="pending_attributes['.$attribute_id.']['.$value.']" value="'.$value.'">'.$value.'</input><br>';
		}
		$return_string .= '</td>';
	}
	$return_string .= '</tr></table>';
	$return_string .= '<input type="submit" name="pending_attributes_selection_form" value="pending_attributes_selection_form">SUBMIT THE FORM</input>';
	$return_string .= '</form>';
	return $return_string;
}


// on front page if(isset($_POST['pending_attributes_selection_form'])

function Process_Pending_Attributes_Form() {

// fixit fixit fixit newentry list was never initialized

    $Session = $GLOBALS['AttributeChangerPlugin']->Current_Session;
// print_r($Session);


// SOMEWHERE IN THIS MESS THE ENTRY LISTS ARE ERRASED

//////////////////////////
	$all_pending_attributes_and_emails = $Session->all_pending_attributes_and_emails;

	if(!isset($_POST['pending_attributes_selection_form'])) {
		exit('pending_attributes_selection_form post not set');
	}

	if(!isset($_POST['pending_attributes'])) {
		print('no pending attributes added');
		return;
	}


	foreach ($_POST['pending_attributes'] as $attribute_id => $values_to_add) {
		foreach ($values_to_add as $value) {

			$attribute_value_id = ADD_USER_ATTRIBUTE_VALUE($attribute_id, $value);
			if($attribute_value_id == false) {
				print("ERROR ADDING ATTRIBUTE VALUE $value");
				continue;
			}
			if($attribute_value_id == false) {
				print("Error with new value adding attribute_id: ".$attribute_id." and value: ".$value);
			}
			else{
				array_push($Session->attribute_list[$attribute_id]['allowed_value_ids'], [$attribute_value_id=>$value]);
				foreach ($all_pending_attributes_and_emails[$attribute_id][$value]['new_entry'] as $email_key) {
					if(!isset($Session->New_Entry_List[$email_key])) {
						$Session->New_Entry_List[$email_key] = array();
					}
					if(!isset($Session->New_Entry_List[$email_key][$attribute_id])) {
						$Session->New_Entry_List[$email_key][$attribute_id] = array();
					}
					if(!in_array($attribute_value_id, $Session->New_Entry_List[$email_key][$attribute_id])) {
						array_push($Session->New_Entry_List[$email_key][$attribute_id], $attribute_value_id);
					}
				} 
			//	print('<br>NNNNNNNNNNNNNNNN<br>');
////////////////////////
/////// HEREREREREEERERE 
				//THIS PRINT DID NOTTTT RETURN ANYTHINGGGGGG below
			//	print_r($Session->New_Entry_List);
			}
		}
	} 
}

function ADD_USER_ATTRIBUTE_VALUE($attribute_id, $value){


	$attribute_info_query = 'SELECT tablename FROM phplist_user_attribute WHERE id = '.$attribute_id;

	$res = Sql_Query($attribute_info_query);

	if($res) {
		$tablename = Sql_Fetch_Row($res)[0];

		$max_listorder_query = 'select max(listorder) as listorder from phplist_listattr_'.$tablename;
		
		$maxitem = Sql_Fetch_Row_Query($max_listorder_query);

	    if (!Sql_Affected_Rows() || !is_numeric($maxitem[0])) {
	        $listorder = 1; # insert the listorder as it's in the textarea / start with 1 '
	    } else {
	        $listorder = $maxitem[0] + 1; # One more than the maximum
	    }
		$val = clean($value);
	    
	    $table = 'phplist_listattr_'.$tablename;

	    if ($val != '') {
	        $query = sprintf('insert into %s (name,listorder) values("%s","%s")', $table, $val, $listorder);
	        $result = Sql_query($query);
	        if($result) {
	        	$value_id_query = "SELECT id FROM ".$table.' WHERE name="'.$val.'"';
	        	print($value_id_query);
	        	$value_id_res = Sql_Query($value_id_query);

	        	if($value_id_res) {
	        		$value_id = Sql_Fetch_Row($value_id_res)[0];
	        		print('<br>aaaaaccccc<br>'.$value_id.'<br>');
	        		return $value_id;
	        	}
	        }
	    }

	}
	return false;
}



// <html>

// 	<body>
// 		<form name="pending_attribute_selection_form">
// 			<table id="pending_attribute_selection_table">
// 				<tr>
// 					<td>
// 						ATTRIBUTE_NAME
// 					</td>
// 				</tr>
// 				<tr>
// 					<td>

// 						<input type="checkbox" name="pending_attribute_checkbox[attribute_id][pending_value]" value="pending_value">pending_value</input>

// 					</td>

// 				</tr>

// 			</table>
// 		</form>
		

// 	</body>




// </html>

?>
