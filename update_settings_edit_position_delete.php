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
$position_id = $_POST['position_id'];

$sql_update_schedule_position = 
"DELETE FROM `".$db."`.`schedule_position` 
WHERE `schedule_position`.`ID` = ".$position_id;

$link->query($sql_update_schedule_position);

//Include database Termination Script
include 'db_disconnect.php';
?>