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

class Schedule
{


//Retrieve an html list of all the facility IDs/names in ID order.
public function drop_down_menu_facility_list()
{
include 'db_connection.php';


$sql_facility_IDs = "SELECT * FROM `".$db."`.`schedule_facility` Order by ID";
$result_facility_IDs = $link->query($sql_facility_IDs);

$menu = "";
while ($row = $result_facility_IDs->fetch_assoc())
{
$menu = $menu . '<option value="'.$row['ID'].'">'.$row['name'].'</option>';
}

include 'db_disconnect.php';
return $menu;
}//function all_Facility_IDs_with_Names





//Retrieve an html list of all the stations IDs/Names in ID order
public function drop_down_menu_station_list()
{
include 'db_connection.php';

$sql_station_info = "SELECT * FROM `".$db."`.`schedule_station` Order by ID";
$result_station_info = $link->query($sql_station_info);

$menu = "";
while ($row = $result_station_info->fetch_assoc())
{
$menu = $menu . '<option value="'.$row['ID'].'">'.$row['name'].'</option>';
}

include 'db_disconnect.php';
return $menu;
}





//Creates a collapsible list view for a the requested schedule template
public function list_view_scheduled_template($template_ID)
{
include 'db_connection.php';
$mySchedule = new Schedule();

$list_view =  
'
<div data-inset="false">
		<ul data-role="listview">';
		
//RETREIVE TEMPLATE SCHEDULE POSITIONS
$sql_template_position_list_day = '
SELECT * 
FROM `'.$db.'`.`schedule_template_position_list`
WHERE ID_template = '.$template_ID;
$result_template_position_list_day = $link->query($sql_template_position_list_day);

while ($row = $result_template_position_list_day->fetch_assoc())
{
$stpl_ID = $row['ID'];
$stpl_ID_schedule_position = $row['ID_schedule_position'];
$stpl_quantity = $row['quantity'];
$stpl_facility_ID = $row['facility'];
$stpl_station_ID = $row['station'];
$stpl_facility_index = $mySchedule->get_index_for_facility($stpl_facility_ID);
$stpl_station_index = $mySchedule->get_index_for_station($stpl_station_ID);
//retrieve position name from schedule position table
$sql_position_name = '
SELECT name
FROM `'.$db.'`.`schedule_position`
WHERE ID = '.$stpl_ID_schedule_position;
$result_position_name = $link->query($sql_position_name);
$object_position_name = $result_position_name->fetch_assoc();
$stpl_position_name = $object_position_name['name'];

$list_view = $list_view . '
	<li>
		<a href="#popupEditPosition" 
			onClick="update_popup_edit_position('.$stpl_ID.', \''.$stpl_position_name.'\', '.$stpl_quantity.', '.$stpl_facility_index.', '.$stpl_station_index.')" 
			class="ui-icon-edit" data-rel="popup" 
			data-position-to="window" 
			data-transition="pop">'.$stpl_position_name.'<span class="ui-li-count">'.$stpl_quantity.' </span>
		</a>
	</li>';
	
		
}//while 

$list_view = $list_view . '	</ul>
</div><!-- /collapsible -->';



include 'db_disconnect.php';
return $list_view;
}



//Create the NEW popup menu for adding a new position to a template.  This is found in the load_settings_create_schedule_templates_position_list file.  
//On the settings_create_schedule_templates_page
public function popup_new_position_scheduled_template()
{
include 'db_connection.php';


//Echo the NEW POSITION POPUP
$popup =  '
	<div data-role="popup" id="popupNewPosition" data-theme="a" class="ui-corner-all">
    <form>
		<div style="padding: 5px 10px;">
		<h3>New Position for Template:</h3>
			<select name="select-choice-newPosition" id="select-choice-newPosition">
			<option value="">Choose Position</option>';
			
//RETRIEVE POSITIONS  FROM DATABSE
$sql_schedule_position = 
"
SELECT ID, name
FROM `".$db."`.`schedule_position`
";
$result_schedule_position = $link->query($sql_schedule_position);
//ECHO POSITION INTO SELECT LIST

while ($row = $result_schedule_position->fetch_assoc())
{
$position_ID = $row['ID'];
$position_name = $row['name'];


$popup = $popup . '<option value="'.$position_ID.'">'.$position_name.'</option>';
}		
$popup = $popup . '</select>
			<label for="slider-fill">Quantity:</label>
			<input type="range" name="slider-fill" id="slider-fill_newPosition" value="1" min="1" max="50" data-highlight="true">';

		/*	
		<fieldset data-role="controlgroup" data-type="horizontal" >
		    <legend>Shift:</legend>
		        <input type="radio" name="radio-choice_newPosition" id="radio-choice-day" value="0" checked="checked">
		        <label for="radio-choice-day">Day</label>
		        <input type="radio" name="radio-choice_newPosition" id="radio-choice-night" value="1">
		        <label for="radio-choice-night">Night</label>
		</fieldset>';*/

$mySchedule = new Schedule;		
$menu_facility = $mySchedule->drop_down_menu_facility_list();
$popup = $popup .	'<select name="select-choice-newFacility" id="select-choice-newFacility">';
$popup = $popup . $menu_facility;
$popup = $popup .	'</select>';


$popup = $popup . '<select name="select-choice-newStation" id="select-choice-newStation">';
$menu_stations = $mySchedule->drop_down_menu_station_list();
$popup = $popup . $menu_stations;	
$popup = $popup . '</select>';

$popup = $popup . '
		</br><hr>
		<a href="#settings_create_schedule_templates_page" data-role="button" data-icon="check" data-inline="true" onClick="update_settings_edit_template_position_new()">Save</a>
		<a href="#settings_create_schedule_templates_page" data-role="button"  data-inline="true">Cancel</a>

		</div>
	</form>
	';



include 'db_disconnect.php';
return $popup;
}




//Create the EDIT popup menu for EDITING a position on the template.  This is found in the load_settings_create_schedule_templates_position_list file.  
//On the settings_create_schedule_templates_page
public function popup_edit_position_scheduled_template()
{
//Echo the EDIT POSITION POPUP	
$edit_popup =  '
	<div data-role="popup" id="popupEditPosition" data-theme="a" class="ui-corner-all">
	<form>
		<div style="padding: 5px 10px;">
		<h3 id="editPositionHeader">Edit Position:<label></label></h3>
			<input type="hidden" id="hiddenPositionID"  value="">
			<label for="slider-fill">Quantity:</label>
			<input type="range" name="slider-fill" id="slider-fill_editPosition" value="1" min="1" max="50" data-highlight="true">';

		
$mySchedule = new Schedule;
$menu_facility = $mySchedule->drop_down_menu_facility_list();
$edit_popup = $edit_popup .	'<select name="select-choice-newFacility" id="select-choice-editFacility">';
$edit_popup = $edit_popup . $menu_facility;
$edit_popup = $edit_popup .	'</select>';


$edit_popup = $edit_popup . '<select name="select-choice-newStation" id="select-choice-editStation">';
$menu_stations = $mySchedule->drop_down_menu_station_list();
$edit_popup = $edit_popup . $menu_stations;	
$edit_popup = $edit_popup . '</select>';

$edit_popup = $edit_popup . '
		</br><hr>
		<a href="#settings_create_schedule_templates_page" data-role="button" data-icon="check" data-inline="true" onClick="update_settings_edit_template_position_save()">Save</a>
		<a href="#settings_create_schedule_templates_page" data-role="button" data-icon="delete" data-inline="true" onClick="update_settings_edit_template_position_delete()">Remove</a>
		<a href="#settings_create_schedule_templates_page" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-inline="true" class="show-page-loading-msg" data-textonly="true" data-textvisible="true" data-msgtext="Loading Positions..." onClick="update_settings_template_position_list_orderUP()"></a>
		<a href="#settings_create_schedule_templates_page" data-role="button" data-icon="arrow-d" data-iconpos="notext" data-inline="true" class="show-page-loading-msg" data-textonly="true" data-textvisible="true" data-msgtext="Loading Positions..." onClick="update_settings_template_position_list_orderDOWN()"></a>
		<a href="#settings_create_schedule_templates_page" data-role="button"  data-inline="true">Cancel</a>

		</div>
	</form>
	</div>
';
return $edit_popup;
}





//Provides a tool that can get an index for a facilty.  The index is used in the javascript to update popups with correct information.
private function get_index_for_facility($facility_ID)
{
include 'db_connection.php';

$index = 0;

//First select all of the facility IDs
$sql_index = 'set @row_number:=-1;'; 
$link->query($sql_index);
$sql_index = 'SELECT *, @row_number:=@row_number+1 as row_number FROM `'.$db.'`.`schedule_facility`;';
$result_index = $link->query($sql_index);

//Loop through selected facility ID's and add one to a temp_index.
while ($row = $result_index->fetch_assoc())
{

	if ($row['ID'] == $facility_ID){
	$index = $row['row_number'];
	}

}


include 'db_disconnect.php';
return $index;
}





//Provides a tool that can get an index for a station.  The index is used in the javascript to update popups with correct information.
private function get_index_for_station($station_ID)
{
include 'db_connection.php';

$index = 0;

//First select all of the facility IDs
$sql_index = 'set @row_number:=-1;'; 
$link->query($sql_index);
$sql_index = 'SELECT *, @row_number:=@row_number+1 as row_number FROM `'.$db.'`.`schedule_station`;';
$result_index = $link->query($sql_index);

//Loop through selected facility ID's and add one to a temp_index.
while ($row = $result_index->fetch_assoc())
{

	if ($row['ID'] == $station_ID){
	$index = $row['row_number'];
	}

}


include 'db_disconnect.php';
return $index;
}




//Creates a popup menu for the schedule templates page.  Allows for replicating a template.
public function popup_new_template()
{
include 'db_connection.php';

$list_view =  
'
<div data-role="popup" id="popupNewTemplate" data-theme="a" class="ui-corner-all">
<form>
	<div style="padding:10px 20px;">
		<h3>Create New Template</h3>
		
		<input type="text" id="newTemplateName" value="" placeholder="Name    Example: Cherries (Day/Night) " data-theme="a">
		<label for="select-choice_newTemplateReplication" class="select">Replication:</label>
		<select name="select-choice_newTemplateReplication" id="select-choice_newTemplateReplication">
			<option value="blank">Blank</option>';
		
			$sql_select_templates = "SELECT * FROM `".$db."`.`schedule_template`";
			$result_select_templates = $link->query($sql_select_templates);
			
			while ($object_select_templates = $result_select_templates->fetch_assoc())
			{
			$template_ID = $object_select_templates['ID'];
			$template_name = $object_select_templates['name'];
			$list_view = $list_view. '<option value="'.$template_ID.'">'.$template_name.'</option>';
			}
			
$list_view =  $list_view . '
		</select>
		
		</br><hr>
		<a href="#settings_schedule_templates_page" data-role="button" data-icon="check" data-inline="true" onClick="update_settings_schedule_template_new()">Save</a>
		<a href="#settings_schedule_templates_page" data-role="button"  data-inline="true">Cancel</a>
        </div>
    </form>
</div>
';
include 'db_disconnect.php';
return $list_view;
}


public function popup_edit_template()
{
include 'db_connection.php';

$popup =  
'
<div data-role="popup" id="popupEditTemplate" data-theme="a" class="ui-corner-all">
<form>
	<div style="padding:10px 20px;">
		<h3 id="editTemplateHeader">Edit Template:</h3>
		
		<input type="hidden" id="hiddenTemplateID"  value="">
		</br><hr>
		<a href="#settings_schedule_templates_page" data-role="button" data-icon="check" data-inline="true" onClick="update_settings_schedule_template_delete()">Delete</a>
		<a href="#settings_schedule_templates_page" data-role="button"  data-inline="true">Cancel</a>
        </div>
    </form>
</div>
';
include 'db_disconnect.php';
return $popup;
}


//Creates a list view that displays all the templates in the database.  Also adds in the functionality of deleting them
public function list_view_templates()
{
include 'db_connection.php';

$list_view = '<ul data-role="listview" data-filter="false" data-filter-placeholder="Search fruits..." data-inset="true">';

$sql_select_templates = "SELECT * FROM `".$db."`.`schedule_template`";
$result_select_templates = $link->query($sql_select_templates);

while ($object_select_templates = $result_select_templates->fetch_assoc())
{
$template_ID = $object_select_templates['ID'];
$template_name = $object_select_templates['name'];
$list_view = $list_view . 
'<li data-icon="edit">
<a href="#popupEditTemplate" 
			onClick="update_edit_popup_template('.$template_ID.', \''.$template_name.'\')" 
			class="ui-icon-edit" data-rel="popup" 
			data-position-to="window" 
			data-transition="pop">'.$template_name.
'</a>
</li>';



}
$list_view = $list_view . '</ul>';

include 'db_disconnect.php';
return $list_view;
}


//Delete a schedule template and removes positions associated with that template.
public function schedule_template_delete($template_ID)
{
include 'db_connection.php';

$sql = 'DELETE FROM `'.$db.'`.`schedule_template` WHERE ID = '.$template_ID;
$link->query($sql);

$sql2 = 'DELETE FROM `'.$db.'`.`schedule_template_position_list` WHERE ID_template = '.$template_ID;
$link->query($sql2);

include 'db_disconnect.php';
}




//Functions for the Schedule Setup Page

public function  load_schedule_setup_page()
{
//Include database Connection Script
include 'db_connection.php';


$date_today = date("Y-m-d");
$date_tomorrow = date("Y-m-d", strtotime($date_today . ' + 1 day') );
$page = 
'
<p>This is the Schedule Setup Page</p>
		<p>Fill in the following information to create a schedule:</p>
		
		<label for="text-basic">Date (YYYY-MM-DD):</label>
		<input type="text" name="text-basic" id="text-date" value="'.$date_tomorrow.'" placeholder="YYYY-MM-DD">
		<label for="select-choice-1" class="select">Schedule Template:</label>
		<select name="select-choice-1" id="select-schedule_template">';
		
$sql_select_templates = "SELECT * FROM `".$db."`.`schedule_template`";
$result_select_templates = $link->query($sql_select_templates);

while ($object_select_templates = $result_select_templates->fetch_assoc())
{
$template_ID = $object_select_templates['ID'];
$template_name = $object_select_templates['name'];
$page = $page . '<option value="'.$template_ID.'">'.$template_name.'</option>';
}
$page = $page . '</select>';
$page = $page . '<div data-role="content" id="schedule_setup_page_content2">';
$page = $page . '<a href="#" class="ui-btn ui-corner-all" id="anchor-button" onClick="create_schedule(document.getElementById(\'text-date\').value, document.getElementById(\'select-schedule_template\').value)">Create</a>';
$page = $page . '</div><!-- /content -->';


return $page;
//Include database Termination Script
include 'db_disconnect.php';
}


public function  load_schedule_setup_page2()
{
//Include database Connection Script
include 'db_connection.php';


$date_today = date("Y-m-d");
$date_tomorrow = date("Y-m-d", strtotime($date_today . ' + 1 day') );
$page = 
'
<p>This is the Schedule Setup Page</p>
		<p>Fill in the following information to create a schedule:</p>
		
		<label for="text-basic">Date (YYYY-MM-DD):</label>
		<input type="text" name="text-basic" id="text-date" value="'.$date_tomorrow.'" placeholder="YYYY-MM-DD">';

$page = $page .
'
<ul data-role="listview" data-inset="true" data-divider-theme="a">
	<li data-role="list-divider">Day</li>
		<li>
			<fieldset data-role="controlgroup">';
			
			$sql_templates = 'SELECT * FROM `'.$db.'`.`schedule_template`';
			$result_templates = $link->query($sql_templates);
			$temp_int = 0;
			while ($row = $result_templates->fetch_assoc())
			{
			$template_ID = $row['ID'];
			$template_name = $row['name'];
			$page = $page . '
				<input type="checkbox" name="checkbox-day'.$temp_int.'" id="checkbox-day'.$temp_int.'" value='.$template_ID.'>
				<label for="checkbox-day'.$temp_int.'">'.$template_name.'</label>';
			$temp_int++;
			}//while
				
$page = $page .'
			</fieldset>
		</li>


	<li data-role="list-divider">Night</li>
		<li>
			<fieldset data-role="controlgroup">';

			$sql_templates = 'SELECT * FROM `'.$db.'`.`schedule_template`';
			$result_templates = $link->query($sql_templates);
			$temp_int = 0;
			while ($row = $result_templates->fetch_assoc())
			{
			$template_ID = $row['ID'];
			$template_name = $row['name'];
			$page = $page . '
				<input type="checkbox" name="checkbox-night'.$temp_int.'" id="checkbox-night'.$temp_int.'" value='.$template_ID.'>
				<label for="checkbox-night'.$temp_int.'">'.$template_name.'</label>';
			$temp_int++;
			}//while	

$page = $page .'				
			</fieldset>
		</li>
</ul>
';


$page = $page . '<div data-role="content" id="schedule_setup_page_content2">';
$page = $page . '<a href="#" class="ui-btn ui-corner-all" id="anchor-button" onClick="create_schedule(document.getElementById(\'text-date\').value)">Create</a>';
$page = $page . '</div><!-- /content -->';


return $page;
//Include database Termination Script
include 'db_disconnect.php';
}
//END Functions for the Schedule Setup Page






//Function for making an Excel file and downloading it.
public function generateExcelSchedule($schedule_id)
{
//Include database Connection Script
include 'db_connection.php';
include 'schedule_functions.php';
//Retrieve POST Values
$new_schedule_value = $schedule_id;

//Retrive schedule date.
$sql_schedule_date = 
"
SELECT * 
FROM  `".$db."`.`schedule` 
WHERE ID = ".$new_schedule_value."
LIMIT 0 , 30";
$result_schedule_date = $link->query($sql_schedule_date);
$object_schedule_date = $result_schedule_date->fetch_assoc();
$schedule_date = $object_schedule_date['date'];
$temp_date = date_create_from_format('Y-m-d', $schedule_date);
$schedule_formated_date = date_format($temp_date, 'l F j, Y');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
//echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("AZ")
							 ->setLastModifiedBy("AZ")
							 ->setTitle("Schedule")
							 ->setSubject("Generated Schedule")
							 ->setDescription("")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Schedule");

// Add some data
//Setup the Three Pages.
$objPHPExcel->getActiveSheet()->setTitle('Card Display');
$objPHPExcel->getActiveSheet()->setBreak( 'J47' , PHPExcel_Worksheet::BREAK_COLUMN );
$objPHPExcel->getActiveSheet()->setBreak( 'S47' , PHPExcel_Worksheet::BREAK_COLUMN );
$objPHPExcel->getActiveSheet()->setBreak( 'AB47' , PHPExcel_Worksheet::BREAK_COLUMN );
$objPHPExcel->getActiveSheet()->setBreak( 'AK47' , PHPExcel_Worksheet::BREAK_COLUMN );
$objPHPExcel->getActiveSheet()->setBreak( 'AT47' , PHPExcel_Worksheet::BREAK_COLUMN );

$objPHPExcel->createSheet(1)->setTitle('List Display');


//DEV: 
$objPHPExcel->setActiveSheetIndex(0);

//Figure out if there is day and night schedules.  To keep day and night together.  All will be displayed on the same page.

//retrieve the shifts on the schedule.  Returns either 1 or 2.
//1 means either a day or night shift.
//2 means there is day and night shifts.
$sql_shifts = 
"SELECT count(distinct shift) as shifts
FROM `".$db."`.`schedule_saved`
WHERE `ID_schedule` = ".$new_schedule_value. "";
$result_shifts = $link->query($sql_shifts);
$object_shifts = $result_shifts->fetch_assoc();
$shifts = $object_shifts['shifts'];

//Only one shift to display. Either Day or Night
if ($shifts == 1)
{
	//Figure out what shift we are working with.  Either A Day or Night shift.  Will decide what colors to use.
	$sql_shift = 
	"SELECT distinct shift
	FROM `".$db."`.`schedule_saved`
	WHERE `ID_schedule` = ".$new_schedule_value. "";
	$result_shift = $link->query($sql_shift);
	$object_shift = $result_shift->fetch_assoc();
	$shift = $object_shift['shift'];//Returns 0 or 1.
	
	//Setup Color Based
	$color = "";
	$start_time = "";
	//if shift is day set to yellow else set to blue.
	if ($shift == 0)
	{
		$color = "FFFAF442";//Yellow
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "7:00AM - 3:30PM";
	}
	else 
	{
		$color = "FF3F7FBF";//Blue
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "4:00PM - 12:30AM";
	}
	
	//figure out how many schedules will be displayed. Determined by the amount of different stations.
	$sql_stations = 
	"SELECT count(distinct station) as stations
	FROM `".$db."`.`schedule_saved`
	WHERE `ID_schedule` = ".$new_schedule_value. "
	AND `shift` = ".$shift;
	$result_stations = $link->query($sql_stations);
	$object_stations = $result_stations->fetch_assoc();
	$stations = $object_stations['stations'];//Stations is how many different schedules to display.
	
	
	//Retrive a list of all the different stations_info.
	$sql_station_info = 
	"SELECT distinct st.ID, st.name
	FROM `".$db."`.`schedule_saved` ss, `".$db."`.`schedule_station` st
	WHERE ss.station = st.ID
	AND  ss.ID_schedule = ".$new_schedule_value."
	AND ss.shift = ".$shift;
	$result_station_info = $link->query($sql_station_info);
	
	
	
	
	
	//Cell Format Map.  Limit is 25 schedules.
	$header_1 = array("A2:I3", "J2:R3", "S2:AA3", "AB2:AJ3", "AK2:AS3", "AT2:BB3", "BC2:BK3", "BL2:BT3", "BU2:CC3", "CD2:CL3", "CM2:CU3", "CV2:DD3", "DE2:DM3", "DN2:DV3", "DW2:EE3", "EF2:EN3", "EO2:EW3", "EX2:FF3", "FG2:FO3", "FP2:FX3", "FY2:GG3", "GH2:GP3", "GQ2:GY3", "GZ2:HH3");
	$header_2 = array("A5:I5", "J5:R5", "S5:AA5", "AB5:AJ5", "AK5:AS5", "AT5:BB5", "BC5:BK5", "BL5:BT5", "BU5:CC5", "CD5:CL5", "CM5:CU5", "CV5:DD5", "DE5:DM5", "DN5:DV5", "DW5:EE5", "EF5:EN5", "EO5:EW5", "EX5:FF5", "FG5:FO5", "FP5:FX5", "FY5:GG5", "GH5:GP5", "GQ5:GY5", "GZ5:HH5");
	$header_3 = array("A2", "J2", "S2", "AB2", "AK2", "AT2", "BC2", "BL2", "BU2", "CD2", "CM2", "CV2", "DE2", "DN2", "DW2", "EF2", "EO2", "EX2", "FG2", "FP2", "FY2", "GH2", "GQ2", "GZ2");
	$header_4 = array("A5", "J5", "S5", "AB5", "AK5", "AT5", "BC5", "BL5", "BU5", "CD5", "CM5", "CV5", "DE5", "DN5", "DW5", "EF5", "EO5", "EX5", "FG5", "FP5", "FY5", "GH5", "GQ5", "GZ5");
	
	
	//Loop through the schedules and format them into the excel object.  Displaying the headers.
	//if stations is more than 25, there will be problem...
	//$tmp = 0; $tmp < $stations; $tmp++
	//$page_number = 0 and increments by 9.  This will determine where to start printing poeple onto the schedule.
	$tmp = 0;
	$page_number = 0; 
	while ($row = $result_station_info->fetch_assoc())
	{
		$station_ID = $row['ID'];
		$station_name = $row['name'];
		
		//Setup A new Header.
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_1[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_2[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		    
		$objPHPExcel->setActiveSheetIndex(0)
		      		->setCellValue($header_3[$tmp], $schedule_formated_date)
		      		->getStyle($header_3[$tmp])->getFont()->setBold(true)->setSize(16);
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_3[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue($header_4[$tmp], $station_name." ". $start_time)
		            ->getStyle($header_4[$tmp])->getFont()->setBold(true)->setSize(11);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_4[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		
		//Now Echo out a list of the employees on this segment of the schedule.
		$sql_positions = 
		"
		SELECT distinct ID_schedule_position
		FROM `".$db."`.`schedule_saved` 
		WHERE ID_schedule = ".$new_schedule_value."
		AND shift = ".$shift."
		AND station = ".$station_ID."
		order by weight ASC
		";
		$result_positions = $link->query($sql_positions);
		
		//Debugging info
		//echo $sql_positions;
		//echo "</br></br></br>";
		
		
		//Put the position into an array to call when ready.
		$positions_array = array();
		$position_ID_array = array();
		$temp_position_array = 0;
		while ($row = $result_positions->fetch_assoc())
		{
			$sql_position_name =
			"
			SELECT ID, name
			FROM `".$db."`.`schedule_position`
			WHERE ID = ".$row['ID_schedule_position'];
			$result_position_name = $link->query($sql_position_name);
			$object_position_name = $result_position_name->fetch_assoc();
			$position_name = $object_position_name['name'];
			$position_ID = $object_position_name['ID'];
			
			$positions_array[$temp_position_array] = $position_name;
			$position_ID_array[$temp_position_array] = $position_ID;
			$temp_position_array++;
		}
		/*
		print_r ($position_ID_array);
		echo "</br>";
		print_r ($positions_array);
		echo "</br></br></br>";
		*/
		//while x, y+position, emp name
		$excel_column = $page_number;
		$excel_row = 7;
		$array_index = 0;
		//Start with columns and then work down before adding to the next column.
		while ($excel_column < $excel_column + 8 )
		{
			while($excel_row < 46)
			{
				
				//Check if array index is valid, otherwise break out of the loops.
				if (!array_key_exists($array_index, $positions_array))
				{
					//Increment the position array index.
					//echo "</br> array increment is now: ". $array_index;
					break 2;//Finished printing, break out of the printing cycle.
				}
				
				//Print Position.
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $positions_array[$array_index]);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(10);
				
				$excel_row++;//Increment to move onto next row
				
				//SQL EMPS in that position.
				$sql_emps = 
				"
				SELECT ID_employee 
				FROM `".$db."`.`schedule_saved` 
				WHERE ID_schedule = ".$new_schedule_value."
				AND shift = ".$shift."
				AND station = ".$station_ID."
				AND ID_schedule_position = ".$position_ID_array[$array_index]."
				order by ID_employee
				";
				
				//echo $sql_emps;
				$result_emps = $link->query($sql_emps);
				//WHILE EMP PRINT NAME.
				while ($row = $result_emps->fetch_assoc())
				{
					$employee_ID = $row['ID_employee'];
					//Check here if there is employees with id = 0.  If so then we will set the senority, first_name, last_name manually.  These are positions that are unfilled upon schedule generation.
					//Retrieve extra information  (employee information/ position information)
					$employee_senority = "";
					$employee_first_name = "";
					$employee_last_name = "";
					if ($employee_ID == 0)
					{
						$employee_senority = 0;
						$employee_first_name = "UNFILLED";
						$employee_last_name = "POSITION";
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
						$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
			
					}
					else
					{
					$sql_employee_information = " 
					SELECT senority, first_name, last_name
					FROM `".$db."`.`employee`
					WHERE ID = ".$employee_ID;
					$result_employee_information = $link->query($sql_employee_information);
					$object_employee_information = $result_employee_information->fetch_assoc();
					$employee_senority = $object_employee_information['senority'];
					$employee_first_name = $object_employee_information['first_name'];
					$employee_last_name = $object_employee_information['last_name'];
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
					
					}
					
					$excel_row++;//Increment to move onto next row
				}
				$excel_row++;//Leave a space and move onto the next position.
				
				
				$array_index++;
			
				
			}
			$excel_column = $excel_column + 2;// Increment X
			$excel_row = 7;//Reset Y
		}
		
		
		
	$page_number = $page_number + 9;//Incrment the page after displaying all the people on the schedule.	
	$tmp++;//increment coutner for the array headers.
	}
}//end (shift = 1)


//Two shifts to display.  Both Day and Night
elseif  ($shift = 2)
{
	
	
	//**********************************************************************************************
	//Start with Day Shift.
	$shift = 0;
	$tmp = 0;//this will keep track of array cell placement.
	//Setup Color Based
	$color = "";
	$start_time = "";
	//if shift is day set to yellow else set to blue.
	if ($shift == 0)
	{
		$color = "FFFAF442";//Yellow
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "7:00AM - 3:30PM";
	}
	else 
	{
		$color = "FF3F7FBF";//Blue
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "4:00PM - 12:30AM";
	}
	
	//figure out how many schedules will be displayed. Determined by the amount of different stations.
	$sql_stations = 
	"SELECT count(distinct station) as stations
	FROM `".$db."`.`schedule_saved`
	WHERE `ID_schedule` = ".$new_schedule_value. "
	AND `shift` = ".$shift;
	$result_stations = $link->query($sql_stations);
	$object_stations = $result_stations->fetch_assoc();
	$stations = $object_stations['stations'];//Stations is how many different schedules to display.
	
	
	//Retrive a list of all the different stations_info.
	$sql_station_info = 
	"SELECT distinct st.ID, st.name
	FROM `".$db."`.`schedule_saved` ss, `".$db."`.`schedule_station` st
	WHERE ss.station = st.ID
	AND  ss.ID_schedule = ".$new_schedule_value."
	AND ss.shift = ".$shift;
	$result_station_info = $link->query($sql_station_info);
	
	
	
	
	//Cell Format Map.  Limit is 25 schedules.
	$header_1 = array("A2:I3", "J2:R3", "S2:AA3", "AB2:AJ3", "AK2:AS3", "AT2:BB3", "BC2:BK3", "BL2:BT3", "BU2:CC3", "CD2:CL3", "CM2:CU3", "CV2:DD3", "DE2:DM3", "DN2:DV3", "DW2:EE3", "EF2:EN3", "EO2:EW3", "EX2:FF3", "FG2:FO3", "FP2:FX3", "FY2:GG3", "GH2:GP3", "GQ2:GY3", "GZ2:HH3");
	$header_2 = array("A5:I5", "J5:R5", "S5:AA5", "AB5:AJ5", "AK5:AS5", "AT5:BB5", "BC5:BK5", "BL5:BT5", "BU5:CC5", "CD5:CL5", "CM5:CU5", "CV5:DD5", "DE5:DM5", "DN5:DV5", "DW5:EE5", "EF5:EN5", "EO5:EW5", "EX5:FF5", "FG5:FO5", "FP5:FX5", "FY5:GG5", "GH5:GP5", "GQ5:GY5", "GZ5:HH5");
	$header_3 = array("A2", "J2", "S2", "AB2", "AK2", "AT2", "BC2", "BL2", "BU2", "CD2", "CM2", "CV2", "DE2", "DN2", "DW2", "EF2", "EO2", "EX2", "FG2", "FP2", "FY2", "GH2", "GQ2", "GZ2");
	$header_4 = array("A5", "J5", "S5", "AB5", "AK5", "AT5", "BC5", "BL5", "BU5", "CD5", "CM5", "CV5", "DE5", "DN5", "DW5", "EF5", "EO5", "EX5", "FG5", "FP5", "FY5", "GH5", "GQ5", "GZ5");
	
	
	//Loop through the schedules and format them into the excel object.  Displaying the headers.
	//if stations is more than 25, there will be problem...
	//$tmp = 0; $tmp < $stations; $tmp++
	//$page_number = 0 and increments by 9.  This will determine where to start printing poeple onto the schedule.
	$tmp = 0;
	$page_number = 0; 
	while ($row = $result_station_info->fetch_assoc())
	{
		$station_ID = $row['ID'];
		$station_name = $row['name'];
		
		//Setup A new Header.
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_1[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_2[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		    
		$objPHPExcel->setActiveSheetIndex(0)
		      		->setCellValue($header_3[$tmp], $schedule_formated_date)
		      		->getStyle($header_3[$tmp])->getFont()->setBold(true)->setSize(16);
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_3[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue($header_4[$tmp], $station_name." ". $start_time)
		            ->getStyle($header_4[$tmp])->getFont()->setBold(true)->setSize(11);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_4[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		
		//Now Echo out a list of the employees on this segment of the schedule.
		$sql_positions = 
		"
		SELECT distinct ID_schedule_position
		FROM `".$db."`.`schedule_saved` 
		WHERE ID_schedule = ".$new_schedule_value."
		AND shift = ".$shift."
		AND station = ".$station_ID."
		order by weight ASC
		";
		$result_positions = $link->query($sql_positions);
		
		//echo $sql_positions;
		//echo "</br></br></br>";
		
		
		//Put the position into an array to call when ready.
		$positions_array = array();
		$position_ID_array = array();
		$temp_position_array = 0;
		while ($row = $result_positions->fetch_assoc())
		{
			$sql_position_name =
			"
			SELECT ID, name
			FROM `".$db."`.`schedule_position`
			WHERE ID = ".$row['ID_schedule_position'];
			$result_position_name = $link->query($sql_position_name);
			$object_position_name = $result_position_name->fetch_assoc();
			$position_name = $object_position_name['name'];
			$position_ID = $object_position_name['ID'];
			
			$positions_array[$temp_position_array] = $position_name;
			$position_ID_array[$temp_position_array] = $position_ID;
			$temp_position_array++;
		}
		/*
		print_r ($position_ID_array);
		echo "</br>";
		print_r ($positions_array);
		echo "</br></br></br>";
		*/
		//while x, y+position, emp name
		$excel_column = $page_number;
		$excel_row = 7;
		$array_index = 0;
		//Start with columns and then work down before adding to the next column.
		while ($excel_column < $excel_column + 8 )
		{
			while($excel_row < 46)
			{
				
				//Check if array index is valid, otherwise break out of the loops.
				if (!array_key_exists($array_index, $positions_array))
				{
					//Increment the position array index.
					//echo "</br> array increment is now: ". $array_index;
					break 2;//Finished printing, break out of the printing cycle.
				}
				
				//Print Position.
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $positions_array[$array_index]);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(10);
				
				$excel_row++;//Increment to move onto next row
				
				//SQL EMPS in that position.
				$sql_emps = 
				"
				SELECT ID_employee 
				FROM `".$db."`.`schedule_saved` 
				WHERE ID_schedule = ".$new_schedule_value."
				AND shift = ".$shift."
				AND station = ".$station_ID."
				AND ID_schedule_position = ".$position_ID_array[$array_index]."
				order by ID_employee
				";
				
				//echo $sql_emps;
				$result_emps = $link->query($sql_emps);
				//WHILE EMP PRINT NAME.
				while ($row = $result_emps->fetch_assoc())
				{
					$employee_ID = $row['ID_employee'];
					//Check here if there is employees with id = 0.  If so then we will set the senority, first_name, last_name manually.  These are positions that are unfilled upon schedule generation.
					//Retrieve extra information  (employee information/ position information)
					$employee_senority = "";
					$employee_first_name = "";
					$employee_last_name = "";
					if ($employee_ID == 0)
					{
						$employee_senority = 0;
						$employee_first_name = "UNFILLED";
						$employee_last_name = "POSITION";
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
						$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
			
					}
					else
					{
					$sql_employee_information = " 
					SELECT senority, first_name, last_name
					FROM `".$db."`.`employee`
					WHERE ID = ".$employee_ID;
					$result_employee_information = $link->query($sql_employee_information);
					$object_employee_information = $result_employee_information->fetch_assoc();
					$employee_senority = $object_employee_information['senority'];
					$employee_first_name = $object_employee_information['first_name'];
					$employee_last_name = $object_employee_information['last_name'];
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
					
					}
					
					$excel_row++;//Increment to move onto next row
				}
				$excel_row++;//Leave a space and move onto the next position.
				
				
				$array_index++;
			
				
			}
			$excel_column = $excel_column + 2;// Increment X
			$excel_row = 7;//Reset Y
		}
		
		
		
	$page_number = $page_number + 9;//Incrment the page after displaying all the people on the schedule.	
	$tmp++;//increment coutner for the array headers.

	}
	
	
	
	
	//***********************************************************************
	//Conitnue on with Night Shift.
	$shift = 1;
	//Setup Color Based
	$color = "";
	$start_time = "";
	//if shift is day set to yellow else set to blue.
	if ($shift == 0)
	{
		$color = "FFFAF442";//Yellow
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "7:00AM - 3:30PM";
	}
	else 
	{
		$color = "FF3F7FBF";//Blue
		//Set the start time.  Static for now, the future this should be setting in database.
		$start_time = "4:00PM - 12:30AM";
	}
	
	//figure out how many schedules will be displayed. Determined by the amount of different stations.
	$sql_stations = 
	"SELECT count(distinct station) as stations
	FROM `".$db."`.`schedule_saved`
	WHERE `ID_schedule` = ".$new_schedule_value. "
	AND `shift` = ".$shift;
	$result_stations = $link->query($sql_stations);
	$object_stations = $result_stations->fetch_assoc();
	$stations = $object_stations['stations'];//Stations is how many different schedules to display.
	
	
	//Retrive a list of all the different stations_info.
	$sql_station_info = 
	"SELECT distinct st.ID, st.name
	FROM `".$db."`.`schedule_saved` ss, `".$db."`.`schedule_station` st
	WHERE ss.station = st.ID
	AND  ss.ID_schedule = ".$new_schedule_value."
	AND ss.shift = ".$shift;
	$result_station_info = $link->query($sql_station_info);
	
	
	
	
	//Cell Format Map.  Limit is 25 schedules.
	$header_1 = array("A2:I3", "J2:R3", "S2:AA3", "AB2:AJ3", "AK2:AS3", "AT2:BB3", "BC2:BK3", "BL2:BT3", "BU2:CC3", "CD2:CL3", "CM2:CU3", "CV2:DD3", "DE2:DM3", "DN2:DV3", "DW2:EE3", "EF2:EN3", "EO2:EW3", "EX2:FF3", "FG2:FO3", "FP2:FX3", "FY2:GG3", "GH2:GP3", "GQ2:GY3", "GZ2:HH3");
	$header_2 = array("A5:I5", "J5:R5", "S5:AA5", "AB5:AJ5", "AK5:AS5", "AT5:BB5", "BC5:BK5", "BL5:BT5", "BU5:CC5", "CD5:CL5", "CM5:CU5", "CV5:DD5", "DE5:DM5", "DN5:DV5", "DW5:EE5", "EF5:EN5", "EO5:EW5", "EX5:FF5", "FG5:FO5", "FP5:FX5", "FY5:GG5", "GH5:GP5", "GQ5:GY5", "GZ5:HH5");
	$header_3 = array("A2", "J2", "S2", "AB2", "AK2", "AT2", "BC2", "BL2", "BU2", "CD2", "CM2", "CV2", "DE2", "DN2", "DW2", "EF2", "EO2", "EX2", "FG2", "FP2", "FY2", "GH2", "GQ2", "GZ2");
	$header_4 = array("A5", "J5", "S5", "AB5", "AK5", "AT5", "BC5", "BL5", "BU5", "CD5", "CM5", "CV5", "DE5", "DN5", "DW5", "EF5", "EO5", "EX5", "FG5", "FP5", "FY5", "GH5", "GQ5", "GZ5");
	
	
	//Loop through the schedules and format them into the excel object.  Displaying the headers.
	//if stations is more than 25, there will be problem...
	//$tmp = 0; $tmp < $stations; $tmp++
	//$page_number = 0 and increments by 9.  This will determine where to start printing poeple onto the schedule.
	
	# $tmp = 0;
	# $page_number = 0; 
	# IF the above variable are rest to zero then the night shift schedules will overwrite the day shift schedules.  With them commented out the schedule Page will continue 
	# where ever it left off.  
	while ($row = $result_station_info->fetch_assoc())
	{
		$station_ID = $row['ID'];
		$station_name = $row['name'];
		
		//Setup A new Header.
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_1[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_1[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($header_2[$tmp]);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])->getFill()
		    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		    ->getStartColor()->setARGB($color);
		    
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle($header_2[$tmp])
		    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		    
		$objPHPExcel->setActiveSheetIndex(0)
		      		->setCellValue($header_3[$tmp], $schedule_formated_date)
		      		->getStyle($header_3[$tmp])->getFont()->setBold(true)->setSize(16);
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_3[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue($header_4[$tmp], $station_name." ". $start_time)
		            ->getStyle($header_4[$tmp])->getFont()->setBold(true)->setSize(11);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($header_4[$tmp])->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
		);
		
		
		//Now Echo out a list of the employees on this segment of the schedule.
		$sql_positions = 
		"
		SELECT distinct ID_schedule_position
		FROM `".$db."`.`schedule_saved` 
		WHERE ID_schedule = ".$new_schedule_value."
		AND shift = ".$shift."
		AND station = ".$station_ID."
		order by weight ASC
		";
		$result_positions = $link->query($sql_positions);
		
		//Debugging How many schedules get made
		//echo $sql_positions;
		//echo "</br></br></br>";
		
		
		//Put the position into an array to call when ready.
		$positions_array = array();
		$position_ID_array = array();
		$temp_position_array = 0;
		while ($row = $result_positions->fetch_assoc())
		{
			$sql_position_name =
			"
			SELECT ID, name
			FROM `".$db."`.`schedule_position`
			WHERE ID = ".$row['ID_schedule_position'];
			$result_position_name = $link->query($sql_position_name);
			$object_position_name = $result_position_name->fetch_assoc();
			$position_name = $object_position_name['name'];
			$position_ID = $object_position_name['ID'];
			
			$positions_array[$temp_position_array] = $position_name;
			$position_ID_array[$temp_position_array] = $position_ID;
			$temp_position_array++;
		}
		/*
		print_r ($position_ID_array);
		echo "</br>";
		print_r ($positions_array);
		echo "</br></br></br>";
		*/
		//while x, y+position, emp name
		$excel_column = $page_number;
		$excel_row = 7;
		$array_index = 0;
		//Start with columns and then work down before adding to the next column.
		while ($excel_column < $excel_column + 8 )
		{
			while($excel_row < 46)
			{
				
				//Check if array index is valid, otherwise break out of the loops.
				if (!array_key_exists($array_index, $positions_array))
				{
					//Increment the position array index.
					//echo "</br> array increment is now: ". $array_index;
					break 2;//Finished printing, break out of the printing cycle.
				}
				
				//Print Position.
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $positions_array[$array_index]);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(10);
				
				$excel_row++;//Increment to move onto next row
				
				//SQL EMPS in that position.
				$sql_emps = 
				"
				SELECT ID_employee 
				FROM `".$db."`.`schedule_saved` 
				WHERE ID_schedule = ".$new_schedule_value."
				AND shift = ".$shift."
				AND station = ".$station_ID."
				AND ID_schedule_position = ".$position_ID_array[$array_index]."
				order by ID_employee
				";
				
				//echo $sql_emps;
				$result_emps = $link->query($sql_emps);
				//WHILE EMP PRINT NAME.
				while ($row = $result_emps->fetch_assoc())
				{
					$employee_ID = $row['ID_employee'];
					//Check here if there is employees with id = 0.  If so then we will set the senority, first_name, last_name manually.  These are positions that are unfilled upon schedule generation.
					//Retrieve extra information  (employee information/ position information)
					$employee_senority = "";
					$employee_first_name = "";
					$employee_last_name = "";
					if ($employee_ID == 0)
					{
						$employee_senority = 0;
						$employee_first_name = "UNFILLED";
						$employee_last_name = "POSITION";
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
						$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
			
					}
					else
					{
					$sql_employee_information = " 
					SELECT senority, first_name, last_name
					FROM `".$db."`.`employee`
					WHERE ID = ".$employee_ID;
					$result_employee_information = $link->query($sql_employee_information);
					$object_employee_information = $result_employee_information->fetch_assoc();
					$employee_senority = $object_employee_information['senority'];
					$employee_first_name = $object_employee_information['first_name'];
					$employee_last_name = $object_employee_information['last_name'];
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, $employee_first_name ." ". $employee_last_name);
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($excel_column, $excel_row)->getFont()->setSize(9);
					
					}
					
					$excel_row++;//Increment to move onto next row
				}
				$excel_row++;//Leave a space and move onto the next position.
				
				
				$array_index++;
			
				
			}
			$excel_column = $excel_column + 2;// Increment X
			$excel_row = 7;//Reset Y
		}
		
		
		
	$page_number = $page_number + 9;//Incrment the page after displaying all the people on the schedule.	
	$tmp++;//increment coutner for the array headers.
	}
	
}
else
{
	//Dont display anthing. Error Happened.  Should never get here.
	echo "Stations was not equal to 1 or 2.  So either NULL, '',  or 3 or higher.";
	echo "This error should not be here.";
}




//Move onto sheeet two and output the schedule again in a list format.
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'Position')
            ->setCellValue('B1', 'First Name')
            ->setCellValue('C1', 'Last Name')
            ->setCellValue('D1', 'Shift')
            ->setCellValue('E1', 'Location')
            ->setCellValue('F1', 'Station');

//Loop through a schedule and add peoples name sinto the cells.  Increment cell value each time.

//retrieve the schedule.
$sql_schedule = 
"SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE `ID_schedule` = ".$new_schedule_value. " 
ORDER BY `schedule_saved`.`ID_schedule_position` ASC, `schedule_saved`.`ID_employee` ASC";
$result_schedule = $link->query($sql_schedule);

$cell_value = 2;

//Loop through the people in the schedule
while ($row = $result_schedule->fetch_assoc())
{
//Setup Variables.
$employee_ID = $row['ID_employee'];
$schedule_position_ID = $row['ID_schedule_position'];
$shift = $row['shift'];
$shift_english;
if ($shift == 0)
$shift_english = "Day";
else
$shift_english = "Afternoon";
$facility_ID = $row['facility'];
$station_ID = $row['station'];

//Check here if there is employees with id = 0.  If so then we will set the senority, first_name, last_name manually.  These are positions that are unfilled upon schedule generation.
//Retrieve extra information  (employee information/ position information)
$employee_senority = "";
$employee_first_name = "";
$employee_last_name = "";
if ($employee_ID == 0)
{
	$employee_senority = 0;
	$employee_first_name = "UNFILLED POSITION";
	$employee_last_name = "UNFILLED POSITION";
}
else
{
$sql_employee_information = " 
SELECT senority, first_name, last_name
FROM `".$db."`.`employee`
WHERE ID = ".$employee_ID;
$result_employee_information = $link->query($sql_employee_information);
$object_employee_information = $result_employee_information->fetch_assoc();
$employee_senority = $object_employee_information['senority'];
$employee_first_name = $object_employee_information['first_name'];
$employee_last_name = $object_employee_information['last_name'];
}
//Retrieve position name 
$sql_position_information = "
SELECT name
FROM `".$db."`.`schedule_position`
WHERE ID = ".$schedule_position_ID;
//echo $sql_position_information;
$result_position_information = $link->query($sql_position_information);
$object_position_information = $result_position_information->fetch_assoc();
$schedule_position_name = $object_position_information['name'];



//Write the user into the spreadsheet.
$objPHPExcel->setActiveSheetIndex(1)
			->setCellValue('A'.$cell_value, $schedule_position_name)
            ->setCellValue('B'.$cell_value, $employee_first_name)
            ->setCellValue('C'.$cell_value, $employee_last_name)
            ->setCellValue('D'.$cell_value, $shift_english)
            ->setCellValue('E'.$cell_value, findFacilityName($facility_ID))
            ->setCellValue('F'.$cell_value, findStationName($station_ID));

//echo "cell value = " . $cell_value;
//echo "shift value = " . $shift;
$cell_value = $cell_value + 1;
}//End While




//Display the Card View upon opening the schedule.
$objPHPExcel->setActiveSheetIndex(0);

// Save Excel 2007 file
//echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('Schedule.xlsx');

echo 'The schedule has been created, click to ';
echo '<a href="Schedule.xlsx" target="_blank" >Download</a>';
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;


}//END function generateExcelSchedule($schedule_id)




}//class Schedule

?>