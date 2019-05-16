<?php
/*
 *
 * masterFunctions file all forms Actions are recorded here
 * $$$$$$$$44
 * $$$$$$$$$$$$$$$$$$$$$44
 *
 * Action sequence details are as follow
 *
 *
 * if (isset($_GET["button"]) && !empty($_GET["button"]) && $_GET["button"] == 'cancel') {
 *
 * *****************
 *
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add')
 *
 * **********
 *
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'update')
 *
 * ************
 *
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'login')
 *
 */



/*
 *
 *
 *
 *
 *
 *
 * ***********************Cancel Button Action
 *
 *
 *
 *
 *
 *
 *
 *
 */
// if( isset($_GET['action']) && $_GET['action'] =='user_recorded_file'){
//   foreach ($_FILES as $file => $file2) {
//     //checking if audio file is not empty
//     if (!empty($file2['name'])) {
//         $file_name = uploadRecordedAudio($file2);
//         echo $file_name;
//         exit();
//       }
//     }
// }
if (isset($_GET["button"]) && !empty($_GET["button"]) && $_GET["button"] == 'cancel') {


  //5.6.112 as this was crashing evrything
    // update("data_dictionary", array("dd_editable" => '1'), array("display_page" => $_GET['display']));

    // exit($_SESSION[return_url2]);
    if ($_GET['table_type'] == 'child') {
        $link_to_return = $_SESSION['child_return_url'];
    }
    /*
      else if ($_GET['checkFlag'] == 'true')
      $link_to_return = $_SESSION['return_url2']; */ else if ($_GET['addFlag'] == 'true')
        $link_to_return = BASE_URL . "system/main.php?display?" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];
    else
        $link_to_return = $_SESSION['return_url2'];


    if(isset($_SESSION['link_in_case_of_DDetiable_2'])){
     $link_to_return = $_SESSION['link_in_case_of_DDetiable_2'];
     unset($_SESSION['link_in_case_of_DDetiable_2']);
   }

    if ($_GET['fnc'] != 'onepage') {
        echo "<script>window.location='$link_to_return'</script>";
    } else {
        echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }
}



/*
 *
 *
 * ***********************
 * 8*******************************
 *
 * Add Function
 *
 *
 * ************************************
 * ***************
 * *******************
 * *************************************
 */

// THIS HANDLES addimport FILEIMPORT and textarea manual import
// ALL FIELDS NEEDS TO BE ASSIGNED TO THE $_POST ARRAY WITH FEILD AS KEY AND RESPECTIVE VALUE ASSIGNED TO IT SO IT CAN BE USED INSIDE addData() function
if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add' && $_GET['actionType'] == 'addimport') {
    // GET THE IMPORT FIELDS(DB TABLE COLUMNS FROM ADDIMPORT FUNCTION PARAMTERS i.e. 4rth field and onwards
    log_event($_GET['display'],'add');
    $customFunctionImportFields = $_SESSION['addImportParameters'];
    array_splice($customFunctionImportFields, 0, 3);
    //    echo "<pre>\$customFunctionImportFields<br>";
    //    print_r($customFunctionImportFields);
    //    echo "</pre>";
    //    echo "<pre>";
    //    print_r($_GET); die;
    $importFieldsLength = count($customFunctionImportFields);
    // HANDLE addimport POPUP FORM SUBMIT for csv file import
    if(!empty($_FILES['addImportFile']['name']) )
    {
        #$file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream',
        #    'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        //        echo "<pre>";
        //        print_r($_FILES);
        //        print_r($_SESSION);
        //        print_r($_SESSION['addImportParameters']);
        //        echo "</pre>";
        $customFunctionThirdParameter = $_SESSION['addImportParameters']['2'];
        ###DEFAULT TO C IF C|T IS NOT PRESENT IN THIRD PARAM OF ADDIMPORT
        $importFieldSeparator = 'C';
        if(stripos($customFunctionThirdParameter, 'T') !== false)
        {
            $importFieldSeparator = 'T';
        }
        //         $csvFile = fopen($_FILES['addImportFile']['tmp_name'], 'r');
        //        fclose($csvFile);
        //        print_r(fgetcsv($csvFile) );
        //        die;
        #  [0] => 18 [1] => csv test product1 [2] => Description of test product 1 [3]
        if (!empty($_FILES['addImportFile']['name']) ) { ##&& in_array($_FILES['file']['type'], $file_mimes)
            if (is_uploaded_file($_FILES['addImportFile']['tmp_name'])) {
                $csvFile = fopen($_FILES['addImportFile']['tmp_name'], 'r');
                //fgetcsv($csv_file);
                $csvRowNumber = 1;
                $errorCsvRows = array();
                $successCsvRows = array();
                // get data records from csv file
                while (($csvRowData = fgetcsv($csvFile)) !== FALSE) {
                    $fieldCountCsvRow = count($csvRowData);
                    if(!empty($csvRowData) && $fieldCountCsvRow == $importFieldsLength)
                    {
                        #"$csvRowData[0], $csvRowData[1], $csvRowData[2], $csvRowData[3], $csvRowData[4]";
                        // Check if employee already exists with same email
                        #$sql_query = "SELECT emp_id, emp_name, emp_salary, emp_age FROM emp WHERE emp_email = '" . $emp_record[2] . "'";###PARAMS FIELDS AND THEN THE DB TABLE TO INSERT INTO WE CHECK AND SKIP IF EXISTS.
                        #$resultset = mysqli_query($conn, $sql_query) or die("database error:" . mysqli_error($conn));
                        // if employee already exist then update details otherwise insert new record
                        #if (mysqli_num_rows($resultset)) {
                        #    $sql_update = "UPDATE emp set emp_name='" . $emp_record[1] . "', emp_salary='" . $emp_record[3] . "', emp_age='" . $emp_record[4] . "' WHERE emp_email = '" . $emp_record[2] . "'";
                        #    mysqli_query($conn, $sql_update) or die("database error:" . mysqli_error($conn));
                        #} else {
                        #    $mysql_insert = "INSERT INTO emp (emp_name, emp_email, emp_salary, emp_age )VALUES('" . $emp_record[1] . "', '" . $emp_record[2] . "', '" . $emp_record[3] . "', '" . $emp_record[4] . "')";
                        #    mysqli_query($conn, $mysql_insert) or die("database error:" . mysqli_error($conn));
                        #}
                        ###ASSIGN DATA TO $_POST WITH RESPECTIVE FIELD KEYS FROM addimport Parameters AND VALUES FROM CSV FILE###
                        for($i = 0; $i < $importFieldsLength; $i++)
                        {
                            $_POST[$customFunctionImportFields[$i]] = $csvRowData[$i];
                        }
                        ###CALL addData() to insert each single row#####
                        $insertStatus = addData();
                        if($insertStatus == false)
                        {
                            $errorCsvRows[$csvRowNumber] = "Either field count didn't match or some other error occured while importing from CSV.";
                        }
                        else
                        {
                            $successCsvRows[$csvRowNumber] = "CSV Row imported Successfully.";
                        }
					/*Code Changes for Task 5.4.108 start*/
					}elseif(count($csvRowData)>0){
							//echo "skip empty record";
					}
					/*Code Changes for Task 5.4.108 end*/
                    else
                    {
                        $errorCsvRows[$csvRowNumber] = "Either field count didn't match or some other error occured while importing from CSV.";
                    }
                    $csvRowNumber++;
                }
                fclose($csvFile);
            }
        }

        $_SESSION['errorsAddImport'] = $errorCsvRows;
        $_SESSION['SuccessAddImport'] = $successCsvRows;
    }
    ###HANDLE addimport POPUP FORM SUBMIT for csv file import###
    else if(!empty($_POST['addImportText']) )
    {
        //        echo "<pre>";
        //        var_dump($_POST['addImportText']);
        $textAreaAddImporTData = $_POST['addImportText'];

        ###UNSET THE $_POST FOR TEXTAREA AS THE addData() function Relies on $_POST###
        unset($_POST['addImportText']);

        $importRowsArray = explode("\r\n", $textAreaAddImporTData);#explode("\n", $textAreaAddImporTData);

        #print_r($importRowsArray);

        $errorTextRows = array();
        $successTextRows = array();

        $textRowNumber = 1;

        foreach ($importRowsArray as $key => $importTextRowData) {

            $importTextRowFeildsData = explode(',', $importTextRowData);##GET ALL FIELDS SEPARATED BY comma ,##

            $fieldCountTextRow = count($importTextRowFeildsData);


            if(!empty($importTextRowFeildsData) && $fieldCountTextRow == $importFieldsLength)
            {
                ###ASSIGN DATA TO $_POST WITH RESPECTIVE FIELD KEYS FROM addimport Parameters AND VALUES FROM CSV FILE###
                for($i = 0; $i < $importFieldsLength; $i++)
                {
                    $_POST[$customFunctionImportFields[$i]] = $importTextRowFeildsData[$i];
                }

                ###CALL addData() to insert each single row#####
                $insertStatus = addData();
                if($insertStatus == false)
                {
                    $errorTextRows[$textRowNumber] = "Either field count didn't match or some other error occured while importing from CSV.";
                }
                else
                {
                    $successTextRows[$textRowNumber] = "Manual Row data imported Successfully.";
                }
				/*Code Changes for Task 5.4.108 start*/
            }elseif(count($importTextRowFeildsData)>0){
					//echo "skip empty record";
			}
			/*Code Changes for Task 5.4.108 end*/
            else
            {
                $errorTextRows[$textRowNumber] = "Either field count didn't match or some other error occured while importing from Manual input.";
            }

            $textRowNumber++;

        }

        $_SESSION['errorsAddImport'] = $errorTextRows;
        $_SESSION['SuccessAddImport'] = $successTextRows;
    }
    //    print_r($_SESSION['errorsAddImport']);
    //    print_r($_SESSION['SuccessAddImport']);
    //    die("TESING CSV");

    ###REDIRECT TO THE LIST FROM WHICH USER CAME AND SHOW A POPUP MESSAGE REGARDING SUCCESS/ERRORS ACCORDINGLY############
    $link_to_return = BASE_URL . "system/main.php?display=" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];



    ###http://localhost/GenericPlatform/system/main.php?display=myproduct&tab=products&tabNum=1&ta=products&search_id=91&checkFlag=true&table_type=parent&edit=true#false
    ###ADD ADDITIONAL REDIRECT PARAMS FOR REDIRETING TO VIEW AND EDIT OPERATIONS
    if(!empty($_GET['search_id']) )
    {
        $link_to_return .= "&search_id={$_GET['search_id']}";
    }
    if(!empty($_GET['edit']))
        $link_to_return .= "&edit={$_GET['edit']}";

    if ($_GET['fnc'] != 'onepage') {
        echo "<script>window.location='$link_to_return'</script>";
    } else {
        echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }

}


#####THIS WILL HANDLE ALL ADD OPERATIONS IN COMMON SO IT WILL BE A FUNCTION INSTEAD. IT SHOULD HANDLE INDIVIDUAL ADD AS WELL AS BULK IMPORT/ADD#####
else if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add') {
  log_event($_GET['display'],'add');
  addData();
}

function addData()
{

	$save_add_url = "";
    if (array_key_exists('save_add_record', $_POST)) {
		$save_add_url = $_SESSION['save_add_url'];
        unset($_POST['save_add_record']);
		unset($_SESSION['save_add_url']);
    }

	if (array_key_exists('field_name_unique', $_POST)) {
        unset($_POST['field_name_unique']);
    }
    if (array_key_exists('old_audio', $_POST)) {
        unset($_POST['old_audio']);
    }
    if (array_key_exists('recorded_audio', $_POST)) {
        unset($_POST['recorded_audio']);
    }
    $row = get('data_dictionary', 'dict_id=' . $_SESSION['dict_id']);


    ### if addimport then CHECK FOR DD.table_type = PARENT/CHILD AND IF CHILD THEN FIELD ITS PARENT DD.dict_id and its keyfield and autoincrement it###
    if($_GET['actionType'] == 'addimport' || 1)
    {
        $keyfield = $row['keyfield'];
        $tableType = strtolower(trim($row['table_type']) );

        #echo "<font color=red>\$keyfield:$keyfield :: \$tableType:$tableType " . var_dump((int)$_SESSION['parent_value'] ) . " </font><br>";

        if ($tableType == 'parent' && !empty($keyfield) )
        {
            ##$_SESSION['parent_key_value'] = $keyfield;
        }
        ###if the table is a child table - then the function must add the parent ID to every record
        else if ($tableType == 'child' && !empty($keyfield))
        {
            #$rowParent = get('data_dictionary', 'dict_id=' . $_SESSION['dict_id']);
            // DONT ASSIGN STRING VALUES, ONLY ASSIGN REAL PRIMARY KEYS WHICH WILL ALWAYS BE INTEGERS OR 0 IF TYPECASTED FROM STRING

            if((int)$_SESSION['parent_value'] !== 0 ){
              $_POST["$keyfield"] = $_SESSION['parent_value'];
            }
                //SET KEYFIELD TO THE PARENT VALUE TO PRESERVE PARENT->CHILD RELATIONSHIP
        }
    }
    if (!empty($row['list_filter'])) {
        $listFilter = explode(";", $row['list_filter']);
        $firstParent = $listFilter[0];
        if (!empty($listFilter[1])) {
            $listCond = $listFilter[1];
        }
        if (!empty($listFilter[0])) {
            $i = 0;
            $firstListFilter = explode(",", $listFilter[0]);
            foreach ($firstListFilter as $val) {
                $condition = explode("=", $val);
                $keyVal[$i] = array(trim($condition[0]) => trim($condition[1]));
                $i++;
            }
        }
        foreach ($keyVal as $val) {
            if (!empty($val['projects'])) {
                $pid = $val['projects'];
            }
            if (!empty($val['users'])) {
                $uid = $val['users'];
            }
        }
        if (!empty($pid)) {
            $project = array($pid => $_SESSION['search_id2']);
        }
        if (!empty($uid)) {
            $user = array($uid => $_SESSION['uid']);
        }
    }
    $data = $_POST;
    if (!empty($user)) {
        $userKey = key($user);
        if (array_key_exists($userKey, $data))
            unset($data[$userKey]);
        $data = array_merge($user, $data);
    }

    if (!empty($project)) {
        $projectKey = key($project);

        if (array_key_exists($projectKey, $data))
            unset($data[$projectKey]);

        $data = array_merge($project, $data);
    }
    unset($data['imgu']);
    ////assigning user_id field a value of current session if list_filter doesn't' have
    $field = 'false';
    if (empty($user)) {
        $tblName = $_SESSION['update_table2']['database_table_name'];
        $con = connect();
        $rs = $con->query("SHOW COLUMNS FROM $tblName");
        while ($fdCol = $rs->fetch_assoc()) {
            if ($fdCol['Field'] == 'user_id') {
                $field = 'true';
                break;
            }
        }
    }
    if ($field == 'true') {
        $additional_array = array('user_id' => $_SESSION['uid']);
        $data = array_merge($additional_array, $data);
    }
    if(empty(trim($_POST[$keyfield]))){
      if($row['parent_table'] =='user'){
        $data[$keyfield] = $_SESSION['uid'];
      }
    }
	/* Storing Base Latitude and Longitude Start*/
	//$dataFdRecord = getDataFieldRecordByDictId($_SESSION['dict_id']);
	//$data = setBaseGpsCordinate($dataFdRecord,$data);
	/* Storing Base Latitude and Longitude End*/
    $check = insert($_SESSION['update_table2']['database_table_name'], $data);
    ###RETURN INSTEAD OF REDIRECT FOR addimport ACTION
    if($_GET['actionType'] == 'addimport') {
        return $check;
    }
    if(!empty($save_add_url)){
		$link_to_return = $save_add_url;
	} else {
		$link_to_return = BASE_URL . "system/main.php?display=" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];
	}

    if ($_GET['fnc'] != 'onepage') {
        echo "<script>window.location='$link_to_return'</script>";
    } else {
        echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }


}


/*
 *
 * Update function STARTS here
 * Also check table type to call function;
 *
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'update') {
    $_GET['table_type'] = trim(strtolower($_GET['table_type']));
	switch($_GET['table_type']){
		case 'login':
			callToLogin();
			break;

		case 'signup':
			callToSignup();
			break;

		case 'facebooklogin':
			echo json_encode(facebookLogin());
			break;

		case 'linkedinlogin':
			echo json_encode(linkedInLogin());
			break;

		case 'linkedinimportprofile':
			echo json_encode(importProfileFromLinkedIn());
			break;

		case 'forgotpassword':
			forgotPassword();
			break;

		case 'reset_password':
			resetPassword();
			break;

		case 'change_password':
			changePassword();
			break;

		default:
			$save_add_url = "";
			if (array_key_exists('save_add_record', $_POST)) {
				$save_add_url = $_SESSION['save_add_url'];
				unset($_POST['save_add_record']);
				unset($_SESSION['save_add_url']);
			}

			if (array_key_exists('old_audio', $_POST)) {
				unset($_POST['old_audio']);
			}
			$ddRecord = get('data_dictionary', 'dict_id=' . $_SESSION['dict_id']);
      if($ddRecord['dd_editable']=='11'){
        $_SESSION['show_with_edit_button'] = true;
        $_SESSION['show_with_edit_button_DD'] = $ddRecord['dict_id'];

      }
			$ddRecord['keyfield'] = firstFieldName($ddRecord['database_table_name']);
			/****** for pdf files ********** */
			foreach ($_POST['pdf'] as $img => $img2) {
				if (!empty($img2['uploadcare']) && !empty($img2['imageName'])) {
					$ret = uploadPdfFile($img2['uploadcare'], $img2['imageName']);
					$oldImage = $_POST[$img]['img_extra'];
					if (!empty($ret['image'])) {
						unset($_POST['pdf'][$img]);
						$_POST[$img] = $ret['image'];
					}
					//if user want to replace current image
					if (!empty($oldImage)) {
						@unlink(USER_UPLOADS . "pdf/$oldImage");
					}
					//if user didn't touch the with images
				} else {
					//if user clicks on remove current image
					if (empty($img2['uploadcare']) && !empty($img2['imageName'])) {
						if (!empty($_POST[$img]['img_extra'])) {
							@unlink(USER_UPLOADS . "pdf/$img2[imageName]");
							unset($_POST['pdf'][$img]);
							$_POST[$img] = '';
						} else {
							unset($_POST['pdf'][$img]);
						}
					} else {
						unset($_POST['pdf'][$img]);
					}
				}
			}
			//deleting array which is used for holding imgu values
			if (empty($_POST['pdf']))
				unset($_POST['pdf']);

			/*****For images********** */
			foreach ($_POST['imgu'] as $img => $img2) {
				if (!empty($img2['uploadcare']) && !empty($img2['imageName'])) {
					$ret = uploadImageFile($img2['uploadcare'], $img2['imageName']);
					$oldImage = $_POST[$img]['img_extra'];
					if (!empty($ret['image'])) {
						unset($_POST['imgu'][$img]);
						$_POST[$img] = $ret['image'];
					}
					//if user want to replace current image
					if (!empty($oldImage)) {
						@unlink(USER_UPLOADS . "$oldImage");
						@unlink(USER_UPLOADS . "thumbs/$oldImage");
					}
					//if user didn't touch the with images
				} else {
					//if user clicks on remove current image
					if (empty($img2['uploadcare']) && !empty($img2['imageName'])) {
						if (!empty($_POST[$img]['img_extra'])) {
							@unlink(USER_UPLOADS . "$img2[imageName]");
							@unlink(USER_UPLOADS . "thumbs/$img2[imageName]");
							unset($_POST['imgu'][$img]);
							$_POST[$img] = '';
						} else {
                            if (empty($_POST['user_id'])){
                                echo "<script>
                                        alert('Please upload some photo and then update.');
                                        window.location.href = document.referrer;
                                    </script>";
                            }
							unset($_POST['imgu'][$img]);
						}
					} else {
						unset($_POST['imgu'][$img]);
					}
				}
			}
			//deleting array which is used for holding imgu values
			if (empty($_POST['imgu']))
				unset($_POST['imgu']);

    $recordedFileNmae = '';
    if(isset($_POST['recorded_audio']) && !empty($_POST['recorded_audio'])){
      $recordedFileNmae = uploadRecordedAudio($_POST['recorded_audio']);
      if(!empty($recordedFileNmae)){
        $_POST['temp_audio_file'] = $recordedFileNmae;
      }
    }
			foreach ($_FILES as $file => $file2) {
				//checking if audio file is not empty
				if (!empty($file2['name'])) {
            $file_name = uploadAudioFile($file2);
  					// if file successfully uploaded to target dir
  					if (!empty($file_name)) {
  						$_POST[$file] = $file_name;
  					}
				} else {
					$_POST[$file] = '';
				}
				//Dealing with database now
				$row = getWhere($ddRecord['database_table_name'], array($ddRecord['keyfield'] => $_SESSION['search_id2']));
				$oldFile = $row[0][$file];
				if ($oldFile != "") {
					if (file_exists(USER_UPLOADS . "audio/" . $oldFile)) {
						@unlink(USER_UPLOADS . "audio/" . $oldFile);
					}
				}
			}

      if(isset($_POST['temp_audio_file'])){
        $_POST['audio_file'] = $_POST['temp_audio_file'];
      }
      // die();
			/* Storing Base Latitude and Longitude Start*/
			$dataFdRecord = getDataFieldRecordByDictId($_SESSION['dict_id']);
			$_POST = setBaseGpsCordinate($dataFdRecord,$_POST);
			/* Storing Base Latitude and Longitude End*/

			/* removing extra fields that are not preset in table start */
			$tableFields = getColumnNames($ddRecord['database_table_name']);
			$fieldsNotPresent = array_diff_key($_POST,$tableFields);
			if(!empty($fieldsNotPresent)){
				foreach($fieldsNotPresent as $key=>$value){
					unset($_POST[$key]);
				}
			}
			/* removing extra fields that are not preset in table end */
			$status = update($ddRecord['database_table_name'], $_POST, array($ddRecord['keyfield'] => $_SESSION['search_id2']));

      // **** DISABLED BY CJ (this reset dd_editable!!)			update('data_dictionary', array('dd_editable' => '1'), array('dict_id' => $_SESSION['dict_id']));

			if ($_GET['checkFlag'] == 'true') {
				if($save_add_url){
					$link_to_return = $save_add_url;
				}else{
					if ($_GET['table_type'] == 'child'){
						$link_to_return = $_SESSION['child_return_url2'];
					} else {
						$link_to_return = $_SESSION['return_url2'];
					}

                    if(isset($_SESSION['link_in_case_of_DDetiable_2'])){
                     $link_to_return = $_SESSION['link_in_case_of_DDetiable_2'];
                     unset($_SESSION['link_in_case_of_DDetiable_2']);
                   }
				}
				if ($_GET['fnc'] != 'onepage') {
					//exit($link_to_return);
					if($status === true)
						echo "<script>window.location='$link_to_return';</script>";
					else
					{
						echo "<script> alert(\"$status\"); window.location='$link_to_return'; </script>";
					}
				} else {
					if($status === true)
						echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]';</script>";
					else
					{
						echo "<script> alert(\"$status\"); window.location='$link_to_return$_SESSION[anchor_tag]'; </script>";
					}
				}
			} else {
				if (!empty($_SESSION['display2'])) {
					$_SESSION[display] = $_SESSION['display2'];
				}
				if ($_GET['fnc'] != 'onepage') {
					if($status === true)
						echo "<script>window.location = '?display=$_SESSION[display]&tab=$_SESSION[tab]&tabNum=$_GET[tabNum]';</script>";
					else
					{
						echo "<script> alert(\"$status\"); window.location = '?display=$_SESSION[display]&tab=$_SESSION[tab]&tabNum=$_GET[tabNum]'; </script>";
					}
				} else {
					if($status === true)
						echo "<script>window.location='$_SESSION[return_url2]$_SESSION[anchor_tag]';</script>";
					else
					{
						echo "<script> alert(\"$status\"); window.location='$_SESSION[return_url2]$_SESSION[anchor_tag]'; </script>";
					}
				}
			}
		break;
	}
	exit;
}

/*
 * // To Do: Login Function functionality
 *
 */

function callToLogin(){
	$table = $_SESSION['update_table2']['database_table_name'];
  $primaryKey = $_SESSION['update_table2']['keyfield'];
  $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$query = "SELECT * FROM $table WHERE ";
		$emailValue = trim($_POST[$_SESSION['user_field_email']]);
		$usernameValue = trim($_POST[$_SESSION['user_field_uname']]);
		$passValue = trim($_POST[$_SESSION['user_field_password']]);
		if( (!empty($emailValue) || !empty($usernameValue) ) && !empty($passValue)){
			$passValue = md5($passValue);
			if($emailValue){
				$query .= "$_SESSION[user_field_email] = '$emailValue'" ;
			} elseif($usernameValue){
				$query .= "$_SESSION[user_field_uname] = '$usernameValue'" ;
			}
			$query .= " AND $_SESSION[user_field_password] = '$passValue'" ;
			$userQuery = $con->query($query) OR $message = mysqli_error($con);
			if($userQuery->num_rows > 0){
				$user = $userQuery->fetch_assoc();
				if($user['isActive'] == 1){
					$_SESSION['uid'] = $user[$primaryKey];
                    setUserDataInSession($con,$user);
					$message = '';
					//$message = PROFILE_COMPLETE_MESSAGE;
					$returnUrl = BASE_URL."index.php";
				} else {
					$message = "Your account is Inactive. Please contact to administrator";
				}
			} else {
			    $message = resetPasswordIfFlagIsSet($con);
                if($message === false){
                  $message = "Invalid Username/Email or Password";
                }else{
                  $returnUrl = BASE_URL."index.php";
                }
			}
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
  log_event('login','login');
	echo "<script>window.location='".$returnUrl."';</script>";
}
/*
 * // To Do: Login Function functionality
 *
 */
function callToSignup(){

  log_event($_GET['display'],'signup');

	$table = $_SESSION['update_table2']['database_table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$emailField = $_SESSION['user_field_email'];
		$passField = $_SESSION['user_field_password'];
		$unameField = $_SESSION['user_field_uname'];

		$emailValue = trim($_POST[$_SESSION['user_field_email']]);
		$passValue = trim($_POST[$_SESSION['user_field_password']]);
		$unameValue = trim($_POST[$_SESSION['user_field_uname']]);
		if(!empty($emailValue) && !empty($passValue) && !empty($unameValue)){
			$checkEmailUnique = $con->query("SELECT * FROM $table WHERE $emailField = '$emailValue'");
			if($checkEmailUnique->num_rows > 0){
				$message = "Email address already exist.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$checkUsernameUnique = $con->query("SELECT * FROM $table WHERE $unameField = '$unameValue'");
			if($checkUsernameUnique->num_rows > 0){
				$message = "Username already exist.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$_POST = array_map("trim", $_POST);
			$data = $_POST;
			$data[$passField] = md5($data[$passField]);
			$data['user_privilege_level'] = 5;
			$data['isActive'] = 0;
			$data['token'] = uniqid();
			$data['timeout'] = date('Y-m-d H:i:s',strtotime('+1 day'));
			$user_id = insert($table,$data);
			$status = sendVerificationEmail($data);
			if($status == true){
				$message = "Please verify your email address sent to your email";
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = $status;
			}
		}

	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: forgotPassword Function
 *
 */
function forgotPassword(){
	$table = $_SESSION['update_table2']['database_table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$emailField = $_SESSION['user_field_email'];
		$emailValue = trim($_POST[$_SESSION['user_field_email']]);
		if(!empty($emailValue)){
			$checkEmail = $con->query("SELECT * FROM $table WHERE $emailField = '$emailValue'");
			if($checkEmail->num_rows == 0){
				$message = "Email address is not registered with us.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$user = $checkEmail->fetch_assoc();
			if($user['isActive'] == 1){
				$data['token'] = uniqid();
				$data['timeout'] = date('Y-m-d H:i:s',strtotime('+1 day'));
				$where = array($emailField => $emailValue);
				update($table,$data,$where);
				$status = sendResetLinkEmail($data,$emailValue);
				if($status == true){
					$message = "Please check your email with reset password link.";
					$returnUrl = BASE_URL."index.php";
				} else {
					$message = $status;
				}
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}

	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: resetPassword Function
 *
 */
function resetPassword(){
	$table = $_SESSION['update_table2']['database_table_name'];
	$token = $_SESSION['reset_token'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$checkToken = $con->query("SELECT * FROM $table WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
		if($checkToken->num_rows > 0){
			$passwordField = $_SESSION['user_field_password'];
			$confirmPasswordField = $_SESSION['user_field_confirm_password'];
			$passwordValue = trim($_POST[$passwordField]);
			$confirmPasswordValue = trim($_POST[$confirmPasswordField]);
			if(empty($passwordValue)){
				$message = "Please enter new password.";
			} elseif(empty($confirmPasswordValue)){
				$message = "Please confirm your password.";
			} elseif($passwordValue != $confirmPasswordValue){
				$message = "Your passwords do not match. Please type carefully.";
			} else {
				$user = $checkToken->fetch_assoc();
				$passwordValue = md5($passwordValue);
				$update = $con->query("UPDATE $table SET $passwordField='$passwordValue' , token=NULL ,timeout=NULL WHERE token = '$token'");
				unset($_SESSION['reset_token']);
				$message = "Your password has been changed. Please login.";
				$returnUrl = BASE_URL."index.php";
			}
		} else {
			$returnUrl = BASE_URL."index.php";
			$message = "Invalid or expired token. Please check your email or try again.";
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: change Password Function
 *
 */
function changePassword(){
	$table = $_SESSION['update_table2']['database_table_name'];
	$token = $_SESSION['reset_token'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $user_id = $_SESSION['uid'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$userQuery = $con->query("SELECT * FROM $table WHERE $primaryKey = '$user_id'");
		if($userQuery->num_rows > 0){
			$oldPasswordField = $_SESSION['user_field_old_password'];
			$passwordField = $_SESSION['user_field_password'];
			$confirmPasswordField = $_SESSION['user_field_confirm_password'];
			$oldPasswordValue = trim($_POST[$oldPasswordField]);
			$passwordValue = trim($_POST[$passwordField]);
			$confirmPasswordValue = trim($_POST[$confirmPasswordField]);
			if(empty($oldPasswordValue)){
				$message = "Please enter old password.";
			} elseif(empty($passwordValue)){
				$message = "Please enter your new password.";
			} elseif(empty($confirmPasswordValue)){
				$message = "Please confirm your password.";
			} elseif($passwordValue != $confirmPasswordValue){
				$message = "Your passwords do not match. Please type carefully.";
			} else {
				$user = $userQuery->fetch_assoc();
				if(md5($oldPasswordValue) != $user[$passwordField]){
					$message = "Your old password is incorrect. Please try again.";
				} else {
					$passwordValue = md5($passwordValue);
					$update = $con->query("UPDATE $table SET $passwordField='$passwordValue' WHERE $primaryKey = '$user_id'");
					$message = "Your password has been changed.";
					$returnUrl = BASE_URL."index.php";
				}
			}
		} else {
			$returnUrl = BASE_URL."index.php";
			$message = "Invalid user. Please check your email or try again.";
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}


function sendResetLinkEmail($data,$email){
	$returnUrl = $_SESSION['return_url2'];
	$to = $email;
	$token = $data['token'];
	$table = $_SESSION['update_table2']['database_table_name'];
	$url = BASE_URL_SYSTEM."main.php?action=reset_email&table=$table&token=$token";
	$subject = "Reset Password | Generic Platform";
	$message = "<html><head><title>Reset Password</title></head><body>";
	$message .= "Hi,<br/>";
	$message .= "Please click <a href='".$url."'>here</a> to reset your password or visit the below link.</br>";
	$message .= "$url";
	$message .= "<br/><br/>Regards,<br>Generic Platform";
	$message .= "</body></html>";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
	//$headers .= 'Cc: myboss@example.com' . "\r\n";

	try{
		$sent = mail($to,$subject,$message,$headers);
		if($sent){
			return true;
		}
		return "Unable to send email";
	} catch(Exception $e){
		return $e->getMessage();
	}
}

function sendVerificationEmail($data){
	$returnUrl = $_SESSION['return_url2'];
	$to = $data[$_SESSION['user_field_email']];
	$token = $data['token'];
	$table = $_SESSION['update_table2']['database_table_name'];
	$url = BASE_URL_SYSTEM."main.php?action=verify_registration_email&table=$table&token=$token";
	$subject = "Email Verification | Generic Platform";
	$message = "<html><head><title>Email Verification</title></head><body>";
	$message .= "Hi,<br/>";
	$message .= "Please click <a href='".$url."'>here</a> to verify your email address or visit the below link.</br>";
	$message .= "$url";
	$message .= "<br/><br/>Regards,<br>Generic Platform";
	$message .= "</body></html>";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
	//$headers .= 'Cc: myboss@example.com' . "\r\n";

	try{
		$sent = mail($to,$subject,$message,$headers);
		if($sent){
			return true;
		}
		return "Unable to send email";
	} catch(Exception $e){
		return $e->getMessage();
	}
}
/*
 * // To Do: Login Function functionality
 *
 */
function facebookLogin(){
	$table = $_SESSION['update_table2']['database_table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$facebookIdValue = $_POST['id'];
		$emailField = $_SESSION['user_field_email'];
		$unameField = $_SESSION['user_field_uname'];
		$emailValue = $_POST['email'];
		$passField = $_SESSION['user_field_password'];
		if(!empty($emailValue)){
			// Check If email already exist. If exist then create a new session of that user
			$check = "SELECT * FROM $table WHERE $emailField = '$emailValue'";
			$checkQuery = $con->query($check);
			$data[$emailField] = $emailValue;
			$data['oauth_provider'] = 'Facebook';
			$data['facebook_account'] = $facebookIdValue;
			if($checkQuery->num_rows > 0){
				$user = $checkQuery->fetch_assoc();
				$where = array($primaryKey => $user[$primaryKey]);
				update($table,$data,$where);
				$user = get($table,"$primaryKey=$user[$primaryKey]");
			} else {
				// Insert new record in users table
				$uname = explode("@",$emailValue)[0];
				$data[$unameField] = $uname.time();
				$data['user_privilege_level'] = 5;
				$user_id = insert($table,$data);
				$user = get($table,"$primaryKey=$user_id");
			}
			if($user['isActive'] == 1){
				$_SESSION['uid'] = $user[$primaryKey];
                setUserDataInSession($con,$user);
				$message = '';
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}

/*
 * // To Do: Login Function functionality
 *
 */
function linkedInLogin(){
	$table = $_SESSION['update_table2']['database_table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$linkedInIdValue = $_POST['id'];
		$emailField = $_SESSION['user_field_email'];
		$unameField = $_SESSION['user_field_uname'];
		$emailValue = $_POST['emailAddress'];
		$passField = $_SESSION['user_field_password'];
		if(!empty($emailValue)){
			// Check If email already exist. If exist then create a new session of that user
			$check = "SELECT * FROM $table WHERE $emailField = '$emailValue'";
			$checkQuery = $con->query($check);
			$data[$emailField] = $emailValue;
			$data['oauth_provider'] = 'linkedIn';
			if($checkQuery->num_rows > 0){
				$user = $checkQuery->fetch_assoc();
				$where = array($primaryKey => $user[$primaryKey]);
				update($table,$data,$where);
				$user = get($table,"$primaryKey=$user[$primaryKey]");
			} else {
				// Insert new record in users table
				$uname = explode("@",$emailValue)[0];
				$data[$unameField] = $uname.time();
				$data['user_privilege_level'] = 5;
				$user_id = insert($table,$data);
				$user = get($table,"$primaryKey=$user_id");
			}
			if($user['isActive'] == 1){
				$_SESSION['uid'] = $user[$primaryKey];
                setUserDataInSession($con,$user);
				$message = '';
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}

/*
 * // To Do: Import Profile From LinkedIn
 *
 */
function importProfileFromLinkedIn(){
	$display_page = $_GET['display_page'];
	$returnUrl = BASE_URL."index.php";
	$message = "";
	$con = connect();
	$query = "SELECT * FROM field_dictionary
					INNER JOIN data_dictionary ON (data_dictionary.`table_alias` = field_dictionary.`table_alias`)
					where data_dictionary.table_alias = '$display_page' ORDER BY field_dictionary.display_field_order";
	$ddQuery = $con->query($query);
	if($ddQuery->num_rows > 0){

		$data = array();
		while($ddRecord = $ddQuery->fetch_assoc()){
			$table = $ddRecord['database_table_name'];
			$primaryKey = $ddRecord['keyfield'];
			$fieldIdentifier = trim($ddRecord['field_identifier']);
			switch($fieldIdentifier){
				case 'firstname':
					$data[$ddRecord['generic_field_name']] = $_POST['firstName'];
					break;

				case 'lastname':
					$data[$ddRecord['generic_field_name']] = $_POST['lastName'];
					break;

				case 'country':
					$countryCode = strtolower($_POST['location']['country']['code']);
					$countryDetails = file_get_contents("https://restcountries.eu/rest/v2/alpha/$countryCode");
					$countryDetails = json_decode($countryDetails,true);
					if(!empty($countryDetails)){
						$data[$ddRecord['generic_field_name']] = $countryDetails['name'];
					}
					break;

				case 'about':
					$data[$ddRecord['generic_field_name']] = $_POST['headline'];
					break;

				case 'profileimage':
					if(isset($_POST['pictureUrl']) && !empty($_POST['pictureUrl'])){
						$content = file_get_contents($_POST['pictureUrl']);
						//Store in the filesystem.
						$image_name = uniqid().".jpg";
						$fp = fopen(USER_UPLOADS.$image_name, "w");
						fwrite($fp, $content);
						fclose($fp);
						$data[$ddRecord['generic_field_name']] = $image_name;
					}
					break;

				case 'description':
					$data[$ddRecord['generic_field_name']] = $_POST['summary'];
					break;
			}
		}
		$user = get($table,"$primaryKey=$_SESSION[uid]");
		if(!empty($user) && $user['oauth_provider'] == 'linkedIn'){
			if(!empty($data)){
				$where = array($primaryKey => $_SESSION['uid']);
				update($table,$data,$where);
				$message = 'Profie Imported successfully.';
			}
		} else {
			$message = 'Your account is not signed up through LinkedIn.';
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}



/*
 *
 *
 *
 * ********************Login Function
 *
 *
 *
 *
 *
 *
 *
 *


 *  */



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'login') {


    $tbl = $_SESSION['select_table']['database_table_name'];

    $pKey = $_SESSION['select_table']['keyfield'];

    $con = connect();

    $loginKeys = array_keys($_POST);

    $value1 = $_POST[$loginKeys[0]];

    $value2 = $_POST[$loginKeys[1]];
	if(!empty($value1) && !empty($value2)){
		$userName = $_SESSION['select_table']['username'];

		//exit("SELECT * FROM $tbl where $loginKeys[0] = '$value1' or $userName = '$value1' and $loginKeys[1] = '$value2' ");

		$rs = $con->query("SELECT * FROM $tbl where $loginKeys[0] = '$value1' or $userName = '$value1' and $loginKeys[1] = '$value2' ");

		$row = $rs->fetch_assoc();


		if ($row) {

			$_SESSION['uid'] = $row[$pKey];

			$_SESSION['uname'] = $row[$_SESSION['select_table']['username']];

			$_SESSION['user_privilege'] = $row[user_privilege_level];

			if (isset($_SESSION['callBackPage'])) {


				echo "<META http-equiv='refresh' content='0;URL=" . $_SESSION['callBackPage'] . "'>";

				unset($_SESSION['callBackPage']);
				exit();
			} else {

				FlashMessage::add(PROFILE_COMPLETE_MESSAGE);
				echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL . "index.php'>";
				exit();
			}
		} else {
			FlashMessage::add('UserName or Password Incorrect.');
			echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
			exit();
		}
	} else {
		FlashMessage::add('UserName or Password Incorrect.');
		echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
		exit();
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'genericlogin') {


    $tbl = $_SESSION['select_table']['database_table_name'];

    $pKey = $_SESSION['select_table']['keyfield'];

    $con = connect();

    $loginKeys = array_keys($_POST);

    $value1 = $_POST[$loginKeys[0]];

    $value2 = $_POST[$loginKeys[1]];

	if(!empty($value1) && !empty($value2)){
		$rs = $con->query("SELECT * FROM $tbl where $loginKeys[0] = '$value1' and $loginKeys[1] = '$value2' ");
		$row = $rs->fetch_assoc();
		if ($row) {

			$_SESSION['uid'] = $row[$pKey];

			$_SESSION['uname'] = $row[$_SESSION['select_table']['username']];

			$_SESSION['user_privilege'] = $row[user_privilege_level];

			if (isset($_SESSION['callBackPage'])) {


				echo "<META http-equiv='refresh' content='0;URL=" . $_SESSION['callBackPage'] . "'>";

				unset($_SESSION['callBackPage']);
				exit();
			} else {

				FlashMessage::add(PROFILE_COMPLETE_MESSAGE);
				echo BASE_URL;die;
				header("Location:".BASE_URL);
				exit();
			}
		} else {
			FlashMessage::add('UserName or Password Incorrect.');
			header("Location:".$_POST['uri']);
			exit();
		}
	} else {
		FlashMessage::add('UserName or Password Incorrect.');
		header("Location:".$_POST['uri']);
		exit();
	}
}


/**
 * Verify Token and update status to active
 *
 **/
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'verify_registration_email'){
	verifyEmail();
}

function verifyEmail(){
	$con = connect();
	$returnUrl = BASE_URL."index.php";
	$token = trim($_GET['token']);
	$table = trim($_GET['table']);
	$checkToken = $con->query("SELECT * FROM $table WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
	if($checkToken->num_rows > 0){
		$update = $con->query("UPDATE $table SET token=NULL ,timeout=NULL,isActive=1 WHERE token = '$token'");
		$message = "Your email has been verified.";
	} else {
		$message = "Invalid or expired token. Please check your email or try again.";
	}

	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'reset_email'){
	redirectToResetPassword();
}

function redirectToResetPassword(){
	$con = connect();
	$returnUrl = BASE_URL."index.php";
	$token = trim($_GET['token']);
	$table = trim($_GET['table']);
	$checkToken = $con->query("SELECT * FROM $table WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
	if($checkToken->num_rows > 0){
		$dataDictionary = get('data_dictionary',"table_type='reset_password'");
		$display_page = $dataDictionary['display_page'];
		$navigation = get('navigation',"target_display_page='$display_page'");
		$layout = $itemStyle = "";
		if(!empty($navigation)){
			$layout = $navigation['page_layout_style'];
			$itemStyle = $navigation['item_style'];
		}
		$_SESSION['reset_token'] = $token;
		$returnUrl = BASE_URL_SYSTEM."main.php?display=$display_page&layout=$layout&style=$itemStyle";

	} else {
		$message = "Invalid or expired token. Please check your email or try again.";
	}

	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * GET Data FD Record
 */
function getDataFieldRecordByDictId($dict_id){
	$con = connect();
	$ddFDRecord = array();
	$ddFdQuery = $con->query("SELECT * FROM data_dictionary DD INNER JOIN field_dictionary FD ON(DD.table_alias = FD.table_alias) WHERE DD.dict_id ='$dict_id' ORDER BY FD.display_field_order");
	if($ddFdQuery->num_rows >0){
		while($row = $ddFdQuery->fetch_assoc()){
			$ddFDRecord[] = $row;
		}
	}
	return $ddFDRecord;
}

/*
 * SET Base Gps Cordinate
 */
function setBaseGpsCordinate($dataFdRecord,$postData){
	$base_latitude_field = $base_longitude_field = "";
	$addressFields = array();
	if(!empty($dataFdRecord)){
		foreach($dataFdRecord as $fd){
			$fd['generic_field_name'] =  trim($fd['generic_field_name']);
			$fd['field_identifier'] =  trim(strtolower($fd['field_identifier']));
			if($fd['field_identifier'] == 'base_latitude'){
				$base_latitude_field = $fd['generic_field_name'];
			} elseif($fd['field_identifier'] == 'base_longitude'){
				$base_longitude_field = $fd['generic_field_name'];
			} elseif($fd['field_identifier'] == 'address'){
				$addressFields['address'] = $fd['generic_field_name'];
			} elseif($fd['field_identifier'] == 'city'){
				$addressFields['city'] = $fd['generic_field_name'];
			} elseif($fd['field_identifier'] == 'state'){
				$addressFields['state'] = $fd['generic_field_name'];
			} elseif($fd['field_identifier'] == 'zip'){
				$addressFields['zip'] = $fd['generic_field_name'];
			}  elseif($fd['field_identifier'] == 'country'){
				$addressFields['country'] = $fd['generic_field_name'];
			}
		}
		if(!empty($base_latitude_field) && !empty($base_longitude_field) && !empty($addressFields)){
			$formatedAddress = [];
			if (array_key_exists($addressFields['address'], $postData)) {
				$formatedAddress[] = $postData[$addressFields['address']];
			}
			if (array_key_exists($addressFields['city'], $postData)) {
				$formatedAddress[] = $postData[$addressFields['city']];
			}
			if (array_key_exists($addressFields['zip'], $postData)) {
				$formatedAddress[] = $postData[$addressFields['zip']];
			}
			if (array_key_exists($addressFields['country'], $postData)) {
				$formatedAddress[] = $postData[$addressFields['country']];
			}
			$formatedAddress = array_filter($formatedAddress);
			if(!empty($formatedAddress)){
				$gpsCordinate = getLatLong($formatedAddress);
				if(!empty($gpsCordinate['lat']) && !empty($gpsCordinate['lng'])){
					if (array_key_exists($base_latitude_field, $postData)) {
						$postData[$base_latitude_field] = $gpsCordinate['lat'];
					}

					if (array_key_exists($base_longitude_field, $postData)) {
						$postData[$base_longitude_field] = $gpsCordinate['lng'];
					}
				}
			}
		}
	}
	return $postData;
}

/*
 * calculate lat lng based on address
 */
function getLatLong($formatedAddress){
	//Formatted address
	$arr['lat'] = $arr['lng'] = "";

	$formattedAddr = urlencode(implode(',',$formatedAddress));
  $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$formatedAddress.'&sensor=false'.'&key='.GOOGLE_GEO_API_KEY);
  $output= json_decode($geocode);

  $arr['lat'] = $output->results[0]->geometry->location->lat;
	$arr['lng']  = $output->results[0]->geometry->location->lng;
	//Send request and receive json data by address
	// $url="https://maps.googleapis.com/maps/api/geocode/json?address=$formattedAddr&key=".GOOGLE_GEO_API_KEY;
	// $curl = curl_init();
	// curl_setopt($curl, CURLOPT_URL, $url);
	// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($curl, CURLOPT_HEADER, false);
	// $geocodeFromAddr = curl_exec($curl);
	// curl_close($curl);
	// $output = json_decode($geocodeFromAddr);
	// if($output->status =='OK'){
	// 	//Get latitude and longitute from json data
	// 	$arr['lat']  = $output->results[0]->geometry->location->lat;
	// 	$arr['lng'] = $output->results[0]->geometry->location->lng;
	// }
	//Return latitude and longitude of the given address
	return $arr;
}


/**
 * a temporary method to reset user password if its reset_password flag is on
 */
function resetPasswordIfFlagIsSet($con){

  $table = $_SESSION['update_table2']['database_table_name'];
  $primaryKey = $_SESSION['update_table2']['keyfield'];

  $query = "SELECT * FROM $table WHERE ";
  $emailValue = trim($_POST[$_SESSION['user_field_email']]);
  $usernameValue = trim($_POST[$_SESSION['user_field_uname']]);
  $passValue = trim($_POST[$_SESSION['user_field_password']]);

  if( (!empty($emailValue) || !empty($usernameValue) ) && !empty($passValue)){
    $passValue = md5($passValue);
    if($emailValue){
      $query .= "$_SESSION[user_field_email] = '$emailValue'" ;
    } elseif($usernameValue){
      $query .= "$_SESSION[user_field_uname] = '$usernameValue'" ;
    }
  }
  $userQuery = $con->query($query) OR $message = mysqli_error($con);
  if($userQuery->num_rows > 0){
    $user = $userQuery->fetch_assoc();
    if(empty($user['password']) && ($user['reset_password'] || $user['reset_password_flag'])){
      $data = array('password' => $passValue,'reset','reset_password'=>0,'reset_password_flag'=>0);
      $where = array('user_id' => $user['user_id']);
      $result = update($table,$data,$where);
      $_SESSION['uid'] = $user[$primaryKey];
      setUserDataInSession($con,$user);
      return '';
    }else{
      return false;
    }
  }
  return "Invalid Username/Email or Password";
}
