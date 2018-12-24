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



if (isset($_GET["button"]) && !empty($_GET["button"]) && $_GET["button"] == 'cancel') {



    update("data_dictionary", array("dd_editable" => '1'), array("display_page" => $_GET['display']));


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


//        $csvFile = fopen($_FILES['addImportFile']['tmp_name'], 'r');
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


                    }
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


            }
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

    addData();

}


function addData()
{
    if (array_key_exists('field_name_unique', $_POST)) {
        unset($_POST['field_name_unique']);
    }
    if (array_key_exists('old_audio', $_POST)) {
        unset($_POST['old_audio']);
    }

//    echo "<pre>";
//    $_SESSION['dict_id'] .
//    $_SESSION['update_table2']['database_table_name'] .
//    $_SESSION['update_table2']['keyfield'] . "<br>" . $_SESSION[return_url2];
//    die("TETING ADD SESSION");
//    print_r($_POST);die;

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
            if((int)$_SESSION['parent_value'] !== 0 );
            // DONT ASSIGN STRING VALUES, ONLY ASSIGN REAL PRIMARY KEYS WHICH WILL ALWAYS BE INTEGERS OR 0 IF TYPECASTED FROM STRING
                $_POST["$keyfield"] = $_SESSION['parent_value'];
                //SET KEYFIELD TO THE PARENT VALUE TO PRESERVE PARENT->CHILD RELATIONSHIP
        }
    }

//    echo "<pre>TESTING AddDATA POST";
//    print_r($_POST);
//    #print_r($row);
//    #print_r($_SESSION);
//    echo "</pre>";#die;

    if (!empty($row['list_filter'])) {

        $keyfield = explode(";", $row['list_filter']);

        $firstParent = $keyfield[0];
        //print_r($keyfield);die;

        if (!empty($keyfield[1])) {
            $listCond = $keyfield[1];
        }


        //  $checkFlag = false;

        if (!empty($keyfield[0])) {
            $i = 0;

            $keyfield = explode(",", $keyfield[0]);

            foreach ($keyfield as $val) {

                $keyField = explode("=", $val);

                $keyVal[$i] = array(trim($keyField[0]) => trim($keyField[1]));

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
        //print_r($pid);die;

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

        // print_r($projectKey);die;

        if (array_key_exists($projectKey, $data))
            unset($data[$projectKey]);

        $data = array_merge($project, $data);

        //  print_r($data);die;
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

    // echo "<pre>";print_r($data);die;

    $check = insert($_SESSION['update_table2']['database_table_name'], $data);

    ###RETURN INSTEAD OF REDIRECT FOR addimport ACTION
    if($_GET['actionType'] == 'addimport')
    {
        return $check;
    }

    /* if ($_GET['table_type'] == 'child' && $_GET['checkFlag'] == 'true')
      $link_to_return = $_SESSION['add_url_list'];
      else */
    $link_to_return = BASE_URL . "system/main.php?display=" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];

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
			
		case 'facebooklogin':
			echo json_encode(facebookLogin());
			break;
		default:
			if (array_key_exists('old_audio', $_POST)) {
				unset($_POST['old_audio']);
			}
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

			foreach ($_FILES as $file => $file2) {
				//checking if audio file is not empty
				if (!empty($_FILES[$file]['name'])) {
					$file_name = uploadAudioFile($file2);
					// if file successfully uploaded to target dir
					if (!empty($file_name)) {
						$_POST[$file] = $file_name;
					}
				} else {
					$_POST[$file] = '';
				}
				//Dealing with database now
				$row = getWhere($_SESSION['update_table2']['database_table_name'], array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));
				$oldFile = $row[0][$file];
				if ($oldFile != "") {
					if (file_exists(USER_UPLOADS . "audio/" . $oldFile)) {
						@unlink(USER_UPLOADS . "audio/" . $oldFile);
					}
				}
			}
			$status = update($_SESSION['update_table2']['database_table_name'], $_POST, array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));

			update('data_dictionary', array('dd_editable' => '1'), array('dict_id' => $_SESSION['dict_id']));
			
			if ($_GET['checkFlag'] == 'true') {
				if ($_GET['table_type'] == 'child')
					$link_to_return = $_SESSION['child_return_url2'];
				else
					$link_to_return = $_SESSION['return_url2'];

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
		$emailValue = $_POST[$_SESSION['user_field_email']];
		$passValue = $_POST[$_SESSION['user_field_password']];
		if(!empty($emailValue) && !empty($passValue)){
			$query .= "$_SESSION[user_field_email] = '$emailValue'" ;
			$query .= " AND $_SESSION[user_field_password] = '$passValue'" ;
			$userQuery = $con->query($query) OR $message = mysqli_error($con);
			if($userQuery->num_rows > 0){
				$user = $userQuery->fetch_assoc();
				$_SESSION['uid'] = $user[$primaryKey];
				$_SESSION['user_privilege'] = $user['user_privilege_level'];
				$_SESSION['uname'] = $user[$_SESSION['user_field_email']];
				$message = '';
				//$message = PROFILE_COMPLETE_MESSAGE;
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Invalid Email/Password";
			}
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
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
		return [
			'message' => "Comming soon",
			'returnUrl' =>$returnUrl
		];
		// TO be done
		$facebookIdValue = $_POST['id'];
		$emailValue = $_POST[$_SESSION['user_field_email']];
		//$check = "SELECT * FROM $table WHERE $_SESSION[user_field_email] = '$emailValue'";
		$query = "SELECT * FROM $table WHERE ";
		$passValue = $_POST[$_SESSION['user_field_password']];
		if(!empty($emailValue) && !empty($passValue)){
			$query .= "$_SESSION[user_field_email] = '$emailValue'" ;
			$query .= " AND $_SESSION[user_field_password] = '$passValue'" ;
			$userQuery = $con->query($query) OR $message = mysqli_error($con);
			if($userQuery->num_rows > 0){
				$user = $userQuery->fetch_assoc();
				$_SESSION['uid'] = $user[$primaryKey];
				$_SESSION['user_privilege'] = $user['user_privilege_level'];
				$_SESSION['uname'] = $user[$_SESSION['user_field_email']];
				$message = '';
				//$message = PROFILE_COMPLETE_MESSAGE;
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Invalid Email/Password";
			}
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
		//pr($_POST['uri']);
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



