<?php
    $inData = getRequestInfo();

    $userID = $inData["UserID"];
    $firstName = $inData["FirstName"];
    $lastName = $inData["LastName"];
    $email = $inData["Email"];
    $phoneNumber = $inData["PhoneNumber"];

    $conn = new mysqli("%", "hackerman", "WeWillGet100", "COP4331");
    
    if($conn->connect_error)
    {
        returnWithError( $conn->connect_error );
    }
    else
    {
        $stmt = $conn->prepare("DELETE FROM Contacts WHERE UserID = $userID AND FirstName = $firstName AND LastName = $lastName AND Email = $email AND PhoneNumber = $phoneNumber");
        $stmt->bind_param("sssss", $userID, $firstName, $lastName, $email, $phoneNumber);
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