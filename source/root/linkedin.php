<html>
	<head>
		<script type="text/javascript" src="//platform.linkedin.com/in.js">
			api_key:   772dtbul04ktif
			authorize: true
			onLoad: onLinkedInLoad
			scope: r_emailaddress
		</script>
		<title>Apps login</title>
	</head>
 <body>
	<div>
		<?php 
		if (isset($_SESSION['username'])) {
			echo "<p>Current User:".$_SESSION['username']."</p>";
		}?>
		<p><button onclick="onLogoutClick()">Log Out</button></p>
	</div>
    <div> 
		<h3>Linkedin</h3>
		<div>
			<script type="in/Login"></script>
			<div id='profile' style="visibility: hidden">
			    <img src="" id="ProfilePic" alt="Profile Pic" style="width:100px;height:100px;">
				<span id="ProfileURL"></span>
				<div><b>First Name: </b><span id='FirstName'></span></div>
				<div><b>LastName: </b><span id='LastName'></span></div>
				<div><b>headline: </b><span id='headline'></span></div>
				<div><b>UserId: </b><span id='UserId'></span></div>
				<div><b>Location: </b><span id='Location'></span></div>
				<div><b>Industry: </b><span id='Industry'></span></div>
				<div><b>Summary: </b><span id='Summary'></span></div>
				<div><b>EmailAddress: </b><span id='EmailAddress'></span></div>
				<div id="Positionsdiv" style="visibility: hidden"><b>Work: </b><table><tbody id="Positions"></tbody>	</table></div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		    // Setup an event listener to make an API call once auth is complete
			function onLinkedInLoad() {
				IN.Event.on(IN, "auth", getProfileData);
			}

			function onLogoutClick()
			{
				console.log("logging out");
				IN.User.logout(afterLogout);
			}

			function afterLogout()
			{
				window.location = "index.php";
				<?php
				//header('Location: index.php');
				?>
			}

			// Handle the successful return from the API call
			function onSuccess(data) {
				console.log(data);
				document.getElementById("profile").style.visibility='visible';
				document.getElementById("FirstName").innerHTML = data.firstName;
				document.getElementById("LastName").innerHTML = data.lastName;
				document.getElementById("headline").innerHTML = data.headline;
				document.getElementById("UserId").innerHTML = data.id;			
				document.getElementById("ProfilePic").src = data.pictureUrl;	
				document.getElementById("Location").innerHTML = data.location.name;
				document.getElementById("Industry").innerHTML = data.industry;
				document.getElementById("Summary").innerHTML = data.summary;
				obj = data.positions;
				if(obj._total != 0)
					document.getElementById("Positionsdiv").style.visibility='visible'
				var tbody = document.getElementById('Positions');
				for (var i = 0; i < obj._total; i++) 
				{
					val = obj.values;
					var tr = "<tr>";
					tr += "<td>" + val[i].title + "</td><td>" + val[i].company.name + "</td><td>" + val[i].endDate.month + "/" + val[i].endDate.year + "</td><td>" + val[i].isCurrent + "</td></tr>";
					tbody.innerHTML += tr;
				}
				document.getElementById("ProfileURL").innerHTML = "<a href='" + data.publicProfileUrl + "'>Profile URL</a>";
				document.getElementById("EmailAddress").innerHTML = data.emailAddress;
			}

			// Handle an error response from the API call
			function onError(error) {
				console.log(error);
			}

			// Use the API call wrapper to request the member's basic profile data
			function getProfileData() {
				IN.API.Raw("/people/~:(id,firstName,lastName,headline,picture-url,location,industry,summary,positions,public-profile-url,email-address)?format=json").result(onSuccess).error(onError);
			}

		</script>
</body>
</html>