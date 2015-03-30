<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
require_once DRUPAL_ROOT . '/includes/common.inc';
require_once DRUPAL_ROOT . '/includes/module.inc';
require_once DRUPAL_ROOT . '/includes/unicode.inc';
require_once DRUPAL_ROOT . '/includes/file.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

include 'ChromePhp.php';

//Chromephp::log("this file is getting run right now!!");

 header('Content-Type: application/json');

    $aResult = array();

    if( !isset($_POST['functionname']) ) { 
  //    Chromephp::log(" no function arguments name");
      $aResult['error'] = 'No function name!'; }

    if( !isset($_POST['arguments']) ) { 
     // Chromephp::log("no function arguments");
      $aResult['error'] = 'No function arguments!'; }

    if( !isset($aResult['error']) ) {
       // Chromephp::log(" there was no error");
        
       // Chromephp::log(($_POST['arguments']));  
        switch($_POST['functionname']) {
            case 'add':
               if( is_array($_POST['arguments'])) {
                //  Chromephp::log("there was an error in arguments");
                   $aResult['error'] = 'Error in arguments!';
               }
               else {
                 //  Chromephp::log("the function is getting called");
                  // Chromephp::log(floatval($_POST['arguments'][0]));
                  // $aResult['result'] = add(floatval($_POST['arguments'][0]), floatval($_POST['arguments'][1]));  // this is where it gets called.
                      $aResult['result'] = add($_POST['arguments']); // just send the entire array through...               
               }
               break;

            case 'save':
                if(is_array($_POST['arguments'])) {
                
                   $aResult['error'] = 'Error in arguments!';
               }else {
                      $aResult['result'] = save($_POST['arguments']);   // call save instead of add
               }
               break;

            default:
               $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
               break;
        }

    }

    echo json_encode($aResult);

// this function renames all the groups because they are screwed up... 
// this function also gets rid of any nulls within the json string.
function json_fixer($args){

/*Chromephp::log("before getting rid of the nulls");
  Chromephp::log($args); 
  Chromephp::log(array_filter($args));
Chromephp::log($args);
  /*if (($key = array_search('null', $args)) !== false) {
    unset($args[$key]);
    }
    Chromephp::log("json fixer is getting called");
    Chromephp::log($args);*/
    // first get rid of the nulls... 
    /*for ($g = 0; $g < count($args->children); $g++){  // go through the json file and search up the correct index.
      // go thorugh every gorup
      $group = $args->children[$g];  // this is going to be the current group...
      for($r = 0; $r < count($group->children); $r++){
        // this is giong through every single person.. check for nulls... 
        $group1 = $group->children[$r];
        Chromephp::log($group1);
        if (is_null($group1)){
          // then we have to get rid of this group... 

        }  
      }*/


    //}
}

// this function updates the json file when there is any movement inside the javascript
// we need it to recalculate the weights and output them onto the screen...
function add($arguments1){  
  
  $decoded = json_decode($arguments1);

  json_fixer($decoded);  
  $file = 'TESTING.json';  
  file_put_contents($file, $arguments1);

Chromephp::log("after altering the json file"); 
Chromephp::log("we are going to call weight changer "); 

$weights = computeWeights();


}



// might not need this function because we can just place it right into the json file... 
function computeWeights(){


}
// function saves the json file into the database.
// we need a checker in the start that says whether or not we can save because some groups are not trios...
function save($arguments1){
  
  
  $decoded = json_decode($arguments1);  // takes in the json file
      
//$answer=right_form_checker($decoded); // if this is null then continue, elsereturn

/*if(!is_null($answer)){
 if returned msg.
  return $answer;
}*/

  Chromephp::log($decoded);
  
  //Chromephp::log(count($decoded->children));
 //Chromephp::log("just before the for loop");
$mentor_id;
$junior_id;
$senior_id;
$thearray = array();  // this is going to be pushed to
for ($g = 0; $g < count($decoded->children); $g++){  // go through the json file and search up the correct index.
  //Chromephp::log("the loop is running right now");
  // go through all the groups.
  //Chromephp::log($g);
  $current_group = $decoded->children[$g];  // this is the current group that we are searching
  //Chromephp::log($current_group); 
  //ChromePhp::log($current_group->children);
  for($r=0; $r < count($current_group->children);$r++){
    
    $applicant = $current_group->children[$r];
    $applicant_name = $applicant->name;
    $applicant_familyname = $applicant->familyname;
    $applicant_position = $applicant->position;
   /* Chromephp::log($r);
    Chromephp::log($applicant);
    Chromephp::log($applicant_position);
    Chromephp::log($applicant->familyname);
    Chromephp::log($applicant->name);
    Chromephp::log("just before the if statement");
    Chromephp::log(is_string($applicant_position));*/
    // sql query and get the id.
    $mentor = 'Mentor';
    if($applicant_position == $mentor){
      Chromephp::log("found a mentor");
   //   $query = "SELECT * FROM TESTING.maestro_signup_mentor_20142015 WHERE first_name = ".$applicant_name." AND "
   //   ."last_name = ". $applicant_familyname;
        $mentor_id = traverser($applicant_name, $applicant_familyname, "mentor");  // finding a mentor finding his unique id...
        Chromephp::log($mentor_id);
        // what do we do with this 10 now...
    }
    $junior = "Junior";
    if($applicant_position == $junior){
      Chromephp::log("found a junior");
        $junior_id = traverser($applicant_name, $applicant_familyname, "student");  
        Chromephp::log($junior_id);
    }

    $senior = "Senior";
    if($applicant_position == $senior){
      Chromephp::log("found a senior");
        $senior_id = traverser($applicant_name, $applicant_familyname, "student");  
        Chromephp::log($senior_id);
    }

  }
// we need to create the group for this particular array now
$myArray = array(
      'mentor'=> $mentor_id,
      'senior' => $senior_id,
      'junior' => $junior_id,
      );
array_push($thearray, $myArray); // push the array...
}
  
  // lets print the array and see... 
Chromephp::log($thearray);
$encode = json_encode($thearray);
Chromephp::log($encode);


   $query = "INSERT INTO TESTING.maestro_matched_trios (timestamp, mentoring_year, weightings, percentage, trios) VALUES (:ts, :my, :w, :p, :t)";
db_query($query, array(
        ':ts' => date('Y-m-d H:i:s'),
        ':my' => "20142015",
        ':w'  => "",
        ':p'  => 0.43,
        ':t'  => $encode,
        ));
  
    Chromephp::log("after inserting into the db");
  }


// gathers the indices for the json array...
function traverser($firstname, $lastname, $choose){  
    
    //Chromephp::log("traverser is being called ");
    if ($choose == "mentor"){
    $query = "SELECT * FROM TESTING.maestro_signup_mentor_20142015";
  }else{
    
    $query = "SELECT * FROM TESTING.maestro_signup_student_20142015";
    Chromephp::log($query);
  }
    $result1 = db_query($query)->fetchAll();
      
        for ($a = 0; $a < count($result1); $a++){
            if ($result1[$a]->last_name == $lastname &&
              $result1[$a]->first_name == $firstname){
              return $result1[$a]->id;
            }
        }
}

// this function is going to check whether the json file has the right form and can be saved into the trio database... 
// if it does not return null then return the message that was sent...
function right_form_checker($json_input){

Chromephp::log("this is the right form checker");
Chromephp::log($json_input);  
  
for ($g = 0; $g < count($json_input->children); $g++){  // go through the json file and search up the correct index.

  $current_group = $json_input->children[$g];
  Chromephp::log("inside the loop checker");
  Chromephp::log($current_group);

  // check the size of each group... 
  if(count($current_group->children) != 3)// if the size is not equal to three...
  {
    return "There is a group that is not three!";
  }

  $mentor = 0;
  $junior = 0;
  $senior = 0;
  for($i = 0; $i < count($current_group->children); $i++){
    
    // going through each child within each group make sure that we have each thing...
    $current_group1 = $current_group->children[$i];
    Chromephp::log($current_group1);
    $position = $current_group1->position; 
    $mentorstring = "Mentor";
    if($position == $mentorstring){
      $mentor = 1;  
    }
   $juniorstring = "Junior";
    if($position == $juniorstring){
      $junior = 1;
    }
    $seniorstring = "Senior";
    if($position == $seniorstring){
      $senior = 1;  // just set to 1 and know that we have found 1
    }

    // end of the loop here...
  }
  // check if we found one of each here.
  if ($mentor == 0  || $junior == 0 || $senior == 0){

    return "there is a group that does not comprise of a Mentor, senior student and junior student";

  }

  }
  }


?>