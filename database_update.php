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


//grab all people in the database...
$sql = 'SELECT * FROM `'.$db.'`.`employee_shift_preference`';
$result = $link->query($sql);

//cycle through people
while ($row = $result->fetch_assoc())
{
	//get id of user
	$user_ID = $row['ID_employee'];

	//get posted position.
	$posted_position  = $row['posted_position'];
	//lookup ID of posted position
	$sqlpp = 
	'SELECT * FROM `'.$db.'`.`posted_positions` WHERE posted_position = "'.$posted_position.'"';
	echo $sqlpp;
	$resultpp = $link->query($sqlpp);
	$objectpp = $resultpp->fetch_assoc();
	$ppID = $objectpp['ID'];
	//insert ID into pp2
	$sql_insert = 
	'
	UPDATE `'.$db.'`.`employee_shift_preference`
	SET posted_position2 = '.$ppID.'
	WHERE ID_employee = '.$user_ID;
	$link->query($sql_insert);
	
}
//end cycle




//Include database Termination Script
include 'db_disconnect.php';
?>