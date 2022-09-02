<?php

  $inData = getRequestInfo();

  $contactID = $inData["ContactID"];
  $firstName = $inData["FirstName"];
  $lastName = $inData["LastName"];
  $email = $inData["Email"];
  $phoneNumber = $inData["PhoneNumber"];
  $userID = $inData["UserID"];

	# open our secrets file
	$secrets_file = fopen("../secrets/creds.txt", "r") or die("unable to open credentials file");

	# fgets() reads an entire line from our file, rtrim() strips any new line characters
	$mysql_user = rtrim(fgets($secrets_file));
	$mysql_pass = rtrim(fgets($secrets_file));

	# close our secrets file
	fclose($secrets_file);

  $conn = new mysqli("localhost", $mysql_user, $mysql_pass, "COP4331");

  if($conn->connect_error)
  {
    returnWithError( $conn->connect_error );
  }
  else
  {
    $stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Email = ?, PhoneNumber = ? WHERE ID = ? AND UserID = ?");
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