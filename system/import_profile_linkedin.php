<?php
require_once("functions_loader.php");
include("header.php");
$conn = connect();
$nav = $conn->query("SELECT * FROM navigation where item_target='".BASE_URL_SYSTEM."import_profile_linkedin.php'");
if($nav->num_rows > 0){
	$navRecord = $nav->fetch_assoc();
	$displayPage = $navRecord['target_display_page']; ?>
	<script type="text/javascript" src="//platform.linkedin.com/in.js">
		api_key		: <?php echo LINKEDIN_APP_ID. PHP_EOL; ?>
		authorize	: true
		scope		: r_basicprofile r_emailaddress 
		onLoad		: onLinkedInLoad
	</script>
	<script>
		// Setup an event listener to make an API call once auth is complete
		function onLinkedInLoad() {
			IN.UI.Authorize().place();
			IN.Event.on(IN, "auth", getProfileData);
		} 
		// Use the API call wrapper to request the member's profile data
		function getProfileData() {
			IN.API.Profile("me").fields("id", "first-name", "last-name", "headline", "location", "picture-url", "public-profile-url", "email-address","summary").result(displayProfileData).error(onError);
		}
		// Handle the successful return from the API call
		function displayProfileData(data){
			var user = data.values[0];
			// Save user data
			saveUserData(user);
		}

		// Save user data to the database
		function saveUserData(userData){
			$.ajax({
				type: 'POST',
				url: '?action=update&table_type=linkedinimportprofile&display_page=<?php echo $displayPage; ?>',
				dataType: 'json',
				data: userData,
				beforeSend: function(xhr) {
					
				},
				success: function(response){
					if(response.message){
						alert(response.message);
						window.location.href = response.returnUrl;
					} else {
						window.location.href = response.returnUrl;
					}
				},
				error: function(xhr, status, error) {
					alert("Something went wrong. Please try again.");
				}
			});
		}

		// Handle an error response from the API call
		function onError(error) {
			console.log(error);
		}

		// Destroy the session of linkedin
		function logout(){
			IN.User.logout(removeProfileData);
		}

		// Remove profile data from page
		function removeProfileData(){
			//document.getElementById('profileData').remove();
		}
	</script>

<?php
}
?>
<h2 style="text-align:center;width:100%;">Loading...</h2>