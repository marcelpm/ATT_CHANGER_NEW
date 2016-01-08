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
	

?>