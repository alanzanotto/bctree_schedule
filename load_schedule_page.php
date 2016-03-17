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


//div1
//SELECT all schedules created.  order by newest on top.
//make drop down menu.  value = schedule id, name/text = date of schedule.

$sql_created_schedules = 
"
SELECT ID, date
FROM `".$db."`.`schedule`
ORDER BY ID DESC 
LIMIT 20
";
$result_created_schedule = $link->query($sql_created_schedules);

//CHOOSE SCHEDULE
echo 
'
<div id="schedule_page_content_1">
<select name="select-choice-schedule_ID" id="select-choice-schedule_ID" onchange="update_displayed_schedule(this.value)">
<option value="">Select Schedule:</option>
';
//fill in the select
while ($row = $result_created_schedule->fetch_assoc())
{
$cs_ID = $row['ID'];
$cs_date = $row['date'];
echo 
'<option value="'.$cs_ID.'">'.$cs_date.'</option>';
}

echo 
'
</select>
</div>
';	

//CHOOSE STATION 	
echo 
'
<div id="schedule_page_content_2">
<select name="select-choice-station_ID" id="select-choice-station_ID" onchange="update_displayed_schedule_station()">
	<option value="">Select Station:</option>
';

$sql_stations = 
"
SELECT *
FROM `".$db."`.`schedule_station`
";
$result_stations = $link->query($sql_stations);
//fill in the select
while ($row = $result_stations->fetch_assoc())
{
$staion_ID = $row['ID'];
$station_name = $row['name'];
echo 
'<option value="'.$staion_ID.'">'.$station_name.'</option>';
}

echo '
</select>
</div>
';

//CHOOSE SHIFT	
echo 
'
<div id="schedule_page_content_3">
<select name="select-choice-shift_ID" id="select-choice-shift_ID" onchange="update_displayed_schedule_shift()">
	<option value="">Select Shift:</option>
	<option value="0">Day</option>
	<option value="1">Night</option>
</select>
</div>
';
	
//div2.
//will be placeholder for all the schedule information that is retrieved.
//get_schedule.php [schedule_id]
//this script will simply just print all the users names from 
//schedule_saved where schedule_id = [schedule_id]
echo 
'
<div id="schedule_page_content_4">
	
</div>

';




//Include database Termination Script
include 'db_disconnect.php';

?>
