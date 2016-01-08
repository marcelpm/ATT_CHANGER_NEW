<?php

    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];

    $Session = $attribute_changer->Current_Session;

    include_once($AttributeChangerData['PLUGIN_CLASS_DIR'].'/AttributeChangerPlugin.php');

	function Process_All_New_And_Modify() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;

        $case_array = $AttributeChangerData['case_array'];

        $Session = $attribute_changer->Current_Session;

        print('<br>process_New_Entries<br>');

        if(count($Session->Committed_New_Entries) > 0) {
            Push_New_Entries();
        }
        if(count($Session->Committed_Modify_Entries) > 0) {
            Push_Modify_Entries();
        }
        $return_html = '<html><body>Complete</body></html>';

        $attribute_changer->Close_Session();

        print($return_html);
    }

    //$Failed_New_Entries;

    function Push_New_Entries() {
        print('<br>Push_New_Entries<br>');
        
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;

        $case_array = $AttributeChangerData['case_array'];

        $Session = $attribute_changer->Current_Session;

        foreach ($Session->Committed_New_Entries as $email_key => $new_attributes_and_values) {

            $exists = Sql_Fetch_Row_Query(sprintf('select id from %s where email = "%s"', $AttributeChangerData['tables']['user'],$email_key));
            if($exists[0]) {
                //$Failed_New_Entries[$email_key] = $new_attributes_and_values;
            }
            else{

                $new_user_id = addNewUser($email_key);
                $new_value_array = array();

                foreach ($new_attributes_and_values as $attribute_id => $attribute_value_id) {
                    if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_3') {
                        $new_value_array = array();
                        foreach ($attribute_value_id as $individual_id) {
                            if(array_key_exists($individual_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                array_push($new_value_array, $individual_id);
                            }
                        }
                        $proper_this_attribute_value = implode(',', $new_value_array);

                    }
                    else if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_2') {
                        

                        if(array_key_exists($attribute_value_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                            $proper_this_attribute_value = $attribute_value_id;
                        }
                    }
                    else{///HERE IS MESSSSSSSSY
                        if( in_array($attribute_value_id, $Session->New_Entry_List[$email_key][$attribute_id]) ) {
                            $proper_this_attribute_value = $attribute_value_id;
                        }
                    }
                    print('<br>new user:  '.$email_key.' attribute id:   '.$attribute_id.' value  :'.$proper_this_attribute_value.'<br>');
                    //need a way for 'STICKY' attributes
                    
                    SaveCurrentUserAttribute($new_user_id, $attribute_id, $proper_this_attribute_value);

                    //saveUserAttribute($new_user_id, $attribute_id, $proper_this_attribute_value);
                }   
            }
        }
    }
// function AddUser($email_key) {
//                     $exists = Sql_Fetch_Row_Query(sprintf('select id from %s where email = "%s"', $GLOBALS['tables']['user'], $email_key));
//                     if($exists[0]) {
//                         return false;
//                     }
//                     else{
//                         $new_user_query = sprintf("insert into %s (email) values (%s)", $GLOBALS['tables']['user'], $email_key);
//                     }

//                 }



    //$Failed_Modify_Entries;
    function Push_Modify_Entries() {
        print('<br>HERE<br>');

        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;

        $case_array = $AttributeChangerData['case_array'];

        $Session = $attribute_changer->Current_Session;


        foreach ($Session->Committed_Modify_Entries as $email_key => $modify_attributes_and_values) {

           

            $exists = Sql_Fetch_Row_Query(sprintf('select id from %s where email = "%s"', $AttributeChangerData['tables']['user'],$email_key));
            if(!$exists[0]) {
                //$Failed_Modify_Entries[$email_key] = $modify_attributes_and_values;
            }
            else{
                $modify_user_id = $exists[0];
                print('<br>current user id<br>'.$modify_user_id.'<br>');
                $modify_value_array = array();

                foreach ($modify_attributes_and_values as $attribute_id => $attribute_value_id) {
                    if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_3') {
                        $modify_value_array = array();
                        foreach ($attribute_value_id as $individual_id) {
                            if(array_key_exists($individual_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                array_push($modify_value_array, $individual_id);
                            }
                        }
                        $proper_this_attribute_value = implode(',', $modify_value_array);

                    }
                    else if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_2') {
                        
                        
                        if(array_key_exists($attribute_value_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                            $proper_this_attribute_value = $attribute_value_id;
                        }
                    }
                    else{///HERE IS MESSSSSSSSY
                        if( in_array($attribute_value_id, $Session->Modify_Entry_List[$email_key][$attribute_id]) ) {
                            $proper_this_attribute_value = $attribute_value_id;
                        }
                    }
                    print('<br>modify user:  '.$email_key.' attribute id:   '.$attribute_id.' value  :'.$proper_this_attribute_value.'<br>');
                    //need a way for 'STICKY' attributes
                    print("<br>mod user id : ".$modify_user_id."<br>");


                    print("<br>IS HEREEEE FOR USER ".$modify_user_id.' AND WITH VALUE : '.$proper_this_attribute_value.'<br>');

                    SaveCurrentUserAttribute($modify_user_id, $attribute_id, $proper_this_attribute_value);
                }   
            }
        }
    }

    function SaveCurrentUserAttribute($modify_user_id, $attribute_id, $proper_this_attribute_value){
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;


        $Session = $attribute_changer->Current_Session;

        $exists = Sql_Fetch_Row_Query(sprintf('select email from %s where id = "%d"', $AttributeChangerData['tables']['user'], $modify_user_id));
        if(!$exists[0]) {
            return;
        }

        $current_value_query = sprintf('select value from %s where (userid,attributeid)=(%d,%d)', $AttributeChangerData['tables']['user_attribute'], $modify_user_id, $attribute_id);
        $current_value_return = Sql_Fetch_Row_Query($current_value_query);

        if(!$current_value_return[0]) {

            $update_query = sprintf('insert into %s  (userid,attributeid, value) values (%d,%d,"%s")', $AttributeChangerData['tables']['user_attribute'], $modify_user_id, $attribute_id, $proper_this_attribute_value);
        }
        else{
            print("<br>IS HEREEEE FOR USER ".$modify_user_id.' AND WITH VALUE : '.$proper_this_attribute_value.'<br>');
            $update_query = sprintf('update %s set value= "%s" where userid = %d and attributeid = %d', $AttributeChangerData['tables']['user_attribute'], $proper_this_attribute_value, $modify_user_id, $attribute_id);
            
        }
        Sql_Query($update_query);
    }

?>