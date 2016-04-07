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
//$position_ID = $_POST["position_ID"];
//$template_ID = $_POST["template_ID"];
//$ordering = $_POST["ordering"];

$position_ID = 453;
$template_ID = 27;
$ordering = 1;

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

//this query will be used to help flip the positions.
//SELECT * FROM schedule_template_position_list WHERE id = (SELECT MAX(id) FROM schedule_template_position_list WHERE id < 453)

//Don't allow position to be increased if user is already #1 || Don't allow position to be decreased if user is already at the bottom
// || don't allow position to change if user_id doesn't exits.
if ( (($position_ID == $first_position) && ($ordering == -1)) || (($position_ID == $last_position) && ($ordering == 1)) || ($position_ID == "") )
{
    //Do nothing
    echo "</br>";
    echo "</br>";
    echo "The position is not ok to change.";
}
//else
{
    //Proceed with the position flip.
    echo "</br>";
    echo "</br>";
    echo "The position has passed the test and can be changed";
    $other_employee_senority = $user_senority + $senority_change;
    echo "</br>";
    echo "other_employee_senority: ". $other_employee_senority;
    
     
     //SQL to UPDATE each Employee.
     
     echo "</br>";
     echo "Done processing request";
    
}
//else case means we can adjust the position.


//Include database Termination Script
include 'db_disconnect.php';


?>