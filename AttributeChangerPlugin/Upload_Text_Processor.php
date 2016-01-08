<?php

//add the text input to the project, same as file processor except its a string in memory already




//if (!defined('PHPLISTINIT')) die(); ## avoid pages being loaded directly


//.///////////////////////////////////////////////////
////////////////////////////////////still need to make the include this attribute sticky




$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
$PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
$AttributeChangerData = $attribute_changer->AttributeChangerData;

$WWW_FILES_DIR = $attribute_changer->AttributeChangerData['www_files_dir'];

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
';

$attribute_changer->Test_Create_Temp_Dir();


$FILE_LOCATION;

// if(isset($_FILES['attribute_changer_file_to_upload'])) {
//     print('eff');
//     print('<html><head><script src="'.$javascript_src.'"></script></head><body>'.$page_print.'</body></html>');
// }


if(isset($_POST['attribute_changer_upload_text']) && $_POST['attribute_changer_upload_text'] == 'attribute_changer_upload_text') {

	print("<br>in hererererer");
    if(!$attribute_changer->Test_Create_Temp_Dir()) {
        
        print("<html><body>Error with temp directory</body></html>");
        return;
    }

//HERE HAVE A CHECK FOR GOOD SETUP
    $Current_Session = $attribute_changer->New_Session();
    //print_r($Current_Session);
    

    $text_string = 'email,name, jobTitle, calledToBar,companyName,streetAddress,city,province,postalCode,AreasOfPractice
"djarcaig@milburnlaw.ca","Devin M. Jarcaig","Clerk","2012","Milburn & Associates","20 Toronto St. Suite 860","Toronto","Ontario","M5C 2B8",""
"myermus@yermuslaw.com","Michael A. Yermus","Lawyer;Paralegal","2004","Yermus & Associates","Yonge-Norton Centre5255 Yonge St. Suite 1300","Toronto","Ontario","M2N 6P4",""
"fatd@turmeldoyon.com","Félix-Antoine Doyon","Clerk;man;Paralegal","2012","Doyon Avocats","400 Boul. Jean-Lesage Suite 115","Québec","Québec","G1K 8W1",""
"info@hallpc.ca","Sam Hall","","2007","Hall Legal Counsel","661 University Ave. Suite 800","Toronto","Ontario","M5G 1M1",""
"equinn@quinnestatelaw.ca","Eileen Quinn","","2006","Quinn Estate Law","381 Woodworth Dr.","Ancaster","Ontario","L9G 2N2",""
"snicoll@panlegal.ca","Scott L. Nicoll","","1994","Panorama Legal LLP","309 Panorama Place5577 153A St.","Surrey","British Columbia","V3S 5K7",""
"al@wrlaw.ca","Ada Lam","","2008","Winright Law Corporation","550 Broadway W. Suite 621","Vancouver","British Columbia","V5Z 0E9","Civil Litigation; Insurance Law; Alternative Dispute Resolution; Mediation; Motor Vehicle Litigation; Personal Injury Law"
"ldattilo@panlegal.ca","Lauren S. Dattilo","","1997","Panorama Legal LLP","309 Panorama Place5577 153A St.","Surrey","British Columbia","V3S 5K7",""
"saquail@aqwlaw.ca","Susanna Quail","","2015","Allevato Quail & Worth","510 Hastings St. W. Suite 405","Vancouver","British Columbia","V6B 1L8",""
"jquail@aqwlaw.ca","James L. Quail","","1980","Allevato Quail & Worth","510 Hastings St. W. Suite 405","Vancouver","British Columbia","V6B 1L8",""
"nmiller@milleriplaw.com","Nancy A. Miller","","1984","Miller IP Law","84 Neilson Dr.","Toronto","Ontario","M9C 1V7",""
"paul@menlove.ca","Paul Andrews","","2010","Menlove Law Professional Corporation","316 Main St.","Picton","Ontario","K0K 2T0",""
"lworth@qwalaw.ca","Leigha Worth","","2002","Allevato Quail & Worth","510 Hastings St. W. Suite 405","Vancouver","British Columbia","V6B 1L8",""
"paul@wallerlaw.ca","Paul Waller","","2006","Waller Law","1321 Blanshard St. Suite 301","Victoria","British Columbia","V8W 0B6",""
"hugh.hughes@live.ca","Hugh Travis Hughes","","2007","Travis Hughes LLP","560 Chatham St. W.","Windsor","Ontario","N9A 5N2",""
"ls@wrlaw.ca","Laura K. Sutherland","","2008","Winright Law Corporation","550 Broadway W. Suite 621","Vancouver","British Columbia","V5Z 0E9",""
"jborkowski@panlegal.ca","Jeffory M. Borkowski","","1995","Panorama Legal LLP","309 Panorama Place5577 153A St.","Surrey","British Columbia","V3S 5K7",""
"pbuxton@panlegal.ca","Peter F. Buxton","","1981","Panorama Legal LLP","309 Panorama Place5577 153A St.","Surrey","British Columbia","V3S 5K7",""
"mary@edwinflak.com","Marysun P. Cunha","","1995","Cunha & Skervin LLP","10 Duke St. W. Suite 101","Kitchener","Ontario","N2H 3W4",""
"wk@wrlaw.ca","Winston Kam","","2009","Winright Law Corporation","550 Broadway W. Suite 621","Vancouver","British Columbia","V5Z 0E9",""
"lhennick@yermuslaw.com","Lawson Hennick","","2010","Yermus & Associates","Yonge-Norton Centre5255 Yonge St. Suite 1300","Toronto","Ontario","M2N 6P4",""
"dperllin@guardianlegalconsultants.ca","Daniel Perlin","","2012","Guardian Legal Consultants Professional Corporation","250 University Ave. Suite 200","Toronto","Ontario","M5H 3E5",""
"droberts@reorg.com","Douglas E. Roberts","","19862000","Douglas E. Roberts Legal Counsel","209 10836 24 St. S.E.","Calgary","Alberta","T2Z 4C9",""
"hfraese@mcgurkllp.com","Heather Fraese","","2012","McGurk LLP Family Law Lawyers","Suite 1450421 7th Avenue S.W.","Calgary","Alberta","T2P 4K9","Family Law; Adoption; Advocacy-Family Law; Child Custody and Access; Child Protection; Co-Habitation Agreements; Collaborative Family Law; Divorce; Domestic Contracts; Family Business Succession Planning; Family Law Litigation; Matrimonial Law; Nuptual Agreements; Prenuptial Parenting & Separation Agreements; Separation; Surrogacy Agreements; Uncontested Divorce"
"nbarron@yermuslaw.com","Norma Barron","","2014","Yermus & Associates","Yonge-Norton Centre5255 Yonge St. Suite 1300","Toronto","Ontario","M2N 6P4",""
"","Sonia Mann","","2014","My Visa Source","1112 Pender St. W. Suite 401","Vancouver","British Columbia","V6E 2S1",""
"lewens-jones@mcgurkllp.com","Lindsay C. Ewens-Jones","","2000","McGurk LLP Family Law Lawyers","Suite 1450421 7th Avenue S.W.","Calgary","Alberta","T2P 4K9",""
"","Robert Gunnarsson","","2003","Gunnarsson Law","220-145 Chadwick Crt.","North Vancouver","British Columbia","V7M 3K1",""
"lawyer@wvantassel.com","Wayne Van Tassel","","2006","Van Tassel Law","1027 Pandora Ave.","Victoria","British Columbia","V8V 3P6",""
"","François-Xavier T. Doyon","","2012","Doyon Avocats","400 Boul. Jean-Lesage Suite 115","Québec","Québec","G1K 8W1",""
"kc@wrlaw.ca","Ka Won Cheung","","2013","Winright Law Corporation","550 Broadway W. Suite 621","Vancouver","British Columbia","V5Z 0E9",""
"hmcgurk@mcgurkllp.com","Heather A. McGurk","","2000","McGurk LLP Family Law Lawyers","Suite 1450421 7th Avenue S.W.","Calgary","Alberta","T2P 4K9","Family Law; Adoption; Advocacy-Family Law; Child Custody and Access; Child Protection; Co-Habitation Agreements; Collaborative Family Law; Divorce; Domestic Contracts; Family Business Succession Planning; Family Law Litigation; Matrimonial Law; Nuptual Agreements; Prenuptial Parenting & Separation Agreements; Separation; Surrogacy Agreements; Uncontested Divorce"
"sean.vanderlee.lawyer@gmail.com","Sean van der Lee","","2011","van der Lee Professional Corporation","815 1st St. S.W. Suite 408","Calgary","Alberta","T2P 1N3",""
"ronke@astutelegal.ca","Ibironke Olorunojowon","","2012","Astute Legal","1321 Blanshard St. Suite 301","Victoria","British Columbia","V8W 0B6",""
"jmaynes@panlegal.ca","Jason J. Maynes","","2008","Panorama Legal LLP","309 Panorama Place5577 153A St.","Surrey","British Columbia","V3S 5K7",""';
    

    $target_dir = $PLUGIN_FILES_DIR.'temp_table_uploads/';


    $target_file = $target_dir . $_POST['attribute_changer_text_name'];

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
    if (count($text_string) > 1000000000) {
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
    	$the_file = fopen($target_file, "w");

        if ($the_file) {

        	fwrite($the_file, $text_string);
        	fclose($the_file);

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