<?php
/*
	timereg/timereg-day-delete-process.php

	access: timekeeping

	Allows the deletion of an unwanted time entry.
*/

// includes
include_once("../include/config.php");
include_once("../include/amberphplib/main.php");


if (user_permissions_get('timekeeping'))
{
	/////////////////////////

	$id				= security_script_input("/^[0-9]*$/", $_GET["id"]);
	$date				= security_script_input("/^\S*$/", $_GET["date"]);
	

	
	// make sure the time entry actually exists
	$mysql_string		= "SELECT id, locked FROM `timereg` WHERE id='$id'";
	$mysql_result		= mysql_query($mysql_string);
	$mysql_num_rows		= mysql_num_rows($mysql_result);
	
	if (!$mysql_num_rows)
	{
		$_SESSION["error"]["message"][] = "The time entry you have attempted to delete - $id - does not exist in this system.";
	}
	else
	{
		$mysql_data = mysql_fetch_array($mysql_result);

		if ($mysql_data["locked"])
		{
			$_SESSION["error"]["message"][] = "This time entry has been locked and can not be adjusted.";
		}
	}


		
	//// ERROR CHECKING ///////////////////////


	/// if there was an error, go back to the entry page
	if ($_SESSION["error"]["message"])
	{	
		$_SESSION["error"]["form"]["timereg_delete"] = "failed";
		header("Location: ../index.php?page=timekeeping/timereg-day.php&date=". $date ."&editid=$id");
		exit(0);
	}
	else
	{
		// delete
		$mysql_string = "DELETE FROM `timereg` WHERE id='$id'";
						
		if (!mysql_query($mysql_string))
		{
			$_SESSION["error"]["message"][] = "A fatal SQL error occured: ". mysql_error();
		}
		else
		{
			$_SESSION["notification"]["message"][] = "Time entry successfully removed.";
		}

		// display updated details
		header("Location: ../index.php?page=timekeeping/timereg-day.php&date=". $date ."");
		exit(0);
	}

	/////////////////////////
	
}
else
{
	// user does not have perms to view this page/isn't logged on
	error_render_noperms();
	header("Location: ../index.php?page=message.php");
	exit(0);
}


?>
