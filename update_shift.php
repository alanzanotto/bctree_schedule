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

//Retrieve POST Values
$user_id = $_POST["new_user_id"];
$shift_value = $_POST["new_shift_value"];

//Include database Connection Script
include 'db_connection.php';


//Write SQL to Perform Database Operation (UPDATE SHIFT)
$sql = " 
UPDATE `".$db."`.`employee_shift_preference` SET `shift` = '". $shift_value ."' WHERE `employee_shift_preference`.`ID_employee` = ".$user_id;

$link->query($sql);


//Include database Termination Script
include 'db_disconnect.php';

?>
