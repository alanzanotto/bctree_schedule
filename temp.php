<?php
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

+++++++++++++++++++++++++++++++++++++++++


//loop through positions and retrieve emps and add them to the  file. While making sure to stay on the page. 
		while($row = $result_positions->fetch_assoc())
		{
			
			//Get position ID
			$position_ID = $row['ID_schedule_position'];
			//Retrive the name of the position
			//....
			
			$sql_emps = 
			"
			SELECT ID_employee 
			FROM `".$db."`.`schedule_saved` 
			WHERE ID_schedule = ".$new_schedule_value."
			AND shift = ".$shift."
			AND station = ".$station_ID."
			AND ID_schedule_position = ".$position_ID."
			order by ID_employee
			";
			$result_emps = $link->query($sql_emps);
			
			//Retrive employee name and position and add into an array
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
			}
			
			
			//Check which cell is being worked on.  Dont overshoot.
			//A Regular page goes 9 Columns long and from Cell 7 to 46
			//print position name.
			//loop through emps, retrieve first and last name, and then print
			
		    //Page 1
		    //0 to 8
		    
		    //Page 2
		    //9 to 17
		    for ($x = $page_number; $x < $page_number + 8; $x = $x + 2)
		    {
		    	
		    	for ($y = 7; $y < 46; $y = $y + 3)
		    	{
		    	//Set cell B5 with a string value
		    	$temp_y = $y + 1;
    			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "Line Operator");
    			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $temp_y, "Alan Zanotto");
		    	}
		    }
    		
    

		}
