<?php

  $inData = getRequestInfo();

  $contactID = $inData["ContactID"];
  $firstName = $inData["FirstName"];
  $lastName = $inData["LastName"];
  $email = $inData["Email"];
  $phoneNumber = $inData["PhoneNumber"];
  $userID = $inData["UserID"];

	# Retrieve mysql credentials from secret file
	$secrets_file = fopen("../secrets/creds.txt", "r") or die("unable to open credentials file");
	$mysql_user = rtrim(fgets($secrets_file));
	$mysql_pass = rtrim(fgets($secrets_file));
	fclose($secrets_file);

  $conn = new mysqli("localhost", $mysql_user, $mysql_pass, "COP4331");

  if($conn->connect_error)
  {
    returnWithError( $conn->connect_error );
  }
  else
  {
    # Update relevant columns in Contacts table
    $stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Email = ?, PhoneNumber = ?, DateEdited = CURRENT_TIMESTAMP WHERE ID = ? AND UserID = ?");
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $phoneNumber, $contactID, $userID);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    returnWithError("");
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
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

?>