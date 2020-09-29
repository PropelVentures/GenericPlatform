<?php
/*
(inside:

 * Cancel Button Action
 * Add Function  (Add Import Function)
* function addData()  - for all Add actions
* Update function 
function checkImageesInDataANdUpload(){
function setBaseGpsCordinate($dataFdRecord,$postData){
 * calculate lat lng based on address
function getLatLong($formatedAddress){
	
	
 *
 * masterFunctions file all forms Actions are recorded here
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
 * ***********************Cancel Button Action
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
    // update("data_dictionary", array("dd_editable" => '1'), array("page_name" => $_GET['page_name']));

    // exit($_SESSION[return_url2]);
    if ($_GET['table_type'] == 'child') {
        $link_to_return = $_SESSION['child_return_url'];
    }

    if(empty($link_to_return)){
        $link_to_return = $_SESSION['return_url'];
    }
    /*
      else if ($_GET['checkFlag'] == 'true')
      $link_to_return = $_SESSION['return_url2']; */
    else if ($_GET['addFlag'] == 'true')
        $link_to_return = BASE_URL . "system/main-loop.php?display?" . $_GET['page_name'] . "&table_alias=" . $_GET['table_alias'] . "&ComponentOrder=" . $_GET['ComponentOrder'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];
    else
        $link_to_return = $_SESSION['return_url2'];


    if(isset($_SESSION['link_in_case_of_dd_editable_2'])){
     $link_to_return = $_SESSION['link_in_case_of_dd_editable_2'];
     unset($_SESSION['link_in_case_of_dd_editable_2']);
   }

    if ($_GET['fnc'] != 'onepage') {
        //echo "<script>window.location='$link_to_return'</script>";
    } else {
        //echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }
}



/*
 *
 * Add Function  (Add Import Function)
 *
 */

// THIS HANDLES addimport FILEIMPORT and textarea manual import
// ALL FIELDS NEEDS TO BE ASSIGNED TO THE $_POST ARRAY WITH FEILD AS KEY AND RESPECTIVE VALUE ASSIGNED TO IT SO IT CAN BE USED INSIDE addData() function
if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add' && $_GET['actionType'] == 'addimport') {
    // GET THE IMPORT FIELDS(DB TABLE COLUMNS FROM ADDIMPORT FUNCTION PARAMTERS i.e. 4rth field and onwards
    log_event($_GET['page_name'],'add');
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
    $link_to_return = BASE_URL . "system/main-loop.php?page_name=" . $_GET['page_name'] . "&table_alias=" . $_GET['table_alias'] . "&ComponentOrder=" . $_GET['ComponentOrder'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];



    ###http://localhost/GenericPlatform/system/main-loop.php?page_name=myproduct&table_alias=products&ComponentOrder=1&table_aliasproducts&search_id=91&checkFlag=true&table_type=parent&edit=true#false
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
  log_event($_GET['page_name'],'add');
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
            if((int)$_SESSION['parent_value'] !== 0 && empty($_POST["$keyfield"])){
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
    checkImageesInDataANdUpload();
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
        $tblName = $_SESSION['update_table2']['table_name'];
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
	//$dataFdRecord = get_FD_rec_By_DictId($_SESSION['dict_id']);
	//$data = setBaseGpsCordinate($dataFdRecord,$data);
	/* Storing Base Latitude and Longitude End*/
    $check = insert($_SESSION['update_table2']['table_name'], $data);
    ###RETURN INSTEAD OF REDIRECT FOR addimport ACTION
    if($_GET['actionType'] == 'addimport') {
        return $check;
    }
    if(!empty($save_add_url)){
		$link_to_return = $save_add_url;
	} else {
		$link_to_return = BASE_URL . "system/main-loop.php?page_name=" . $_GET['page_name'] . "&table_alias=" . $_GET['table_alias'] . "&ComponentOrder=" . $_GET['ComponentOrder'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'] . "&search_id=" . $_SESSION['search_id'];
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'update')
    {
		// print_r($_GET);
		// print_r($_POST);
		// print_r($_SESSION);
		// die();
        $_GET['table_type'] = trim(strtolower($_GET['table_type']));
        $_GET['table_type'] = trim(strtolower($_GET['component_type']));
        
	    switch($_GET['table_type'])
        {
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
    			if(array_key_exists('save_add_record', $_POST))
                {
    				$save_add_url = $_SESSION['save_add_url'];
    				unset($_POST['save_add_record']);
    				unset($_SESSION['save_add_url']);
    			}

    			if(array_key_exists('old_audio', $_POST))
                {
    				unset($_POST['old_audio']);
    			}

    			$ddRecord = get('data_dictionary', 'dict_id=' . $_SESSION['dict_id']);

                if($ddRecord['dd_editable'][1]=='1')
                {
                    $_SESSION['show_with_edit_button'] = true;
                	$_SESSION['show_with_edit_button_DD'] = $ddRecord['dict_id'];
      		    }

				$ddRecord['keyfield'] = firstFieldName($ddRecord['table_name']);

				/****** for pdf files ********** */
			    foreach ($_POST['pdf'] as $img => $img2)
			    {
					if(!empty($img2['uploadcare']) && !empty($img2['imageName']))
					{
						$ret = uploadPdfFile($img2['uploadcare'], $img2['imageName']);
					    $oldImage = $_POST[$img]['img_extra'];
					    if (!empty($ret['image']))
					    {
							unset($_POST['pdf'][$img]);
						    $_POST[$img] = $ret['image'];
					    }

					    //if user want to replace current image
					    if (!empty($oldImage))
					    {
					    	@unlink(USER_UPLOADS . "pdf/$oldImage");
					    }

					    //if user didn't touch the with images
				    }
				    else
				    {
					    //if user clicks on remove current image
					    if(empty($img2['uploadcare']) && !empty($img2['imageName']))
					    {
						    if(!empty($_POST[$img]['img_extra']))
						    {
							    @unlink(USER_UPLOADS . "pdf/$img2[imageName]");
							    unset($_POST['pdf'][$img]);
							    $_POST[$img] = '';
						    }
						    else
						    {
							    unset($_POST['pdf'][$img]);
						    }
					    }
					    else
					    {
							unset($_POST['pdf'][$img]);
					    }
					}
				}

			    //deleting array which is used for holding imgu values
				if(empty($_POST['pdf']))
				{
					unset($_POST['pdf']);
				}
				

			    /*****For images********** */
       		    checkImageesInDataANdUpload();
				   

                $recordedFileNmae = '';
    			if(isset($_POST['recorded_audio']) && !empty($_POST['recorded_audio']))
            	{
        		    $recordedFileNmae = uploadRecordedAudio($_POST['recorded_audio']);

        		    if(!empty($recordedFileNmae))
        		    {
        		    	$_POST['temp_audio_file'] = $recordedFileNmae;
        		    }
        		}

        		foreach ($_FILES as $file => $file2)
        		{
	   				//checking if audio file is not empty
        			if (!empty($file2['name']))
        			{
        				$file_name = uploadAudioFile($file2);
  						// if file successfully uploaded to target dir
        				if (!empty($file_name))
        				{
        					$_POST[$file] = $file_name;
        				}
        			}
        			else
        			{
        				$_POST[$file] = '';
        			}

        			//Dealing with database now
        			$row = getWhere($ddRecord['table_name'], array($ddRecord['keyfield'] => $_SESSION['search_id2']));
        			$oldFile = $row[0][$file];

        			if($oldFile != "")
        			{
        				if (file_exists(USER_UPLOADS . "audio/" . $oldFile))
        				{
        					@unlink(USER_UPLOADS . "audio/" . $oldFile);
        				}
        			}
        		}

		        if(isset($_POST['temp_audio_file']))
		        {
		            $_POST['audio_file'] = $_POST['temp_audio_file'];
				}
				
				
				
    		    
				/* Storing Base Latitude and Longitude Start*/
				// $dataFdRecord = "";
				// if( get_FD_rec_By_DictId($_SESSION['dict_id'])){
				//     $dataFdRecord = get_FD_rec_By_DictId($_SESSION['dict_id']);
				// }
			
				// if(!empty($dataFdRecord)){
				//     $_POST = setBaseGpsCordinate($dataFdRecord,$_POST);
				// }
				
			    /* removing extra fields that are not preset in table start */
			    $tableFields = getColumnNames($ddRecord['table_name']);
			    $fieldsNotPresent = array_diff_key($_POST,$tableFields);
			    if(!empty($fieldsNotPresent))
			    {
					foreach($fieldsNotPresent as $key => $value)
					{
					    unset($_POST[$key]);
				    }
				}
				
			    /* removing extra fields that are not preset in table end */
			    $status = update($ddRecord['table_name'], $_POST, array($ddRecord['keyfield'] => $_SESSION['search_id2']));

      		    // **** DISABLED BY CJ (this reset dd_editable!!)
      		    //update('data_dictionary', array('dd_editable' => '1'), array('dict_id' => $_SESSION['dict_id']));

			    $dd_editable_bit2 = $ddRecord['dd_editable'][1];
			    if(empty($dd_editable_bit2) || is_null($dd_editable_bit2))
			    {
			    	$dd_editable_bit2 = '0';
			    }
                //
			    if($dd_editable_bit2=='0' || $dd_editable_bit2=='1')
			    {
			    	$current_link_in_tab =   $_SESSION[$ddRecord['dict_id'].'current_dd_url_in_tab'];
			    	if(!empty($current_link_in_tab))
			    	{
			    		unset($_SESSION[$ddRecord['dict_id'].'current_dd_url_in_tab']);
			    		echo "<script> window.location='$current_link_in_tab'; </script>";
			    	}
			    }

			    if ($_GET['checkFlag'] == 'true')
			    {
			    	if($save_add_url)
			    	{
			    		$link_to_return = $save_add_url;
			    	}
			    	else
			    	{
			    		if ($_GET['table_type'] == 'child')
			    		{
			    			$link_to_return = $_SESSION['child_return_url2'];
			    		}
			    		else
			    		{
			    			$link_to_return = $_SESSION['return_url2'];
			    		}

			    		if(isset($_SESSION['link_in_case_of_dd_editable_2']))
			    		{
			    			$link_to_return = $_SESSION['link_in_case_of_dd_editable_2'];
			    			unset($_SESSION['link_in_case_of_dd_editable_2']);
			    		}
			    	}

			    	if ($_GET['fnc'] != 'onepage')
			    	{
					    //exit($link_to_return);
			    		if($status === true)
			    		{
			    			echo "<script>window.location='$link_to_return';</script>";
			    		}
			    		else
			    		{
			    			echo "<script> alert(\"$status\"); window.location='$link_to_return'; </script>";
			    		}
			    	}
			    	else
			    	{
			    		if($status === true)
			    		{
			    			echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]';</script>";
			    		}
			    		else
			    		{
			    			echo "<script> alert(\"$status\"); window.location='$link_to_return$_SESSION[anchor_tag]'; </script>";
			    		}
			    	}
			    }
			    else
			    {
			    	if (!empty($_SESSION['temp_page_name']))
			    	{
			    		$_SESSION[page_name] = $_SESSION['temp_page_name'];
			    	}

			    	if ($_GET['fnc'] != 'onepage')
			    	{
			    		if($status === true)
			    		{
			    			echo "<script>window.location = '?page_name=$_SESSION[page_name]&table_alias=$_SESSION[tab]&ComponentOrder=$_GET[ComponentOrder]';</script>";
			    		}
			    		else
			    		{
			    			echo "<script> alert(\"$status\"); window.location = '?page_name=$_SESSION[page_name]&table_alias=$_SESSION[tab]&ComponentOrder=$_GET[ComponentOrder]'; </script>";
			    		}
			    	}
			    	else
			    	{
			    		if($status === true)
			    		{
			    			echo "<script>window.location='$_SESSION[return_url2]$_SESSION[anchor_tag]';</script>";
			    		}
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



function checkImageesInDataANdUpload(){
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
  if (empty($_POST['imgu'])){
    unset($_POST['imgu']);
  }
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


    $tbl = $_SESSION['select_table']['table_name'];

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


    $tbl = $_SESSION['select_table']['table_name'];

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

