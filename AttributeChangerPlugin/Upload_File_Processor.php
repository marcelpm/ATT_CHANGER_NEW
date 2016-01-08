<?php



//if (!defined('PHPLISTINIT')) die(); ## avoid pages being loaded directly


//.///////////////////////////////////////////////////
////////////////////////////////////still need to make the include this attribute sticky





$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
$PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
$AttributeChangerData = $attribute_changer->AttributeChangerData;

include_once($PLUGIN_FILES_DIR.'Display_Functions.php');


$page_print =  '
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

$attribute_changer->Test_Create_Temp_Dir();

$FILE_LOCATION;

// if(isset($_FILES['attribute_changer_file_to_upload'])) {
//     print('eff');
//     print('<html><head><script src="'.$javascript_src.'"></script></head><body>'.$page_print.'</body></html>');
// }


if(isset($_FILES['attribute_changer_file_to_upload']) && !empty($_FILES['attribute_changer_file_to_upload'])) {


    if(!$attribute_changer->Test_Create_Temp_Dir()) {

        print("<html><body>Error with temp directory</body></html>");
        return;
    }

//HERE HAVE A CHECK FOR GOOD SETUP
    $Current_Session = $attribute_changer->New_Session();
    //print_r($Current_Session);
    


    $target_dir = $PLUGIN_FILES_DIR.'temp_table_uploads/';

    $target_file = $target_dir . basename($_FILES["attribute_changer_file_to_upload"]["name"]);

    $uploadOk = 1;
    $new_file_type = pathinfo($target_file,PATHINFO_EXTENSION);

    $new_html = '';
    // Check if file already exists
    if (file_exists($target_file)) {

        while(file_exists($target_file)) {
            $new_filename =pathinfo($target_file,PATHINFO_FILENAME);

            $new_filename = $new_filename.strval(rand(0,100));

            $target_file = $target_dir.$new_filename.'.'.$new_file_type;
        }
        $new_html = $new_html."<div>File already exists, added rand value. File is: ".basename($target_file).'</div>';
    }

    // Check file size
    if ($_FILES["attribute_changer_file_to_upload"]["size"] > 1000000000) {
        $new_html = $new_html."<div>Sorry, your file is too large > 1GB. </div>";
        $uploadOk = 0;
    }
    // Allow certain file formats

    //add other comma separated
    if($new_file_type != "csv") {
        $new_html = $new_html."<div>Sorry, only csv allowed. </div>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $new_html = $new_html."<div>Sorry, your file was not uploaded. </div>".$page_print;
        $file_is_good = false;

    } 
    // if everything is ok, try to upload file
    else {

        if (move_uploaded_file($_FILES["attribute_changer_file_to_upload"]["tmp_name"], $target_file)) {

            $new_html = $new_html."<div>The file ". basename($target_file). " has been uploaded.</div>";

            

            $Current_Session->Set_File_Location($target_file);
            //print($Current_Session->Get_File_Location());
            $Current_Session->file_is_good = true;

        } 
        else {
            $error = error_get_last();
            print($error['message']);
            $new_html = $new_html."<div>Sorry, there was an error uploading your file.</div>";
            $Current_Session->file_is_good = false;
        }
    }

    print($new_html);
}


?>