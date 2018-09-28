<?php
session_start();
include_once("application/config.php");
include_once($GLOBALS['DATABASE_APP_DIR']."db.php");
include_once("application/functions.php");
include("special_config.php");

/**************************************************************/
/**********Create Project**************************************/
/**************************************************************/
if(isset($_POST["projectSubmit"]) && $_POST["projectSubmit"]!=""){

	$member =$_POST['user'];
	$role =$_POST['role'];

	$projectName = mysql_real_escape_string($_POST["projectName"]);
	$projectCode = mysql_real_escape_string($_POST["projectCode"]);
	$description = mysql_real_escape_string($_POST["description"]);

	$amount = mysql_real_escape_string($_POST["projectPrice"]);
	$quantity = mysql_real_escape_string($_POST["quantity"]);
	$isLive = 1;
	if(PROJECT_VISIBILITY){
	$isLive = mysql_real_escape_string($_POST["isLive"]);
	}
	$affiliation1 = mysql_real_escape_string($_POST["affiliation1"]);
	$affiliation2 = mysql_real_escape_string($_POST["affiliation2"]);
	$cat_id = mysql_real_escape_string($_POST["project_categories"]);
	$current_date = date("Y-m-d h:i:s");
	$expiryDate = date("Y-m-d h:i:s", strtotime($_POST["expiryDate"]));


	$query = "INSERT INTO {$projectTblArray['tableAlias']}
	({$projectTblArray['uid_fld']}, {$projectTblArray['cid_fld']}, {$projectTblArray['pname_fld']}, {$projectTblArray['description_fld']},
	 {$projectTblArray['create_date_fld']}, {$projectTblArray['expiry_date_fld']}, {$projectTblArray['amount_fld']},
	 {$projectTblArray['quantity_fld']}, {$projectTblArray['isLive_fld']}, {$projectTblArray['affiliation_id_1_fld']},
	  {$projectTblArray['affiliation_id_2_fld']}) VALUES('".$_SESSION['uid']."', ".$cat_id.", '".$projectName."', '".$description."', '".$current_date."', '".$expiryDate."', '".$amount."', '".$quantity."', '".$isLive."', '".$affiliation1."', '".$affiliation1."')";
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...
	$query = "INSERT INTO {$projectTblArray['database_table_name']}
	({$projectTblArray['uid_fld']}, {$projectTblArray['cid_fld']}, {$projectTblArray['pname_fld']}, {$projectTblArray['description_fld']},
	 {$projectTblArray['create_date_fld']}, {$projectTblArray['expiry_date_fld']}, {$projectTblArray['amount_fld']},
	 {$projectTblArray['quantity_fld']}, {$projectTblArray['isLive_fld']}, {$projectTblArray['affiliation_id_1_fld']},
	  {$projectTblArray['affiliation_id_2_fld']}) VALUES('".$_SESSION['uid']."', ".$cat_id.", '".$projectName."', '".$description."', '".$current_date."', '".$expiryDate."', '".$amount."', '".$quantity."', '".$isLive."', '".$affiliation1."', '".$affiliation1."')";



	$result = mysql_query($query);
	if($result){

		$project_id = mysql_insert_id();
		if(!isset($_POST['fork_project'])){
			$_POST['fork_project'] = 0;
		}
		if(!isset($_POST['copy_project'])){
			$_POST['copy_project'] = 0;
		}
		if(!isset($_POST['subscribe_project'])){
			$_POST['subscribe_project'] = 0;
		}
		if(!isset($_POST['share_project'])){
			$_POST['share_project'] = 0;
		}
		if(!isset($_POST['project_description'])){
			$_POST['project_description'] = 0;
		}
		if(!isset($_POST['project_transactions'])){
			$_POST['project_transactions'] = 0;
		}
		if(!isset($_POST['project_comments'])){
			$_POST['project_comments'] = 0;
		}
		if(!isset($_POST['project_img_gallery'])){
			$_POST['project_img_gallery'] = 0;
		}

		$pc_query = "INSERT INTO project_config(pid, uid,  fork, copy, subscribe, share, description, transaction, comments, image_gallery) VALUES(".$project_id.", ".$_SESSION['uid'].", ".$_POST['fork_project'].", ".$_POST['copy_project'].", ".$_POST['subscribe_project'].", ".$_POST['share_project'].", ".$_POST['project_description'].", '".$_POST['project_transactions']."', '".$_POST['project_comments']."', '".$_POST['project_img_gallery']."')";

		mysql_query($pc_query );

		for($i=0; $i < count($member); $i++){
			if(isset($member[$i]) && $member[$i] !=""){
				$team_permission_query = "INSERT INTO  project_team (project_id ,  member_id , permission_id) VALUES ( ".$project_id." ,  ".$member[$i].",  ".$role[$i].")";
				//echo $team_permission_query;
				mysql_query($team_permission_query);
			}
		}


		$tags = mysql_real_escape_string($_POST["tags"]);
		if(isset($tags) && $tags !=""){
			$tag_query = "INSERT INTO tags(pid, tname) values(".$project_id.", '".$tags."')";
			 mysql_query($tag_query );
		 }

		 $uploadcare_image_url = $_POST['uploadcare_image_url'];
		 $filename = $_POST['uploadcare_image_name'];

		 $imageInfo = fileUploadCare($uploadcare_image_url, $filename, "project_uploads");


		 if(!empty($imageInfo)){
		    $query = "UPDATE {$projectTblArray['tableAlias']} SET {$projectTblArray['projectImage_fld']}='".$imageInfo['image']."', {$projectTblArray['upload_care_img_url_fld']}='".$uploadcare_image_url."' where {$projectTblArray['pid_fld']} = $project_id";
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...
		    $query = "UPDATE {$projectTblArray['database_table_name']} SET {$projectTblArray['projectImage_fld']}='".$imageInfo['image']."', {$projectTblArray['upload_care_img_url_fld']}='".$uploadcare_image_url."' where {$projectTblArray['pid_fld']} = $project_id";

			$result = mysql_query($query);
			if($result){
				$query = "INSERT INTO project_gallery(pid, pimage) VALUES(".$project_id.", '".$imageInfo['image']."')";
				$result = mysql_query($query);

				FlashMessage::add('Project  added successfull.');
				echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
				exit;
			}
			else{
				FlashMessage::add('Project Image could not be added.Please try again');
				echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
				exit;
			}
		}
		else{
			FlashMessage::add(PROJECT_ADDED_SUCCESS);
			echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
			exit;
		}
	}
	else{
		FlashMessage::add(PROJECT_NOT_ADDED_SUCCESS);
		echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
		exit;
	}
}

/*****************************************************************/
/**********Update Project****************************************/
/***************************************************************/
if(isset($_POST["projectUpdate"]) && $_POST["projectUpdate"]!=""){

	$member =$_POST['user'];
	$role =$_POST['role'];

	$projectName = mysql_real_escape_string($_POST["projectName"]);
	$projectCode = mysql_real_escape_string($_POST["projectCode"]);
	$description = mysql_real_escape_string($_POST["description"]);
	$amount = mysql_real_escape_string($_POST["projectPrice"]);
	$project_id = mysql_real_escape_string($_POST["pid"]);
	$quantity = mysql_real_escape_string($_POST["quantity"]);
	$isLive = 1;
	if(PROJECT_VISIBILITY){
	$isLive = mysql_real_escape_string($_POST["isLive"]);
	}
	$affiliation1 = mysql_real_escape_string($_POST["affiliation1"]);
	$affiliation2 = mysql_real_escape_string($_POST["affiliation2"]);
	$cat_id = mysql_real_escape_string($_POST["category"]);
	$tags = mysql_real_escape_string($_POST["tags"]);
	//$current_date = date("Y-m-d h:i:s");
	$expiryDate = date("Y-m-d h:i:s", strtotime($_POST["expiryDate"]));

	if(!isset($_POST['fork_project'])){
		$_POST['fork_project'] = 0;
	}
	if(!isset($_POST['copy_project'])){
		$_POST['copy_project'] = 0;
	}
	if(!isset($_POST['subscribe_project'])){
		$_POST['subscribe_project'] = 0;
	}
	if(!isset($_POST['share_project'])){
		$_POST['share_project'] = 0;
	}
	if(!isset($_POST['project_description'])){
		$_POST['project_description'] = 0;
	}
	if(!isset($_POST['project_transactions'])){
		$_POST['project_transactions'] = 0;
	}
	if(!isset($_POST['project_comments'])){
		$_POST['project_comments'] = 0;
	}
	if(!isset($_POST['project_img_gallery'])){
		$_POST['project_img_gallery'] = 0;
	}

	$checkpc = "SELECT * FROM project_config WHERE pid =".mysql_real_escape_string($_POST["pid"]);
	$result_pc = mysql_query($checkpc);
	if ( mysql_num_rows($result_pc) != 0 ) {
		$pc_query = "UPDATE project_config SET fork = ".$_POST['fork_project'].", copy = ".$_POST['copy_project'].", subscribe = ".$_POST['subscribe_project'].", share = ".$_POST['share_project']." , description = ".$_POST['project_description'].", transaction = ".$_POST['project_transactions'].", comments = ".$_POST['project_comments'].", image_gallery = ".$_POST['project_img_gallery']." where pid = $project_id";
	}
	else{
		$pc_query = "INSERT INTO project_config(pid, uid, fork, copy, subscribe, share, description, transaction, comments, image_gallery) VALUES(".$project_id.",".$_SESSION['uid'].", ".$_POST['fork_project'].", ".$_POST['copy_project'].", ".$_POST['subscribe_project'].", ".$_POST['share_project'].", ".$_POST['project_description'].", '".$_POST['project_transactions']."', '".$_POST['project_comments']."', '".$_POST['project_img_gallery']."')";
	}
		mysql_query($pc_query );

	if(isset($_POST['uploadcare_image_url']) && $_POST['uploadcare_image_url']!=""){

			if(isset($_POST["oldProjectImage"]) && $_POST["oldProjectImage"]!=""){
				unlink("img/".$_POST["oldProjectImage"]);
				unlink("img/thumb_".$_POST["oldProjectImage"]);
			}

			$uploadcare_image_url = $_POST['uploadcare_image_url'];
			$filename = $_POST['uploadcare_image_name'];

			$imageInfo = fileUploadCare($uploadcare_image_url, $filename, "project_uploads");

			if(!empty($imageInfo)){
				$query = "UPDATE {$projectTblArray['tableAlias']} SET {$projectTblArray['pname_fld']}='".$projectName."',
				{$projectTblArray['cid_fld']}=".$cat_id.", {$projectTblArray['description_fld']}='".$description."',
				 {$projectTblArray['projectImage_fld']}='".$imageInfo['image']."', {$projectTblArray['upload_care_img_url_fld']}='".$uploadcare_image_url."',
				  {$projectTblArray['expiry_date_fld']}='".$expiryDate."', {$projectTblArray['amount_fld']}='".$amount."',{$projectTblArray['quantity_fld']}='".$quantity."',
				  {$projectTblArray['isLive_fld']}='".$isLive."' ,{$projectTblArray['affiliation_id_1_fld']}='".$affiliation1."',{$projectTblArray['affiliation_id_2_fld']}='".$affiliation2."' where {$projectTblArray['pid_fld']}=$project_id";
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
				$query = "UPDATE {$projectTblArray['database_table_name']} SET {$projectTblArray['pname_fld']}='".$projectName."',
				{$projectTblArray['cid_fld']}=".$cat_id.", {$projectTblArray['description_fld']}='".$description."',
				 {$projectTblArray['projectImage_fld']}='".$imageInfo['image']."', {$projectTblArray['upload_care_img_url_fld']}='".$uploadcare_image_url."',
				  {$projectTblArray['expiry_date_fld']}='".$expiryDate."', {$projectTblArray['amount_fld']}='".$amount."',{$projectTblArray['quantity_fld']}='".$quantity."',
				  {$projectTblArray['isLive_fld']}='".$isLive."' ,{$projectTblArray['affiliation_id_1_fld']}='".$affiliation1."',{$projectTblArray['affiliation_id_2_fld']}='".$affiliation2."' where {$projectTblArray['pid_fld']}=$project_id";


				$result1 = mysql_query($query);
				if($result1){

					$query = "Update project_gallery SET pimage='".$imageInfo['image']."' WHERE pid=$project_id";
					$result = mysql_query($query);

					FlashMessage::add(PROJECT_UPDATE_SUCCESS);
					echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
					exit;
				}
				else{
					FlashMessage::add(PROJECT_IMAGE_NOT_UPDATE_SUCCESS);
					echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
					exit;
				}
			}
		}

	else{
			$query = "UPDATE {$projectTblArray['tableAlias']} SET {$projectTblArray['pname_fld']}='".$projectName."', {$projectTblArray['cid_fld']}=".$cat_id.", {$projectTblArray['description_fld']}='".$description."', {$projectTblArray['expiry_date_fld']}='".$expiryDate."', {$projectTblArray['amount_fld']}='".$amount."',{$projectTblArray['quantity_fld']}='".$quantity."',{$projectTblArray['isLive_fld']}='".$isLive."', {$projectTblArray['affiliation_id_1_fld']}='".$affiliation1."', {$projectTblArray['affiliation_id_2_fld']}='".$affiliation2."'  where {$projectTblArray['pid_fld']}=$project_id";
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
			$query = "UPDATE {$projectTblArray['database_table_name']} SET {$projectTblArray['pname_fld']}='".$projectName."', {$projectTblArray['cid_fld']}=".$cat_id.", {$projectTblArray['description_fld']}='".$description."', {$projectTblArray['expiry_date_fld']}='".$expiryDate."', {$projectTblArray['amount_fld']}='".$amount."',{$projectTblArray['quantity_fld']}='".$quantity."',{$projectTblArray['isLive_fld']}='".$isLive."', {$projectTblArray['affiliation_id_1_fld']}='".$affiliation1."', {$projectTblArray['affiliation_id_2_fld']}='".$affiliation2."'  where {$projectTblArray['pid_fld']}=$project_id";
	}
	$result = mysql_query($query);

	if(isset($project_id) && $project_id!=""){
	mysql_query("delete from project_team where project_id = $project_id");
	for($i=0; $i < count($member); $i++){
			if(isset($member[$i]) && $member[$i] !=""){
				$team_permission_query = "INSERT INTO  project_team (project_id ,  member_id , permission_id) VALUES ( ".$project_id." ,  ".$member[$i].",  ".$role[$i].")";
				//echo $team_permission_query;
				mysql_query($team_permission_query);
			}
		}
	}
	$checktags = "SELECT * FROM tags WHERE pid =".mysql_real_escape_string($_POST["pid"]);
	$result_tags = mysql_query($checktags);
	if ( mysql_num_rows($result_tags) != 0 ) {
		mysql_query("UPDATE tags SET tname='".$tags."' WHERE pid=".$project_id);
	}
	else{
		//echo "INSERT INTO tags(pid, tname) VALUES(".$project_id.", '".$tags."'";exit;
		mysql_query("INSERT INTO tags(pid, tname) VALUES(".$project_id.", '".$tags."')");
	}

	if($result){
		FlashMessage::add(PROJECT_UPDATE_SUCCESS);
		echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
		exit;
	}
	else{
		FlashMessage::add(PROJECT_NOT_UPDATE_SUCCESS);
		echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
		exit;
	}
}

/*****************************************************************/
/**********Delete Project****************************************/
/***************************************************************/
if(isset($_GET["action"]) && isset($_GET["pid"]) && $_GET["action"]=="delete" && $_GET["pid"]!=""){
	$query ="SELECT * FROM projects where pid=".$_GET["pid"]." AND uid=".$_SESSION['uid'];
	$result = mysql_query($query);
	$project = mysql_fetch_array($result);
	$project_img = 	$project['projectImage'];
	if($project_img != ""){
		if(file_exists("project_uploads/".$project_img)){
			unlink("project_uploads/".$project_img);
		}
		if(file_exists("project_uploads/thumbs/".$project_img)){
			unlink("project_uploads/thumbs/".$project_img);
		}

		$query ="DELETE FROM {$projectTblArray['tableAlias']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
		$query ="DELETE FROM {$projectTblArray['database_table_name']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];
		mysql_query($query);

		$query2 = "DELETE FROM tags where pid=".$_GET["pid"];
		mysql_query($query2);

		$query3 = "DELETE FROM project_config where pid=".$_GET["pid"];
		mysql_query($query3);

		FlashMessage::add(PROJECT_DELETE_SUCCESS);
		echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
		exit;
	}
	else{

		$query ="DELETE FROM {$projectTblArray['tableAlias']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
		$query ="DELETE FROM {$projectTblArray['database_table_name']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];

		mysql_query($query);

		$query2 = "DELETE FROM tags where pid=".$_GET["pid"];
		mysql_query($query2);

		$query3 = "DELETE FROM project_config where pid=".$_GET["pid"];
		mysql_query($query3);

		FlashMessage::add(PROJECT_DELETE_SUCCESS);
		echo "<META http-equiv='refresh' content='0;URL=projects.php'>";
		exit;
	}

}

/**********************************************************************/
/**********Delete Project Image****************************************/
/**********************************************************************/
if(isset($_GET["action"]) && isset($_GET["pid"]) && $_GET["action"]=="remove_project_image" && $_GET["pid"]!=""){
	$query ="SELECT * FROM {$projectTblArray['tableAlias']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
	$query ="SELECT * FROM {$projectTblArray['database_table_name']} where {$projectTblArray['pid_fld']}=".$_GET["pid"]." AND {$projectTblArray['uid_fld']}=".$_SESSION['uid'];


	$result = mysql_query($query);
	$project = mysql_fetch_array($result);
	$project_img = 	$project['projectImage'];

		if($project_img !=""){
			if(file_exists("project_uploads/".$project_img)){
				unlink("project_uploads/".$project_img);
			}
			if(file_exists("project_uploads/thumbs/".$project_img)){
				unlink("project_uploads/thumbs/".$project_img);
			}
			$query = "UPDATE {$projectTblArray['tableAlias']} SET {$projectTblArray['projectImage_fld']}='', {$projectTblArray['upload_care_img_url_fld']}='' where {$projectTblArray['pid_fld']}=".$_GET["pid"];
			mysql_query($query);

			$query1 = "DELETE FROM project_gallery  where pid=".$_GET["pid"];
			mysql_query($query1);

			FlashMessage::add(PROJECT_REMOVE_IMAGE_SUCCESS);
			echo "<META http-equiv='refresh' content='0;URL=createProject.php?pid=".$_GET["pid"]."'>";
			exit;
		}
		else{
			$query = "UPDATE {$projectTblArray['tableAlias']} SET  {$projectTblArray['upload_care_img_url_fld']}='' where pid=".$_GET["pid"];
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
			$query = "UPDATE {$projectTblArray['database_table_name']} SET  {$projectTblArray['upload_care_img_url_fld']}='' where pid=".$_GET["pid"];


			mysql_query($query);

			$query1 = "DELETE FROM project_gallery  where pid=".$_GET["pid"];
			mysql_query($query1);

			FlashMessage::add(PROJECT_IMAGE_REMOVE_NOT_SUCCESS);
			echo "<META http-equiv='refresh' content='0;URL=createProject.php?pid=".$_GET["pid"]."'>";
			exit;
		}
}

?>