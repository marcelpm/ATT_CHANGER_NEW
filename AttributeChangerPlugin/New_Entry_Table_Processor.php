<?php
//require_once(dirname(__FILE__).'Attribute_Changer_Plugin.php');

//$attribute_changer = $GLOBALS['plugins']['Attribute_Changer_Plugin'];


// if($attribute_changer->Current_Session == null) {
//     print("ERRORROROR");
//     return;
// }

    // $javascript_src = PLUGIN_ROOTDIR.'/AttributeChangerPlugin/Script_For_Attribute_Changer.js';     
//print_r($_POST['New_Entry_List']);


    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];

    $Session = $attribute_changer->Current_Session;

        function Build_New_Entry_Email_List() {
            $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
            $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
            $AttributeChangerData = $attribute_changer->AttributeChangerData;

            $case_array = $AttributeChangerData['case_array'];

            $Session = $attribute_changer->Current_Session;
           
            $Columns_To_Accept = array();
            
            foreach ($_POST['New_Entry_Attribute_Column_Select'] as $attribute_id => $include_value) {

                if(array_key_exists($attribute_id, $Session->attribute_list)) {
                    array_push($Columns_To_Accept, $attribute_id);
                }
                
            }


            $Session->New_Entries_Columns_To_Select = $Columns_To_Accept;

            if(!isset($_POST['Hidden_New_Entry_List'])) {
                //error
                print("<html><body>THERE WAS AN ERROR WITH THE HIDDEN INPUT</body></html>");
                return false;
            }
            foreach ($_POST['Hidden_New_Entry_List'] as $hidden_email_key => $include_value) {


                if(!isset($_POST['New_Entry_List'][$hidden_email_key]['include'])) {

                    unset($Session->Committed_New_Entries[$hidden_email_key]);
                }
                else{

                    $Session->Committed_New_Entries[$hidden_email_key] = array();
                    foreach ($Columns_To_Accept as $key => $attribute_id) {

                        if(isset($_POST['New_Entry_List'][$hidden_email_key][$attribute_id])) {

                            if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_3') {
                                
                                foreach ($_POST['New_Entry_List'][$hidden_email_key][$attribute_id] as $checkbox_key_id => $checkbox_value_id) {

                                    if(!isset($Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                        //print("<br>checkbox_value_id<br>");
                                    }
                                        print("YARRRRR: ".$checkbox_key_id.'<br>');
                                        print("YARRRRR again: ".$checkbox_value_id.'<br>');

                                    if(array_key_exists($checkbox_value_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                        //print("huuuur again again<br>");

                                        if(!isset($Session->Committed_New_Entries[$hidden_email_key][$attribute_id])) {
                                            $Session->Committed_New_Entries[$hidden_email_key][$attribute_id] = array();
                                        }
                                        array_push($Session->Committed_New_Entries[$hidden_email_key][$attribute_id], $checkbox_value_id);
                                    }
                               
                                }
                            }
                            else  if($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_2') {
                                    
                                    if(in_array($_POST['New_Entry_List'][$hidden_email_key][$attribute_id], $Session->attribute_list[$attribute_id])) {

                                        $Session->Committed_New_Entries[$hidden_email_key][$attribute_id] = array_search($_POST['New_Entry_List'][$hidden_email_key][$attribute_id], $Session->attribute_list[$attribute_id]);
                                        
                                    }

                                }

                            else if ($case_array[$Session->attribute_list[$attribute_id]['type']] == 'case_1') {
                                //have test for good text here, HTML SPECIAL CHARS
                                $Session->Committed_New_Entries[$hidden_email_key][$attribute_id] = $_POST['New_Entry_List'][$hidden_email_key][$attribute_id];
                            }

                        }
                    }
                }
            }

            return true;
        }


        // function Get_Allowed_Attributes($attribute_id) {
        //     if(isset($Session->attribute_list[$attribute_id]) && $Session->attribute_list[$attribute_id]['type'] == ('checkboxgroup'|'checkbox'|'radio'|'select')) {
        //         return $Session->attribute_list[$attribute_id]['allowed_value_ids'];
        //     }
        //     else{
        //         return false;
        //     }
        // }


include_once(PLUGIN_ROOTDIR.'/AttributeChangerPlugin.php');

        $Current_New_Entry_Block;

        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;

        $case_array = $AttributeChangerData['case_array'];

        $Session = $attribute_changer->Current_Session;

        if(isset($_POST['New_Entry_Form_Submitted'])) {
            

           
            if(!Build_New_Entry_Email_List()){
                
                die();
            }
        }

        if(isset($_POST['New_Entries_Table_Submit_All']) && $_POST['New_Entries_Table_Submit_All'] === 'New_Entries_Table_Submit_All' ) {

            //$GLOBALS['plugins']['AttributeChangerPlugin']->Retreive_And_Unserialize();

            include_once($PLUGIN_FILES_DIR.'Display_Functions.php');
            include_once($PLUGIN_FILES_DIR.'Display_Adjustment_Functions.php');


            if(Initialize_Modify_Entries_Display() == null) {
                include_once($PLUGIN_FILES_DIR.'New_And_Modify_Entry_Processor.php');
                Process_All_New_And_Modify();
            }
            else{
                $HTML_TO_DISPLAY = BuildModifyEntryDom()->saveHTML();
                print('<html><body><script src="'.$javascript_src.'"></script>'.$HTML_TO_DISPLAY.'</body></html>');
            }

        }

        if(isset($_POST['New_Entry_Change_Display_Amount']) && $_POST['New_Entry_Change_Display_Amount'] == 'New_Entry_Change_Display_Amount') {

            print("HUUUUUUR");
            
            $new_display_amounts = $AttributeChangerData['displayAmounts'];


            if(isset($_POST['New_Entries_New_Display_Amount'])) {

                if(!isset($new_display_amounts[$_POST['New_Entries_New_Display_Amount']]) || $new_display_amounts[$_POST['New_Entries_New_Display_Amount']] != true) {

                }
                else{
                    include_once($PLUGIN_FILES_DIR.'Display_Adjustment_Functions.php');

                    if(New_Entry_Change_Display_Amount($_POST['New_Entries_New_Display_Amount']) != true) {
                        print("<br>ARRRRRK<br>");
                    }
                    else{
                         print('<br>'.$Session->Current_New_Entries_Display_Amount.'<br>');
                    }
                }
            }
            $HTML_TO_DISPLAY = BuilNewEntryDom()->saveHTML();

            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');
            
        }
        if(isset($_POST['New_Entries_Table_Next_Page']) && $_POST['New_Entries_Table_Next_Page'] === 'New_Entries_Table_Next_Page') {
            $truth = New_Entry_Display_Next_Page();
            if($truth == false) {
                $HTML_TO_DISPLAY = BuilNewEntryDom()->saveHTML();
            }
            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');
        }

        if(isset($_POST['New_Entries_Table_Previous_Page']) && $_POST['New_Entries_Table_Previous_Page'] === 'New_Entries_Table_Previous_Page') {
            $truth = New_Entry_Display_Previous_Page();
            if($truth == false) {
                $HTML_TO_DISPLAY = BuilNewEntryDom()->saveHTML();
            }
            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');
        }
     

?>