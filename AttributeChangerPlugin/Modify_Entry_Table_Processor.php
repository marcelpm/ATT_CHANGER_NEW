<?php

$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
$PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
$AttributeChangerData = $attribute_changer->AttributeChangerData;



include_once($AttributeChangerData['PLUGIN_CLASS_DIR'].'/AttributeChangerPlugin.php');
require_once($PLUGIN_FILES_DIR.'/Display_Functions.php');
require_once($PLUGIN_FILES_DIR.'/Display_Adjustment_Functions.php');



//$javascript_src = 'plugins/AttributeChangerPlugin/Script_For_Attribute_Changer.js';


print('<br> AAAAAAAAAAAA<br>');



//NOT WORKINGG YET -> CHECK THE PRINT_R
        function Build_Modify_Entry_Email_List() {
            $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
            $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
            $AttributeChangerData = $attribute_changer->AttributeChangerData;

            $case_array = $AttributeChangerData['case_array'];


            $Session = $attribute_changer->Current_Session;

            print("<br>");
            print_r($_POST['Modify_Entry_List']['al@a.com']);
            print('<br>');

            $Columns_To_Accept = array();
            
            foreach ($_POST['Modify_Entry_Attribute_Column_Select'] as $attribute_id => $include_value) {

                if(array_key_exists($attribute_id, $Session->attribute_list)) {
                    array_push($Columns_To_Accept, $attribute_id);
                }
                
            }

            $Session->Modify_Entries_Columns_To_Select = $Columns_To_Accept;

            if(!isset($_POST['Hidden_Modify_Entry_List'])) {
                //error
                print("<html><body>THERE WAS AN ERROR WITH THE HIDDEN INPUT</body></html>");
                return false;
            }
            foreach ($_POST['Hidden_Modify_Entry_List'] as $hidden_email_key => $include_value) {

                if($_POST['Modify_Entry_List'][$hidden_email_key]['include'] != 'include') {

                    $Session->Committed_Modify_Entries[$hidden_email_key] = -1;

                   
                }

                else{

                    
                    // print("<br>have in here<br>");
                    // print($hidden_email_key);
                    // print('<br>');
                    $Session->Committed_Modify_Entries[$hidden_email_key] = array();

                    foreach ($Columns_To_Accept as $key => $attribute_id) {



                        
                            
                        if(isset($_POST['Modify_Entry_List'][$hidden_email_key][$attribute_id])) {


                            $this_attribute_case = $case_array[$Session->attribute_list[$attribute_id]['type']];
                            
                            print("<br>CASE: ".$this_attribute_case.'<br>');

                            if($this_attribute_case == 'case_3') {
                                
                                
                                foreach ($_POST['Modify_Entry_List'][$hidden_email_key][$attribute_id] as $checkbox_key_id => $checkbox_value_id) {

                                    if(!isset($Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                        //print("<br>checkbox_value_id<br>");
                                    }

                                    if(array_key_exists($checkbox_value_id, $Session->attribute_list[$attribute_id]['allowed_value_ids'])) {
                                        //print("huuuur again again<br>");
                                        if(!isset($Session->Committed_Modify_Entries[$hidden_email_key][$attribute_id])) {
                                            $Session->Committed_Modify_Entries[$hidden_email_key][$attribute_id] = array();
                                        }
                                        array_push($Session->Committed_Modify_Entries[$hidden_email_key][$attribute_id], $checkbox_value_id);
                                    }
                               
                                }
                            }
                            else if($this_attribute_case == 'case_2' ) {                                     
                                    


                                    if(in_array($_POST['Modify_Entry_List'][$hidden_email_key][$attribute_id], $Session->attribute_list[$attribute_id])) {

                                        $Session->Committed_Modify_Entries[$hidden_email_key][$attribute_id] = array_search($_POST['Modify_Entry_List'][$hidden_email_key][$attribute_id], $Session->attribute_list[$attribute_id]);
                                        
                                    }

                                }

                            else if ($this_attribute_case == 'case_1') {
                                //have test for good text here, HTML SPECIAL CHARS
                                $Session->Committed_Modify_Entries[$hidden_email_key][$attribute_id] = $_POST['Modify_Entry_List'][$hidden_email_key][$attribute_id];

                            }

                        }
                    }
                }
            }

            return true;
        }



$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
$PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
$AttributeChangerData = $attribute_changer->AttributeChangerData;

$case_array = $AttributeChangerData['case_array'];


$Session = $attribute_changer->Current_Session;


include_once($AttributeChangerData['PLUGIN_CLASS_DIR'].'/AttributeChangerPlugin.php');

        $Current_Modify_Entry_Block;


        if(isset($_POST['Modify_Entry_Form_Submitted'])) {

 
            if(!Build_Modify_Entry_Email_List()){

                die();
            }
        }

        if(isset($_POST['Modify_Entries_Table_Submit_All']) && $_POST['Modify_Entries_Table_Submit_All'] == 'Modify_Entries_Table_Submit_All' ) {

            include_once($PLUGIN_FILES_DIR.'New_And_Modify_Entry_Processor.php');
            
            Process_All_New_And_Modify();
        }

        if(isset($_POST['Modify_Entry_Change_Display_Amount']) && $_POST['Modify_Entry_Change_Display_Amount'] == 'Modify_Entry_Change_Display_Amount') {

            $Modify_display_amounts = $AttributeChangerData['displayAmounts'];


            if(isset($_POST['Modify_Entries_New_Display_Amount'])) {
                

                //check for allowed value type, 10,100,1000,10000,'all'
                if(!isset($Modify_display_amounts[$_POST['Modify_Entries_New_Display_Amount']]) || $Modify_display_amounts[$_POST['Modify_Entries_New_Display_Amount']] != true) {
                    //this should only occur if a request is processed, that which oriognaly came from a form not sent by the site
                }
                else{

                    //alow to be processed
                    include_once($PLUGIN_FILES_DIR.'isplay_Adjustment_Functions.php');

                    if(Modify_Entry_Change_Display_Amount($_POST['Modify_Entries_New_Display_Amount']) != true) {

                    }
                    else{

                    }
                }
            }
            $Session = $GLOBALS['AttributeChangerPlugin']->Current_Session;

            print("aa<br>ataa");
            print('<br>avv'.$Session->Modify_Entries_Number_Of_Blocks.'avv<br>');

            $HTML_TO_DISPLAY = BuildModifyEntryDom()->saveHTML();

            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');

            
        }
        if(isset($_POST['Modify_Entries_Table_Next_Page']) && $_POST['Modify_Entries_Table_Next_Page'] === 'Modify_Entries_Table_Next_Page') {
            


            $truth = Modify_Entry_Display_Next_Page();
           
            $HTML_TO_DISPLAY = BuildModifyEntryDom()->saveHTML();
            
            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');
        }

        if(isset($_POST['Modify_Entries_Table_Previous_Page']) && $_POST['Modify_Entries_Table_Previous_Page'] === 'Modify_Entries_Table_Previous_Page') {
            $truth = Modify_Entry_Display_Previous_Page();
            
            $HTML_TO_DISPLAY = BuildModifyEntryDom()->saveHTML();
            
            print('<html><body><script src="'.$javascript_src.'""></script>'.$HTML_TO_DISPLAY.'</body></html>');
        }


?>