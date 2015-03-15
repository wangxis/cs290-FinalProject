<?php
session_start();
if(isset($_GET['action']) && $_GET['action'] == 'end'){
	$_SESSION = array();
	session_destroy();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <title>Movie Review Webpage</title>
  <script src="finalProj.js"> </script>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h2>Movie Review Webpage</h2>
			 <fieldset><legend>Sign In</legend>
                <form action="content.php" method='POST' id="signInForm">
                    <table>
                        <tr>
                            <td>Username</td>
                            <td><input type='text' name='uname' id='unameField'></td><td><div id='uname'></div></td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td><input type='password' name='pword' id='pwordField'></td><td><div id='pword'></div></td>
                        </tr>
						<tr>
						<td><input type='button' onclick="validateUser()" value='Sign In'></td><td><div id='submit'></div></td>               
                        </tr>
                    </table>
                   
                </form>
				</fieldset>
				<fieldset><legend>Register</legend>
				<form action="content.php" method='POST' id="registerForm">
                    <table>
                        <tr>
                            <td>First name</td>
                            <td><input type='text' name='fname' id='fnameField'></td><td><div id='fname'></div></td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td><input type='text' name='lname' id='lnameField'></td><td><div id='lname'></div></td>
                        </tr>
                       <tr>
                            <td>Username</td>
                            <td><input type='text' name='uname1' id='unameField1'></td><td><div id='uname1'></div></td>
                        </tr>
						<tr>
                            <td>Password</td>
                            <td><input type='password' name='pword1' id='pwordField1'></td><td><div id='pword1'></div></td>
                        </tr>
						<tr>
                            <td><input type='button' onclick="validateNewUser()" value='Submit'></td><td><div id='submit1'></div></td> 
                        </tr>
                    </table>
                    
                </form>
				</fieldset>

</body>
</html>
