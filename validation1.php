<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} 
	
	if($_GET['fname'] == ""){ 
		echo "Fill the first name.<br>";
	}
	if($_GET['lname'] == ""){
		echo "Fill the last name.<br>";
	}
	if($_GET['uname1'] == ""){ 
		echo "Fill the user name.<br>";
	}
	if($_GET['pword1'] == ""){
		echo "Fill the Password.<br>";
	}
	if($_GET['fname'] != "" && $_GET['lname'] != "" && $_GET['uname1'] != "" && $_GET['pword1'] != ""){
		if(!($stmt = $mysqli->prepare("SELECT username FROM user"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$out_uname = NULL;
		if (!$stmt->bind_result($out_uname)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$userNameArr = array(); 
		while($stmt->fetch()){
			array_push($userNameArr, $out_uname);
		}
		$stmt->close();
		$userInDatabase = false;
		for ($i = 0; $i < count($userNameArr); ++$i) {
			if($userNameArr[$i] == $_GET['uname1']){
				$userInDatabase=true;   //true if in the database
			}
		} 
		if ($userInDatabase){
			echo 'User name has been used, choose different one.';
		}else{
			echo "ready";
		}
	} 
?>