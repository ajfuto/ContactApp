<?php
$inData = getRequestInfo();

$firstName = $inData["FirstName"];
$lastName = $inData["LastName"];
$login = $inData["Login"];
$password = $inData["Password"];

# open our secrets file
$secrets_file = fopen("../secrets/creds.txt", "r") or die("unable to open credentials file");

# fgets() reads an entire line from our file, rtrim() strips any new line characters
$mysql_user = rtrim(fgets($secrets_file));
$mysql_pass = rtrim(fgets($secrets_file));

# close our secrets file
fclose($secrets_file);

$conn = new mysqli("localhost", $mysql_user, $mysql_pass, "COP4331");

if ($conn->connect_error) {
	returnWithError($conn->connect_error);
} else {
	$stmt = $conn->prepare("INSERT into Users (FirstName, LastName, Login, Password) VALUES (?,?,?,?)");
	$stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
	$stmt->execute();
	$stmt->close();
	$conn->close();
	returnWithError("");
}

function getRequestInfo()
{
	return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
	header('Content-type: application/json');
	echo $obj;
}

function returnWithError($err)
{
	$retValue = '{"error":"' . $err . '"}';
	sendResultInfoAsJson($retValue);
}
