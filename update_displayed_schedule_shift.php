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
$new_schedule_value = $_POST["new_schedule_value"];
$new_station_value = $_POST["new_station_value"];
$new_shift_value = $_POST["new_shift_value"];
//Variables
$temp_category;


//Include database Connection Script
include 'db_connection.php';


//echo $new_shift_value;

//retrieve the schedule.
$sql_schedule = 
"
SELECT * 
FROM `".$db."`.`schedule_saved` ss, `".$db."`.`schedule_position` sp 
WHERE ss.ID_schedule_position = sp.ID 
AND ss.`ID_schedule` = ".$new_schedule_value." 
AND sp.station = ".$new_station_value." 
AND ss.shift = ".$new_shift_value."
ORDER BY ss.ID_schedule_position ASC, ss.`ID_employee` ASC
";
$result_schedule = $link->query($sql_schedule);

//echo $sql_schedule;
/*
//basic
SELECT * 
FROM `bctreeschedule`.`schedule_saved` 
WHERE `ID_schedule` = 1 
ORDER BY `schedule_saved`.`ID_schedule_position` ASC, 
`schedule_saved`.`ID_employee` ASC

//basic + position info
SELECT * 
FROM `bctreeschedule`.`schedule_saved` ss, `bctreeschedule`.`schedule_position` sp 
WHERE ss.ID_schedule_position = sp.ID 
AND `ID_schedule` = "1" 
ORDER BY ss.ID_schedule_position ASC, ss.`ID_employee` ASC

//basic + position info + station filter
SELECT * 
FROM `bctreeschedule`.`schedule_saved` ss, `bctreeschedule`.`schedule_position` sp 
WHERE ss.ID_schedule_position = sp.ID 
AND ss.`ID_schedule` = 1 
AND sp.station = 8
ORDER BY ss.ID_schedule_position ASC, ss.`ID_employee` ASC

//basic + position info + station filter + shift filter
SELECT * 
FROM `bctreeschedule`.`schedule_saved` ss, `bctreeschedule`.`schedule_position` sp 
WHERE ss.ID_schedule_position = sp.ID 
AND ss.`ID_schedule` = 1 
AND sp.station = 8
AND ss.shift = 0
ORDER BY ss.ID_schedule_position ASC, ss.`ID_employee` ASC

*/

//temp table column to expand when 9 people in a row.
$temp_table_column = 0;
//Loop through the people in the schedule
$temp_facility = "";
$temp_facility_name = "";
while ($row = $result_schedule->fetch_assoc())
{
//Setup Variables.
$employee_ID = $row['ID_employee'];
$schedule_position_ID = $row['ID_schedule_position'];
$shift = $row['shift'];
$current_facility = $row['facility'];


//setup or change of facility header.
if ($temp_facility == "" || $temp_facility != $current_facility)
{

	//if facility hasn't been set.  set and start.
	if ($temp_facility == "")
	{
	$temp_facility = $current_facility;
	$sql_facility_name = "SELECT * FROM `".$db."`.`schedule_facility` WHERE ID = ".$temp_facility;
	$result_facility_name = $link->query($sql_facility_name);
	$object_facility_name = $result_facility_name->fetch_assoc();
	$temp_facility_name = $object_facility_name['name'];
	echo "<div class=\"header_facility\">".$temp_facility_name."</div>";
	}

	//if facility is changing then close last one and start the new one.
	elseif ($temp_facility != $current_facility)
	{
	$temp_facility = $current_facility;
	$sql_facility_name = "SELECT * FROM `".$db."`.`schedule_facility` WHERE ID = ".$temp_facility;
	$result_facility_name = $link->query($sql_facility_name);
	$object_facility_name = $result_facility_name->fetch_assoc();
	$temp_facility_name = $object_facility_name['name'];
	echo "</div>";//close off the div of  the category.
	echo "<div class=\"header_facility\">".$temp_facility_name."</div>";
	$temp_category = "new_facility";
	}

}//if ($temp_facility == "" || $temp_facility != $current_facility)




//Retrieve extra information  (employee information/ position information)
$sql_employee_information = " 
SELECT senority, first_name, last_name
FROM `".$db."`.`employee`
WHERE ID = ".$employee_ID;
$result_employee_information = $link->query($sql_employee_information);
$object_employee_information = $result_employee_information->fetch_assoc();
$employee_senority = $object_employee_information['senority'];
$employee_first_name = $object_employee_information['first_name'];
$employee_last_name = $object_employee_information['last_name'];

//echo $employee_first_name . " ". $employee_last_name;


$sql_position_information = "
SELECT name
FROM `".$db."`.`schedule_position`
WHERE ID = ".$schedule_position_ID;
//echo $sql_position_information;
$result_position_information = $link->query($sql_position_information);
$object_position_information = $result_position_information->fetch_assoc();
$schedule_position_name = $object_position_information['name'];
//echo $schedule_position_name;



if ( !isset($temp_category) || $temp_category == "new_facility")
{
$temp_category = $schedule_position_name;
echo '<div class="schedule_list_block" id="schedule_list_block">
<h4>'.$temp_category.'</h4>';//start a fresh category

//echo "(".$employee_senority.") ". $employee_first_name . " ". $employee_last_name .'</br>';//OLIVER
echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD
}

elseif ($temp_category != $schedule_position_name)
{
echo '</div>';//close up last category
$temp_category = $schedule_position_name;//set new temp category

echo '<div class="schedule_list_block">
<h4>'.$temp_category.'</h4>';//start a fresh category

//echo "(".$employee_senority.") ". $employee_first_name . " ". $employee_last_name .'</br>';//OlIVER
echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD

}

else
{
	//echo "(".$employee_senority.") ". $employee_first_name . " ". $employee_last_name .'</br>';//OLIVER
	echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD
}


}//while loop




//Include database Termination Script
include 'db_disconnect.php';

?>