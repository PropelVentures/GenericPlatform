<?php
class handler{
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
					ORDER BY field_dictionary.field_order";
		$ddQuery = $this->con->query($query);
		if($ddQuery->num_rows > 0){
			$emailField = $passwordField = $usernameField = "";
			while($ddRecord = $ddQuery->fetch_assoc()){
				$table_name = $ddRecord['table_name'];
				if($ddRecord['format_type'] == 'email'){
					$emailField = $ddRecord['generic_field_name'];
				} elseif($ddRecord['format_type'] == 'password') {
					$passwordField = $ddRecord['generic_field_name'];
				} else {
					$usernameField = $ddRecord['generic_field_name'];
				}
			}

			$primaryKey = firstFieldName($table_name);
			$userQuery = $this->con->query("SELECT * FROM $table_name where ($emailField = ".secure($_GET['username'])." OR $usernameField = ".secure($_GET['username']).") and $passwordField = ".secure(md5($_GET['password']))."");

			if($userQuery->num_rows >0 ){
				$user = $userQuery->fetch_assoc();
				$_SESSION['uid'] = $user[$primaryKey];
				$_SESSION['uname'] = $user[$usernameField];
				$_SESSION['user_privilege'] = $user['user_privilege_level'];
				setUserDataInSession($this->con,$user);
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
		$url = BASE_URL."system/main-loop.php?";
		$page_name = 'home';
		if($ddRecord['page_name']){
			$page_name = $ddRecord['page_name'];
		}
		if(isset($_GET['search_id']) && !empty($_GET['search_id'])){
			$url .= self::_appendUrlForParentOrChildPage($page_name,$ddRecord);
		} if(isset($_GET['add']) && $_GET['add'] == true){
			$url .= self::_appendUrlForAddRecord($page_name,$ddRecord);
		} else {
			$url .= self::_appendUrlForDisplayPageOrTab($page_name,$ddRecord);
		}

		$url .= self::_setEditOrView();

		$url .= self::_appendLayoutAndStyle($page_name);

		$url .= self::_appendAnchor($page_name,$ddRecord);

		self::_setReferer($ddRecord);

		self::_setSessionAddUrl($url,$ddRecord);

		header("Location:$url");

		exit;
	}

	private function _appendUrlForDisplayPageOrTab($page_name,$ddRecord){
		$checkTab = $this->con->query("SELECT * FROM data_dictionary where page_name='$page_name' and (component_order='0' OR page_layout ='S-0' OR page_layout ='S-L' OR page_layout='S-R' OR page_layout ='S-C')");
		if($checkTab->num_rows > 0){
			// That means all tab data will be shown on single page_layout_style
			return "page_name=$page_name";
		}
		return "page_name=$page_name&tab=".$ddRecord['table_alias']."&ComponentOrder=".$ddRecord['component_order'];
	}

	private function _appendUrlForParentOrChildPage($page_name,$ddRecord){
		$listSelect = trim($ddRecord['list_select']);
		$listSelectArray = array();
		$primarykey = firstFieldName($ddRecord['table_name']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['table_name']);
		if(!empty($listSelect)){
			$listSelectSeprator = explode(';', $listSelect);
            foreach ($listSelectSeprator as $key=>$list) {
				list($tab,$ComponentOrder,$displayPage) = explode(",", $list,3);
				switch($key){
					case 0:
						$arrayKey = 'ListView';
						break;
					case 1:
						$arrayKey = 'BoxView';
						break;
					default:
						$arrayKey = "$key";
				}
				$listSelectArray[$arrayKey]['table_alias'] =  $table_alias;
				$listSelectArray[$arrayKey]['ComponentOrder'] =  $ComponentOrder;
				$listSelectArray[$arrayKey]['displayPage'] =  $displayPage;
            }
		}
		if(!empty($listSelectArray)){
			$params = $listSelectArray['ListView'];
			return "page_name=".$params['displayPage']."&table_name=".$params['tablbe_name']."&ComponentOrder=".$params['ComponentOrder']."&table_alias=".$params['table_alias']."&search_id=".$_GET['search_id']."&checkFlag=true&table_type=".$table_type;
		}
		return "";

	}

	private function _appendUrlForAddRecord($page_name,$ddRecord){
		$listSelect = trim($ddRecord['list_select']);
		$listSelectArray = array();
		$primarykey = firstFieldName($ddRecord['table_name']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['table_name']);
		if(!empty($listSelect)){
			$listSelectSeprator = explode(';', $listSelect);
            foreach ($listSelectSeprator as $key=>$list) {
				list($tab,$ComponentOrder,$displayPage) = explode(",", $list,3);
				switch($key){
					case 0:
						$arrayKey = 'ListView';
						break;
					case 1:
						$arrayKey = 'BoxView';
						break;
					default:
						$arrayKey = "$key";
				}
				$listSelectArray[$arrayKey]['table_alias'] =  $table_alias;
				$listSelectArray[$arrayKey]['ComponentOrder'] =  $ComponentOrder;
				$listSelectArray[$arrayKey]['displayPage'] =  $displayPage;
            }
		}
		if(!empty($listSelectArray)){
			$params = $listSelectArray['ListView'];
			return "page_name=".$params['displayPage']."&table_name=".$params['table_name']."&ComponentOrder=".$params['ComponentOrder']."&table_alias=".$params['table_alias']."&addFlag=true&checkFlag=true&table_type=".$table_type;
		}
		return "";

	}

	private function _setEditOrView(){
		if(isset($_GET['edit']) && $_GET['edit'] == true){
			return "&edit=true";
		}
		return "";
	}

	private function _appendLayoutAndStyle($page_name){
		$nav = $this->con->query("SELECT * FROM navigation where target_page_name='$page_name'");
		$layout =  "";
		if($nav->num_rows > 0){
			$navRecord = $nav->fetch_assoc();
			$layout = "";
			$itemStyle = $navRecord['item_style'];
		}
		return "&layout=$layout&style=$itemStyle";
	}

	private function _appendAnchor($page_name,$ddRecord){
		$checkTab = $this->con->query("SELECT * FROM data_dictionary where page_name='$page_name' and (component_order='0' OR page_layout ='S-0' OR page_layout ='S-L' OR page_layout='S-R' OR page_layout ='S-C')");
		if($checkTab->num_rows > 0){
			// That means all tab data will be shown on single page
			return "#".$page_name.$ddRecord['dict_id'];
		}
		return "";
	}

	private function _setReferer($ddRecord){
		//unset($_SESSION['child_return_url']);
		//unset($_SESSION['return_url']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['table_name']);
		$refererUrl = BASE_URL."system/main-loop.php?";
		if ($table_type == 'child') {
			$ddParentQuery = $this->con->query("Select * FROM data_dictionary WHERE table_name=".secure($ddRecord['parent_table']));
			if($ddParentQuery->num_rows > 0){
				$ddParentRecord = $ddParentQuery->fetch_assoc();
				$refererUrl .= self::_appendUrlForDisplayPageOrTab($ddParentRecord['page_name'],$ddParentRecord);
				$refererUrl .= self::_appendLayoutAndStyle($ddParentRecord['page_name']);
			}
			$_SESSION['child_return_url'] = $refererUrl;
		} else {
			//$_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
	}

	private function _setSessionAddUrl($url,$ddRecord){
		$_SESSION['add_url_list'] = $url;
		$_SESSION['dict_id'] = $ddRecord['dict_id'];
		$_SESSION['update_table2']['table_name'] = $ddRecord['table_name'];
		if(isset($_GET['parent_id']) && !empty($_GET['parent_id'])){
			$_SESSION['parent_value'] = $_GET['parent_id'];
		}
	}
}
?>
