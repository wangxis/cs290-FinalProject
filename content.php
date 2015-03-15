<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html');
include 'storedInfo.php';

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wangxis-db", "$myPassword", "wangxis-db");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} 

session_start();
if(session_status() == PHP_SESSION_ACTIVE){
	if(!isset($_SESSION['username'])){
		if(isset($_POST['uname'])){
			$_SESSION['username'] = $_POST['uname'];
		}	
	}
	if (isset($_SESSION['username'])){
		echo '<html><head><meta charset="UTF-8"/><title>Movie Review</title><link rel="stylesheet" type="text/css" href="style.css">';
		echo '</head><body>';
		//get user first and last names
		if (!($stmt = $mysqli->prepare("SELECT id, fname, lname FROM user WHERE username=(?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("s", $_SESSION['username'])) {
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$out_fname = NULL;
		$out_lname = NULL;
		$out_id = NULL;
		if (!$stmt->bind_result($out_id, $out_fname, $out_lname)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$stmt->fetch();
		$stmt->close();
		$_SESSION['userId'] = $out_id;
		if (!($stmt = $mysqli->prepare("SELECT title, category, rating FROM movie LEFT JOIN user ON movie.id=user.id WHERE user_id=(?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("i", $_SESSION['userId'])) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$out_title = NULL;
		$out_category = NULL;
		$out_rating = NULL;
		if (!$stmt->bind_result($out_title, $out_category, $out_rating)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$logoutLink = "login.php?action=end";
		echo '<p><h2>Welcome ' . $out_fname . " " . $out_lname . ',' . ' here is your list of rated movies</h2></p>';
		echo "<p><h3><a href='".$logoutLink."'>Log Out</a></h3></p>";
		echo '<fieldset><legend>Movies</legend><table border = "1"><tr><td>Title</td><td>Category</td><td>Your Rating</td></tr>';
		while($stmt->fetch()){
			if($out_rating == NULL){
				$out_rating = "Not rated";
			}
			echo '<tr><td>' . $out_title . '</td><td>' . $out_category . '</td><td>' . $out_rating . '</td></tr>';		
		}
		echo  '</table>';
		$stmt->close();
		echo '<p>Choose a movie from drop down menu to rate/update rating</p>';
		
		if (!($stmt = $mysqli->prepare("SELECT distinct title FROM movie"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$out_title = NULL;
		if (!$stmt->bind_result($out_title)) {
			echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		echo '<form action="content.php" method="POST">';
		echo '<select name="rateTitle">';
		while($stmt->fetch()){
			echo '<option>' . $out_title . '</option>';		
		}
		echo '<option selected value="">';
		echo '<input type="submit" name="ratingReq" Value="Choose"></select></form>';
		$stmt->close();
		$logoutLink = "login.php?action=end";
		echo '</fieldset>';
		if(isset($_POST['ratingReq'])){
			if($_POST['rateTitle'] == ""){
				echo '<p>No title selected</p>';
			} else {
				if (!($stmt = $mysqli->prepare("SELECT rateLetter FROM rate"))) {
					echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$out_rate = NULL;
				if (!$stmt->bind_result($out_rate)) {
					echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				
				$strArr = array();
				$strArr = explode(" ", $_POST['rateTitle']);
				$title = implode(".", $strArr);
				echo '<form action="content.php" method="POST">';
				echo '<p>Rate or update the movie rating</p>';
				echo  $_POST['rateTitle'];
				echo '<select name="newRate">';
				while($stmt->fetch()){
					echo '<option>' . $out_rate . '</option>';		
				}
				echo '<option selected value="">';
				echo '<input type="hidden" name="movieTitle" value=' . $title . '>';
				echo '<input type="submit" name="newRating" Value="submit"></select>';
				echo '</form>';
				$stmt->close();			
			}
		}
		
		if(isset($_POST['newRating'])){
			$arr = array();
			$arr = explode(".",$_POST['movieTitle']);
			$title = implode(" ", $arr);
			if($_POST['newRate'] == ""){
				echo '<p>No rate selected for the movie: ' . $title . '</p>';
			}else {
			//check if the title has been rated 
			if (!($stmt = $mysqli->prepare("SELECT title FROM movie WHERE user_id=(?)"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$stmt->bind_param("i", $_SESSION['userId'])) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$out_title = NULL;
			if (!$stmt->bind_result($out_title)) {
				echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$titleArr = array();
			while($stmt->fetch()){
				array_push($titleArr, $out_title);
			}
			$stmt->close();
			$titleRated = false;
			for ($i = 0; $i < count($titleArr); ++$i) {
				if($titleArr[$i] == $title){
					$titleRated=true;
				}
			}
			//if already rated, then update
			if ($titleRated){
				if (!($stmt = $mysqli->prepare("UPDATE movie SET rating=(?) WHERE title=(?)"))) {
					echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
				if (!$stmt->bind_param("ss", $_POST['newRate'],$title)){
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$stmt->close();		
			} else { //if not rated then insert new row, get category first
				if (!($stmt = $mysqli->prepare("SELECT category FROM movie WHERE title=(?)"))) {
					echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
				if (!$stmt->bind_param("s", $title)) {
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$out_category = NULL;
				if (!$stmt->bind_result($out_category)) {
					echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$stmt->fetch();
				$stmt->close();
			
			
				//insert row
				if (!($stmt = $mysqli->prepare("INSERT INTO movie (title, category, rating, user_id) VALUES (?, ?, ?, ?)"))) {
					echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
				if (!$stmt->bind_param("sssi", $title, $out_category, $_POST['newRate'], $_SESSION['userId'])) {
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$stmt->close();	
			}
			echo '<p>Rate/update successfully!!';
			echo '<form action="content.php"><input type="submit" value="Refresh"></form></p>'; 
			

			}

		}


	} else {
		header ("Location: login.php");
	}
	
}
if(isset($_POST['fname'])){
	if (!($stmt = $mysqli->prepare("INSERT INTO user (fname, lname, username, password) VALUES (?, ?, ?, ?)"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
				if (!$stmt->bind_param("ssss", $_POST['fname'], $_POST['lname'], $_POST['uname1'], $_POST['pword1'])) {
					echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				if (!$stmt->execute()) {
					echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				$stmt->close();
}	
echo '</body></html>';
?>
