<?php


/*


    ALSO WANT TO ADD ALLOWED ATTRIBUTE VALUES DYNAMICAL << on this now

    NEED TO MAKE SURE DOES NOT GIVE ERROR IF THE EMAIL IS THE ONLY ATTRIBUTE IN THE DB



    ALSO HTMLSPECIALCHARS EVURYTHING

*/

class AttributeChangerPlugin extends phplistPlugin {

	
	// File location, selected attribute column matches, all entry data, index by id
	// $sessions;
	// $session_increment;

	public $name = "AttributeChangerPlugin";

	public $pageTitles = array( // Entries in the plugin menu of the dashboard
		'front_page' => 'Begin Data Collection',
	);
	  
	public $topMenuLinks = array( // Entries in the top menu at the top of each page
		'front_page' => array('category' => 'subscribers'),
	);

	public $description = 'Begin Data Collection';

	public $Current_Session = null;


    public $AttributeChangerData = null;


    //THIS SHOULD BE UPGRADED TO A GENERIC TYPE AS PER THE TABLE SETTINGS


    function __construct()
    {

        parent::__construct();

       	$this->coderoot = dirname(__FILE__).'/AttributeChangerPlugin/';
        require_once(dirname(__FILE__).'/AttributeChangerPlugin/Single_Session.php');   

        $this->attribute_changer_tablename = $GLOBALS['table_prefix'].'plugins_attribute_changer_plugin';

        $this->AttributeChangerData = array(
            'tables' => array(
                'user' => $GLOBALS['tables']['user'],
                'attribute' => $GLOBALS['tables']['attribute'],
                'user_attribute' => $GLOBALS['tables']['user_attribute'],

                ),

            'table_prefix' => $GLOBALS['table_prefix'],
            'atribute_value_table_prefix' => $GLOBALS['table_prefix']."listattr_",


            'case_array' => array(
                'textarea' => 'case_1',
                'textline' => 'case_1',
                'hidden' => 'case_1',
                'date' => 'case_1',
                'checkbox' => 'case_1',

                'radio' => 'case_2',
                'select' => 'case_2',

                'checkboxgroup' => 'case_3',
                ),
            'attribute_changer_tablename' => $GLOBALS['table_prefix'].'plugins_attribute_changer_plugin',

            'attribute_changer_table_structure' => array(
                "id" => array("integer not null primary key auto_increment","ID"),
                "adminid" => array("integer not null","adminid"),
                "value" => array('longtext',''),
                ),

            'displayAmounts'=> array(
                10=>10,
                100=>100,
                1000=>1000,
                10000=>10000,
                'all'=>'all',
                ),

            'PLUGIN_CLASS_DIR' => dirname(__FILE__),

            'www_files_dir' => '/lists/admin/plugins/AttributeChangerPlugin/',

           'PLUGIN_FILES_DIR' => dirname(__FILE__).'/AttributeChangerPlugin/',
           'temp_uploads_dir' => dirname(__FILE__).'/AttributeChangerPlugin/temp_table_uploads',

        );

        $GLOBALS['AttributeChangerPlugin'] = $this;
    }



	function adminMenu() {
    	return $this->pageTitles;
	}
	function print_something() {
		print('something');
	}

//SAME AS admintoken struct expect missing entered and expires
	// public $attribute_changer_table_structure = array(
 //        "id" => array("integer not null primary key auto_increment","ID"),
 //        "adminid" => array("integer not null","adminid"),
 //        "value" => array('longtext',''),
 //      );
    

    function Serialize_And_Store() {
        print("arara222ra<br>");

        if(!isset($this->Current_Session) || $this->Current_Session == null) {
            print("ERR NO SESION");
            die();
        }
        
        $session_s = base64_encode(serialize($this->Current_Session));

        $truncate_query = sprintf("truncate table %s", $this->AttributeChangerData['attribute_changer_tablename']);

        Sql_Query($truncate_query);    
        $serialize_insert_query = sprintf("insert into %s (value) values ('%s')", $this->AttributeChangerData['attribute_changer_tablename'], $session_s);
        Sql_Query($serialize_insert_query);

    }

    function Retreive_And_Unserialize() {
        $retrieve_serialized_query = sprintf("select value from %s", $this->AttributeChangerData['attribute_changer_tablename']);
        $retrieve_s_return = Sql_Query($retrieve_serialized_query);

        if(!$retrieve_s_return) {
            print("ERROR NO STORED SESSION");
            die();
        }

        $returned_result = Sql_Fetch_Assoc($retrieve_s_return);

        if(!isset($returned_result['value'])) {
            print("ERROR Improperly stored value data");
            die();
        }

        //print_r($returned_result);
        $serialized_session = $returned_result['value'];
        //print($serialized_session);

        $this->Current_Session = unserialize(base64_decode($serialized_session));
    }


    function Test_Create_Session_Table() {
        $attribute_changer_tablename = $this->AttributeChangerData['attribute_changer_tablename'];

         if(!Sql_Check_For_Table($attribute_changer_tablename)) {
            Sql_create_Table ($attribute_changer_tablename, $this->AttributeChangerData['attribute_changer_table_structure']);
         }
    }


    function New_Session($session_create_table_query ) {
    	

        $this->Test_Create_Session_Table();

        if($this->Current_Session != null) {
			
            if(!empty($this->Current_Session->file_location)) {
                if(is_file($this->Current_Session->file_location)) {
                    unlink($this->Current_Session->file_location);
                }
            }
        }

        $this->Current_Session = new Single_Session;
        return $this->Current_Session;
    }

    function Close_Session() {
        if($this->Current_Session != null) {
            if(isset($this->Current_Session->file_location)) {
                if(is_file($this->Current_Session->file_location)) {
                    unlink($this->Current_Session->file_location);
                }
            }
            $this->Current_Session = null;
        }
    }

	function Test_Create_Temp_Dir() {
		
        $temp_dir = $this->AttributeChangerData['temp_uploads_dir'];

        $attribute_changer_dir = $this->AttributeChangerData['PLUGIN_FILES_DIR'];

        if(!file_exists($attribute_changer_dir)) {
            return false;
        }
        else if(!is_dir($attribute_changer_dir)) {
            return false;
        }
        else{
            if(!file_exists($temp_dir)) {
                if(!mkdir($temp_dir)){
                	$error = error_get_last();
    				print($error['message']);
                }
                return true;
            }
            else{
                if(is_dir($temp_dir)) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
    }

    //now must use new format for entries --- >   pass with [id]=>[value]
    function Test_Entry($entry) {

        if($this->Current_Session == null) {
            return "ERROR NO CURRENT SESSION";
        }

        if(!array_key_exists("email", $entry) || !filter_var($entry['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $email = $entry['email'];
        unset($entry['email']);
        $changing_attributes = array();

        foreach ($entry as $attribute_id => $value_array) {
            if( ($return_values = $this->Test_Attribute_Values($attribute_id, $value_array)) != false ) {
            	//print_r($return_values);
                $changing_attributes[$attribute_id] = $return_values;
            }
        }

        $entry_query = sprintf('select id from %s where email = "%s"', $this->AttributeChangerData['tables']['user'], $email);
        $user_sql_result = Sql_Fetch_Row_Query($entry_query);

        if(!$user_sql_result[0]) {
            //print_r($changing_attributes);
            $this->Add_New_Entry($email, $changing_attributes);
        }
        else{
            $this->Add_Modify_Entry($email, $changing_attributes);
        }

    }

    // //automatically checks for compliance with values and return array with ids where needed
    function Test_Attribute_Values($attribute_id, $value_array) {
        
        $case_array = $this->AttributeChangerData['case_array'];

        if($this->Current_Session == null) {
            return false;
        }

        $Session = $this->Current_Session;
        if(!isset($Session->attribute_list[$attribute_id])) {
        	//print("<br>att id<br>".$attribute_id.'<br>');
        	//print_r($Session->attribute_list);
            return false;
        }

        else{
        	$att = $Session->attribute_list[$attribute_id];

            if(!is_array($value_array)) {

        		if($case_array[$att['type']] === 'case_2' || $case_array[$att['type']] === 'case_3' ) {
        			return false;
        		}
        		else{
        			return array($value_array);
        		}
        	}

            else {

                $return_values = array();


                if($case_array[$att['type']] == 'case_2' || $case_array[$att['type']] == 'case_3' ) {

                    foreach ($value_array as $value) {
                        
                        if(($value_id = array_search($value, $att['allowed_value_ids']))) {
                            if(!in_array($value_id, $return_values)) {
                                //print('<br>value id : '.$value_id);

                                array_push($return_values, $value_id);
                            }
                        }
                    } 
                }
                else {
                    foreach ($value_array as $value) {
                        if(!in_array($value, $return_values)) {
                            array_push($return_values, $value);
                        }
                    }
                    
                }
                //print_r($return_values);
                return $return_values;
            }
            return false;
        }
    }

/////////////////////////////
//HERE
    function Add_Modify_Entry($email_key, $attribute_values) {


        //print_r($attribute_values);
        if($this->Current_Session == null) {
            return "ERROR NO CURRENT SESSION";
        }

        $Session = $this->Current_Session;

        $case_array = $this->AttributeChangerData['case_array'];

        if($attribute_values == null || !is_array($attribute_values) || count($attribute_values) == 0 ) {
            //do nothing, already have a user but theres nothing to updateprint


        }
        else{

            if(!isset($Session->Current_User_Values[$email_key])) {

                if($this->Get_Current_User_Values($email_key) == false) {
                    
                    return false;
                }
            }
            
            // print_r($this->Current_Session->Current_User_Values);
            // print("<br>");
            $new_entries = array();
            $is_new_value = false;

            foreach ($attribute_values as $attribute_id => $attribute_value_array) {

                $new_entries[$attribute_id] = array();


                foreach ($attribute_value_array as $value) {

                    if($case_array[$Session->attribute_list[$attribute_id]['type']] === 'case_1' || $case_array[$Session->attribute_list[$attribute_id]['type']] === 'case_2') {


                        if($value != $Session->Current_User_Values[$email_key][$attribute_id]) {
                            array_push($new_entries[$attribute_id], $value);
                            $is_new_value = true;
                        }
                    }

                    else if(!in_array($value, $Session->Current_User_Values[$email_key][$attribute_id])) {
                        array_push($new_entries[$attribute_id], $value);

                        $is_new_value = true;
                    }
           
                    
                }
            }

            if($is_new_value == true) {
                if(!isset($Session->Modify_Entry_List[$email_key])) {
                    $Session->Modify_Entry_List[$email_key] = array();
                }
                foreach ($new_entries as $attribute_id => $value_array) {
                    if(!isset($Session->Modify_Entry_List[$email_key][$attribute_id])) {
                        $Session->Modify_Entry_List[$email_key][$attribute_id] = array();
                    }
                    foreach ($value_array as $value) {
                        if(!in_array($value, $Session->Modify_Entry_List[$email_key][$attribute_id])) {
                            array_push($Session->Modify_Entry_List[$email_key][$attribute_id], $value);
                        }
                    }
                }
            }  
        }

    }


    function Get_Current_User_Values($email_key) {
        $Session = $this->Current_Session;
        $case_array = $this->AttributeChangerData['case_array'];

        if($this->Current_Session == null) {
            return "ERROR NO CURRENT SESSION";
        }



        $current_user_query = sprintf('select id from %s where email = "%s"', $this->AttributeChangerData['tables']['user'], $email_key);
        $current_user_sql_result = Sql_Fetch_Row_Query($current_user_query);

        if(!$current_user_sql_result[0]) {
                     
            return false;
        }
 
        if(!isset($Session->Current_User_Values[$email_key])) {


            $Session->Current_User_Values[$email_key] = array();

            $Session->Current_User_Values[$email_key]['id'] = $current_user_sql_result[0];
                            
            foreach ($Session->attribute_list as $attribute_id => $attribute_data) {

                $Current_User_Value_Query = sprintf("select value from %s where attributeid = %d and userid = %s", $this->AttributeChangerData['tables']['user_attribute'], $attribute_id, $current_user_sql_result[0]);
                $current_attribute_return = Sql_Fetch_Row_Query($Current_User_Value_Query);

                

                if(!$current_attribute_return[0]) {

                    continue;
                }

                else{

                    $Session->Current_User_Values[$email_key][$attribute_id] = array();

                    
                    if($case_array[$attribute_data['type']] == 'case_3') {

                        $exploded_current_values_ids = explode(',', $current_attribute_return[0]);

                        $Session->Current_User_Values[$email_key][$attribute_id] = $exploded_current_values_ids;

                    }

                    else {

                        $Session->Current_User_Values[$email_key][$attribute_id] = $current_attribute_return[0];
                        
                    }
                }
            }
        }
        return true;
    }


    //attribute values array([att_id] => values), values have been test all are acceptable
    function Add_New_Entry($email_key, $attribute_values) {


        if($this->Current_Session == null) {
            return "ERROR NO CURRENT SESSION";
        }

        $Session = $this->Current_Session;
        if($attribute_values == null || !is_array($attribute_values) || count($attribute_values) == 0 ) {
            //just set email
            
            if(!isset($Session->New_Entry_List[$email_key])) {
                $Session->New_Entry_List[$email_key] = array();
            }
            //else no need to do anything
            return true;
        }

        else{

            foreach ($attribute_values as $attribute_id => $single_attribute_values) {
                if(!is_array($single_attribute_values)) {
                    //shouldnt happen
                }

                else{
                    if(!isset($Session->New_Entry_List[$email_key][$attribute_id])) {
                        $Session->New_Entry_List[$email_key][$attribute_id] = array();
                    }

                    foreach ($single_attribute_values as $numberic_key => $single_value) {

                        if(!in_array($single_value, $Session->New_Entry_List[$email_key][$attribute_id])) {
                            array_push($Session->New_Entry_List[$email_key][$attribute_id], $single_value);
                        }
                    }
                }
            }
        }

    }
    
}

	


?>
