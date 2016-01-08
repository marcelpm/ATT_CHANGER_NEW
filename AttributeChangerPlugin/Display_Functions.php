<?php




class Button_1 {

    
    public function __construct(&$dom, $text, $name, $onClick)
    {
        $this->DOMElement = $dom->createElement('button', $text);
        $this->DOMElement->setAttribute('name', $name);
        $this->DOMElement->setAttribute('onClick', $onClick);
        
    }
    public $DOMElement;

    public function GetDOM() {
        return $this->DOMElement;
    }
}

class Input {


    public function __construct(&$dom, $text, $type, $name, $value)
    {
        $this->DOMElement = $dom->createElement('input', $text);
        $this->DOMElement->setAttribute('type', $type);
        $this->DOMElement->setAttribute('name', $name);
        $this->DOMElement->setAttribute('value', $value);
    }

    public $DOMElement;

    public function GetDOM() {
        return $this->DOMElement;
    }
}

class Table_1 {


    public $firstRow;

    public $DOMElement;

    public function GetDOM() {
        return $this->DOMElement;
    }

    public function __construct(&$dom)
    {
        $this->DOMElement = $dom->createElement('table');
        $this->firstRow = $dom->createElement('tr');
        $this->DOMElement->appendChild($this->firstRow);

    }

    public function AddColumn(&$dom, $domElement) {

        if($domElement instanceof DOMElement){

            $columnHead = $dom->createElement('td');
            $this->firstRow->appendChild($columnHead);
            $columnHead->appendChild($domElement);
        }
    }

}


//NEED VALUE IN OTHER THAN SET DEFAULT ? ACTUALLY NO 


	//print_r($GLOBALS['plugins']['AttributeChangerPlugin']);

	function Get_Attribute_File_Column_Match() {
		
		$attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;
    		
        //multiple per column as ,"val, val, val",
        if($attribute_changer->Current_Session == null) {
            return "ERROR NO CURRENT SESSION";
        }

        $Session = $attribute_changer->Current_Session;

        if($Session->file_location == null || !file_exists($Session->file_location)) {
            return "ERROR WITH SESSION FILE LOCATION";
        }

        $column_match_return_string = '';
        $fp = fopen($Session->file_location, 'r');
        if(!$fp) {
            return "ERRORORORO FILE POINTER BAD";
        }

        $columns = array();
        $current_word = '';
        $current_char ='';

        $first_block = '';


        $first_line = rtrim(fgets($fp));
        
        if(feof($fp)) {
            fclose($fp);
            return 'error no values set';
        }

        $columns = explode(',', $first_line);
        $current_row = 0;
        $first_few_rows = array();
        while($current_row < 10) {
            
            $first_few_rows[$current_row] = rtrim(fgets($fp));
            
            if(substr($first_few_rows[$current_row], -1) === '\n') {
                substr_replace($first_few_rows[$current_row],"", -1);
            }
            $first_few_rows[$current_row] = rtrim($first_few_rows[$current_row]);
     
            if(feof($fp)) {
                if(count(explode(',', $first_few_rows[--$current_row])) <  count($columns)) {
                    if($current_row == 0) {
                        return "ERROR, THERE EXISTS ONLY ONE CSV LINE, AND IT IS WITHOUT ENOUGH COLUMNS";
                    }
                    unset($first_few_rows[$current_row]);
                }
                break;
            }

            $current_row++;
        }

        $number_of_rows = 10;
        if(count($first_few_rows) < 10) {
            $number_of_rows = count($first_few_rows);
        }

        if(count($Session->attribute_list)==0){
            return "ERROR NO ATTRIBUTES TO CHOOSE FROM";
        }


        $column_match_return_string = '
        <form action="" method="post" id="file_column_select_form">
        <table id="column_match_table><tr>';
        $column_match_return_string = $column_match_return_string.sprintf('<input type="hidden" name="file_location" value="%s">', $Session->file_location);
        //create radios for each

        foreach ($columns as $column_key => $column_value) {
            $cell_string = sprintf('<td> Set : %s  to : <br>', $column_value);

            foreach ($Session->attribute_list as $attribute_id => $attribute_info) {
                $cell_string = $cell_string.sprintf('<input type="radio" name="attribute_to_match[%d]" value="%d" class="%s">%s<br>', $attribute_id, $column_key, $column_value, $Session->attribute_list[$attribute_id]['name']);
            }
            $cell_string = $cell_string.sprintf('<input type="radio" name="attribute_to_match[%s]" value="%d" class="%s">%s<br>', 'email', $column_key, $column_value, "email");

            $cell_string = $cell_string.sprintf('<input type="button" id="clear_%s" value="Clear Column" onClick="Clear_Column_Select(\'%s\')"', $column_value, $column_value);
            $column_match_return_string = $column_match_return_string.$cell_string.'</td>';
        }
        $column_match_return_string = $column_match_return_string.'</tr>';

        //print('<br>'.$column_match_return_string.'<br>');

        $value_row = '';

        for($i=1; $i < $number_of_rows; $i++) {
            $value_row = '<tr>';
            foreach ( (explode(',', $first_few_rows[$i])) as $key => $table_value) {
                $value_row=$value_row.sprintf('<td>%s</td>', $table_value);
            }
            $column_match_return_string = $column_match_return_string.$value_row.'</tr>';
        }

        $column_match_return_string = $column_match_return_string.'</table><input type="submit" name="File_Column_Match_Submit" value="submit"> </form>';

        return $column_match_return_string;
    }
















    function Get_Column_Opertation_Block(&$dom, $attr_id){
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;


        //DOM CLASSES ARE:  Safe_Value, Current_Value, Checkbox_Value, Other_Value, Email_Block, Checked
        $command_0 = array('Check','Uncheck');

        $command_1_not_checkbox = array('All','Safe_Value','Current_Value', 'Other_Value');
        $command_1_checkbox = array('All','Checkbox_Value','Current_Value', 'Other_Value');

        $command_2 = array('', 'Unless', 'If');

        $command_3_not_checkbox = array('', 'Any', 'Safe_Value', 'Current_Value', 'Other_Value');
        $command_3_checkbox = array('', 'Any', 'Checkbox_Value', 'Current_Value', 'Other_Value');

        $command_4 = array('', 'Exists', 'Not_Exists', 'Checked', 'Not_Checked');

        $command_array_not_checkbox = array(

                    'Action' => $command_0, 
                    'Subject' => $command_1_not_checkbox,
                    'Conditional' => $command_2, 
                    'Subject_2' => $command_3_not_checkbox,
                    'Predicate' => $command_4, 
                    );
        $command_array_checkbox = array(

                    'Action' => $command_0, 
                    'Subject' => $command_1_checkbox,
                    'Conditional' => $command_2, 
                    'Subject_2' => $command_3_checkbox,
                    'Predicate' => $command_4, 
                    );
        
        $case_array = $AttributeChangerData['case_array'];

        $attr_list = $attribute_changer->Current_Session->attribute_list;

        if(!isset($attr_list[$attr_id])) {
            return null;
        }
        $type = $case_array[$attr_list[$attr_id]['type']];

        $command_array = array();

        if($type == 'case_1' || $type == 'case_2') {
            $command_array = $command_array_not_checkbox;
        }

        else if($type == 'case_3') {
            $command_array = $command_array_checkbox;
        }

        else{
            return null;
        }

        $table = $dom->createElement('table');

        $table->setAttribute('id', 'command_selector_table_'.$attr_id);

        $row = $dom->createElement('tr');
        
        foreach ($command_array as $argument_type => $current_argument_list) {

            $cell = $dom->createElement('td');
            $select_input = $dom->createElement('select');
            $select_input->setAttribute('name', $argument_type);

            foreach ($current_argument_list as $value) {

                $this_option = $dom->createElement('option', $value);
                $this_option->setAttribute('value', $value);
                $select_input->appendChild($this_option);
            }
        
            $cell->appendChild($select_input);
            $row->appendChild($cell);
        }

        $button = $dom->createElement('button', 'Execute Order 66');
        $button->setAttribute('type', 'button');
        $button->setAttribute('onclick', 'execute_command(this, "'.$attr_id.'")');
        $table->appendChild($button);
        $table->appendChild($row);
        return $table;
    }







//------------------1
function BuilNewEntryDom() {
    print("<br> NEW ENTRY TABLE<br> ");
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;

    $dom = new DOMDocument('1.0', 'utf-8');

    $htmlHeader = $dom->createElement('head');

    $dom->appendChild($htmlHeader);





    $form = $dom->createElement('form');
    $form->setAttribute("name", 'New_Entry_Submit_Form_Block__'.$Session->Current_New_Entry_Block_Number);
    $form->setAttribute("method", 'post');
    $form->setAttribute("action", "");

    $hiddenFormData = new Input($dom, '', 'hidden', "New_Entry_Form_Submitted", 'submitted');
    $form->appendChild($hiddenFormData->GetDOM());

    $htmlHeader->appendChild($form);

    $_table = new Table_1($dom);

    $table = $_table->GetDOM();

    $form->appendChild($table);

    GetNewEntryTableHeader_And_Append_To_Table($dom,  $_table);

    $Current_New_Entry_Block = array_slice($Session->New_Entry_List, $Session->Current_New_Entry_Block_Number*$Session->Current_New_Entries_Display_Amount, $Session->Current_New_Entries_Display_Amount);

    foreach ($Current_New_Entry_Block as $email_key => $new_user_attributes_and_values) {
 
        

        $tableRow = GetNewEntryTableRow($dom, $email_key);

        $table->appendChild($tableRow);
    }

    $buttons = Get_New_Entry_Table_Navigation_Buttons($dom);

    $form->appendChild($buttons);
    return $dom;
}

function GetNewEntryTableHeader_And_Append_To_Table(&$dom,  &$table) {
    
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;


    //this NEW ATTR FILES PATH path may not work as its not in the www folder
    //print('<link rel="stylesheet" type="text/css" href="'.$PLUGIN_FILES_DIR.'cssStyles.css"><div class="Current_Value" class="Current_Value">ARARARARARA</div>"');
    


    $emailColumnHead = $dom->createElement('div', 'Email');

    $emailColumnCheckAll = new Button_1($dom, 'Include ALl Emails', 'New_Entry_Include_All_Emails', 'Check_All_Emails()');
    $emailColumnCheckAll->GetDOM()->setAttribute('type', 'button');
    $emailColumnHead->appendChild($emailColumnCheckAll->GetDOM());

    $emailColumnUncheckAll = new Button_1($dom, 'Uncheck all emails', 'New_Entry_Remove_All_Emails', 'Uncheck_All_Emails()');
    $emailColumnUncheckAll->GetDOM()->setAttribute('type', 'button');
    $emailColumnHead->appendChild($emailColumnUncheckAll->GetDOM());


    $table->AddColumn($dom, $emailColumnHead);

    foreach ($Session->attribute_list as $attribute_id => $attribute_info) {

           
        $attributeColumnHead = $dom->createElement('div',"Attribute: ".$attribute_info['name']);


        //THIS COLUMN OPERATION BLOCK IS THE SELECT OPERATIONS
        $attributeColumnHead->appendChild(Get_Column_Opertation_Block($dom, $attribute_id));

        $includeCheckbox = new Input($dom, 'Include this attribute', 'checkbox','New_Entry_Attribute_Column_Select['.$attribute_id.']', 'checked');
        
        if(in_array($attribute_id, $Session->Modify_Entries_Columns_To_Select)) {
            $includeCheckbox->GetDOM()->setAttribute('checked', 'checked');


        }

        if($Session->New_Entries_Columns_To_Select[$attribute_id]== true) {
            $includeCheckbox->GetDOM()->setAttribute('checked', 'checked');
        }




        $attributeColumnHead->appendChild($includeCheckbox->GetDOM());

        $table->AddColumn($dom, $attributeColumnHead);
    }
}

// //------------------2
// function GetNewEntryTableHeader_And_Append_To_Table(&$dom,  &$table) {
//     $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
//     $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
//     $AttributeChangerData = $attribute_changer->AttributeChangerData;

//     $Session = $attribute_changer->Current_Session;
//     $emailColumnHead = $dom->createElement('div', 'Email');

//     $emailColumnCheckAll = new Button_1($dom, 'Include ALl Emails', 'New_Entry_Include_All_Emails', 'checkAll_NewEntry_Emails()');
//     $emailColumnCheckAll->GetDOM()->setAttribute('type', 'button');
//     $checkAll = $emailColumnCheckAll->GetDOM();

//     $emailColumnHead->appendChild($checkAll);

//     $emailColumnUncheckAll = new Button_1($dom, 'Uncheck all emails', 'New_Entry_Remove_All_Emails', "removeAll_NewEntry_Emails()");
//     $emailColumnUncheckAll->GetDOM()->setAttribute('type', 'button');
//     $uncheckAll = $emailColumnUncheckAll->GetDOM();



//     $emailColumnHead->appendChild($uncheckAll);

        
//     $table->AddColumn($dom, $emailColumnHead);


//     foreach ($Session->attribute_list as $attribute_id => $attribute_info) {

//         $attributeColumnHead = $dom->createElement('div',"Attribute: ".$attribute_info['name']);

//         $includeCheckbox = new Input($dom, 'Include this Attribute', 'checkbox', 'New_Entry_Attribute_Column_Select['.$attribute_id.']', 'checked');

//         if(in_array($attribute_id, $Session->New_Entries_Columns_To_Select)) {
//             $includeCheckbox->GetDOM()->setAttribute('checked', 'checked');
//             $checkbox->GetDOM()->setAttribute('class', $current_class.' Checked_Value'); 
//         }

//         $attributeColumnHead->appendChild($includeCheckbox->GetDOM());

//         if($attribute_info['type'] == 'checkboxgroup') {
            
//             $new_entry_include_all = 'New_Entry_Include_All_Checkboxgroup_'.$attribute_id;
//             $includeAllCheckboxgroup = new Button_1($dom, "Include All Checkboxgroup", $new_entry_include_all, 'checkAll_NewEntry_CheckboxGroup("'.$attribute_id.'")');
//             $removeAllCheckboxgroup = new Button_1($dom, "Remove All Checkboxgroup", $new_entry_include_all, 'removeAll_NewEntry_CheckboxGroup("'.$attribute_id.'")');
            
//             $includeAllCheckboxgroup->GetDOM()->setAttribute('type', 'button');
//             $removeAllCheckboxgroup->GetDOM()->setAttribute('type', 'button');


//             $attributeColumnHead->appendChild($includeAllCheckboxgroup->GetDOM());
//             $attributeColumnHead->appendChild($removeAllCheckboxgroup->GetDOM());
//             $table->AddColumn($dom, $attributeColumnHead);

//         }
//         else{
//             $includeAllSafe = new Button_1($dom, "Include all safe values", "New_Entry_Include_All_Safe_Values_".$attribute_id, 'checkAll_NewEntry_SafeValues("'.$attribute_id.'")');
//             $includeAllSafeOrChecked = new Button_1($dom, "Include all safe values or checked", 'New_Entry_Include_All_Safe_Values_Or_Checked'.$attribute_id, 'checkAll_NewEntry_SafeValues_OrChecked("'.$attribute_id.'")');
//             $removeAllSafe = new Button_1($dom, "Remove all safe values", 'New_Entry_Remove_All_Safe_Values_'.$attribute_id, 'removeAll_NewEntry_SafeValues("'.$attribute_id.'")');
//             $removeAllSafeOrChecked = new Button_1($dom, "Remove all safe values or checked", 'New_Entry_Remove_All_Safe_Values_Or_Checked'.$attribute_id, 'removeAll_NewEntry_SafeValues_OrChecked("'.$attribute_id.'")');
            
//             $includeAllSafe->GetDOM()->setAttribute('type', 'button');
//             $includeAllSafeOrChecked->GetDOM()->setAttribute('type', 'button');
//             $removeAllSafe->GetDOM()->setAttribute('type', 'button');
//             $removeAllSafeOrChecked->GetDOM()->setAttribute('type', 'button');

//             $attributeColumnHead->appendChild($includeAllSafe->GetDOM());
//             $attributeColumnHead->appendChild($includeAllSafeOrChecked->GetDOM());

//             $attributeColumnHead->appendChild($removeAllSafe->GetDOM());
//             $attributeColumnHead->appendChild($removeAllSafeOrChecked->GetDOM());
//             $table->AddColumn($dom, $attributeColumnHead);
//         }
//     }
// }

// //---------------------3

function GetNewEntryTableRow(&$dom, $email_key) {
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;
    $row = $dom->createElement('tr');

    $row->appendChild(GetEmailBlock($dom, $email_key));

    foreach ($Session->attribute_list as $attribute_id => $attribute_info) {
  




        $cell = $dom->createElement('td');

        $cell->appendChild(Create_Attribute_Table_Elements($dom, $attribute_id, $email_key));

        $row->appendChild($cell);
    }
    return $row;
}

// //-------------------3.5

function GetEmailBlock(&$dom, $email_key) {
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;


    $Session = $attribute_changer->Current_Session;
                               
    $include = 'include';

    $emailBlock = $dom->createElement('td', $email_key);


    $email_Hidden = $dom->createElement('input');
    $email_Hidden->setAttribute('type', 'hidden');

    $checkbox = $dom->createElement('input');
    $checkbox->setAttribute('type', 'checkbox');

    $class_head = 'Email_Block';

    $Committed_Entry_List = -1;

    if (isset($Session->New_Entry_List[$email_key])) {
        $class_head .= ' New_Entry';
        $checkbox->setAttribute('name', 'New_Entry_List['.$email_key.']['.$include.']');
        $checkbox->setAttribute('value', 'include');
        $Committed_Entry_List = $Session->Committed_New_Entries[$email_key];
        $email_Hidden->setAttribute('name', 'Hidden_New_Entry_List['.$email_key.']');
        $email_Hidden->setAttribute('value', 'include');
    }

    else if (isset($Session->Modify_Entry_List[$email_key])) {

        $class_head .= ' Modify_Entry';
        $checkbox->setAttribute('name', 'Modify_Entry_List['.$email_key.']['.$include.']');
        $checkbox->setAttribute('value', 'include');

        $Committed_Entry_List = $Session->Committed_Modify_Entries[$email_key];

        $email_Hidden->setAttribute('name', 'Hidden_Modify_Entry_List['.$email_key.']');
        $email_Hidden->setAttribute('value', 'include');
    }

    if($Committed_Entry_List != -1) {
        
        $checkbox->setAttribute('checked', 'Checked');  
        $class_head .= ' Checked';
    }

    $checkbox->setAttribute('class', $class_head); 
    $emailBlock->setAttribute('class', $class_head);

    $emailBlock->setAttribute('onclick', 'email_block_clicked(this)');

    $emailBlock->appendChild($email_Hidden);
    $emailBlock->appendChild($checkbox);
    return $emailBlock;
}



// //---------------------5 

    
function Get_New_Entry_Table_Navigation_Buttons (&$dom) {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
        $AttributeChangerData = $attribute_changer->AttributeChangerData;

        $Session = $attribute_changer->Current_Session;

        $displayAmounts = $AttributeChangerData['displayAmounts'];

        $buttonDiv= $dom->createElement('div');
        $submitAll = new Input($dom, '', 'submit', 'New_Entries_Table_Submit_All', 'New_Entries_Table_Submit_All');
        $buttonDiv->appendChild($submitAll->GetDOM());

        if($Session->Current_New_Entry_Block_Number > 0) {
            $nextPage = new Input($dom, '', 'submit', 'New_Entries_Table_Previous_Page', 'New_Entries_Table_Previous_Page');
            $buttonDiv->appendChild($nextPage->GetDOM());
        }
        if($Session->Current_New_Entry_Block_Number < $Session->New_Entries_Number_Of_Blocks - 1) {
            $previousPage = new Input($dom, '', 'submit', 'New_Entries_Table_Next_Page', 'New_Entries_Table_Next_Page');
            $buttonDiv->appendChild($previousPage->GetDOM());
        }

        $displayNumber = $dom->createElement('select');
        $displayNumber->setAttribute('name', 'New_Entries_New_Display_Amount');

        

        foreach ($displayAmounts as $amount) {

            $option = $dom->createElement('option');
            $option->setAttribute('value', $amount);
            $displayNumber->appendChild($option);
            $option->appendChild($dom->createElement('div', $amount));
        }
        $buttonDiv->appendChild($displayNumber);
  
        $changeDisplay = new Input($dom, '', 'submit', "New_Entry_Change_Display_Amount", "New_Entry_Change_Display_Amount");
      
        $buttonDiv->appendChild($changeDisplay->GetDOM());

        $HTML_current_table_info = $dom->createElement("div", "Current Block: ".($Session->Current_New_Entry_Block_Number+1)." of ".($Session->New_Entries_Number_Of_Blocks).". Displaying ".$Session->Current_New_Entries_Display_Amount." entries per page.");

        $buttonDiv->appendChild($HTML_current_table_info);

        return $buttonDiv;
    }



// //------------------6

function BuildModifyEntryDom() {
    print("<br> MODIFY ENTRY TABLE <br>");

    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;


    $dom = new DOMDocument('1.0', 'utf-8');

    $htmlHeader = $dom->createElement('head');

    $dom->appendChild($htmlHeader);


    $form = $dom->createElement('form');
    $form->setAttribute("name", 'Modify_Entry_Submit_Form_Block__'.$Session->Current_Modify_Entry_Block_Number);
    $form->setAttribute("method", 'post');
    $form->setAttribute("action", "");

    $htmlHeader->appendChild($form);

    $hiddenFormData = new Input($dom, '', 'hidden', "Modify_Entry_Form_Submitted", 'submitted');
    $form->appendChild($hiddenFormData->GetDOM());

    $table = new Table_1($dom);

    $form->appendChild($table->GetDOM());

    GetModifyEntryTableHeader_And_Append_To_Table($dom,  $table);

    $tableDOM = $table->GetDOM();




    $Current_Modify_Entry_Block = array_slice($Session->Modify_Entry_List, $Session->Current_Modify_Entry_Block_Number*$Session->Current_Modify_Entries_Display_Amount, $Session->Current_Modify_Entries_Display_Amount);

    foreach ($Current_Modify_Entry_Block as $email_key => $Modify_user_attributes_and_values) {   


        $tableRow = GetModifyTableRow($dom, $email_key);

        $tableDOM->appendChild($tableRow);
    }

    $buttons = Get_Modify_Table_Navigation_Buttons($dom);
    $form->appendChild($buttons);

    return $dom;
}

//--------------------------7

function GetModifyEntryTableHeader_And_Append_To_Table(&$dom,  &$table) {
    
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;


    //this NEW ATTR FILES PATH path may not work as its not in the www folder
    print('<link rel="stylesheet" type="text/css" href="'.$PLUGIN_FILES_DIR.'cssStyles.css"><div class="Current_Value" class="Current_Value">ARARARARARA</div>"');
    


    $emailColumnHead = $dom->createElement('div', 'Email');

    $emailColumnCheckAll = new Button_1($dom, 'Include ALl Emails', 'Modify_Entry_Include_All_Emails', 'Check_All_Emails()');
    $emailColumnCheckAll->GetDOM()->setAttribute('type', 'button');
    $emailColumnHead->appendChild($emailColumnCheckAll->GetDOM());

    $emailColumnUncheckAll = new Button_1($dom, 'Uncheck all emails', 'Modify_Entry_Remove_All_Emails', 'Uncheck_All_Emails()');
    $emailColumnUncheckAll->GetDOM()->setAttribute('type', 'button');
    $emailColumnHead->appendChild($emailColumnUncheckAll->GetDOM());


    $table->AddColumn($dom, $emailColumnHead);

    foreach ($Session->attribute_list as $attribute_id => $attribute_info) {

           
        $attributeColumnHead = $dom->createElement('div',"Attribute: ".$attribute_info['name']);


        //THIS COLUMN OPERATION BLOCK IS THE SELECT OPERATIONS
        $attributeColumnHead->appendChild(Get_Column_Opertation_Block($dom, $attribute_id));

        $includeCheckbox = new Input($dom, 'Include this attribute', 'checkbox','Modify_Entry_Attribute_Column_Select['.$attribute_id.']', 'checked');
        
        if(in_array($attribute_id, $Session->Modify_Entries_Columns_To_Select)) {
            $includeCheckbox->GetDOM()->setAttribute('checked', 'checked');


        }

        if($Session->New_Entries_Columns_To_Select[$attribute_id]== true) {
            $includeCheckbox->GetDOM()->setAttribute('checked', 'checked');
        }




        $attributeColumnHead->appendChild($includeCheckbox->GetDOM());

        $table->AddColumn($dom, $attributeColumnHead);
    }
}


// //-----------------------8

function GetModifyTableRow(&$dom, $email_key) {
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $Session = $attribute_changer->Current_Session;

    $case_array = $AttributeChangerData['case_array'];


    $row = $dom->createElement('tr');
                        
    $row->appendChild(GetEmailBlock($dom, $email_key));

    foreach ($Session->attribute_list as $attribute_id => $attribute_info) {
        $cell = $dom->createElement('td');

        if(isset($Session->Modify_Entry_List[$email_key][$attribute_id])) {

            $cell->appendChild( Create_Attribute_Table_Elements($dom, $attribute_id, $email_key));
        }


        $row->appendChild($cell);

    }
    return $row;
}



function create_selector($type, &$dom, $value, $name){
    if($type != 'checkbox' && $type != 'radio') {
        return -1;
    }
    $selector = $dom->createElement('input');
    $selector->setAttribute('type', $type);
    $selector->setAttribute('name', $name);
    $selector->setAttribute('value', $value);
    return $selector;
}


function Create_Attribute_Table_Elements(&$dom, $attribute_id, $email_key) {
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];


    $Session = $attribute_changer->Current_Session;
   



    $attribute_type = $case_array[$Session->attribute_list[$attribute_id]['type']];


    $attribute_allowed_values = $Session->attribute_list[$attribute_id]['allowed_value_ids'];

    $Current_User_Values = null;


    $Entry_List = array();


    if(isset($Session->New_Entry_List[$email_key])){
        $Entry_Type = 'New_Entry_List';
        $Entry_List = $Session->New_Entry_List[$email_key][$attribute_id];
        $class_head = 'New_Entry';

        $Committed_Attribute_Values = $Session->Committed_New_Entries[$email_key][$attribute_id];
    }

    else if(isset($Session->Modify_Entry_List[$email_key])){

        

        $Entry_Type = 'Modify_Entry_List';



        if(is_array($Session->Current_User_Values[$email_key][$attribute_id])) {
            
            foreach ($Session->Current_User_Values[$email_key][$attribute_id] as $value) {
                array_push($Entry_List, $value);
            } 
        }
        else if(isset($Session->Current_User_Values[$email_key][$attribute_id])) {
            array_push($Entry_List, $Session->Current_User_Values[$email_key][$attribute_id]);
        }




        if(is_array($Session->Modify_Entry_List[$email_key][$attribute_id])) {
            foreach ($Session->Modify_Entry_List[$email_key][$attribute_id] as $value) {
                array_push($Entry_List, $value);
            }
        }
        else if(isset($Session->Modify_Entry_List[$email_key][$attribute_id])) {
            array_push($Entry_List, $Session->Modify_Entry_List[$email_key][$attribute_id]);
        }



        $class_head = 'Modify_Entry';
        $Current_User_Values = $Session->Current_User_Values[$email_key][$attribute_id];

        $Committed_Attribute_Values = $Session->Committed_Modify_Entries[$email_key][$attribute_id];

    }


    $list = $dom->createElement('ul');
    $list->setAttribute('style', "list-style-type:none");

    //VALUE IS THE SET VALUE ex: 'adam lam', could be an id
    foreach ($Entry_List as $key => $value) {

        $class = '';
        $class .= $class_head;

        if($Current_User_Values != null) {
            if(in_array($value, $Current_User_Values)) {
                $class .= ' Current_Value';
            }
        }
        
        if($attribute_type == 'case_1') {

            if($key == 0) {

                $class .= ' Safe_Value';
            }

            $name =$Entry_Type.'['.$email_key.']['.$attribute_id.']';
            $selector = create_selector('radio', $dom, $value, $name);
            $list_element = $dom->createElement('li', $value);
        }

        else if($attribute_type == 'case_2') {

            if($key == 0) {
                $class .= ' Safe_Value';
            }

            $name =$Entry_Type.'['.$email_key.']['.$attribute_id.']';
            $selector = create_selector('radio', $dom, $value, $name);

            $list_element = $dom->createElement('li', $attribute_allowed_values[$value]);
        }

        else if($attribute_type == 'case_3') {

            
            
            $class .= ' Checkbox_Value';
            $name =$Entry_Type.'['.$email_key.']['.$attribute_id.']['.$value.']';

            $selector = create_selector('checkbox', $dom, $value, $name);
            $list_element = $dom->createElement('li', htmlspecialchars($attribute_allowed_values[$value]));
        
            // if($email_key == 'hfraese@mcgurkllp.com' && $attribute_id == 9 && $value==13) {
            //     print('<br>');
            //     print_r($list_element);
            //     print('<br>');
            // }

        }

        else{
            //blank due to no set case_3 error
            return $dom->createElement('div');
        }

        if(is_array($Committed_Attribute_Values)){
            if (in_array($value, $Committed_Attribute_Values)) {

                $class .= ' Checked';
                $selector->setAttribute('checked', 'Checked');
                $selector->setAttribute('class', $class);
            }
        }

        else if ($value == $Committed_Attribute_Values) {

            $class .= ' Checked';
            $selector->setAttribute('checked', 'Checked');
            $selector->setAttribute('class', $class);
        }

        $class .= ' attribute_'.$attribute_id;

        $selector->setAttribute('class', $class);

        //$selector->setAttribute('onclick', 'selector_clicked(this)');


        $list_element->appendChild($selector);
        $list_element->setAttribute('class', $class);
        $list_element->setAttribute('onclick', 'list_element_clicked(this)');

        $list->appendChild($list_element);
    }

    return $list;
}

// //---------------------------------------8.5

function Get_Current_Attribute_Block(&$dom, $email_key, $attribute_id){
    

    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];


    $Session = $attribute_changer->Current_Session;

    $HTML_block = $dom->createElement('div');


    if(!isset($Session->Current_User_Values[$email_key][$attribute_id])) {

        return $HTML_block;
    }
    
    else {

        $domList = $dom->createElement('ui');

        switch ($case_array[$Session->attribute_list[$attribute_id]['type']]) {
            case 'case_3':
                
                foreach ($Session->Current_User_Values[$email_key][$attribute_id] as $key => $current_single_value) {

                    $class .= 'Modify_Entry';
                    $class .= ' Current_Value';
                    $class .= ' attribute_'.$attribute_id;

                    $text = $Session->attribute_list[$attribute_id]['allowed_value_ids'][$current_single_value];
                    $name = 'Modify_Entry_List['.$email_key.']['.$attribute_id.']['.$current_single_value.']';
                    $value = $current_single_value;

                    $checkbox = new Input($dom, $Session->$text, 'checkbox', $name, $value);


                    if(isset($Session->Committed_Modify_Entries[$email_key][$attribute_id])) {
                        if(in_array($current_single_value, $Session->Committed_Modify_Entries[$email_key][$attribute_id])) {
                            $checkbox->GetDOM()->setAttribute('checked', 'checked');
                            $class .= ' Selected';
                        }
                    }

                    $checkbox->GetDOM()->setAttribute('class',$class);


                    $listOption = $dom->createElement('li');

                    $listOption->setAttribute('class', $class);

                    $listOption->appendChild($dom->createElement('div', $text));
                    $listOption->appendChild($checkbox->GetDOM());
                    $domList->appendChild($listOption);
                }
                
                break;
            
            case 'case_2' :
                $class .= 'Modify_Entry';
                $class .= ' Current_Value';
                $class .= ' attribute_'.$attribute_id;


                $text = $Session->attribute_list[$attribute_id]['allowed_value_ids'][$Session->Current_User_Values[$email_key][$attribute_id]];
                $name = 'Modify_Entry_List['.$email_key.']['.$attribute_id.']';
                $value = $Session->Current_User_Values[$email_key][$attribute_id];

                $radio = new Input($dom, $Session->$text, 'radio', $value);
                $radio->GetDOM()->setAttribute('class', $class);

                if(isset($Session->Committed_Modify_Entries[$email_key]) && isset($Session->Committed_Modify_Entries[$email_key][$attribute_id])) {
                    if($Session->Committed_Modify_Entries[$email_key][$attribute_id] === $Session->Current_User_Values[$email_key][$attribute_id]) {
                            $radio->GetDOM()->setAttribute('checked', 'checked');  
                            $class .= ' Selected';                 
                    }
                }

                $radio->GetDOM()->setAttribute('class',$class);

                $listOption = $dom->createElement('li');

                $listOption->setAttribute('class', $class);

                $listOption->appendChild($dom->createElement('div', $text));
                $listOption->appendChild($radio->GetDOM());
                $domList->appendChild($listOption);
                break;

            case 'case_1':
                $class .= 'Modify_Entry';
                $class .= ' Current_Value';
                $class .= ' attribute_'.$attribute_id;


                $text = $Session->attribute_list[$attribute_id]['allowed_value_ids'][$Session->Current_User_Values[$email_key][$attribute_id]];
                $name = 'Modify_Entry_List['.$email_key.']['.$attribute_id.']';
                $value = $Session->Current_User_Values[$email_key][$attribute_id];

                $radio = new Input($dom, $Session->$text, 'radio', $value);
                $radio->GetDOM()->setAttribute('class', $class);

                if(isset($Session->Committed_Modify_Entries[$email_key]) && isset($Session->Committed_Modify_Entries[$email_key][$attribute_id])) {
                    if($Session->Committed_Modify_Entries[$email_key][$attribute_id] === $Session->Current_User_Values[$email_key][$attribute_id]) {
                            $radio->GetDOM()->setAttribute('checked', 'checked');  
                            $class .= ' Selected';                 
                    }
                }
                $checkbox->GetDOM()->setAttribute('class',$class);

                $listOption = $dom->createElement('li');

                $listOption->setAttribute('class', $class);

                $listOption->appendChild($dom->createElement('div', $text));
                $listOption->appendChild($radio->GetDOM());
                $domList->appendChild($listOption);
                break;

            default:
                break;
        }
        $HTML_block->appendChild($domList);
        return $HTML_block;
    }
}


// //--------------------------9

function Get_Modify_Attribute_Value_Display_Checkboxgroup_New_Vals(&$dom, $email_key, $attribute_id) {

    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];


    $Session = $attribute_changer->Current_Session;


    $HTML_value_block = $dom->createElement('div');

    if(!isset($Session->Modify_Entry_List[$email_key][$attribute_id]) || count($Session->Modify_Entry_List[$email_key][$attribute_id]) == 0) {
        return $HTML_value_block;
    }


    $domList = $dom->createElement('ui');


    foreach ($Session->Modify_Entry_List[$email_key][$attribute_id] as $numkey => $checkbox_value_id) {

        $name = "Modify_Entry_List[".$email_key."][".$attribute_id."][".$checkbox_value_id."]";

        $class = 'Modify_Entry';
        $class .= ' Checkbox_Value';
        $class .= ' attribute_'.$attribute_id;

        $value = $checkbox_value_id;
        $text = $Session->attribute_list[$attribute_id]['allowed_value_ids'][$checkbox_value_id];

        $listOption = $dom->createElement('li');
        $listOption->appendChild($dom->createElement('div', $text));


        $checkbox = new Input($dom, $text, 'checkbox', $name, $value);

        if(isset($Session->Committed_Modify_Entries[$email_key]) && isset($Session->Committed_Modify_Entries[$email_key][$attribute_id])) {

            if(in_array($checkbox_value_id, $Session->Committed_Modify_Entries[$email_key][$attribute_id]) ) {
                $checkbox->GetDOM()->setAttribute('checked', 'checked');

               $class .= ' Selected';
            }
        }

        $checkbox->GetDOM()->setAttribute('class', $class);
        $listOption->setAttribute('class', $class);

        $listOption->appendChild($checkbox->GetDOM());
        $domList->appendChild($listOption);
    }
    $HTML_value_block->appendChild($domList);
    return $HTML_value_block;
}


// //----------------------------------------11

function Get_Modify_Table_Navigation_Buttons (&$dom) {
    $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
    $PLUGIN_FILES_DIR = $attribute_changer->AttributeChangerData['PLUGIN_FILES_DIR'];
    $AttributeChangerData = $attribute_changer->AttributeChangerData;

    $case_array = $AttributeChangerData['case_array'];


    $Session = $attribute_changer->Current_Session;


    $buttonDiv= $dom->createElement('div');
    $submitAll = new Input($dom, '', 'submit', 'Modify_Entries_Table_Submit_All', 'Modify_Entries_Table_Submit_All');
    $buttonDiv->appendChild($submitAll->GetDOM());

    if($Session->Current_New_Entry_Block_Number > 0) {
        $nextPage = new Input($dom, '', 'submit', 'Modify_Entries_Table_Previous_Page', 'Modify_Entries_Table_Previous_Page');
        $buttonDiv->appendChild($nextPage->GetDOM());
    }
    if($Session->Current_New_Entry_Block_Number < $Session->New_Entries_Number_Of_Blocks - 1) {
        $previousPage = new Input($dom, '', 'submit', 'Modify_Entries_Table_Next_Page', 'Modify_Entries_Table_Next_Page');
        $buttonDiv->appendChild($previousPage->GetDOM());
    }

    $displayNumber = $dom->createElement('select');
    $displayNumber->setAttribute('name', 'Modify_Entries_New_Display_Amount');

        
    $displayAmounts = $AttributeChangerData['displayAmounts'];

    foreach ($displayAmounts as $amount) {

        $option = $dom->createElement('option');
        $option->setAttribute('value', $amount);
        $displayNumber->appendChild($option);
        $option->appendChild($dom->createElement('div', $amount));
    }
    $buttonDiv->appendChild($displayNumber);

    $changeDisplay = new Input($dom, '', 'submit', "Modify_Entry_Change_Display_Amount", "Modify_Entry_Change_Display_Amount");
  
    $buttonDiv->appendChild($changeDisplay->GetDOM());

    $HTML_current_table_info = $dom->createElement("div", "Current Block: ".($Session->Current_Modify_Entry_Block_Number+1)." of ".$Session->Modify_Entries_Number_Of_Blocks.". Displaying ".$Session->Current_Modify_Entries_Display_Amount." entries per page.");

    $buttonDiv->appendChild($HTML_current_table_info);

    return $buttonDiv;
}


?>