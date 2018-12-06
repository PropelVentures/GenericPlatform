<?php 
class Handler{
	private $con;
	public function __construct($con){
		$this->con = $con;
	}
	
	public function checkLogin(){
		if(!isset($_GET['username']) || !isset($_GET['password'])){
			throw new Exception("Username/Email and Password is required");
		}
		if(empty($_GET['username'])){
			throw new Exception("Please enter Username/Email");
		}
		if(empty($_GET['password'])){
			throw new Exception("Please enter password");
		}
		$query = "SELECT * FROM field_dictionary 
					INNER JOIN data_dictionary ON (data_dictionary.`table_alias` = field_dictionary.`table_alias`)
					where data_dictionary.table_alias = 'login' and data_dictionary.table_type='user'
					ORDER BY field_dictionary.display_field_order";
		$ddQuery = $this->con->query($query);
		if($ddQuery->num_rows > 0){
			$emailField = $passwordField = $usernameField = "";
			while($ddRecord = $ddQuery->fetch_assoc()){
				$table_name = $ddRecord['database_table_name'];
				if($ddRecord['format_type'] == 'email'){
					$emailField = $ddRecord['generic_field_name'];
				} elseif($ddRecord['format_type'] == 'password') {
					$passwordField = $ddRecord['generic_field_name'];
				} else {
					$usernameField = $ddRecord['generic_field_name'];
				}
			}
			$primaryKey = firstFieldName($table_name);
			$userQuery = $this->con->query("SELECT * FROM $table_name where ($emailField = ".secure($_GET['username'])." OR $usernameField = ".secure($_GET['username']).") and $passwordField = ".secure($_GET['password']).";");
			if($userQuery->num_rows >0 ){
				$user = $userQuery->fetch_assoc();
				$_SESSION['uid'] = $user[$primaryKey];
				$_SESSION['uname'] = $user[$usernameField];
				$_SESSION['user_privilege'] = $user['user_privilege_level'];
				return true;
			} else {
				throw new Exception("Invalid Username or Password.");
			}
		}
		throw new Exception("Something went wrong please try again");
	}
	
	public function redirectAccordingToUrl(){
		if(!isset($_GET['dd_id']) || empty($_GET['dd_id'])){
			throw new Exception("DD ID parameter is required");
		}
		$ddQuery = $this->con->query("Select * FROM data_dictionary WHERE dict_id=".secure($_GET['dd_id'],'int'));
		if($ddQuery->num_rows == 0){
			throw new Exception("Invalid DD ID");
		}
		$ddRecord = $ddQuery->fetch_assoc();
		$url = BASE_URL."system/main.php?display=".$ddRecord['display_page'];
		header("Location:$url");
		exit;
	}
}
?>