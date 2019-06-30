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
		$url = BASE_URL."system/main.php?";
		$display_page = 'home';
		if($ddRecord['display_page']){
			$display_page = $ddRecord['display_page'];
		}
		if(isset($_GET['search_id']) && !empty($_GET['search_id'])){
			$url .= self::_appendUrlForParentOrChildPage($display_page,$ddRecord);
		} if(isset($_GET['add']) && $_GET['add'] == true){
			$url .= self::_appendUrlForAddRecord($display_page,$ddRecord);
		} else {
			$url .= self::_appendUrlForDisplayPageOrTab($display_page,$ddRecord);
		}

		$url .= self::_setEditOrView();

		$url .= self::_appendLayoutAndStyle($display_page);

		$url .= self::_appendAnchor($display_page,$ddRecord);

		self::_setReferer($ddRecord);

		self::_setSessionAddUrl($url,$ddRecord);

		header("Location:$url");

		exit;
	}

	private function _appendUrlForDisplayPageOrTab($display_page,$ddRecord){
		$checkTab = $this->con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='0' OR tab_num ='S-0' OR tab_num ='S-L' OR tab_num='S-R' OR tab_num ='S-C')");
		if($checkTab->num_rows > 0){
			// That means all tab data will be shown on single page_layout_style
			return "display=$display_page";
		}
		return "display=$display_page&tab=".$ddRecord['table_alias']."&tabNum=".$ddRecord['tab_num'];
	}

	private function _appendUrlForParentOrChildPage($display_page,$ddRecord){
		$listSelect = trim($ddRecord['list_select']);
		$listSelectArray = array();
		$primarykey = firstFieldName($ddRecord['database_table_name']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['database_table_name']);
		if(!empty($listSelect)){
			$listSelectSeprator = explode(';', $listSelect);
            foreach ($listSelectSeprator as $key=>$list) {
				list($tab,$tabNum,$displayPage) = explode(",", $list,3);
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
				$listSelectArray[$arrayKey]['tab'] =  $tab;
				$listSelectArray[$arrayKey]['tabNum'] =  $tabNum;
				$listSelectArray[$arrayKey]['displayPage'] =  $displayPage;
            }
		}
		if(!empty($listSelectArray)){
			$params = $listSelectArray['ListView'];
			return "display=".$params['displayPage']."&tab=".$params['tab']."&tabNum=".$params['tabNum']."&ta=".$params['tab']."&search_id=".$_GET['search_id']."&checkFlag=true&table_type=".$table_type;
		}
		return "";

	}

	private function _appendUrlForAddRecord($display_page,$ddRecord){
		$listSelect = trim($ddRecord['list_select']);
		$listSelectArray = array();
		$primarykey = firstFieldName($ddRecord['database_table_name']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['database_table_name']);
		if(!empty($listSelect)){
			$listSelectSeprator = explode(';', $listSelect);
            foreach ($listSelectSeprator as $key=>$list) {
				list($tab,$tabNum,$displayPage) = explode(",", $list,3);
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
				$listSelectArray[$arrayKey]['tab'] =  $tab;
				$listSelectArray[$arrayKey]['tabNum'] =  $tabNum;
				$listSelectArray[$arrayKey]['displayPage'] =  $displayPage;
            }
		}
		if(!empty($listSelectArray)){
			$params = $listSelectArray['ListView'];
			return "display=".$params['displayPage']."&tab=".$params['tab']."&tabNum=".$params['tabNum']."&ta=".$params['tab']."&addFlag=true&checkFlag=true&table_type=".$table_type;
		}
		return "";

	}

	private function _setEditOrView(){
		if(isset($_GET['edit']) && $_GET['edit'] == true){
			return "&edit=true";
		}
		return "";
	}

	private function _appendLayoutAndStyle($display_page){
		$nav = $this->con->query("SELECT * FROM navigation where target_display_page='$display_page'");
		$layout = $itemStyle = "";
		if($nav->num_rows > 0){
			$navRecord = $nav->fetch_assoc();
			$layout = $navRecord['page_layout_style'];
			$itemStyle = $navRecord['nav_css_class'];
		}
		return "&layout=$layout&style=$itemStyle";
	}

	private function _appendAnchor($display_page,$ddRecord){
		$checkTab = $this->con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='0' OR tab_num ='S-0' OR tab_num ='S-L' OR tab_num='S-R' OR tab_num ='S-C')");
		if($checkTab->num_rows > 0){
			// That means all tab data will be shown on single page_layout_style
			return "#".$display_page.$ddRecord['dict_id'];
		}
		return "";
	}

	private function _setReferer($ddRecord){
		//unset($_SESSION['child_return_url']);
		//unset($_SESSION['return_url']);
		$table_type = trim($ddRecord['table_type']);
		$table_name = trim($ddRecord['database_table_name']);
		$refererUrl = BASE_URL."system/main.php?";
		if ($table_type == 'child') {
			$ddParentQuery = $this->con->query("Select * FROM data_dictionary WHERE database_table_name=".secure($ddRecord['parent_table']));
			if($ddParentQuery->num_rows > 0){
				$ddParentRecord = $ddParentQuery->fetch_assoc();
				$refererUrl .= self::_appendUrlForDisplayPageOrTab($ddParentRecord['display_page'],$ddParentRecord);
				$refererUrl .= self::_appendLayoutAndStyle($ddParentRecord['display_page']);
			}
			$_SESSION['child_return_url'] = $refererUrl;
		} else {
			//$_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
	}

	private function _setSessionAddUrl($url,$ddRecord){
		$_SESSION['add_url_list'] = $url;
		$_SESSION['dict_id'] = $ddRecord['dict_id'];
		$_SESSION['update_table2']['database_table_name'] = $ddRecord['database_table_name'];
		if(isset($_GET['parent_id']) && !empty($_GET['parent_id'])){
			$_SESSION['parent_value'] = $_GET['parent_id'];
		}
	}
}
?>
