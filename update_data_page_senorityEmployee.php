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
$user_id = $_POST["new_user_id"];
$senority_change = $_POST["senority"];
$user_id = 1;
$senority_change = 1;

$sql_last_senority = 
"
SELECT senority 
FROM `".$db."`.`employee`
ORDER BY senority DESC
LIMIT 1
";
$result_last_senority = $link->query($sql_last_senority);
$object_last_senority = $result_last_senority->fetch_assoc();
$last_senority = $object_last_senority['senority'];




//Select the current users senority.

$sql_user_senority = 
"
SELECT senority
FROM `".$db."`.`employee`
WHERE ID = ".$user_id;
$result_user_senority = $link->query($sql_user_senority);
$object_user_senority = $result_user_senority->fetch_assoc();
$user_senority = $object_user_senority['senority'];


echo "Employee senority: " . $user_senority;
echo "</br>";
echo "Senority Direction: " . $senority_change;
echo "</br>";
echo "last_senority: " . $last_senority;

//Check if at the bottom of the list or at the very top of the list.

//Don't allow senority to be increased if user is already #1 || Don't allow senority to be decreased if user is already at the bottom
// || don't allow senority to change if user_id doesn't exits.
if ( (($user_senority == 1) && ($senority_change == 1)) || (($user_senority == $last_senority) && ($senority_change == -1)) || ($user_senority == ""))
{
    //Do nothing
    echo "</br>";
    echo "</br>";
    echo "The Senority is not ok to change.";
}
else
{
    //Proceed with the senority flip.
    echo "</br>";
    echo "</br>";
    echo "The Senority has passed the test and can be changed";
    $other_employee_senority = $user_senority + $senority_change;
    
    //Select other employees Senority Number.
}
//else case means we can adjust the senority.


//Include database Termination Script
include 'db_disconnect.php';


?>