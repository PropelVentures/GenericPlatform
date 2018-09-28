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

        $fieldValue = ($urow != 'false') ? $urow[$field] : '';
    }
    //////////////
    //////////////////////////
    ///////////////////////////////////////
    ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////

    $userPrivilege = false;
    
    if ($row['visibility'] >= 1) {

        if ($_SESSION['user_privilege'] >= $row['privilege_level'] && $_SESSION['user_privilege'] < 9) {
            
            $userPrivilege = true;
            
        } else if ($_SESSION['user_privilege'] >= 9) {

            // $readonly = '';
            $userPrivilege = true;
        } else {

            $userPrivilege = false;
        }
    } else
        $userPrivilege = false;
    //$row[field_label_name] = $row[field_label_name] . $row['privilege_level'];

    if ($userPrivilege === true) {
        switch ($row['format_type']) {

            case "richtext":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<textarea class='ckeditor' name='$field' $rt_readonly>$fieldValue</textarea>";
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

                    echo "<input type='$row[format_type]' name='$field' value='$value' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'> <input type='hidden' name='$field' value='$crf_value' >";


                    echo "</div>";
                }
                break;

            case "email":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<input type='email' name='$field' value='$fieldValue' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'> ";
                echo "</div>";
                break;

            case "textbox":
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<textarea name='$field' class='form-control' cols='$row[format_length]' $readonly>$fieldValue</textarea>";
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
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
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
                echo "<button type='button' class='btn btn-default transaction_execute' name='transaction_execute' id='$row[dict_id]'>$row[field_label_name]</button>";
                echo "</div>";
                break;

            case "transaction_confirmation":
                return "<div class='new_form'>
                 <button type='button' class='btn btn-default transaction_confirmation' name='transaction_confirmation'>$row[field_label_name]</button>
                </div>";
                break;


            case "transaction_cancel":
                return "<div class='new_form'>
                 <button type='button' class='btn btn-default transaction_cancel' name='transaction_cancel' data-dismiss='modal'>$row[field_label_name]</button>
                </div>";
                break;

            case "transaction_text":
                return "<div class='new_form transaction_text'>
                    $row[field_label_name]
                </div>";
                break;


            default :
                echo "<div class='new_form'><label>$row[field_label_name]</label>";
                echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
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



        if ($image_display == 'true' && $row['editable'] == 'true') {

            ///finding whether file is text file or audio wav file

            $pos = strpos($audio_path, ".wav");

            $pos_mp3 = strpos($audio_path, ".mp3");

            if ($pos !== false || $pos_mp3 !== false) {

                $audio_path_2 = BASE_URL . "users_uploads/audio/" . $audio_path;
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


    if ($image_display == 'true' && $row['editable'] == 'true') {

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


    $img_show = (!empty($img) && file_exists("../users_uploads/" . $img) ) ? $img : 'NO-IMAGE-AVAILABLE-ICON.jpg';

    if ($image_display == 'true' && $row['editable'] == 'true') {
        echo "<div class='left-content'>";
        $masterToolTip = "masterTooltip";

        $title = "title='Click on the Image!'";
    } else {
        echo "<div class='left-content-clone'>";

        $masterToolTip = $title = "";
    }
    echo "<span> <img src='" . BASE_URL . "users_uploads/" . $img_show . "' border='0' width='150' class='img-thumbnail img-responsive user_thumb $masterToolTip' alt='$row[generic_field_name]' $title /> </span>";

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
    // $img_show = (!empty($img) && file_exists("../users_uploads/pdf/" . $img) ) ? $img : 'pdf.png';

    if ($image_display == 'true' && $row['editable'] == 'true') {
        echo "<div class='pdf-content'>";
        $masterToolTip = "masterTooltip";

        $title = "title='Click on the Pdf File!'";
    } else {
        echo "<div class='pdf-content-clone'>";

        $masterToolTip = $title = "";
    }
    echo "<span> <img src='" . BASE_URL . "users_uploads/pdf/pdf.png' border='0' width='128' class='img-thumbnail img-responsive user_thumb $masterToolTip' alt='$row[generic_field_name]' $title /> </span>";

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
    // $img_show = (!empty($img) && file_exists("../users_uploads/pdf/" . $img) ) ? $img : 'pdf.png';

    if ($image_display == 'true' && $row['editable'] == 'true') {
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

        echo "<embed  src='../users_uploads/pdf/$field_val' type='application/pdf' class='pdfInline'></embed> "
        ;

        $field_val = explode("-", $field_val);

        echo "<div class='audio-upload-filename'>$field_val[1]</div>";

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

    if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false)
        $readonly = 'readonly';


    if (!empty($row['required']))
        $required = 'required';

    echo "<input type='hidden' name='$row[generic_field_name]' value='0' >";

    if ($urow != 'false') {
        if ($urow[$row['generic_field_name']] == '1')
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control' checked='checked'>";
        else
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
    }else {

        echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $required title='$row[help_message]' size='$row[format_length]' class='form-control'>";
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
        if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['editable'] == 'false')
            $readonly = 'disabled="disabled"';
    }else{
        
        if( $row['editable'] == 'false'){
            
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
