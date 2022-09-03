<?php

  $inData = getRequestInfo();
	
  $searchResults = "";
  $searchCount = 0;

	# Retrieve mysql credentials from secret file
	$secrets_file = fopen("../secrets/creds.txt", "r") or die("unable to open credentials file");
	$mysql_user = rtrim(fgets($secrets_file));
	$mysql_pass = rtrim(fgets($secrets_file));
	fclose($secrets_file);

  $conn = new mysqli("localhost", $mysql_user, $mysql_pass, "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		# Search search string against all columns in Contacts table
		$stmt = $conn->prepare("select * from Contacts where (FirstName like ? OR LastName like ? OR Email like ? OR PhoneNumber like ?) and UserID=?");
		$searchString = "%" . $inData["search"] . "%";
		$stmt->bind_param("sssss", $searchString, $searchString, $searchString, $searchString, $inData["userId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{"FirstName" : "' . $row["FirstName"] . '", "LastName" : "' . $row["LastName"] . '", "PhoneNumber" : "' . $row["PhoneNumber"] . '","Email" : "' . $row["Email"] . '"}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>