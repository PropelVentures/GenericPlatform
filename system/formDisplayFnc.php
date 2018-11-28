<?php

/*
 * FD fields display on the forms ,Their functions are listed
 * in the following series
 *
 * *************
 * function formating_Update($row, $urow = 'false', $image_display = 'false')
 * **********
 *
 * function audio_upload($row, $urow = 'false', $image_display = 'false')
 * **************
 *
 * function image_upload($row, $urow = 'false', $image_display = 'false')
 * ************
 *
 *  function pdf_upload($row, $urow = 'false', $image_display = 'false')
 * ************
 *
 * function checkbox($row, $urow = 'false')
 * *****************
 *
 * function dropdown($row, $urow = 'false', $fieldValue = 'false')
 *
 * function list_fragment($row, $urow = 'false', $fieldValue = 'false')
 *
 * function boolean_slider($row, $formatArray, $urow = 'false', $fieldValue = 'false')
 *
 * function boolean_button($row, $formatArray, $urow = 'false', $fieldValue = 'false')
 *
 * function number_slider($row, $formatArray, $urow = 'false', $fieldValue = 'false')
 *
 * function datepicker($row, $formatArray, $urow = 'false', $fieldValue = 'false')
 *
 *
 */

/////////////////////********************************************************************
//////////////////////////*****************
/////////////////////********************************************************************
//////////////////////////***************************
/////////////////////************************************************************
//////////////////////////************************************
/////////////////////          FORMATING UPDATE FUNCTION STARTS HERE**********************
//****************************************************************
//////////////////////////**********************************************************
/////////////////////
//////////////////////////********************************************************



function formating_Update($row, $method, $urow, $image_display = 'false', $page_editable = 'false') {

    /* temporary testing */


    // for transaction pop up i have used this if statement

    if ($method != 'transaction') {
        $urow_record = $urow;

        if ($method == 'add') {

            $urow = 'false';
        }



        $field = $row['generic_field_name'];


        $crf_value = $urow_record[$field];

        if (empty($row['format_length']) && $row['format_type'] != 'dropdown') {

            $row['format_length'] = parseFieldType($row);
        }


        if ($_GET['addFlag'] == 'true')
            $row['dd_editable'] = '11';


        if (trim($row['table_type']) != 'transaction') {
            if ($row['dd_editable'] != '11' || $row['editable'] == 'false' || $page_editable == false) {

                $readonly = 'readonly';

                $rt_readonly = 'disabled';

                $image_display = 'false';
            }
        }
        if (!empty($row['required']))
            $required = 'required';


        if (empty($row['format_type'])) {
            $row['format_type'] = 'text';
        }
		$row['strict_disabled'] = '';
		if(!itemEditable($row['editable'])){
			$row['strict_disabled'] = 'disabled';
		}

        $fieldValue = ($urow != 'false') ? $urow[$field] : '';
    }
    //////////////
    //////////////////////////
    ///////////////////////////////////////
    ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////

    $userPrivilege = false;
	if(itemHasVisibility($row['visibility'])){
		$userPrivilege = true;
	}

    /*if ($row['visibility'] >= 1) {

        if ($_SESSION['user_privilege'] >= $row['privilege_level'] && $_SESSION['user_privilege'] < 9) {

            $userPrivilege = true;

        } else if ($_SESSION['user_privilege'] >= 9) {

            // $readonly = '';
            $userPrivilege = true;
        } else {

            $userPrivilege = false;
        }
    } else
        $userPrivilege = false;*/
    //$row[field_label_name] = $row[field_label_name] . $row['privilege_level'];

    if ($userPrivilege === true) {
		$formatArray = explode("-",$row['format_type']);
        switch ($formatArray[0]) {

            case "richtext":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<textarea class='ckeditor' name='$field' $row[strict_disabled] $rt_readonly>$fieldValue</textarea>";
                echo "</div>";
                break;

            case "dropdown":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";

                if ($urow != 'false')
                    dropdown($row, $urow, $fieldValue = 'false', $page_editable);
                else
                    dropdown($row);
                echo "</div>";
                break;

            case "list_fragment":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";

                if ($urow != 'false')
                    list_fragment($row);
                echo "</div>";
                break;

            case "crf":

                if ($method != 'add') {
                    echo "<div class='new_form'><label>$row[field_label_name]</label>";

                    $value = dropdown($row, $urow = 'list_display', $crf_value);

                    echo "<input type='$row[format_type]' name='$field' value='$value' $row[strict_disabled] $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'> <input type='hidden' name='$field' value='$crf_value' >";


                    echo "</div>";
                }
                break;

            case "email":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<input type='email' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'> ";
                echo "</div>";
                break;

            case "textbox":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<textarea name='$field' class='form-control' cols='$row[format_length]' $row[strict_disabled] $readonly>$fieldValue</textarea>";
                echo "</div>";
                break;

            case "tag":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                if ($urow != 'false')
                    tagFnc($row, $urow, $image_display);
                else
                    tagFnc($row);
                echo "</div>";
                break;

            case "checkbox":
                echo "<div class='new_form'><label class='boolen_label'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    checkbox($row, $urow, $page_editable);
                else
                    checkbox($row);
                echo "</div>";
                break;

            case "new_line":
                echo "<br>";
                break;


            case "line_divider":
                echo "<hr>";
                break;

            case "image":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                if ($urow != 'false')
                    image_upload($row, $urow, $image_display);
                else
                    image_upload($row);
                echo "</div>";
                break;

            case "pdf":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                if ($urow != 'false')
                    pdf_upload($row, $urow, $image_display);
                else
                    pdf_upload($row);
                echo "</div>";
                break;


            case "pdf_inline":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                if ($urow != 'false')
                    pdf_inline($row, $urow, $image_display);
                else
                    pdf_inline($row);
                echo "</div>";
                break;

            case "audio":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                if ($urow != 'false')
                    audio_upload($row, $urow, $image_display);
                else
                    audio_upload($row);
                echo "</div>";
                break;


            case "transaction_execute":
                echo "<div class='new_form'>";
                echo "<button type='button' class='btn btn-default transaction_execute' $row[strict_disabled] name='transaction_execute' id='$row[dict_id]'>$row[field_label_name]</button>";
                echo "</div>";
                break;

            case "transaction_confirmation":
                return "<div class='new_form'>
                 <button type='button' class='btn btn-default transaction_confirmation' $row[strict_disabled] name='transaction_confirmation'>$row[field_label_name]</button>
                </div>";
                break;


            case "transaction_cancel":
                return "<div class='new_form'>
                 <button type='button' class='btn btn-default transaction_cancel' name='transaction_cancel' $row[strict_disabled] data-dismiss='modal'>$row[field_label_name]</button>
                </div>";
                break;

            case "transaction_text":
                return "<div class='new_form transaction_text'>
                    $row[field_label_name]
                </div>";
                break;
			case "boolean":
				switch(@$formatArray[1]){
					case "slider":
						echo "<div class='new_form'><label class='boolen_label'>$row[field_label_name]</label>";
						if ($urow != 'false'){
							boolean_slider($row,$formatArray,$urow, $page_editable);
						} else {
							boolean_slider($row,$formatArray);
						}
						echo "</div>";
					break;

					case "button":
						echo "<div class='new_form'><label class='boolen_label'>$row[field_label_name]</label>";
							boolean_button($row,$formatArray,$urow, $page_editable);
						echo "</div>";

					break;

					default:
						echo "<div class='new_form'><label>$row[field_label_name]</label>";
						echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
						echo "</div>";
				}
			break;

			case "number":
				switch(@$formatArray[1]){
					case "slider":
						echo "<div class='new_form'><label>$row[field_label_name]</label>";
							number_slider($row,$formatArray,$urow, $page_editable);
						echo "</div>";
					break;

					default:
						echo "<div class='new_form'><label>$row[field_label_name]</label>";
						echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
						echo "</div>";
				}
			break;

			case "datepicker":
				echo "<div class='new_form'><label>$row[field_label_name]</label>";
					datepicker($row,$formatArray,$urow, $page_editable);
				echo "</div>";
			break;

            default :
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
                echo "</div>";
        }///switch conditions end here
    }/////userprivilege ends here
}

/*
 * audio UPLOAD FUNCTION
 */

function audio_upload($row, $urow = 'false', $image_display = 'false') {




    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';


    if (!empty($urow[$row[generic_field_name]])) {

        $audio_path = $urow[$row[generic_field_name]];



        if ($image_display == 'true' && $row['strict_disabled'] == '') {

            ///finding whether file is text file or audio wav file

            $pos = strpos($audio_path, ".wav");

            $pos_mp3 = strpos($audio_path, ".mp3");

            if ($pos !== false || $pos_mp3 !== false) {

                $audio_path_2 = USER_UPLOADS . "audio/" . $audio_path;
            } else {

                $audio_path_2 = $audio_path;
            }

            echo "<div class='audio-css'>


<audio controls src='$audio_path_2' id='audio'></audio><div class='recording_msg'></div>";

            echo "<div class='button_panel'>
			<a class='button' id='record'>" . audioRecord . "</a>
      <a class='button disabled one' id='pause'>" . audioPause . "</a>

                        <a class='button' id='remove'>" . audioclear . "</a>

<input type='hidden' name='old_audio' class='old_audio' id='$row[generic_field_name]' value='$audio_path'>";


            if ($pos !== false || $pos_mp3 !== false) {
                echo "<div class='audio-upload-filename'>$audio_path</div>";
            }
            echo "</div>";
        } else {

            echo "<div class='audio-css'>


<audio controls src='' id='audio'></audio><div class='recording_msg'></div>";
        }

        echo "</div>";
    } else {
//////////When there is no recording


        echo "<div class='audio-css'>

<div class='recording_msg'></div>";

        if ($image_display == 'true') {

            echo "<input type='file' name='$row[generic_field_name]' class='form-control fileField' >";

            echo "<div class='button_panel'>
			<a class='button' id='record'>" . audioRecord . "</a>
      <a class='button disabled one' id='pause'>" . audioPause . "</a>

                        <a class='button disabled' id='remove'>" . audioClear . "</a>

<input type='hidden' name='old_audio' class='old_audio' id='$row[generic_field_name]' value='$audio_path'>

		</div>";
        } else {

            echo "<input type='file' name='$row[generic_field_name]' class='form-control fileField'  disabled>";
        }

        echo "</div>";
    }
}

/*
 * @function tagFnc
 */

function tagFnc($row, $urow = 'false', $image_display = 'false') {


    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';

    ///if fd is not editable


    $row[generic_field_name] = trim($row[generic_field_name]);

    $field_value = $urow[$row[generic_field_name]];


    $list = getMulti($row['database_table_name'], "$row[generic_field_name] != '' and $row[generic_field_name] != 'NULL'", $row[generic_field_name]);



    foreach ($list as $item) {

        foreach ($item as $key => $value) {
            $new_m[] = $value;
        }
    }

    ///fetching data from multi array
    foreach ($new_m as $val) {
        //echo trim($val);
        $merger = $merger . $val . ",";
    }
    $merger = explode(',', $merger);


    ///removing white spaces

    foreach ($merger as $val) {

        $merger2[] = trim($val);
    }

    ////removing duplicate items and empty items and also wrapping in single quotes
    foreach (array_filter(array_unique($merger2)) as $val) {

        // $val = trim($val);
        $next_level[] = "'$val'";
    }

    $auto_complete = implode(',', $next_level);


    if ($image_display == 'true' && $row['strict_disabled'] == '') {

        echo "<script type='text/javascript'>
            $(document).ready(function () {
                $('.$row[generic_field_name]').tagit({
                    availableTags: [$auto_complete],
                        allowSpaces:true
                });
            });
        </script>";
    } else {

        echo "<script type='text/javascript'>
            $(document).ready(function () {
                $('.$row[generic_field_name]').tagit({
                    availableTags: [$auto_complete],
                        readOnly:true
                });
            });
        </script>";
    }



    echo "<input name='$row[generic_field_name]' class='$row[generic_field_name]' value='$field_value'>";
}

/*
 * IMAGE UPLOAD FUNCTION
 */

function image_upload($row, $urow = 'false', $image_display = 'false') {



    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';



    $row[generic_field_name] = trim($row[generic_field_name]);

    $img = ($urow != 'false') ? $urow[$row[generic_field_name]] : '';

     $img_test= USER_UPLOADS . $img;


    $img_show = (!empty($img) && file_exists($img_test) ) ? $img : 'NO-IMAGE-AVAILABLE-ICON.jpg';
/*
echo "<br> 3 - img = $img<br>";
echo "<br> 4 - img_show = $img_show<br>";
echo "<br> 5 - img_test = $img_test<br>";
echo "<br> 6 - file_exists(img_test) = <br>"  . (file_exists($img_test) ? "Yes" : "No");
die;
*/
    if ($image_display == 'true' && $row['strict_disabled'] == '') {
        echo "<div class='left-content'>";
        $masterToolTip = "masterTooltip";

        $title = "title='Click on the Image!'";
    } else {
        echo "<div class='left-content-clone'>";

        $masterToolTip = $title = "";
    }
    echo "<span> <img src='" . USER_UPLOADS . "" . $img_show . "' border='0' width='150' class='img-thumbnail img-responsive user_thumb $masterToolTip' alt='$row[generic_field_name]' $title /> </span>";

    /* if (!empty($_SESSION['profile-image'])) {


      $img_name = $_SESSION['dict_id'];
      } else {

      $img_name = 'no-profile';
      } */

    if (!empty($urow[$row[generic_field_name]])) {
        $field_val = $urow[$row[generic_field_name]];
        echo "<input type='hidden' name='imgu[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' value='$field_val'/>";
    } else {
        echo "<input type='hidden' name='imgu[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' />";
    }


    echo "<input type='hidden' name='imgu[$row[generic_field_name]][uploadcare]' id='$row[generic_field_name]' />";

    echo "<div class='img-extra'></div>";



    echo " </div>";
}

/*
 * PDF UPLOAD FUNCTION
 */

function pdf_upload($row, $urow = 'false', $image_display = 'false') {


    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';





    $row[generic_field_name] = trim($row[generic_field_name]);

    // $img = ($urow != 'false') ? $urow[$row[generic_field_name]] : '';
    // $img_show = (!empty($img) && file_exists(USER_UPLOADS . "pdf/" . $img) ) ? $img : 'pdf.png';

    if ($image_display == 'true' && $row['strict_disabled'] == '') {
        echo "<div class='pdf-content'>";
        $masterToolTip = "masterTooltip";

        $title = "title='Click on the Pdf File!'";
    } else {
        echo "<div class='pdf-content-clone'>";

        $masterToolTip = $title = "";
    }
    echo "<span> <img src='" . USER_UPLOADS . "pdf/pdf.png' border='0' width='128' class='img-thumbnail img-responsive user_thumb $masterToolTip' alt='$row[generic_field_name]' $title /> </span>";

    /* if (!empty($_SESSION['profile-image'])) {


      $img_name = $_SESSION['dict_id'];
      } else {

      $img_name = 'no-profile';
      } */

    if (!empty($urow[$row[generic_field_name]])) {
        $field_val = $urow[$row[generic_field_name]];
        echo "<input type='hidden' name='pdf[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' value='$field_val'/>";

        $field_val = explode("-", $field_val);

        echo "<div class='audio-upload-filename'>$field_val[1]</div>";
    } else {
        echo "<input type='hidden' name='pdf[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' />";

        echo "<div class='audio-upload-filename'>No File!</div>";
    }


    echo "<input type='hidden' name='pdf[$row[generic_field_name]][uploadcare]' id='$row[generic_field_name]' />";

    echo "<div class='img-extra'></div>";



    echo " </div>";
}

/*
 * PDF UPLOAD FUNCTION
 */

function pdf_inline($row, $urow = 'false', $image_display = 'false') {


    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';





    $row[generic_field_name] = trim($row[generic_field_name]);

    $field_val = $urow[$row[generic_field_name]];
    // $img = ($urow != 'false') ? $urow[$row[generic_field_name]] : '';
    // $img_show = (!empty($img) && file_exists(USER_UPLOADS . "pdf/" . $img) ) ? $img : 'pdf.png';

    if ($image_display == 'true' && $row['strict_disabled'] == '') {
        echo "<div class='pdf-content'> <a href='' title='" . pdfInline . "' class='pdf_inline_anchor'>" . pdfInline . "</a>";
        echo "  <img  class='user_thumb' alt='$row[generic_field_name]' style='display:none;' />";
    } else {
        echo "<div class='pdf-content-clone'>";

        $masterToolTip = $title = "";
    }


    /* if (!empty($_SESSION['profile-image'])) {


      $img_name = $_SESSION['dict_id'];
      } else {

      $img_name = 'no-profile';
      } */

    if (!empty($urow[$row[generic_field_name]])) {

        echo "<embed  src='" . USER_UPLOADS . "pdf/$field_val' type='application/pdf' class='pdfInline'></embed> "
        ;

        $field_val1 = explode("-", $field_val);

        echo "<div class='audio-upload-filename'>$field_val1[1]</div>";

        echo "<input type='hidden' name='pdf[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' value='$field_val'/>";
    } else {
        echo "<input type='hidden' name='pdf[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' />";

        echo "<div class='audio-upload-filename'>" . noFile . "</div>";
    }


    echo "<input type='hidden' name='pdf[$row[generic_field_name]][uploadcare]' id='$row[generic_field_name]' />";

    echo "<div class='img-extra'></div>";



    echo " </div>";
}

/*
 *
 * CHECKBOX FUNCTION
 */

function checkbox($row, $urow = 'false', $page_editable = 'false') {

    $readonly = '';
    $required = '';

    if ($_GET['addFlag'] == 'true')
        $row['dd_editable'] = '11';

    if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['strict_disabled']=='disabled')
        $readonly = 'readonly';


    if (!empty($row['required']))
        $required = 'required';

    echo "<input type='hidden' name='$row[generic_field_name]' value='0' >";

    if ($urow != 'false') {
        if ($urow[$row['generic_field_name']] == '1')
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]' size='$row[format_length]' class='form-control checkbox' checked='checked'>";
        else
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]' size='$row[format_length]' class='form-control checkbox'>";
    }else {

        echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]' size='$row[format_length]' class='form-control checkbox'>";
    }
}

////////////////DROPDOWN function///


function dropdown($row, $urow = 'false', $fieldValue = 'false', $page_editable = 'check') {


    $con = connect();

    $rs = $con->query("SELECT * FROM  data_dictionary where table_alias = '$row[dropdown_alias]'");

    $dd = $rs->fetch_assoc();

//print_r($row);die;
    $list_fields = explode(',', $dd['list_fields']);

//print_r($list_fields);die;

    $keyfield = '';

    if ($_GET['addFlag'] == 'true')
        $row['dd_editable'] = '11';

    if (trim($row['table_type']) != 'transaction') {
        if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['strict_disabled'] == 'disabled')
            $readonly = 'disabled="disabled"';
    }else{

        if( $row['strict_disabled'] == 'disabled'){

            $readonly = 'disabled="disabled"';
        }


    }
    if (!empty($row['required']))
        $required = 'required';





    if (!empty($row[format_length]))
        $length = "style='width:$row[format_length]em'";

    $itemDis = array();
    foreach ($list_fields as $val) {


        $newVal = explode('*', $val);

        //print_r($newVal);die;

        if ($newVal[0] == '' && !empty($newVal[1])) {

            $tilde = explode('~', $newVal[1]);

//print_r($tilde);die;

            if ($tilde[0] == '' && !empty($tilde[1])) {

                $inviKey = $tilde[1];
            } else {

                $visiKey = $tilde[0];
            }
        } else
            $itemDis[] = $val;
    }/// foreach ends here

    if (isset($inviKey)) {
        $itemDis[] = $inviKey;

        $key = $inviKey;
    } else {
        $itemDis[] = $visiKey;

        $key = $visiKey;
    }
//print_r($itemDis);die;
    $list_fields = implode(',', $itemDis);


    //// this check is to avoid list display view
    $list_sort = explode('-', $dd['list_sort']);

    // print_r($list_sort);die;

    if ($list_sort[0] == '' && !empty($list_sort[1])) {

        $order = "order by " . $list_sort[1] . " DESC";
    } else if ($list_sort[1] == '' && !empty($list_sort[0])) {

        $order = "order by " . $list_sort[0] . " ASC";
    } else {

        $order = '';
    }


    if ($urow == 'list_display') {

        $qry = $con->query("SELECT $list_fields FROM  $dd[database_table_name] where $key='$fieldValue'");

        $res = $qry->fetch_assoc();

        $res2 = $res;

        unset($res2[$inviKey]);

        return $data = implode(dropdownSeparator, $res2);
    } else {

        $qry = $con->query("SELECT $list_fields FROM  $dd[database_table_name] $order");

        echo "<select name='$row[generic_field_name]'  class='form-control' $readonly $length>";

        while ($res = $qry->fetch_assoc()) {

            $res2 = $res;
            unset($res2[$inviKey]);


            $data = implode(dropdownSeparator, $res2);

            if ($urow[$row[generic_field_name]] == $res[$key]) {

                echo "<option value='$res[$key]' selected >$data</option>";
            } else
                echo "<option value='$res[$key]'>$data</option>";
        }
        echo "</select>";
    }
}

/* * **********
 * *******************
 * ***************************
 *
 * ******************LIST FRAGMENT FUNCTION **************
 * *****
 * *************************************
 */

function list_fragment($row2) {

    $con = connect();

    $rs = $con->query("SELECT * FROM  field_dictionary where table_alias = '$row2[dropdown_alias]'");

    $fields = '';

    $labels = array();
    ///taking care of fields and checks in FD
    while ($row = $rs->fetch_assoc()) {

        if ($_SESSION['user_privilege'] >= $row['privilege_level'] && $_SESSION['user_privilege'] < 9) {

            if ($row['visibility'] >= 1)
                $userPrivilege = true;
            else
                $userPrivilege = false;
        } else if ($_SESSION['user_privilege'] >= 9) {

            // $readonly = '';
            $userPrivilege = true;
        } else {

            $userPrivilege = false;
        }



        if ($userPrivilege) {


            ////extracting labels

            array_push($labels, $row['field_label_name']);

            //extracting fields name
            if (!empty($fields)) {
                $fields = $fields . ',' . $row['generic_field_name'];
            } else {

                $fields = $row['generic_field_name'];
            }
        }
    }


    $rs = $con->query("SELECT * FROM  data_dictionary where table_alias = '$row2[dropdown_alias]'");

    $dd = $rs->fetch_assoc();


    //$list_fields = explode(',', $dd['list_fields']);
//print_r($list_fields);die;

    $query = get_listFragment_record($dd['database_table_name'], $dd['keyfield'], $dd['list_filter'], $dd['list_extra_options'], $fields);





    echo "<table class='list_fragment'>";


    /*     * **
     *
     * It will display Headings if there are
     */

    if (!empty($labels[0])) {

        echo "<thead><tr>";

        $i = 1;
        foreach ($labels as $val) {

            echo "<th class='list_td$i' > $val </th>";

            $i++;
        }

        echo "</tr></thead>";
    }



    /*     * ****
     *
     * It will display Records in TD
     */

    echo "<tbody>";
    while ($rec = $query->fetch_assoc()) {


        echo "<tr>";

        $i = 1;
        foreach ($rec as $val) {


            echo "<td class='list_td$i' >$val</td>";

            $i++;
        }
        echo "</tr>";
    }


    ///////table ends here

    echo "</tbody></table>";
}


/*
 *
 * BOOLEAN SLIDER FUNCTION
 */

function boolean_slider($row, $formatArray, $urow = false, $page_editable = false) {
    $required = '';
    $disabled = '';
    if ($_GET['addFlag'] == 'true'){
		 $row['dd_editable'] = '11';
	}
	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['strict_disabled']== 'disabled'){
		$disabled = 'disabled';
	}
    if (!empty($row['required'])){
		$required = 'required';
	}
	echo "<input type='hidden' name='$row[generic_field_name]' value='0' >";
	echo '<div class="boolean_slider_box">
			<span><b>'.(isset($formatArray[2]) ? strtoupper($formatArray[2]) : 'OFF').'</b></span>
			<label class="boolean_slider">';
			if ($urow != false) {
				if ($urow[$row['generic_field_name']] == '1'){
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' size='$row[format_length]' checked='checked'>";
				} else {
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' size='$row[format_length]' >";
				}
			} else {
				echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' size='$row[format_length]'>";
			}

	echo 	'<span class="boolean_slider_span"></span>
			</label>
			<span><b>'.(isset($formatArray[3]) ? strtoupper($formatArray[3]) : 'ON').'</b></span>
		</div>';
}
/*
 *
 * BOOLEAN BUTTON FUNCTION
 */

function boolean_button($row, $formatArray, $urow = false, $page_editable = false) {
    $required = '';
    $disabled = '';
	$disabledClass = '';
    if ($_GET['addFlag'] == 'true'){
		 $row['dd_editable'] = '11';
	}
	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['strict_disabled'] == 'disabled'){
		$disabled = 'disabled';
		$disabledClass = 'disable_btn';
	}

    if (!empty($row['required'])){
		$required = 'required';
	}
	echo "<input type='hidden' name='$row[generic_field_name]' value='0'>";
	echo '<div class="boolean_button_box">';
	if ($urow != false) {
		echo "<label class='boolean_button $disabledClass'>
				<span>".(isset($formatArray[2]) ? strtoupper($formatArray[2]) : 'OFF')."<span>
				<input $disabled $row[strict_disabled] type='radio' name='$row[generic_field_name]' value='0' ".($urow[$row['generic_field_name']] == '0' ? 'checked="checked"':'').">
				<span class='boolean_button_checkmark'></span>
			</label>
			<label class='boolean_button $disabledClass'>
				<span>".(isset($formatArray[3]) ? strtoupper($formatArray[3]) : 'ON')."<span>
				<input $disabled $row[strict_disabled] type='radio' name='$row[generic_field_name]' value='1' ".($urow[$row['generic_field_name']] == '1' ? 'checked="checked"':'').">
				<span class='boolean_button_checkmark'></span>
			</label>";
	} else {
		echo "<label class='boolean_button $disabledClass'>
				<span>".(isset($formatArray[2]) ? strtoupper($formatArray[2]) : 'OFF')."<span>
				<input $disabled $row[strict_disabled] type='radio' name='$row[generic_field_name]' value='0'>
				<span class='boolean_button_checkmark'></span>
			</label>
			<label class='boolean_button $disabledClass'>
				<span>".(isset($formatArray[3]) ? strtoupper($formatArray[3]) : 'ON')."<span>
				<input $disabled $row[strict_disabled] type='radio' name='$row[generic_field_name]' value='1'>
				<span class='boolean_button_checkmark'></span>
			</label>";
	}
	echo '</div>';
}

/*
 *
 * NUMBER SLIDER FUNCTION
 */

function number_slider($row, $formatArray, $urow = false, $page_editable = false) {
    $required = '';
    $disabled = 'false';
    if ($_GET['addFlag'] == 'true'){
		 $row['dd_editable'] = '11';
	}
	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false){
		$disabled = 'true';
	}
	if($row['strict_disabled'] == 'disabled'){
		$disabled = 'true';
	}
    if (!empty($row['required'])){
		$required = 'required';
	}
	echo '<script>
			$( function() {
				var handle = $( "#uiSliderCustom" );
					$( "#sliderCustom" ).slider({
						min: '.(isset($formatArray[2]) ? $formatArray[2] : '0').',
						max: '.(isset($formatArray[3]) ? $formatArray[3] : '100').',
						value: '.(isset($urow[$row['generic_field_name']]) ? $urow[$row['generic_field_name']] : '0').',
						disabled: '.$disabled.',
						create: function() {
							handle.text( $( this ).slider( "value" ) );
							$("#number_'.$row[generic_field_name].'").val(  $( this ).slider( "value" ) );
						},
					slide: function( event, ui ) {
						handle.text( ui.value );
						$("#number_'.$row[generic_field_name].'").val( ui.value );
					}
				});
			});
		</script>';
	echo "<input id='number_$row[generic_field_name]' type='hidden' name='$row[generic_field_name]'>";
	echo "<div id='sliderCustom'>
			<div id='uiSliderCustom' class='ui-slider-handle'></div>
		</div>";
}

/*
 *
 * DATEPICKER FUNCTION
 */

function datepicker($row, $formatArray, $urow = false, $page_editable = false) {
    $required = '';
    $disabled = '';
    if ($_GET['addFlag'] == 'true'){
		 $row['dd_editable'] = '11';
	}
	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false){
		$disabled = 'disabled';
	}
    if (!empty($row['required'])){
		$required = 'required';
	}
	echo "<script>
			$( function() {
				$('#datepicker_$row[generic_field_name]').datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'yy-mm-dd'
				});
			});
		  </script>";
	echo "<input type='text' id='datepicker_$row[generic_field_name]' value='".(isset($urow[$row['generic_field_name']]) ? $urow[$row['generic_field_name']] : '')."' name='$row[generic_field_name]' $row[strict_disabled] $disabled $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
}

