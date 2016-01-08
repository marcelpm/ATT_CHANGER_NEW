    /*


    Require easy to initiate operation using selects
    TYPE 2 + 1

    _Action     _Subject            _Conditional    _Subject (2)    *_Predicate
    |check      |All                |               |               |
    |uncheck    |Safe_Value         |unless         |Any            |Exists
                |Current_Value      |if             |Safe_Value     |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked
                
    TYPE 3 

    _Action     _Subject            *_Conditional   *_Subject (2)   *_Predicate
    |check      |All                |               |               |
    |uncheck    |Checkbox_Value     |unless         |Any            |Exists
                |Current_Value      |if             |Checkbox_Value |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked

    */






//check or uncheck the email_block, change the class, un/check the selector
//Checked elements have class 'Checked', 'Email_Block'
    function email_block_clicked(e) {
        var classes = e.className.split(' ');
       // window.alert(e.className);
        var index = classes.indexOf('Checked');
        var selector = e.getElementsByClassName('Email_Block')[0];

        if(index > -1){
            classes.splice(index, 1);
            selector.checked= false;
        }
        else{
            classes.push('Checked');
            selector.checked = true;
        }

        e.className = classes.join(' ');
        selector.className = e.className;
    }


//check or uncheck the atribute_list block, change the class, un/check the selector
//Checked elements have class 'Checked', 'Email_Block'
    function list_element_clicked(e) {

        var classes = e.className.split(' ');
       // window.alert(e.className);
        var index = classes.indexOf('Checked');
        var selector = e.getElementsByTagName('input')[0];

        if(index > -1){
            classes.splice(index, 1);
            selector.checked= false;
        }
        else{
            classes.push('Checked');
            selector.checked = true;
        }

        e.className = classes.join(' ');
        selector.className = e.className;

       
    }


//Get all 'Email_Block' dom objects, add class 'Checked' and check the selector, 
//can probably simplify using email_block_clicked()
    function checkAll_Emails() {
        var elements = document.getElementsByClassName('Email_Block');
        var i;
        for(i=0; i<elements.length; i++) {
            var classes = elements[i].className.split(' ');
            var index = classes.indexOf('Checked');

            if(index < 0) {
                classes.push('Checked');
                if(elements[i].type == 'checkbox') {
                    elements[i].checked = true;
                }   
            }
            elements[i].className = classes.join(' ');
        }
    }


//Get all 'Email_Block' dom objects, remove class 'Checked' and uncheck the selector, 
//can probably simplify using email_block_clicked()
    function removeAll_Emails() {
        var elements = document.getElementsByClassName('Email_Block');
        var i;
        for(i=0; i<elements.length; i++) {
            var classes = elements[i].className.split(' ');
            var index = classes.indexOf('Checked');

            if(index > -1) {
                classes.splice(index, 1);
                if(elements[i].type == 'checkbox') {
                    elements[i].checked = false;
                }   
            }
            elements[i].className = classes.join(' ');
        }
    }

/*
    Require easy to initiate operation using selects
    TYPE 2 + 1

    _Action     _Subject            _Conditional    _Subject      _Predicate
    |check      |All                |               |               |
    |uncheck    |Safe_Value         |unless         |Any            |Exists
                |Current_Value      |if             |Safe_Value     |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked
                
    TYPE 3 

    _Action     _Subject            *_Conditional   *_Subject       *_Predicate
    |check      |All                |               |               |
    |uncheck    |Checkbox_Value     |unless         |Any            |Exists
                |Current_Value      |if             |Checkbox_Value |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked
*/


/*

    To Do: 

    Allow for updates to commands easily, must be able to create a function, push to a list and be reachable by the processor 
        Also to be guarenteed
        Make some form of array to build command tree possible paths, one way
        

     __q q
    =___/
       \\
        v
        v \
        | v
        | v\
        |/\v
        v  v
        v  v
    
    Action set can be made to use AJAX 

    Make the Processor Occur in Incremental steps, building dynamicaly for different action types
        Right now is only using 1 type : un/check

    
*/

 
//DOM CLASSES ARE:  Safe_Value, Current_Value, Checkbox_Value, Other_Value, Email_Block, Checked
var command_0 = ['Check','Uncheck'];

var command_1_not_checkbox = ['All','Safe_Value','Current_Value', 'Other_Value'];
var command_1_checkbox = ['All','Checkbox_Value','Current_Value', 'Other_Value'];

var command_2 = ['Unless', 'If'];

var command_3_not_checkbox = ['Any', 'Safe_Value', 'Current_Value', 'Other_Value'];
var command_3_checkbox = ['Any', 'Checkbox_Value', 'Current_Value', 'Other_Value'];

var command_4 = ['Exists', 'Not_Exists', 'Checked', 'Not_Checked'];


var command_array_not_checkbox = [command_0, command_1_not_checkbox, command_2, command_3_not_checkbox, command_4];
var command_array_checkbox = [command_0, command_1_checkbox, command_2, command_3_checkbox, command_4];


//Parse input command array
//ensure that all syntax requirements will be met
    function Check_If_Good_Command(commands){
        if(commands.length != 5 && commands.length != 2) {
            return -1;
        }


        var is_checkbox = true;
        var is_good_command = true;

        for(var i=0; i<commands.length; i++) {
            if(!command_array_checkbox[i].indexOf(commands[i])){
                is_checkbox = false;
            }
        }

        

        if(!is_checkbox) {
            for(var i=0; i<commands.length; i++) {
                if(command_array_not_checkbox[i].indexOf(commands[i]) < 0) {
                    is_good_command = false;
                }
            }
        }

        if(is_good_command == false) {

           return -1;
        }

        
        return true;
    }



//t
function execute_command(e, attribute_id) {

    var the_table = document.getElementById('command_selector_table_'.concat(attribute_id));
    var cells = the_table.getElementsByTagName('td');
    var command_string = '';
    var i;
    for(i=0; i<cells.length; i++){

        var the_select = cells[i].childNodes;

        var the_value =the_select[0].value;
        command_string += ' ';
        command_string += the_value;

    }

    Process_Commands(attribute_id, command_string);
    
}

//check if the command string syntax is ok
//use dom class structure to execute processing
function Process_Commands(attribute_id, commandString){

    in_commands = commandString.split(' ');

    var commands = new Array();


    for(var i=0; i<in_commands.length; i++) {
        if(in_commands[i] != null) {
            if(in_commands[i] != '') {
                if(typeof in_commands[i] == 'string') {
                    commands.push(in_commands[i]);
                }
            }
        }
    }
 
    if(!Check_If_Good_Command(commands)) {
        return -1;
    }
            
    var attribute_class = 'attribute_'.concat(attribute_id);

    var action_function = Get_Action(commands[0]);
    var subject_function = Get_Subject(commands[1]);



    if(action_function == -1 || subject_function == -1) { 
        
        return -1;
    }


    var subject = subject_function(attribute_class);

    if(!subject || subject.length == 0) {
        return 'NONE TO ACT ON';
    }



    if(commands.length == 2) {

        for(var i=0; i<subject.length; i++) {
            action_function(subject[i]);
        }
        return true;
    }
    else{
       
        var to_return = Process_Long_Commands(commands, subject, action_function);
        return to_return;
    }
}


//additional commands will be conditional, subject, predicate
//requires comparing each of the subject's siblings to some predicate
//if requrements are met, execute the command through action_function()

//this transition between 2 and 5 arg long commands is rough, make it through tables

/*

    __a__->__a1__->__a2___->__a3__->__a4___
                   __a21__          __a41__
                                    __a42__

    __b__->__b-1___->__b-1-2__->__b-1-2-3___
          |__b-1.1__           v__b-1-2-3.1__
          |__b-1.2__->
          v__b-1.3__->__b-1.3-2____
                     v__b-1.3-2.1__



-->generalize assignment of a class
-->for each in new/mod entry_list - > if this matches some class rule -> assign

*/     


// var command_map new Array(); //<string,command_object'>


// var command_object = function(cmd,  params){
//     self.cmd = cmd


//     function Parse_Commands(cmds, Cmd_Rules){




//         for (var i = Cmd_Rules.length - 1; i >= 0; i--) {

//             if(Cmd_Rules[i].fits(cmd[i])){

//             }
//         };
//     }
// }


function Process_Long_Commands(commands, subject, action_function) {
    

    //PROCESS_LONG_COMMANDS
    var passed_subjects = new Array();



    for(var i=0; i<subject.length; i++) {
        //CYCLE THROUGH EACH SUBJECT TO TEST IF IT IS TO BE ACTED ON, CHECK ALL ITS PREDICATES
        
        var subject_2_function = Get_Sibling(commands[3]);

        var subject_2 = subject_2_function(subject[i], 'td');


        var predicate_function = Get_Predicate(commands[4]);

        if(predicate_function == -1 || subject_2_function == -1){
            return -1;
        }
        var predicate_result = predicate_function(subject_2);


        switch(commands[2]) {
            case 'Unless':
                if(predicate_result == false) { 
                    
                    passed_subjects.push(subject[i]);

                } 
                break;
                
            case 'If':
            
                if(predicate_result == true) { 
                     passed_subjects.push(subject[i]);
                }
                break;

            default:
                return -1;
        }
    }

    for(var i=0; i<passed_subjects.length; i++) {

        //THEN ACT ON EACHED PASSED SUBJECT

        action_function(passed_subjects[i]);

        
    }
    return true;

}


//This is an interface to return the action argument's corresponding function
//the default does not need to be checked if previously itterated all arguments
    var Get_Action = function(action_input) {

        //GET_ACTION

        switch(action_input){
            case 'Check':
                return check_element;

            case 'Uncheck':
                return uncheck_elements;

            default:
                return -1;
        }
    }



//for the element passed, it is assumed it is of type 'li' for the sake of this project, To become varible later
//set class to contain 'Checked' and also for contained Selector set the class and 'checked'
var check_element = function (element) {

    //CHECK_ELEMENT 

    if(element.className.indexOf('Checked') < 0){
        element.className += ' Checked';

        var element_children = element.childNodes;

        for(var i=0; i<element_children.length; i++) {
            if(element_children[i].type == 'checkbox' || element_children[i].type == 'radio') {

                if(element_children[i].className.indexOf('Checked') < 0) {
                    element_children.className += 'Checked';
                }
                element_children[i].checked = true;
            }
        }
    }       
}



//for the element passed, it is assumed it is of type 'li' for the sake of this project, To become varible later
//set class to not contain 'Checked' and also for contained Selector remove 'Checked' class
var uncheck_elements = function (element) {
    //uncheck_elements
 //window.alert('arf');
    var classes = element.className.split(' ');
    var index = classes.indexOf('Checked');
    if(index > -1){

         

        classes.splice(index, 1);
        element.className = classes.join(' ');

        var element_children = element.childNodes;

        for(var i=0; i<element_children.length; i++) {

            if(element_children[i].type == 'checkbox' || element_children[i].type == 'radio') {

                var extra_classes = element_children[i].className.split(' ');
                var extra_index = extra_classes.indexOf('Checked');
                if(extra_index > -1) {
                    extra_classes.splice(extra_index, 1);
                    element_children[i].className = extra_classes.join(' ');
                }
                element_children[i].checked = false;
            }
        }
    }     
}


//This is an interface to return the Subject's corresponding accessor function
//the default does not need to be checked if previously itterated all arguments
var Get_Subject = function(subject_type) {
    //Get_Subject
    switch(subject_type) {
        case 'All':
            return Get_All;
            break;

        case 'Safe_Value':
            return Get_Safe_Value;
            break;

        case 'Current_Value':
            return Get_Current_Value;
            break;

        case 'Other_Value':

            return Get_Other;


        case 'Checkbox_Value':
            return Get_Checkbox_Value
        
        default:
            return -1;
    }
}


        //Main Subject accessor  -- Get any elements with matching attribute + class_to_match that are 'li'
            var Get_All = function(attribute_class) {
                
                var return_array = Filter_Class(attribute_class, null);

                return return_array;
            }


        //Main Subject accessor
            var Filter_Class = function(attribute_class, class_to_match) {

            
                var elements = document.getElementsByClassName(attribute_class);
                var return_array = new Array();

                if(class_to_match == null) {
                
                   for(var i=0; i<elements.length; i++) {

                        if(elements[i].tagName.toLowerCase() == 'li') {

                            return_array.push(elements[i]);
                        }   
                    } 
                }

                else{
                    
                    for(var i=0; i<elements.length; i++) {
                        if(elements[i].className.indexOf(class_to_match) > -1) {

                            if(elements[i].tagName.toLowerCase() == 'li') {

                                return_array.push(elements[i]);
                            }
                        }
                    }
                }
                window.alert(class_to_match);
                return return_array;
            }
    //THESE ARE THE VARIOUS FUNCTIONS TO GET SUBJECTS
    //SEVERAL function not used because are covered by other arguments, ex Get_Checked()
            var Get_Checked = function(attribute_class) {

                var return_array = Filter_Class(attribute_class, 'Checked');
                return return_array;
            }

            var Get_Safe_Value = function(attribute_class) {

                var safe_array = Filter_Class(attribute_class, 'Safe_Value');

                return safe_array;
            }


            var Get_Current_Value = function(attribute_class) {

                var current_array = Filter_Class(attribute_class, 'Current_Value');
                return current_array;
            }



            var Get_Checkbox_Value = function(attribute_class) {

                var checkbox_array = Filter_Class(attribute_class, 'Checkbox_Value');
                return checkbox_array;
            }

            var Get_Current_Checkbox = function(attribute_class) {
                var current_array = Filter_Class(attribute_class, 'Checkbox_Value');
                var checkbox_array = Filter_Class(attribute_class, 'Checkbox_Value');

                var return_array = new Array();

                for(var i=0; i<checkbox_array.length; i++) {
                    if(current_array.indexOf(checkbox_array[i]) > -1){
                        return_array.push(checkbox_array[i]);
                    }
                }
                return return_array;
            }



            var Get_Other = function(attribute_class) {
                

                var attribute_array = Get_All(attribute_class);

                var current_array = Filter_Class(attribute_class, 'Current_Value');
                var safe_array = Filter_Class(attribute_class, 'Safe_Value');

                attribute_array = Remove_Matches(attribute_array, current_array);
                attribute_array = Remove_Matches(attribute_array, safe_array);

                return attribute_array;
            }




     
/*
   Here I willingly ignite the candle, the night approaches. There is none left to burn, spare
    a tiny fragment. That which remains must carry itself though, and reaching the end, will find success. 
So heartedly free a man is, to be.
Be never, buyt whence to be but the thing that decideldly spoke and said it was. Ha, damn, NO , that is a string,
this is a thing, and all are left to cheer, the rain sits, hanging over the horizon, a damp stillness sits atop this day, its weight keeping all things within the calm. We rest, always weary that something might stir, remaining poised on the distance. 
The clock had struck noon, the ring had scared the dog long off, carrying his limp tail behind, twilring as if some strange tail system technician inside the dog's lower back had decided that up and down was too plain for today, and additional motion from side to was the answer.
A faint howl came from behind, Curly turned around and saw the dog, now yelling at flock of birds, all of whom were perched on the hydro wire. All except one. This one was sitting just underneath, almost hunched over. The dog ran too it, and picked it up in his mouth, all without a sound from the bird.
He brought the black mess of feathers over to us. After a better look at the bird, it was obvious this create was onto the next life. The dog spat it out, sitting over it and howling softly to the setting sun. One of the men sitting nearest had attempted to console the dog by calling him over.
There was no such luck. The dog continued its cry. Another man got up and approached him when a high pitched screech filled the air. However faint, we eventually discovered that it came from infront of us, some ways under the setting sun, now taunting the horizon. This would be it, our last day in paradise, our final sunset.

--> Margin creator , write text starting any point on screen, create boxes with =======+||
                                                                                        ||
 ---> WAY of displaying doc strings/info, also organize, write into files
            @Override,;;;
            """docstring"""


--> Highlight + Click --> drag = copy   &&  unclick = paste
*/




/*
    Require easy to initiate operation using selects
    TYPE 2 + 1

    _Action     _Subject            _Conditional    _Subject      _Predicate
    |check      |All                |               |               |
    |uncheck    |Safe_Value         |unless         |Any            |Exists
                |Current_Value      |if             |Safe_Value     |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked
                
    TYPE 3 

    _Action     _Subject            *_Conditional   *_Subject       *_Predicate
    |check      |All                |               |               |
    |uncheck    |Checkbox_Value     |unless         |Any            |Exists
                |Current_Value      |if             |Checkbox_Value |Not_Exists
                |Other_Value                        |Current_Value  |Checked
                                                    |Other_Value    |Not_Checked
*/

//ADD 'Attribute_X'
//or !none!


    //This is the interface to find the sibling subject class
            var Get_Sibling = function(class_to_match) {
                //Get SIBLING

                //update switch/case to extend this application command set
                switch(class_to_match) {
                    case 'Current_Value':
                        
                        return Get_Sibling_Current_Value;

                    case 'Safe_Value':
                        return Get_Sibling_Safe_Value;

                    case 'Other_Value':
                        return Get_Sibling_Not_Current_Not_Safe;

                    case 'Any':
                        //window.alert(Get_Sibling_All);
                        return Get_Sibling_All;

                    case 'Checkbox_Value':
                        return Get_Sibling_Checkbox_Value;

                    default:
                        return -1;
                }
            }


        //These are the varous sibling accessor functions for each type to be found
        //Some are not needed as covered by predicate ex: get_sibling_checked

        //have a way of updating the above list
            var Get_Sibling_Checked = function(leading_subject, top_delimiter) {
                var return_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Checked');
                return return_array;
            }

            var Get_Sibling_Checkbox_Value = function(leading_subject, top_delimiter) {
                var return_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Checkbox_Value');
                return return_array;
            }

            var Get_Sibling_All = function(leading_subject, top_delimiter) {
               
                var attribute_class = Get_Attribute_Class(leading_subject);
                if(!attribute_class) {
                //window.alert(leading_subject);
                    return -1;
                }
                var return_array = Find_Sibling_Match(leading_subject, top_delimiter, attribute_class);
                
                return return_array;
            }

            var Get_Sibling_Current_Value = function(leading_subject, top_delimiter) {
                

                var current_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Current_Value');
                
                return current_array;
            }


            var Get_Sibling_Safe_Value = function(leading_subject, top_delimiter) {

                var safe_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Safe_Value');
                return safe_array;
            }

            var Get_Sibling_Not_Current_Value = function(leading_subject, top_delimiter) {

                var all_array = Get_Sibling_All(leading_subject, top_delimiter);
                var current_array = Get_Sibling_Current_Value(leading_subject, top_delimiter);
            }


            var Get_Sibling_Not_Current_Not_Safe = function(leading_subject, top_delimiter) {
                var current_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Current_Value');
                var safe_array = Find_Sibling_Match(leading_subject, top_delimiter, 'Safe_Value');

                var all_array = Get_Sibling_All(leading_subject, top_delimiter);

                all_array = Remove_Matches(all_array, safe_array);
                all_array = Remove_Matches(all_array, current_array);

                return all_array;
            }




            //DOM FIND SIBLINGS IN SAME CELL MATCHING CLASS TO MATCH
            var Find_Sibling_Match = function(leading_subject, top_delimiter, class_to_match) {

                var top_element = Get_Top_Element(leading_subject, top_delimiter);
                if(top_element == null) {
                    return null;
                }
                
                var match_array = top_element.getElementsByClassName(class_to_match);

                var return_array = new Array();

                for(var i=0; i<match_array.length; i++) {
                    if(match_array[i].tagName.toLowerCase() == 'li'){

                        if(match_array[i] != leading_subject) {
                            return_array.push(match_array[i]); 
                        }
                    }
                }
                return return_array;
            }





//This is an interface to return the Predicate argument's corresponding function
//the default does not need to be checked if previously itterated all arguments

//predicate returns a true or false because is testing if the subject_2 element is/not checked or does/not exist
//can change this based on the operation type, check if
    var Get_Predicate = function(to_match) {
        switch(to_match) {
            case 'Exists':
                return Test_Exists;

            case 'Not_Exists':
                return Test_Not_Exists;
                
            case 'Checked':

                return Test_Checked;

            case 'Not_Checked':
                return Test_Not_Checked;

            default:
                return -1;
        }
    }

            var Test_Checked = function(elements) {
                for(var i=0; i<elements.length; i++) {
                    if(elements[i].className.indexOf('Checked') > -1){
                        return true;
                    }
                }
                return false;
            }
            
            var Test_Not_Checked = function(elements) {

                for(var i=0; i<elements.length; i++) {
                    if(elements[i].className.indexOf('Checked') < 0){
                        return true;
                    }
                }
                return false;
            }

            var Test_Exists = function(elements) {
                if(elements && elements.length > 0){
                    return true;
                }
                return false;
            }

            var Test_Not_Exists = function(elements) {
                if(elements && elements.length > 0){
                    return false;
                }
                return true;
            }







            //ARRAY HELPER FUNCTION
            var Remove_Matches = function(to_remove_from, to_match) {
                //iterate through to_remove_from, see if theres a match in to_match
                    //if theres no match then add to filtered list 

                if(!to_remove_from || !to_match) {
                    return -1;
                }

                if(!Array.isArray(to_remove_from) || !Array.isArray(to_match)){
                    
                    return -1;
                }

                var return_array = new Array();

                for(var i=0; i<to_remove_from.length; i++) {
                    if(to_match.indexOf(to_remove_from[i]) < 0) {
                        return_array.push(to_remove_from[i]);
                    }
                }
                return return_array;
            }
            
            //STRING HELPER FUNCTION
            var Get_Attribute_Class = function(leading_subject) {

                if(leading_subject==null || !isElement_1(leading_subject)) {
                    
                    return -1;
                }

                var attribute_class = null;
                var classes = leading_subject.className.split(' ');

                for(var i=0; i<classes.length; i++) {
                    if(classes[i].indexOf('attribute_') > -1){
                        attribute_class = classes[i];
                        break;
                    }
                }
                return attribute_class;
            }


            function isElement_1(obj) {
              try {
                //Using W3 DOM2 (works for FF, Opera and Chrom)
                return obj instanceof HTMLElement;
              }
              catch(e){
                //Browsers not supporting W3 DOM2 don't have HTMLElement and
                //an exception is thrown and we end up here. Testing some
                //properties that all elements have. (works on IE7)
                return (typeof obj==="object") &&
                  (obj.nodeType===1) && (typeof obj.style === "object") &&
                  (typeof obj.ownerDocument ==="object");
              }
            }


            //DOM TRAVERSAL HELPER
            var Get_Top_Element = function(leading_subject, top_delimiter) {

                if(!leading_subject || !isElement_1(leading_subject) ) {
                    
                    return -1;
                
                }
                if(!top_delimiter || typeof top_delimiter != 'string') {
                    return -1;
                }
                
                
                
                var top_element = leading_subject;
                var tag = top_element.tagName;

                

                while(tag.toLowerCase() != top_delimiter) {

                    if(top_element == document.body) {
                        window.alert('ERROR GETTING TOP NODE');
                        return null;
                    }
                    top_element = top_element.parentNode;
                    tag = top_element.tagName;
                }

                return top_element;
            }


               

            function Check_All_Emails(){
                var subject = document.getElementsByClassName('Email_Block');
                for(var i=0; i<subject.length; i++) {
                    if(subject[i].tagName.toLowerCase() == 'input') {
                        subject[i].checked = true;
                    }
                    if(subject[i].className.indexOf('Checked') < 0){
                        subject[i].className += ' Checked';
                    }
                }
            }

            function Uncheck_All_Emails(){

                var subject = document.getElementsByClassName('Email_Block');
                for(var i=0; i<subject.length; i++) {
                    
                    if(subject[i].tagName.toLowerCase() == 'input') {

                        subject[i].checked = false;
                    }
                    var classes = subject[i].className.split(' ');

                    

                    var index = classes.indexOf('Checked');

                    //window.alert(index);

                    if(index > -1){
                        classes.splice(index, 1);
                    }
                    subject[i].className = classes.join(' ');
                }
            }







//FOR FIRST PAGE
    function Test_Upload_Text(){
        var the_text = document.getElementById("attribute_changer_text_to_upload");
        if(the_text.innerHTML == "") {
            document.getElementById("error_printing").innerHTML="Error: No Text Input";
            return;
        }
        else{
            if(the_text.innerHTML[0].length > 1000000000) {
                document.getElementById("error_printing").innerHTML="Error: Text Cannot Exceed 1 Billion Characters";
                return;
            }
            else{
                document.getElementById("text_upload_form").submit();
            }
        }
    }

    function Test_Upload_File(){
        window.alert('ar');
        var the_file = document.getElementById("attribute_changer_file_to_upload");
        if(!the_file.files) {
            document.getElementById("error_printing").innerHTML="Error: Not Supported By This Browser";
            return;
        }
        if(!the_file.files[0]) {
            document.getElementById("error_printing").innerHTML="Error: Must Have File Selected";
            return;
        }
        else{
            if(the_file.files[0].size > 1000000000) {
                document.getElementById("error_printing").innerHTML="Error: File Cannot Exceed 1GB";
                return;
            }
            else{
                document.getElementById("file_upload_form").submit();
            }
        }
    }



