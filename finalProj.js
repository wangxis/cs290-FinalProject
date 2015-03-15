

function validateUser() {
	var username = document.getElementById("unameField").value;
	var password = document.getElementById("pwordField").value;
	//var submit = document.getElementById("submit");
    var request = new XMLHttpRequest();
	if(!request) {
		throw "Failed to create XMLHttpReuest";
	}
	request.onreadystatechange = function() {
			if(this.readyState === 4) {
				var res = request.responseText;
				if(res == "ready"){
					document.getElementById("signInForm").submit();
				} else if (res == "User name is empty.") {
					document.getElementById("uname").innerHTML = res;
				} else if (res == "Password is empty."){
					document.getElementById("pword").innerHTML = res;
				} else if (res.charAt(0) == 'U'){
					document.getElementById("uname").innerHTML = res.substr(0,19);
					document.getElementById("pword").innerHTML = res.substr(19,37);
				} else if(res == "Please register first."){
					document.getElementById("submit").innerHTML = res;
				}
			}	
		}
request.open("GET", "validation.php?uname=" + username + "&pword=" + password);
request.send();
}

function validateNewUser() {
	var firstname = document.getElementById("fnameField").value;
	var lastname = document.getElementById("lnameField").value;
	var username = document.getElementById("unameField1").value;
	var password = document.getElementById("pwordField1").value;
var request = new XMLHttpRequest();
	if(!request) {
		throw "Failed to create XMLHttpReuest";
	}
	request.onreadystatechange = function() {
			if(this.readyState === 4) {
				var res = request.responseText;
				if(res == "ready"){
					document.getElementById("registerForm").submit();
				} else {
					document.getElementById("submit1").innerHTML = res;
				}
				
			}	
		}
request.open("GET", "validation1.php?fname=" + firstname + "&lname=" + lastname + "&uname1=" + username + "&pword1=" + password);
request.send();
}