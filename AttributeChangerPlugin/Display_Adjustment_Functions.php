<?php



    function Initialize_New_Entries_Display() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        if(count($Session->New_Entry_List) == 0) {
            return null;
        }

        $Session->Committed_New_Entries = array();
        ksort($Session->New_Entry_List);

        $Session->Current_New_Entries_Display_Amount = 100;
        $Session->New_Entries_Total_Amount = count($Session->New_Entry_List);
        $Session->New_Entries_Number_Of_Blocks = (int)($Session->New_Entries_Total_Amount/$Session->Current_New_Entries_Display_Amount) + (((int)$Session->New_Entries_Total_Amount % (int) $Session->Current_New_Entries_Display_Amount)? 1:0);
        
       
        $Session->Current_New_Entry_Block_Number = 0;

        $Session->New_Entries_Columns_To_Select = array();

        return true;
         
    }
    
    function New_Entry_Change_Display_Amount($New_Amount) {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        $new_display_amounts = $attribute_changer->AttributeChangerData['displayAmounts'];

        if(!isset($new_display_amounts[$New_Amount]) || $new_display_amounts[$New_Amount] != true) {
            return false;
        }
        if($New_Amount === 'all') {
            $Session->New_Entries_Number_Of_Blocks =1;
            $Session->Current_New_Entries_Display_Amount = $Session->New_Entries_Total_Amount;
            $Session->Current_New_Entry_Block_Number = 0;
            return true;
        }
        $Session->Current_New_Entries_Display_Amount = $New_Amount;
        $Session->New_Entries_Number_Of_Blocks = (int)($Session->New_Entries_Total_Amount/$Session->Current_New_Entries_Display_Amount) + (((int)$Session->New_Entries_Total_Amount % (int)$Session->Current_New_Entries_Display_Amount)? 1:0);
        


        $Session->Current_New_Entry_Block_Number = 0;
        return true;
    }

    function New_Entry_Display_Next_Page() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        if($Session->Current_New_Entry_Block_Number < $Session->New_Entries_Number_Of_Blocks-1) {
            //$Session->Current_New_Entry_Block_Number = $Session->Current_New_Entry_Block_Number + 1;
            return true;
        }
        else{
            //because there are no more blocks
            return false;
        }
    }
    
    function New_Entry_Display_Previous_Page() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        if($Session->Current_New_Entry_Block_Number > 0) {
            $Session->Current_New_Entry_Block_Number = $Session->Current_New_Entry_Block_Number-1;
            return true;
        }
        else{
            //because there are no more blocks
            return false;
        }
    }




    function Initialize_Modify_Entries_Display() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;
            
        if(count($Session->Modify_Entry_List) == 0) {
            //print("ARRARARAR brroooo");
            return null; 
        }
        ksort($Session->Modify_Entry_List);
        $Session->Committed_Modify_Entries = array();

        $Session->Current_Modify_Entries_Display_Amount = 100;
        $Session->Modify_Entries_Total_Amount = count($Session->Modify_Entry_List);


        $display_amount = $Session->Current_Modify_Entries_Display_Amount;
        $total_amount = $Session->Modify_Entries_Total_Amount;

        $Session->Modify_Entries_Number_Of_Blocks = $total_amount/$display_amount + ((int)$total_amount%(int)$display_amount > 0)? 1:0;



        // (int)($Session->Modify_Entries_Total_Amount/$Session->Current_Modify_Entries_Display_Amount) + (((int)$Session->Modify_Entries_Total_Amount % (int)$Session->Current_Modify_Entries_Display_Amount)? 1:0);

        $Session->Current_Modify_Entry_Block_Number = 0;

        $Session->Modify_Entries_Columns_To_Select = array();
        return true;
         
    }   


    function Modify_Entry_Change_Display_Amount($New_Amount) {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;
        $new_display_amounts = $attribute_changer->AttributeChangerData['displayAmounts'];
        
        if(!isset($new_display_amounts[$New_Amount]) || $new_display_amounts[$New_Amount] != true) {
            return false;
        }
        if($New_Amount === 'all') {
            $Session->Modify_Entries_Number_Of_Blocks =1;
            $Session->Current_Modify_Entries_Display_Amount = $Session->Modify_Entries_Total_Amount;
            $Session->Current_Modify_Entry_Block_Number = 0;
            return true;
        }
        $Session->Current_Modify_Entries_Display_Amount = $New_Amount;
        $Session->Modify_Entries_Number_Of_Blocks = (int)($Session->Modify_Entries_Total_Amount/$Session->Current_Modify_Entries_Display_Amount) + (((int)$Session->Modify_Entries_Total_Amount % (int)$Session->Current_Modify_Entries_Display_Amount)? 1:0);

        $Session->Current_Modify_Entry_Block_Number = 0;
        return true;
    }

    function Modify_Entry_Display_Next_Page() {

        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        if($Session->Current_Modify_Entry_Block_Number < $Session->Modify_Entries_Number_Of_Blocks-1) {
            $Session->Current_Modify_Entry_Block_Number = $Session->Current_Modify_Entry_Block_Number + 1;
            //print("<br>Whys THIS<br>".$Session->Current_Modify_Entry_Block_Number);       
            return true;
        }
        else{
           // print("<br>Whys THISaaa<br>".$Session->Modify_Entries_Number_Of_Blocks); 
            // $Session = $GLOBALS['plugins']['AttributeChangerPlugin']->Current_Session;
            // print("aa<br>aa");
            // print('<br>avv'.$Session->Modify_Entries_Number_Of_Blocks.'avv<br>');

            //because there are no more blocks
            return false;
        }
    }
    
    function Modify_Entry_Display_Previous_Page() {
        $attribute_changer = $GLOBALS['AttributeChangerPlugin'];
        $Session = $attribute_changer->Current_Session;

        if($Session->Current_Modify_Entry_Block_Number > 0) {
            $Session->Current_Modify_Entry_Block_Number = $Session->Current_Modify_Entry_Block_Number-1;
            return true;
        }
        else{
            //because there are no more blocks
            return false;
        }
    }








?>