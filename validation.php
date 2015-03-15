<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} 

	if($_GET['uname'] == ""){ 
		echo "User name is empty.";
	}
	if($_GET['pword'] == ""){
		echo "Password is empty.";
	}
	if($_GET['uname'] != "" && $_GET['pword'] != ""){
		if (!($stmt = $mysqli->prepare("SELECT username, password FROM user"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$out_uname = NULL;
		$out_pword = NULL;
		if (!$stmt->bind_result($out_uname, $out_pword)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$userNameArr = array(); 
		$pwordArr = array();
		while($stmt->fetch()){
			array_push($userNameArr, $out_uname);//store the author in the array
			array_push($pwordArr, $out_pword);
		}
		$stmt->close();
		$userInDatabase = false;
		for ($i = 0; $i < count($userNameArr); ++$i) {
			if($userNameArr[$i] == $_GET['uname'] && $pwordArr[$i] == $_GET['pword']){
				$userInDatabase=true;   //true if in the database
			}
		}
		if (!$userInDatabase){
			echo 'Please register first.';
		}else{
			echo "ready";
		}
	} 
	
?>