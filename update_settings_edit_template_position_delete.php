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

//retrive post variables
$template_ID = $_POST['template_ID'];
$position_ID = $_POST['position_ID'];
$position_shift = $_POST['position_shift'];
$position_quantity = $_POST['position_quantity'];


//delete the row
$sql_delete_row = 
"
DELETE FROM `".$db."`.`schedule_template_position_list`
WHERE ID = ".$position_ID;
$link->query($sql_delete_row);






//Include database Termination Script
include 'db_disconnect.php';
?>