<?php
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

		/*
		<fieldset data-role="controlgroup" data-type="horizontal" >
		    <legend>Shift:</legend>
		        <input type="radio" name="radio-choice_editPosition" id="radio-choice-day_editPosition" value="0">
		        <label for="radio-choice-day_editPosition">Day</label>
		        <input type="radio" name="radio-choice_editPosition" id="radio-choice-night_editPosition" value="1">
		        <label for="radio-choice-night_editPosition">Night</label>
		</fieldset>';*/
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




//Function for displaying a scheudle.
public function display_schedule_winfield_layout($schedule_id)
{
//Include database Connection Script
include 'db_connection.php';

//Retrieve POST Values
$new_schedule_value = $schedule_id;

//Variables
$temp_category;


//Pre Size & A/B Line
echo '
<div class="customPrintPage">
	<div class="customHeaderDay">Tuesday </br> August 25, 2015 7:00AM - 3:30PM</div>
	
		<div class="customStationHeader">PRESIZE</div>
		<div class="customPositionArea">
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
		</div>
		
		
		<div class="customStationHeader">A / B LINE</div>
		<div class="customPositionArea">
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			
		</div>	
		
</div>
';


//Pre Size & A/B Line
echo '
<div class="customPrintPage">
	<div class="customHeaderDay">Tuesday </br> August 25, 2015 7:00AM - 3:30PM</div>
	
		<div class="customStationHeader">PRESIZE</div>
		<div class="customPositionArea">
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
		</div>
		
		
		<div class="customStationHeader">A / B LINE</div>
		<div class="customPositionArea">
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			<div class="customPositions">Alan Zanotto | Dumper Operator</div>
			
		</div>	
		
</div>
';


/*

echo 
'
<div class="customPrintPage">
<div class="customHeaderDay">Tuesday </br> August 25, 2015 7:00AM - 3:30PM</div>
<div class="customStationHeader">PRESIZE</div>
<div class="customStationHeader">A / B LINE</div>


</div>


<div class="customPrintPage">
<div class="customHeaderDay">Tuesday </br> August 25, 2015 7:00AM - 3:30PM</div>
<div class="customStationHeader">PRESIZE</div>
<div class="customStationHeader">A / B LINE</div>

</div>
';
*/









/* COMMENTING OUT THE SCHEDULE PART TO BUILD THE TEMPLATES
//retrieve the schedule.
$sql_schedule = 
"SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE `ID_schedule` = ".$new_schedule_value. "
ORDER BY `schedule_saved`.`ID_schedule_position` ASC, `schedule_saved`.`ID_employee` ASC";
$result_schedule = $link->query($sql_schedule);

//echo $sql_schedule;




//temp table column to expand when 9 people in a row.
$temp_table_column = 0;
//Loop through the people in the schedule
while ($row = $result_schedule->fetch_assoc())
{
//Setup Variables.
$employee_ID = $row['ID_employee'];
$schedule_position_ID = $row['ID_schedule_position'];
$shift = $row['shift'];

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



if ( !isset($temp_category) )
{
$temp_category = $schedule_position_name;
echo '<div class="schedule_list_block" id="schedule_list_block">
<h4>'.$temp_category.'</h4>';//start a fresh category

//echo "(". $employee_senority .") ". $employee_first_name . " ". $employee_last_name .'</br>';//OLIVER
echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD

}

elseif ($temp_category != $schedule_position_name)
{
echo '</div>';//close up last category
$temp_category = $schedule_position_name;//set new temp category

echo '<div class="schedule_list_block">
<h4>'.$temp_category.'</h4>';//start a fresh category

//echo "(". $employee_senority .") ". $employee_first_name . " ". $employee_last_name .'</br>';//OLIVER
echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD

}

else
{
	//echo "(". $employee_senority .") ". $employee_first_name . " ". $employee_last_name .'</br>';//OLIVER
	echo "&#9744". $employee_first_name . " ". $employee_last_name .'</br>';//WINFIELD
}


}//while loop

*/


//Include database Termination Script
include 'db_disconnect.php';

}


//Function for making an Excel file and downloading it.
public function generateExcelSchedule($schedule_id)
{
//Include database Connection Script
include 'db_connection.php';
include 'schedule_functions.php';
//Retrieve POST Values
$new_schedule_value = $schedule_id;


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


//Setup Day Shift Production Header.
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:I3');
$objPHPExcel->getActiveSheet()->getStyle('A2:I3')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('A2:I3')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A2:I3')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A2:I3')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A2:I3')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A5:I5');
$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('A5:I5')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A5:I5')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A5:I5')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('A5:I5')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    
$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValue('A2', 'Tuesday, July 21, 2015')
      		->getStyle('A2')->getFont()->setBold(true)->setSize(16);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A5', 'Cherry Production 7:00AM - 3:30PM')
            ->getStyle('A5')->getFont()->setBold(true)->setSize(11);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A5')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);



//Setup Day Shift Sorting Header.
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('J2:R3');
$objPHPExcel->getActiveSheet()->getStyle('J2:R3')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('J2:R3')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J2:R3')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J2:R3')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J2:R3')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('J5:R5');
$objPHPExcel->getActiveSheet()->getStyle('J5:R5')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('J5:R5')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J5:R5')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J5:R5')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('J5:R5')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    
$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValue('J2', 'Tuesday, July 21, 2015')
      		->getStyle('J2')->getFont()->setBold(true)->setSize(16);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('J2')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J5', 'Cherry Sorting 7:00AM - 3:30PM')
            ->getStyle('J5')->getFont()->setBold(true)->setSize(11);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('J5')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);


//Setup Day Shift Operations Header.
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S2:AA3');
$objPHPExcel->getActiveSheet()->getStyle('S2:AA3')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('S2:AA3')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S2:AA3')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S2:AA3')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S2:AA3')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S5:AA5');
$objPHPExcel->getActiveSheet()->getStyle('S5:AA5')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
    
$objPHPExcel->getActiveSheet()->getStyle('S5:AA5')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S5:AA5')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S5:AA5')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('S5:AA5')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    
$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValue('S2', 'Tuesday, July 21, 2015')
      		->getStyle('S2')->getFont()->setBold(true)->setSize(16);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('S2')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('S5', 'Vaughan Operations 7:00AM - 3:30PM')
            ->getStyle('S5')->getFont()->setBold(true)->setSize(11);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('S5')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);


//Setup Night Shift Production Header.
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AB2:AJ3');
$objPHPExcel->getActiveSheet()->getStyle('AB2:AJ3')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF3F7FBF');//Blue Color [FF][HEXCODE]
    
$objPHPExcel->getActiveSheet()->getStyle('AB2:AJ3')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB2:AJ3')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB2:AJ3')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB2:AJ3')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AB5:AJ5');
$objPHPExcel->getActiveSheet()->getStyle('AB5:AJ5')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF3F7FBF');//Blue Color [FF][HEXCODE]
    
$objPHPExcel->getActiveSheet()->getStyle('AB5:AJ5')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB5:AJ5')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB5:AJ5')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AB5:AJ5')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    
$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValue('AB2', 'Tuesday, July 21, 2015')
      		->getStyle('AB2')->getFont()->setBold(true)->setSize(16);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('AB2')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('AB5', 'Vaughan Operations 4:00PM - 12:30AM')
            ->getStyle('AB5')->getFont()->setBold(true)->setSize(11);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('AB5')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);


//Setup Night Shift Sorting Header.
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AK2:AS3');
$objPHPExcel->getActiveSheet()->getStyle('AK2:AS3')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF3F7FBF');//Blue Color [FF][HEXCODE]
    
$objPHPExcel->getActiveSheet()->getStyle('AK2:AS3')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK2:AS3')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK2:AS3')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK2:AS3')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('AK5:AS5');
$objPHPExcel->getActiveSheet()->getStyle('AK5:AS5')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF3F7FBF');//Blue Color [FF][HEXCODE]
    
$objPHPExcel->getActiveSheet()->getStyle('AK5:AS5')
    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK5:AS5')
    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK5:AS5')
    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('AK5:AS5')
    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    
$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValue('AK2', 'Tuesday, July 21, 2015')
      		->getStyle('AK2')->getFont()->setBold(true)->setSize(16);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('AK2')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('AK5', 'Vaughan Operations 4:00PM - 12:30AM')
            ->getStyle('AK5')->getFont()->setBold(true)->setSize(11);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('AK5')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    	  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, )
);








$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A7', 'Position')
            ->setCellValue('B7', 'First Name')
            ->setCellValue('C7', 'Last Name')
            ->setCellValue('D7', 'Shift')
            ->setCellValue('E7', 'Location')
            ->setCellValue('F7', 'Station');




//Loop through a schedule and add peoples name sinto the cells.  Increment cell value each time.

//retrieve the schedule.
$sql_schedule = 
"SELECT * 
FROM `".$db."`.`schedule_saved`
WHERE `ID_schedule` = ".$new_schedule_value. " 
ORDER BY `schedule_saved`.`ID_schedule_position` ASC, `schedule_saved`.`ID_employee` ASC";
$result_schedule = $link->query($sql_schedule);

$cell_value = 8;

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
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$cell_value, $schedule_position_name)
            ->setCellValue('B'.$cell_value, $employee_first_name)
            ->setCellValue('C'.$cell_value, $employee_last_name)
            ->setCellValue('D'.$cell_value, $shift_english)
            ->setCellValue('E'.$cell_value, findStationName($station_ID))
            ->setCellValue('F'.$cell_value, findFacilityName($facility_ID));

echo "cell value = " . $cell_value;
echo "shift value = " . $shift;
$cell_value = $cell_value + 1;
}//End While




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