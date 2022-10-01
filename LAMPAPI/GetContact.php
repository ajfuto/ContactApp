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
		$stmt = $conn->prepare("select * from Contacts where ID=?");
		$id = $inData["id"];
		$stmt->bind_param("s", $id);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo( $row['ID'], $row['FirstName'], $row['LastName'], $row['PhoneNumber'], $row['Email'], $row['DateCreated'], $row['DateEdited'] );
		}
		else
		{
			returnWithError("No Records Found");
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
	
	function returnWithInfo( $id, $firstName, $lastName, $phone, $email, $dcreated, $dedited )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","phoneNumber":"' . $phone . '","email":"' . $email . '","create":"' . $dcreated . '","edited":"' . $dedited . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>