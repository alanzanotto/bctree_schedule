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

//Retieve all the schedule templates.
$sql_templates = '
SELECT *
FROM `'.$db.'`.`schedule_template`
ORDER BY ID ASC
';
$result_templates = $link->query($sql_templates);


echo '<select name="select-choice-template" id="select-choice-template" onChange="load_settings_create_schedule_template_position_list(this.value)">';
echo '<option value="">Select</option>';
while ($row = $result_templates->fetch_assoc())
{
$template_ID = $row['ID'];
$template_name = $row['name'];

	echo '<option value="'.$template_ID.'">'.$template_name.'</option>';
			
}
echo '</select>';


//Include database Termination Script
include 'db_disconnect.php';
?>