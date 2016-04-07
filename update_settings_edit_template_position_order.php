<?php

/*******************************************************************************
* Copyright 2016 Alan A. Zanotto
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*    http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*******************************************************************************/

//Include database Connection Script
include 'db_connection.php';

//Retrieve POST Values

$position_ID = $_POST["position_ID"];
$template_ID = $_POST["template_ID"];
$ordering = $_POST["ordering"];

//Debugging Test Variables
//$position_ID = 455;
//$template_ID = 27;
//$ordering = 1;


echo "Position ID: " .$position_ID;
echo "</br>";
echo "Template ID: " .$template_ID;
echo "</br>";
echo "Ordering: " .$ordering;
echo "</br>";

//retrive first position in the template (upper boundry)
$sql_first_position = 
"
SELECT *
FROM `".$db."`.`schedule_template_position_list`
WHERE ID_template = ".$template_ID."
ORDER BY ID
LIMIT 1
";
$result_first_position = $link->query($sql_first_position);
$object_first_position = $result_first_position->fetch_assoc();
$first_position = $object_first_position['ID'];

//retrive last position in the template (lower boundry)
$sql_last_position =
"
SELECT *
FROM `".$db."`.`schedule_template_position_list`
WHERE ID_template = ".$template_ID."
ORDER BY ID DESC
LIMIT 1
";
$result_last_position = $link->query($sql_last_position);
$object_last_position = $result_last_position->fetch_assoc();
$last_position = $object_last_position['ID'];



//Print some Debuggin information for queries.
echo $sql_first_position;
echo "</br>";
echo $sql_last_position;

echo "</br>";
echo "First Poistion: " .$first_position;
echo "</br>";
echo "Last Position: ". $last_position;


//Don't allow position to be increased if user is already #1 || Don't allow position to be decreased if user is already at the bottom
// || don't allow position to change if user_id doesn't exits.
if ( (($position_ID == $first_position) && ($ordering == -1)) || (($position_ID == $last_position) && ($ordering == 1)) || ($position_ID == "") )
{
    //Do nothing
    echo "</br>";
    echo "</br>";
    echo "The position is not ok to change.";
}
else
{
    //Proceed with the position flip.
    echo "</br>";
    echo "</br>";
    echo "The position has passed the test and can be changed";
    

    $other_position = "";
    //Direction of Change.
    if ($ordering == -1)
    {
        //Bump position higher.  We can assume there is a higher postion
        //Retrive the position before the current one we are working with.
        $sql_other_position = 
        "
        SELECT ID 
        FROM `".$db."`.`schedule_template_position_list` 
        WHERE ID = (SELECT MAX(ID) FROM `".$db."`.`schedule_template_position_list` WHERE ID < ".$position_ID.")
        ";
        $result_other_position = $link->query($sql_other_position);
        $object_other_position = $result_other_position->fetch_assoc();
        $other_position = $object_other_position['ID'];
        
    }
    else
    {
        //Bump Position lower.  We can assume there is a lower position.
        //Retrive the position after the current one we are working with.
        $sql_other_position = 
        "
        SELECT ID 
        FROM `".$db."`.`schedule_template_position_list` 
        WHERE ID = (SELECT MIN(ID) FROM `".$db."`.`schedule_template_position_list` WHERE ID > ".$position_ID.")
        ";
        echo "</br>";
        echo $sql_other_position;
        echo "</br>";
        $result_other_position = $link->query($sql_other_position);
        $object_other_position = $result_other_position->fetch_assoc();
        $other_position = $object_other_position['ID'];
    
    }
    
    
    echo "</br>";
    echo "Other Position now...: ".$other_position;
    echo "</br>";
    //SQL to UPDATE each Employee.
    
    //Primary key doesn't allow us to easly swap the IDS. so we have to set the first change to ID 0. and then correct it after the other ID has been trasfered 
    //over.
    $sql_update_postion = 
    "
    UPDATE `".$db."`.`schedule_template_position_list`
    SET ID = 0
    WHERE ID = ".$position_ID;
    $link->query($sql_update_postion);
    
    //Change the other position.
    $sql_update_other_position =
    "
    UPDATE `".$db."`.`schedule_template_position_list`
    SET ID = ".$position_ID."
    WHERE ID = ".$other_position;
    $link->query($sql_update_other_position);
    
    $sql_update_postion = 
    "
    UPDATE `".$db."`.`schedule_template_position_list`
    SET ID = ".$other_position."
    WHERE ID = 0";
    $link->query($sql_update_postion);
    
    
    
    
    echo "Done processing request";
    
}
//else case means we can adjust the position.


//Include database Termination Script
include 'db_disconnect.php';


?>