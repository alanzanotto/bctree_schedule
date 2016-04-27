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

$employee_ID = $_POST['employee_ID'];
$employee_first_name = $_POST['first_name'];
$employee_last_name = $_POST['last_name'];


//Update Users name From Employee table
$sql_update_name = 
"
UPDATE  `".$db."`.`employee` SET  `first_name` =  '".$employee_first_name."',
`last_name` =  '".$employee_last_name."' WHERE  `employee`.`ID` = ".$employee_ID;

$link->query($sql_update_name);



//Include database Termination Script
include 'db_disconnect.php';

?>
