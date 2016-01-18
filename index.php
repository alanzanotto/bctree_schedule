<!DOCTYPE html>
<html>
	<head>
	<title>Home</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="jquery.mobile-1.4.5.min.css" />
<script src="jquery-1.11.1.min.js"></script>
<script src="jquery.mobile-1.4.5.min.js"></script>
<script src="schedule_javascript.js"></script>
<link rel="stylesheet" type="text/css" href="schedule_css.css">
</head> 
<body> 


<!-- THE HOME PAGE -->
<div data-role="page" id="home_page">

	<div data-role="header">
	<a href="#settings_page" data-icon="gear">Settings</a>
		<h1>Home</h1>
		<div data-role="navbar">
		<ul>
			<li><a href="#home_page" class="ui-state-persist">Home Page</a></li>
			<li><a href="#data_page" onclick="load_data_page()">Data Page</a></li>
			<li><a href="#schedule_setup_page" onclick="load_schedule_setup_page()">Schedule Setup Page</a></li>
			<li><a href="#schedule_page" onclick="load_schedule_page()">Schedule Page</a></li>
			</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content">	
		<p>Use the menu links above to choose which action you wish to do.</p>
		<p><b>Data Page:</b>  Use this for changing employee's preferences and choosing who is, and who is not, available.</p>
		<p><b>Schedule Setup Page:</b> Use this for creating a new schedule.  Write in the date for the schedule and then choose a schedule template from the drop down menu. <p>
		<p><b>Schedule Page:</b> Use this for viewing what the schedule will be. <p>
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE HOME PAGE END -->


<!-- THE SETTINGS PAGE -->
<div data-role="page" id="settings_page" >

	<div data-role="header">
		<a href="#home_page" data-icon="home">Home</a>
		<h1>Settings</h1>
		<div data-role="navbar">
			<ul>
			<li><a href="#settings_positions_page" onClick="load_settings_position_page()">Positions</a></li>
			<li><a href="#settings_posted_positions_page" onclick="load_settings_posted_positions_page()">Posted Positions</a></li>
			<li><a href="#settings_schedule_templates_page" onclick="load_settings_schedule_templates_page()">Schedule Templates</a></li>
			<li><a href="#settings_create_schedule_templates_page" onclick="load_settings_create_schedule_template_page()">Create Schedule Templates</a></li>
			</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content">	
		<p>Use the menu links above to choose which action you wish to do.</p>
		<p><b>Positions:</b>  Use this for viewing and creating new Positions that are on the schedule.  For example, a line operator for A/Bline, and a line operator for Presize.  They both require the same posted position, but they are there own positions on the schedule.</p>
		<p><b>Posted Positions:</b> Use this for viewing and creating new Posted Positions.<p>
		<p><b>Schedule Templates:</b> Use this for viewing and creating new schedule templates.  These are used for creating the schedules. <p>
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE SETTINGS PAGE END -->


<!-- THE SETTINGS POSITIONS PAGE -->
<div data-role="page" id="settings_positions_page" >

	<div data-role="header">
		<a href="#home_page" data-icon="home">Home</a>
		<h1>Positions</h1>
		<a href="#newPopupSchedulePosition"  data-icon="plus" data-rel="popup" data-position-to="window" data-transition="pop">New</a>
		<div data-role="navbar">
		<ul>
			<li><a href="#settings_positions_page" onClick="load_settings_position_page()" class="ui-state-persist">Positions</a></li>
			<li><a href="#settings_posted_positions_page" onclick="load_settings_posted_positions_page()">Posted Positions</a></li>
			<li><a href="#settings_schedule_templates_page" onclick="load_settings_schedule_templates_page()">Schedule Templates</a></li>
			<li><a href="#settings_create_schedule_templates_page" onclick="load_settings_create_schedule_template_page()">Create Schedule Templates</a></li>
		</ul>
	</div><!-- /navbar -->
</div><!-- /header -->

	
	<div data-role="content" id="settings_positions_page_content">
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE POSITIONS PAGE END -->


<!-- THE SETTINGS POSTED POSITIONS PAGE -->
<div data-role="page" id="settings_posted_positions_page" >

	<div data-role="header">
		<a href="#home_page" data-icon="home">Home</a>
		<h1>Posted Positions</h1>
		<a href="#popupNewPostedPosition"  data-icon="plus" data-rel="popup" data-position-to="window" data-transition="pop">New</a>
		<div data-role="navbar">
		<ul>
			<li><a href="#settings_positions_page" onClick="load_settings_position_page()">Positions</a></li>
			<li><a href="#settings_posted_positions_page" onclick="load_settings_posted_positions_page()" class="ui-state-persist">Posted Positions</a></li>
			<li><a href="#settings_schedule_templates_page" onclick="load_settings_schedule_templates_page()">Schedule Templates</a></li>
			<li><a href="#settings_create_schedule_templates_page" onclick="load_settings_create_schedule_template_page()">Create Schedule Templates</a></li>
			</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content" id="setting_posted_positions_content">	
		
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE Posted Positions PAGE END -->

<!-- THE SETTINGS SCHEDULE TEMPLATES PAGE -->
<div data-role="page" id="settings_schedule_templates_page" >

	<div data-role="header">
		<a href="#home_page" data-icon="home">Home</a>
		<h1>Schedule Templates</h1>
		<a href="#popupNewTemplate"  data-icon="plus" data-rel="popup" data-position-to="window" data-transition="pop">New</a>
		<div data-role="navbar">
		<ul>
			<li><a href="#settings_positions_page" onClick="load_settings_position_page()">Positions</a></li>
			<li><a href="#settings_posted_positions_page" onclick="load_settings_posted_positions_page()">Posted Positions</a></li>
			<li><a href="#settings_schedule_templates_page" onclick="load_settings_schedule_templates_page()" class="ui-state-persist">Schedule Templates</a></li>
			<li><a href="#settings_create_schedule_templates_page" onclick="load_settings_create_schedule_template_page()">Create Schedule Templates</a></li>
			</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content" id="settings_schedule_templates_page_content">	
		
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE SCHEDULE TEMPLATES PAGE END -->


<!-- THE SETTINGS CREATE SCHEDULE TEMPLATES PAGE -->
<div data-role="page" id="settings_create_schedule_templates_page" >

	<div data-role="header">
		<a href="#home_page" data-icon="home">Home</a>
		<h1>Create Schedule Templates</h1>
		<a href="#popupNewPosition"  data-icon="plus" data-rel="popup" data-position-to="window" data-transition="pop">New</a>
		<div data-role="navbar">
		<ul>
			<li><a href="#settings_positions_page" onClick="load_settings_position_page()">Positions</a></li>
			<li><a href="#settings_posted_positions_page" onclick="load_settings_posted_positions_page()">Posted Positions</a></li>
			<li><a href="#settings_schedule_templates_page" onclick="load_settings_schedule_templates_page()">Schedule Templates</a></li>
			<li><a href="#settings_create_schedule_templates_page" onclick="load_settings_create_schedule_template_page()" class="ui-state-persist">Create Schedule Templates</a></li>
		</ul>
	</div><!-- /navbar -->
</div><!-- /header -->

	<div data-role="content" id="settings_create_schedule_templates_page_content_1">	
	
	</div><!-- /content1 -->

	<div data-role="content" id="settings_create_schedule_templates_page_content_2">	
	
	</div><!-- /content2 -->
	
	
	
	
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE SCHEDULE TEMPLATES PAGE END -->


<!-- THE DATA PAGE -->
<div data-role="page" id="data_page">

	<div data-role="header">
		<h1>Data</h1>
		<a href="#popupNewEmployee"  data-icon="plus" data-rel="popup" data-position-to="window" data-transition="pop">New</a>
		<div data-role="navbar">
		<ul>
			<li><a href="#home_page">Home Page</a></li>
			<li><a href="#data_page" class="ui-state-persist show-page-loading-msg " data-textonly="true" data-textvisible="true" data-msgtext="Loading Employees..." onclick="load_data_page()">Data Page</a></li>
			<li><a href="#schedule_setup_page" onclick="load_schedule_setup_page()">Schedule Setup Page</a></li>
			<li><a href="#schedule_page" onclick="load_schedule_page()">Schedule Page</a></li>
		</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content" id="data_page_content">	
		<p>This is the Data Page</p>	
	</div><!-- /content -->
	
	<div data-role="popup" id="popupNewEmployee" data-theme="a" class="ui-corner-all">
    <form>
		<div style="padding: 5px 10px;">
		<h3 id="popupNewEmployeeHeader">New Employee:<label></label></h3>
		
		<input type="text" name="text-NewEmployee_first_name" id="newEmployee_first_name" value="" placeholder="First Name">
		<input type="text" name="text-NewEmployee_last_name" id="newEmployee_last_name" value="" placeholder="Last Name">
		</br><hr>
		<a href="#data_page" data-role="button" data-icon="check" data-inline="true" class="show-page-loading-msg" data-textonly="true" data-textvisible="true" data-msgtext="Loading Employees..." onClick="update_data_page_new()">Save</a>
		<a href="#data_page" data-role="button"  data-inline="true">Cancel</a>

		</div>
	</form>
	</div>
	
	
	<div data-role="popup" id="popupDeleteEmployee" data-theme="a" class="ui-corner-all">
    <form>
		<div style="padding: 5px 10px;">
		<h3 id="popupDeleteEmployeeHeader">Delete Employee?<label></label></h3>
		<input type="hidden" id="deleteEmployeeHidden" value="">
		</br><hr>
		<a href="#data_page" data-role="button" data-icon="delete" data-inline="true" class="show-page-loading-msg" data-textonly="true" data-textvisible="true" data-msgtext="Loading Employees..." onClick="update_data_page_deleteEmployee()">Delete</a>
		<a href="#data_page" data-role="button"  data-inline="true">Cancel</a>

		</div>
	</form>
	</div>
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE DATA PAGE END -->


<!-- THE SCHEDULE SETUP PAGE -->
<div data-role="page" id="schedule_setup_page">

	<div data-role="header">
		<h1>Schedule Setup</h1>
		
		<div data-role="navbar">
		<ul>
			<li><a href="#home_page">Home Page</a></li>
			<li><a href="#data_page" onclick="load_data_page()">Data Page</a></li>
			<li><a href="#schedule_setup_page" class="ui-state-persist" onclick="load_schedule_setup_page()">Schedule Setup Page</a></li>
			<li><a href="#schedule_page" onclick="load_schedule_page()">Schedule Page</a></li>
			</ul>
		</div><!-- /navbar -->
	</div><!-- /header -->

	<div data-role="content" id="schedule_setup_page_content">	
	
	</div><!-- /content -->
	
	
	<div data-role="content" id="schedule_setup_page_content2">
		
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE SCHEDULE SETUP PAGE END -->




<!-- THE SCHEDULE PAGE -->
<div data-role="page" id="schedule_page">

	<div data-role="header" id="schedule_page_header">
		<a href="#newPopupScheduleRandomize"  data-icon="recycle" data-rel="popup" data-position-to="window" data-transition="pop">Shuffle</a>
		<h1>Schedule</h1>
		
		<div data-role="navbar">
		<ul>
			<li><a href="#home_page">Home Page</a></li>
			<li><a href="#data_page" onclick="load_data_page()">Data Page</a></li>
			<li><a href="#schedule_setup_page" onclick="load_schedule_setup_page()">Schedule Setup Page</a></li>
			<li><a href="#schedule_page" class="ui-state-persist" onclick="load_schedule_page()">Schedule Page</a></li>
		</ul>
	</div><!-- /navbar -->
	</div><!-- /header -->
	
	
	
	<div data-role="popup" id="newPopupScheduleRandomize" data-theme="a" class="ui-corner-all">
    <form>
		<div style="padding: 5px 10px;">
		<h3 id="editPositionHeader">Shuffle the Sorters?</h3>

		</br><hr>
		<a href="#schedule_page" data-role="button" data-icon="check" data-inline="true" onClick="update_schedule_page_shuffle()">Shuffle</a>
		<a href="#schedule_page" data-role="button"  data-inline="true">Cancel</a>

		</div>
	</form>
	</div>
	
	
	<div data-role="content" id="schedule_page_content">

	
	<div id="schedule_page_content_1">
	</div>
	
	
		
	</div><!-- /content -->
	
	
	
	<div data-role="footer" id="schedule_page_footer">
		<h4></h4>
	</div><!-- /footer -->

</div><!-- /page -->
<!-- THE SCHEDULE PAGE END -->


</body>
</html>