<?php

print("HEY0");
if (!defined('PHPLISTINIT')) die(); ## avoid pages being loaded directly
if ($GLOBALS["commandline"]) {
 echo 'not to oppened by command line';
 die();
}
print('<br><br>');
print_r($GLOBALS['tables']['attribute']);
print("HEY1");
//include_once($GLOBALS['AttributeChangerPlugin']->$AttributeChangerData['PLUGIN_CLASS_DIR'].'/AttributeChangerPlugin.php');
print("HEY2");
$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
$PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];

$WWW_FILES_DIR = $attribute_changer->AttributeChangerData['www_files_dir'];


$AttributeChangerData = $attribute_changer->AttributeChangerData;
print("HEY3");
require_once($PLUGIN_FILES_DIR.'Single_Session.php');
print("HEY4");
require_once($PLUGIN_FILES_DIR.'Display_Functions.php');
print("HEY5");
require_once($PLUGIN_FILES_DIR.'Display_Adjustment_Functions.php');
print("HEY6");

$javascript_src = $WWW_FILES_DIR.'Script_For_Attribute_Changer9.js';

print($javascript_src);

// //CHANGE THE PAGE PRINT TO REFLECT THE PROPER PLUGIN DIR
$page_print = '
<div>Attribute Changer</div>
<div id="error_printing"></div>
<form action="" method="post" enctype="multipart/form-data" id="file_upload_form">
    Select file to upload:
    (must be comma separated text)
    <input type="file" name="attribute_changer_file_to_upload" id="attribute_changer_file_to_upload">
    <input type="button" value="attribute_changer_upload_file_button" name="attribute_changer_upload_file_button" id="attribute_changer_upload_file_button" onClick="Test_Upload_File()">
</form>
<form action="" method="post" name="upload_the_text">
    Click to use a default file to test:

    <input type="submit" value="attribute_changer_upload_text" name="attribute_changer_upload_text">
    desired_file_name:<input type="text" name="attribute_changer_text_name">
</form>
<form action="" method="post" name="resetTable">
<input type="submit" value="resetTable" name="resetTable">
</form>

<form action="" method="post" name="submitTest">
    <input type="submit" value="submitTest" name="submitTest"></input>
</form>
';

if(!isset($_POST)) {

    print('<html><head><link rel="stylesheet" type="text/css" href="'.$WWW_FILES_DIR.'cssStyles.css"><script src="'.$javascript_src.'"></script></head><body>'.$page_print.'</body></html>');
}

else{

    printf('<html><head><link rel="stylesheet" type="text/css" href="'.$WWW_FILES_DIR.'cssStyles.css"><script src="'.$javascript_src.'"></script></head><body>SOMETHING HAPPENED, HERES THE FRONT :<br>'.$page_print.'</body></html>');
}



if(isset($_POST['attribute_changer_upload_text']) && $_POST['attribute_changer_upload_text'] == 'attribute_changer_upload_text') {
    
    include_once($PLUGIN_FILES_DIR.'Upload_Text_Processor.php');

    if(!isset($attribute_changer->Current_Session) || $attribute_changer->Current_Session == null) {

        print("<html><html>");
    }
    if($attribute_changer->Current_Session->file_is_good == false){
        print('</body></html>');
    }
    else{
        $print_html = Get_Attribute_File_Column_Match();

        $attribute_changer->Serialize_And_Store();
        print('<html><body>'.$print_html.'</body></html>');
    }
}

if(isset($_POST['resetTable'])) {
    $query = sprintf("truncate table %s", $AttributeChangerData['tables']['user']);
    $ret1 = Sql_Query($query);
    $query =sprintf("truncate table %s", $GLOBALS['tables']['user_attribute']);
    $ret2 = Sql_Query($query);

    include_once($PLUGIN_FILES_DIR.'New_And_Modify_Entry_Processor.php');

    $id = addNewUser('djarcaig@milburnlaw.ca@');
    if(!$id){
        print("error with user clear<br>");
        return -1;
    }
    SaveCurrentUserAttribute($id, '1' , 'fake name');
    SaveCurrentUserAttribute($id, '1' , '1');


}



if(isset($_POST['submitTest']) && $_POST['submitTest'] == 'submitTest') {
    


    include_once($PLUGIN_FILES_DIR.'Upload_Test_File_Processor.php');

    if(!isset($attribute_changer->Current_Session) || $attribute_changer->Current_Session == null) {

        print("<html><html>");
    }
    if($attribute_changer->Current_Session->file_is_good == false){
        print('</body></html>');
    }
    else{
        $print_html = Get_Attribute_File_Column_Match();

        $attribute_changer->Serialize_And_Store();
        print('<html><body>'.$print_html.'</body></html>');
    }    
}

if(isset($_FILES['attribute_changer_file_to_upload']) && !empty($_FILES['attribute_changer_file_to_upload'])) {

    include_once($PLUGIN_FILES_DIR.'Upload_File_Processor.php');

    if(!isset($attribute_changer->Current_Session) || $attribute_changer->Current_Session == null) {

        print("<html><html>");
    }
    if($attribute_changer->Current_Session->file_is_good == false){
        print('</body></html>');
    }
    else{
        $print_html = Get_Attribute_File_Column_Match();

        $attribute_changer->Serialize_And_Store();
        print('<html><body>'.$print_html.'</body></html>');
    }
    
}

require_once('ADD_ALLOWED_ATTRIBUTE_VALUES_PAGE.php');

if(isset($_POST['File_Column_Match_Submit'])) {


    $attribute_changer->Retreive_And_Unserialize();

    include_once($PLUGIN_FILES_DIR.'Column_Match_Processor.php');

    if(count($attribute_changer->Current_Session->all_pending_attributes_and_emails) > 0) {

        print('<html><body>'.$print_html);
        print(Get_Pending_Attributes_Selection_HTML());
        print('</body><html>');
    }
    else {
        if($attribute_changer->Current_Session->column_match_good == false) {

            print('<html><body>'.$print_html.'</body></html>');

            $attribute_changer->Serialize_And_Store();
            die();
        }

        if(Initialize_New_Entries_Display()!=null) {

            $display_html = BuilNewEntryDom()->saveHTML();

            $attribute_changer->Serialize_And_Store();
            


                    //print_r($attribute_changer->Current_Session->New_Entry_List);
            print($display_html);
        }

        else{
            
            if(Initialize_Modify_Entries_Display()!=null) {

                $display_html =  BuildModifyEntryDom()->saveHTML();

            }
            else{

                $display_html = $display_html.'There is nothing new or to modify</body></html>';
            }

            print($display_html);
        }
            print('<br>');
            //print_r($attribute_changer->Current_Session->New_Entry_List);
            print('<br>');
        print("HERE1");

       // print_r($attribute_changer->Current_Session->all_pending_attributes_and_emails);
        $attribute_changer->Serialize_And_Store();

        print("HERE2");    
    }
    
}
if(isset($_POST['pending_attributes_selection_form']) && $_POST['pending_attributes_selection_form'] == 'pending_attributes_selection_form') {

    $attribute_changer->Retreive_And_Unserialize();
    Process_Pending_Attributes_Form();
    if($attribute_changer->Current_Session->column_match_good == false) {

        print('<html><body>'.$print_html.'</body></html>');

        $attribute_changer->Serialize_And_Store();
        die();
    }

    if(Initialize_New_Entries_Display()!=null) {

        $display_html = BuilNewEntryDom()->saveHTML();

        $attribute_changer->Serialize_And_Store();
        


                //print_r($attribute_changer->Current_Session->New_Entry_List);
        print($display_html);
    }

    else{
        
        if(Initialize_Modify_Entries_Display()!=null) {

            $display_html =  BuildModifyEntryDom()->saveHTML();

        }
        else{

            $display_html = $display_html.'There is nothing new or to modify</body></html>';
        }

        print($display_html);
    }
        print('<br>');
        //print_r($attribute_changer->Current_Session->New_Entry_List);
        print('<br>');
    print("HERE1");

   // print_r($attribute_changer->Current_Session->all_pending_attributes_and_emails);
    $attribute_changer->Serialize_And_Store();

    print("HERE2");  
}

if(isset($_POST['New_Entry_Form_Submitted'])) {


    $attribute_changer->Retreive_And_Unserialize();
    //print_r($attribute_changer->Current_Session->New_Entry_List);

    include_once($PLUGIN_FILES_DIR.'New_Entry_Table_Processor.php');

    $attribute_changer->Serialize_And_Store();

}

if(isset($_POST['Modify_Entry_Form_Submitted'])) {

    $attribute_changer->Retreive_And_Unserialize();
    // print_r($attribute_changer->Current_Session);

    $Session = $GLOBALS['AttributeChangerPlugin']->Current_Session;

    print('is here');

    include_once($PLUGIN_FILES_DIR.'Modify_Entry_Table_Processor.php');


    print("aa<br>aa");
    print('<br>avv'.$Session->Modify_Entries_Number_Of_Blocks.'avv<br>');
    

    print('HEYYYYYYYY');
    print_r($attribute_changer->Current_Session->Committed_Modify_Entries);
    print('heyyyyyyy');
    print("going to serialize<br>");
    print_r($attribute_changer->Current_Session->Modify_Entries_Number_Of_Blocks);
    $attribute_changer->Serialize_And_Store();
    print("RETTT");




}

?>