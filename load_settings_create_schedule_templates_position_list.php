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
include 'myClasses.php';

//Retrieve POST Values
$template_ID = $_POST["requested_template_ID"];

if ( $template_ID == "" )
{
echo "Please Select a template to display...";
}
else
{
//echo "The template ID is set to: " . $template_ID;


//DISPLAY list view for a particular template
$mySchedule = new Schedule;
echo $mySchedule->list_view_scheduled_template($template_ID);



//DISPLAY new Popup
echo $mySchedule->popup_new_position_scheduled_template();



//DISPLAY edit popup 
echo $mySchedule->popup_edit_position_scheduled_template();


}//else




//Include database Termination Script
include 'db_disconnect.php';
?>