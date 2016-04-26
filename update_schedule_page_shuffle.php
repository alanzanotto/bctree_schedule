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
//$schedule_ID = $_POST["schedule_ID"];
$schedule_ID = 2;

//Setup Other Variables.
$station = 21;//Cherry Line Sorting is this ID.
$non_rotation = 31;//this is the Sort 1(Non-Rotational) position ID.
$shift;
$day_array = array();
$day_weight_array = array();

$night_array = array();
$night_weight_array = array();
$arr_index = 0;


//DAY RANDOMIZE*******************************************************************



//Retrive the Cherry Sorting People seperated by A Day and Night Crew.
//Retrive Day shift Cherry Sorting People.
$shift = 0;

//People are in first in order of senority.
$sql_day_cherry_sorters = 
"
SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE ID_schedule = ".$schedule_ID." 
AND shift = ".$shift."
AND station = ".$station ."
AND ID_schedule_position != ".$non_rotation."
ORDER BY weight";
echo "</br></br></br>";
echo $sql_day_cherry_sorters;
$result_day_cherry_sorters = $link->query($sql_day_cherry_sorters);
//Add them into a day and night array of their own.

$arr_index = 0;
while ($row = $result_day_cherry_sorters->fetch_assoc())
{
    //Add Persons positions to the array.
    $position_ID = $row['ID_schedule_position'];
    $weight = $row['weight'];
    $day_array[$arr_index] = $position_ID;
    $day_weight_array[$arr_index] = $weight;
    
    $arr_index++;//Increment index.
}

print_r ($day_array);

//Select the people again but randomize them this time.  Then loop through them and delte them.
//People are in first in order RAND().
$sql_day_cherry_sorters = 
"
SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE ID_schedule = ".$schedule_ID." 
AND shift = ".$shift."
AND station = ".$station ."
AND ID_schedule_position != ".$non_rotation."
ORDER BY RAND()";

echo "</br></br></br>After Randomization:</br></br></br>";
echo $sql_day_cherry_sorters;
$result_day_cherry_sorters = $link->query($sql_day_cherry_sorters);

$arr_index = 0;//Reset index.
while ($row = $result_day_cherry_sorters->fetch_assoc())
{
    //Retrive User Information
    	
    $ID_schedule_position = $row['ID_schedule_position'];
    $ID_employee = $row['ID_employee'];
    $facility = $row['facility'];
    $station = $row['station'];
    $weight = $row['weight'];
    
    echo "</br></br>";
    echo "UserID = ".$ID_employee." OLD Position: ".$ID_schedule_position." NEW Position: ".$day_array[$arr_index];
    
    $sql_update_person =
    "
    UPDATE schedule_saved
    SET ID_schedule_position = ".$day_array[$arr_index] .",
    weight = ".$day_weight_array[$arr_index]."
    WHERE ID_schedule = ".$schedule_ID."
    AND ID_schedule_position = ".$ID_schedule_position."
    AND ID_employee = ".$ID_employee."
    AND shift = ".$shift."
    AND facility = ".$facility."
    AND station = ".$station."
    AND weight = ".$weight;
    
    echo $sql_update_person;
    $link->query($sql_update_person);
    
    
    $arr_index++;
}



//NIGHT RANDOMIZE*******************************************************************
//Note that code will read day but because we swtiched the shift variable to 1 it will be querying night.



//Retrive the Cherry Sorting People seperated by A Day and Night Crew.
//Retrive Day shift Cherry Sorting People.
$shift = 1;

//Reset the arrays
$day_array = array();
$day_weight_array = array();

$night_array = array();
$night_weight_array = array();

//People are in first in order of senority.
$sql_day_cherry_sorters = 
"
SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE ID_schedule = ".$schedule_ID." 
AND shift = ".$shift."
AND station = ".$station ."
AND ID_schedule_position != ".$non_rotation."
ORDER BY weight";
echo "</br></br></br>";
echo $sql_day_cherry_sorters;
$result_day_cherry_sorters = $link->query($sql_day_cherry_sorters);
//Add them into a day and night array of their own.

$arr_index = 0;
while ($row = $result_day_cherry_sorters->fetch_assoc())
{
    //Add Persons positions to the array.
    $position_ID = $row['ID_schedule_position'];
    $weight = $row['weight'];
    $day_array[$arr_index] = $position_ID;
    $day_weight_array[$arr_index] = $weight;
    
    $arr_index++;//Increment index.
}

print_r ($day_array);

//Select the people again but randomize them this time.  Then loop through them and delte them.
//People are in first in order RAND().
$sql_day_cherry_sorters = 
"
SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE ID_schedule = ".$schedule_ID." 
AND shift = ".$shift."
AND station = ".$station ."
AND ID_schedule_position != ".$non_rotation."
ORDER BY RAND()";

echo "</br></br></br>After Randomization:</br></br></br>";
echo $sql_day_cherry_sorters;
$result_day_cherry_sorters = $link->query($sql_day_cherry_sorters);

$arr_index = 0;//Reset index.
while ($row = $result_day_cherry_sorters->fetch_assoc())
{
    //Retrive User Information
    	
    $ID_schedule_position = $row['ID_schedule_position'];
    $ID_employee = $row['ID_employee'];
    $facility = $row['facility'];
    $station = $row['station'];
    $weight = $row['weight'];
    
    echo "</br></br>";
    echo "UserID = ".$ID_employee." OLD Position: ".$ID_schedule_position." NEW Position: ".$day_array[$arr_index];
    
    $sql_update_person =
    "
    UPDATE schedule_saved
    SET ID_schedule_position = ".$day_array[$arr_index] .",
    weight = ".$day_weight_array[$arr_index]."
    WHERE ID_schedule = ".$schedule_ID."
    AND ID_schedule_position = ".$ID_schedule_position."
    AND ID_employee = ".$ID_employee."
    AND shift = ".$shift."
    AND facility = ".$facility."
    AND station = ".$station."
    AND weight = ".$weight;
    
    echo $sql_update_person;
    $link->query($sql_update_person);
    
    
    $arr_index++;
}







//Include database Termination Script
include 'db_disconnect.php';
?>
