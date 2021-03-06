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


//Retrieve POST values
$new_position_name = $_POST["new_position_name"];
$new_position_PP_ID = $_POST['new_position_PP_ID'];
$new_position_NR = $_POST['new_position_NR'];


if($new_position_name != "")
{
//INSERT query
$sql_add_position =
'
INSERT INTO `'.$db.'`.`schedule_position` (`ID`, `ID_posted_position_requirement`, `name`, `non_rotational`) 
VALUES (NULL, "'.$new_position_PP_ID.'", "'.$new_position_name.'", "'.$new_position_NR.'");
';
//FIRE
$link->query($sql_add_position);
}

//Include database Termination Script
include 'db_disconnect.php';
?>