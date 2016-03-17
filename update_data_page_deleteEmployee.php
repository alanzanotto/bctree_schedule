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


//Delete User From Employee Preferences 
$sql_delete_employee_preferences = 
"DELETE FROM `".$db."`.`employee_shift_preference` 
WHERE `employee_shift_preference`.`ID_employee` = ".$employee_ID;

$link->query($sql_delete_employee_preferences);

//Delete User From Employee Table
$sql_delete_employee = 
"DELETE FROM `".$db."`.`employee` 
WHERE `employee`.`ID` = ".$employee_ID;

$link->query($sql_delete_employee);


//Include database Termination Script
include 'db_disconnect.php';

?>
