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
	// pr($row);

    /* temporary testing */

    // for transaction pop up i have used this if statement
	$sigle_line_alignment="";
    if ($method != 'transaction') {
        $urow_record = $urow;

        if ($method == 'add') {

            $urow = 'false';
        }



        $field = $row['generic_field_name'];


        $crf_value = $urow_record[$field];

        if (empty($row['format_length']) && ($row['format_type'] != 'dropdown') && $row['format_type'] != 'multi_dropdown') {

            $row['format_length'] = parseFieldType($row);
        }


        if ($_GET['addFlag'] == 'true')
            $row['dd_editable'] = '11';

		// Show Empty Field for Login
		$TABLE_TYPE = trim(strtoupper($row['table_type']));
		if(in_array($TABLE_TYPE,array('LOGIN'))){
			$row['dd_editable'] = '11';
		}
        if (trim($row['table_type']) != 'transaction') {
					$DD_EDITABLE_TEMP = $row['dd_editable'];
					if(isset($row['temp_dd_editable'])){
						$DD_EDITABLE_TEMP = $row['temp_dd_editable'];
					}
            if ($DD_EDITABLE_TEMP != '11' || $row['editable'] == 'false' || $page_editable == false) {

                $readonly = 'readonly';

                $rt_readonly = 'disabled';

                $image_display = 'false';
				if(!empty($row['view_operations'])){
					$sigle_line_alignment = getAlignmentClass($row['view_operations']);
				}
            }else{
				if(!empty($row['edit_operations'])){
					$sigle_line_alignment = getAlignmentClass($row['edit_operations']);
				}
			}
        }
        if (!empty($row['required']))
            $required = 'required';


        if (empty($row['format_type'])) {
            $row['format_type'] = 'text';
        }
		$row['strict_disabled'] = '';
		if(!loginNotRequired() && !itemEditable($row['editable'])){
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
	if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level'])){
		$userPrivilege = true;
	}

	if(loginNotRequired()){
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
	$dimensions = getFieldDimension($row['format_length']);
	$dimWidth = $dimensions['width'];
	$dimStyle = $dimensions['style'];
	$fd_css_class = $row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
    if ($userPrivilege === true) {
		$formatArray = explode("-",$row['format_type']);
        switch ($formatArray[0]) {
            case "richtext":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
				/*Code Start for Task 5.4.20*/
                echo "<textarea class='ckeditor $fd_css_class' cols='$row[format_length]' name='$field' $row[strict_disabled] size=$dimWidth $rt_readonly style='$dimStyle;$dimWidth$fd_css_style'>$fieldValue</textarea>";
                /*Code End for Task 5.4.20*/
				echo "</div></div>";
                break;

            case "dropdown":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";

                if ($urow != 'false')
                    dropdown($row, $urow, $fieldValue = 'false', $page_editable);
                else
                    dropdown($row);
                echo "</div></div>";
                break;

			case "multi_dropdown":
				echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label>$row[field_label_name]</label>";
				if ($urow != 'false')
					multi_dropdown($row, $formatArray, $urow, $fieldValue = 'false', $page_editable);
				else
					multi_dropdown($row, $formatArray);
				echo "</div></div>";
				break;

            case "list_fragment":
                echo "<div class='$sigle_line_alignment $fd_css_class' style='overflow:auto!important;max-height:500px; $fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";

                if ($urow != 'false')
                    list_fragment($row);
                echo "</div></div><br>";
                break;

            case "crf":

                if ($method != 'add') {
                    echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";

                    $value = dropdown($row, $urow = 'list_display', $crf_value);

                    echo "<input type='$row[format_type]' name='$field' value='$value' $row[strict_disabled] $readonly $required title='$row[help_message]'  size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$fd_css_style'> <input type='hidden' name='$field' value='$crf_value' >";


                    echo "</div></div>";
                }
                break;

            case "email":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                echo "<input type='email' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]' size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$fd_css_style'> ";
                echo "</div></div>";
                break;

            case "textbox":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                echo "<textarea name='$field' class='form-control $fd_css_class' cols='$row[format_length]' $row[strict_disabled] size=$dimWidth $readonly style='$dimStyle;$fd_css_style'>$fieldValue</textarea>";
                echo "</div></div>";
                break;

            case "tag":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    tagFnc($row, $urow, $image_display, $dimStyle, $dimWidth);
                else
                    tagFnc($row, $dimStyle, $dimWidth);
                echo "</div></div>";
                break;

            case "checkbox":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='boolen_label' class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    checkbox($row, $urow, $page_editable);
                else
                    checkbox($row);
                echo "</div></div>";
                break;
						case "checklist":
								echo "<br><div class='new_form $fd_css_class' style='$fd_css_style margin-bottom:0;'>";
								if ($urow != 'false')
										checklist($row, $urow, $page_editable);
								else
										checklist($row);
								echo "</div>";
								break;
						case "progressbar":

								$progressbarDimensions = getFieldFormatLength($row['format_length']);
								$progressWidth = $progressbarDimensions['width'];
								$progressHeight = $progressbarDimensions['height'];
								$inputSize = getDefaultLengthsByType($row);
								echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='boolen_label' class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
								if(!$readonly){
									echo "<input type='text' name='$field' value='$fieldValue' size='$inputSize' $row[strict_disabled] $readonly $required title='$row[help_message]'  class='form-control $fd_css_class' style='$fd_css_style'> ";

								}else{
									progressbar($fieldValue,$fd_css_class,$fd_css_style,$progressWidth,$progressHeight);
								}
								echo "</div></div>";
								break;
            case "new_line":
                echo "<br>";
                break;


            case "line_divider":
                echo "<hr>";
                break;

            case "image":
								if($readonly){
									if(empty(trim($fieldValue))){
										break;
									}
								}
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    image_upload($row, $urow, $image_display);
                else
                    image_upload($row);
                echo "</div></div>";
                break;

						case "image_only":
								if($readonly){
									if(empty(trim($fieldValue))){
										break;
									}
								}
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    image_upload($row, $urow, $image_display);
                else
                    image_upload($row);
                echo "</div></div>";
                break;

            case "pdf":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    pdf_upload($row, $urow, $image_display);
                else
                    pdf_upload($row);
                echo "</div></div>";
                break;
						case 'datetime':
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";

						dateTimePicker($row,$formatArray,$dimWidth,$dimStyle,$urow, $page_editable);
						echo "</div></div>";
							break;

            case "video":
							showVideo($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize);
						break;

						case "video_only":
							if($readonly){
								if(empty(trim($fieldValue))){
									break;
								}
							}
							showVideo($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize);
							break;

							case "ppt":
								showPPT($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize);
							break;

							case "ppt_only":
								if($readonly){
									if(empty(trim($fieldValue))){
										break;
									}
								}
								showPPT($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize);
								break;

            case "pdf_inline":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    pdf_inline($row, $urow, $image_display);
                else
                    pdf_inline($row);
                echo "</div></div>";
                break;

            case "audio":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                if ($urow != 'false')
                    audio_upload($row, $urow, $image_display);
                else
                    audio_upload($row);
                echo "</div></div>";
                break;


            case "transaction_execute":
                echo "<div class='new_form $fd_css_class' style='$fd_css_style'>";
                echo "<button type='button' class='btn btn-default transaction_execute $fd_css_class' $row[strict_disabled] name='transaction_execute' id='$row[dict_id]' style='$fd_css_style'>$row[field_label_name]</button>";
                echo "</div>";
                break;

            case "transaction_confirmation":
                return "<div class='new_form $fd_css_class' style='$fd_css_style'>
                 <button type='button' class='btn btn-default transaction_confirmation $fd_css_class' $row[strict_disabled] name='transaction_confirmation'  style='$fd_css_style'>$row[field_label_name]</button>
                </div>";
                break;


            case "transaction_cancel":
                return "<div class='new_form $fd_css_class' style='$fd_css_style'>
                 <button type='button' class='btn btn-default transaction_cancel $fd_css_class' name='transaction_cancel' $row[strict_disabled] data-dismiss='modal' style='$fd_css_style'>$row[field_label_name]</button>
                </div>";
                break;

            case "transaction_text":
                return "<div class='new_form transaction_text $fd_css_class' style='$fd_css_style'>
                    $row[field_label_name]
                </div>";
                break;
			case "boolean":
				switch(@$formatArray[1]){
					case "slider":
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='boolen_label' class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
						if ($urow != 'false'){
							boolean_slider($row,$formatArray,$urow, $page_editable);
						} else {
							boolean_slider($row,$formatArray);
						}
						echo "</div></div>";
					break;

					case "button":
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='boolen_label' class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
							boolean_button($row,$formatArray,$urow, $page_editable);
						echo "</div></div>";

					break;

					default:
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
						echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]' size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$fd_css_style'>";
						echo "</div></div>";
				}
			break;
			case "number":
				switch(@$formatArray[1]){
					case "slider":
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
							number_slider($row,$formatArray,$urow, $page_editable);
						echo "</div></div>";
					break;

					default:
						echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
						echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required title='$row[help_message]'  size=$dimWidth class='form-control $fd_css_class'  style='$dimStyle;$fd_css_style'>";
						echo "</div></div>";
				}
			break;

			case "datepicker":
				echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
					datepicker($row,$formatArray,$urow, $page_editable);
				echo "</div></div>";
			break;

			case "confirm_password":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                echo "<input type='password' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]' size=$dimWidth' class='form-control $fd_css_class' $dimStyle style='$fd_css_style'>";
                echo "</div></div>";
			break;

			case "old_password":
                echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
                echo "<input type='password' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]'  size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$fd_css_style'>";
                echo "</div></div>";
			break;

            default :
                /*Code Start for Task 5.4.112*/
				if(("$row[format_type]" == "text")){
					if(!empty("$row[format_length]")){
						$params = explode(",","$row[format_length]");
						}
				}
				if(isKeyField($row)){
					$place_holder = KEYFIELD_PLACEHOLDER;
					echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label>$row[field_label_name]</span></label>";
					// echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]' $dimensions['style'] size=$dimensions['width'] class='form-control'>";
					echo "<input type='$row[format_type]' name='$field' placeholder='$place_holder' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]'  size=$dimWidth class='form-control'>";
					echo "</div></div>";
					return;
				}
				if(isset($params)){
					$height = $params['1'].'em';
					$width = $params['0'];
					$style = "style='height:$height;$fd_css_style'";
					echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
					echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]' size=$width class='form-control $fd_css_class' $style >";
					echo "</div></div>";
			/*Code Start for Task 5.4.112*/
				}else{
					echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
					// echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]' $dimensions['style'] size=$dimensions['width'] class='form-control'>";
					echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $disabled $required title='$row[help_message]'  size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$fd_css_style'>";
					echo "</div></div>";
				}
        }///switch conditions end here
    }/////userprivilege ends here
}

function parseProgressBarStyles($style){

	$style  = trim($style);

	$result = [];
	if(!empty($style)){
		$values =  explode(";",$style);
		foreach ($values as $key => $value) {
			if(!empty($value)){

				$subvalues =  explode(":",$value);
				if(!empty(trim($subvalues[0])) && !empty(trim($subvalues[1]))){
					$result[trim($subvalues[0])] = trim($subvalues[1]);
				}
			}
		}
	}

	return $result;
}
function progressbar($value,$class,$style,$width,$height){
	$color  = '#7cc4ff';
	$backgroundColor = '#c9c9c9';
	// $height = '10px';
	// $width = '50px';
	$cssStyles = parseProgressBarStyles($style);
	// pr($cssStyles);
	// if(isset($cssStyles['color'])){
	// 	$color = $cssStyles['color'];
	// }
	//
	// if(isset($cssStyles['background-color'])){
	// 	$backgroundColor = $cssStyles['background-color'];
	// }
	//
	// if(isset($cssStyles['width'])){
	// 	$width = $cssStyles['width'];
	// }
	//
	// if(isset($cssStyles['height'])){
	// 	$width = $height['height'];
	// }

	echo "<style>
				.progress {
					height: 1.5em;
  				width: 100%;
  				background-color: $backgroundColor;
  				position: relative;
				}
				.progress:before {
  				content: attr(data-label);
  				font-size: 0.8em;
  				position: absolute;
  				text-align: center;
  				top: 5px;
  				left: 0;
  				right: 0;
				}
				.progress .value {
  				background-color: $color;
  				display: inline-block;
  				height: 100%;
				}
			</style>";
			echo "<div style='width:$width;height:$height' class='progress $class' >
  			<span class='value' style='width:$value%;'></span>
			</div>";
}

function checklist($row, $urow = 'false', $page_editable = 'false'){
	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
	$readonly = '';
	$required = '';
	$fieldLabel  = ' '.$row['field_label_name'];
	if ($_GET['addFlag'] == 'true')
			$row['dd_editable'] = '11';

	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false || $row['strict_disabled']=='disabled')
			$readonly = 'readonly';


	if (!empty($row['required']))
			$required = 'required';

	echo "<input type='hidden' name='$row[generic_field_name]' value='0' >";

	if ($urow != 'false') {
			if ($urow[$row['generic_field_name']] == '1')
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' checked='checked' style='$dimStyle;$fd_css_style'><label style='margin-left:15px'>$fieldLabel</label>";
			else
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' style='$dimStyle;$fd_css_style'><label style='margin-left:15px'>$fieldLabel</label>";
	}else {

			echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' style='$dimStyle;$fd_css_style'><label style='margin-left:15px'>$fieldLabel</label>";
	}
}
function get_field_width_height($format_length){
	if(!empty("$row[format_length]")){
		$params = explode(",","$row[format_length]");
	}
	if(isset($params)){
		$height = $params['1'].'em';
		$width = $params['0'];
		$style = "style='height:$height;'";
	}
}

/*
 * audio UPLOAD FUNCTION
 */

function audio_upload($row, $urow = 'false', $image_display = 'false') {

	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
	echo "<script src='js/audio_recorder.js'></script>";

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
						<audio controls src='$audio_path_2' id='audio' ></audio><div class=' $fd_css_class' style='$fd_css_style' >
						<audio id='recordedAudio' style='display:none'></audio>

						</div>";
            echo "<div class='button_panel'>
						<a class='button' title='Press to start recording' id='startRecord' onclick='startRecording()'>" . audioRecord . "</a>
      			<a class='button disabled one recording_msg' title='Press to stop recording' id='stopRecord' style='background-color:red;display:none' onclick='stopRecording()'>Recording</a>
						<a class='button' id='remove' title='Will remove the selected and recorded files' onclick='clearAudio()'>" . audioClear . "</a>
						<input type='hidden' name='old_audio' class='old_audio' id='$row[generic_field_name]' value='$audio_path'>
						<input type='hidden' name='recorded_audio' class='old_audio' id='recorded_audio'></div>";
						echo "<div><input type='file' name='$row[generic_field_name]' accept='.mp3,.wav' id ='input_audio_file' class='form-control fileField' onchange='onInputFileChange()'></div>";

            if ($pos !== false || $pos_mp3 !== false) {
                echo "<div id='audio_message' class='audio-upload-filename'>$audio_path</div>";
            }else{
							  echo "<div id='audio_message' class='audio-upload-filename'></div>";
						}
            // echo "</div>";
        } else {

            echo "<div class='audio-css'>
						<audio controls src='' id='audio' ></audio><div class=' $fd_css_class' style='$fd_css_style'></div>";
        }

        echo "</div>";
    } else {
			//////////When there is no recording
        echo "<div class='audio-css'>
				<div class=''></div>";

        if ($image_display == 'true') {
            // echo "<input type='file' name='$row[generic_field_name]' id ='input_audio_file' class='form-control fileField' onchange='onInputFileChange()'>";

            echo "	<audio id=recordedAudio type='audio/mpeg'></audio>
						<div class='button_panel'>
						<a class='button' id='startRecord' onclick='startRecording()'>" . audioRecord . "</a>
						<a class='button disabled one recording_msg' title='Press to stop recording' id='stopRecord' style='background-color:red;display:none' onclick='stopRecording()'>Recording</a>
            <a class='button disabled' id='remove' onclick='clearAudio()'>" . audioClear . "</a>
						<input type='hidden' name='old_audio' class='old_audio' id='$row[generic_field_name]' value='$audio_path'>
						<input type='hidden' name='recorded_audio' class='old_audio' id='recorded_audio'></div>
						<div><input type='file' name='$row[generic_field_name]' accept='.mp3,.wav' id ='input_audio_file' class='form-control fileField' onchange='onInputFileChange()'></div>
						 <div id='audio_message' class='audio-upload-filename'>
						</div>";
        } else {

            echo "<input type='file' name='$row[generic_field_name]' class='form-control fileField'  disabled>";
        }

        // echo "</div>";
    }
}

/*
 * @function tagFnc
 */

function tagFnc($row, $urow = 'false', $image_display = 'false', $dimStyle, $dimWidth) {

	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
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



    echo "<input name='$row[generic_field_name]' class='$row[generic_field_name] $fd_css_class'   style='$dimStyle;$fd_css_style' size=$dimWidth value='$field_value'>";
}

/*
 * IMAGE UPLOAD FUNCTION
 */

function image_upload($row, $urow = 'false', $image_display = 'false') {


	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
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
	if(empty($row['format_length'])){
		echo "<span> <img src='" . USER_UPLOADS . "" . $img_show . "' border='0' width='150' class='img-thumbnail img-responsive user_thumb $masterToolTip $fd_css_class' style='$fd_css_style' alt='$row[generic_field_name]' $title /> </span>";
	}else{
		$format_length_value = getImageDimension($row['format_length']);
		$image_width = $format_length_value['width'];
		$image_height = $format_length_value['height'];
		echo "<span> <img src='" . USER_UPLOADS . "" . $img_show . "' border='0' width='$image_width' height='$image_height' class='user_thumb $masterToolTip $fd_css_class' style='$fd_css_style' alt='$row[generic_field_name]' $title /> </span>";
	}
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

	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
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

        echo "<div class='audio-upload-filename $fd_css_class' style='$fd_css_style'>$field_val[1]</div>";
    } else {
        echo "<input type='hidden' name='pdf[$row[generic_field_name]][imageName]' class='$row[generic_field_name]' />";

        echo "<div class='audio-upload-filename $fd_css_class' style='$fd_css_style'>No File!</div>";
    }


    echo "<input type='hidden' name='pdf[$row[generic_field_name]][uploadcare]' id='$row[generic_field_name]' />";

    echo "<div class='img-extra'></div>";



    echo " </div>";
}

/*
 * PDF UPLOAD FUNCTION
 */

function pdf_inline($row, $urow = 'false', $image_display = 'false') {

	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
    ///for adding we don't need an edit button to be clicked.
    if ($_GET['addFlag'] == 'true')
        $image_display = 'true';





    $row[generic_field_name] = trim($row[generic_field_name]);

    $field_val = $urow[$row[generic_field_name]];
    // $img = ($urow != 'false') ? $urow[$row[generic_field_name]] : '';
    // $img_show = (!empty($img) && file_exists(USER_UPLOADS . "pdf/" . $img) ) ? $img : 'pdf.png';

	/*Code Start for Task 5.4.112*/
				if("$row[format_type]" == "pdf_inline"){
					if(!empty("$row[format_length]")){
						$params = explode(",","$row[format_length]");
						}
				}
				if(isset($params)){
					$height = $params['1'].'px';
					$width = $params['0'];
					$style = "style='height:$height;'";
				}
	/*Code End for Task 5.4.112*/

    if ($image_display == 'true' && $row['strict_disabled'] == '') {
        echo "<div class='pdf-content $fd_css_class'  style='$fd_css_style'> <a href='' title='" . pdfInline . "' class='pdf_inline_anchor'>" . pdfInline . "</a>";
        echo "  <img  class='user_thumb' alt='$row[generic_field_name]' style='display:none;' />";
    } else {
        echo "<div class='pdf-content-clone $fd_css_class' style='$fd_css_style'>";

        $masterToolTip = $title = "";
    }


    /* if (!empty($_SESSION['profile-image'])) {


      $img_name = $_SESSION['dict_id'];
      } else {

      $img_name = 'no-profile';
      } */

    if (!empty($urow[$row[generic_field_name]])) {
		/*Code Changes Start for Task 5.4.20*/
        //echo "<embed  src='" . USER_UPLOADS . "pdf/$field_val' type='application/pdf' class='pdfInline'></embed> "
        echo "<embed  src='" . USER_UPLOADS . "pdf/$field_val' type='application/pdf' class='pdfInline' $style size=$width></embed> "
		;
		/*Code Changes End for Task 5.4.20*/
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
	$fd_css_class  =$row['fd_css_class'];
	$fd_css_style = $row['fd_css_code'];
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
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' checked='checked' style='$dimStyle;$fd_css_style'>";
        else
            echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' style='$dimStyle;$fd_css_style'>";
    }else {

        echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $readonly $row[strict_disabled] $required title='$row[help_message]'  size=$dimWidth class='form-control checkbox $fd_css_class' style='$dimStyle;$fd_css_style'>";
    }
}

////////////////DROPDOWN function///


function dropdown($row, $urow = 'false', $fieldValue = 'false', $page_editable = 'check') {


    $con = connect();
		$fd_css_class  =$row['fd_css_class'];
		$fd_css_style = $row['fd_css_code'];
    $rs = $con->query("SELECT * FROM  data_dictionary where table_alias = '$row[dropdown_alias]'");

    $dd = $rs->fetch_assoc();
		$isAllowedToAdd = false;
		if(isAllowedToShowByPrivilegeLevel($dd)){
			$isAllowedToAdd  = true;
		}
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
        $length = "style='width:$row[format_length]px'";
		// $length = "style='width:$row[format_length]em'";

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

		$table = $dd['database_table_name'];
		$primeryKey == $key;
		$primeryfieldValue = $fieldValue;
		$listFields = $list_fields;
		$listFields = str_replace($dd['keyfield'].',','',$listFields);
		$listFields = str_replace($dd['keyfield'],'',$listFields);

		// $list = str_replace($dd,'',$list_fields);




    if ($urow == 'list_display') {
        $qry = $con->query("SELECT $list_fields FROM  $dd[database_table_name] where $key='$fieldValue'");

        $res = $qry->fetch_assoc();

        $res2 = $res;

        unset($res2[$inviKey]);

        return $data = implode(dropdownSeparator, $res2);
    } else {
        $qry = $con->query("SELECT $list_fields FROM  $dd[database_table_name] $order");


			if($isAllowedToAdd){
				  echo "<select onclick='addnewOption(this)' data-table='$table' data-key='$primeryKey' data-primaryvalue='$primeryfieldValue' data-inputfields='$listFields' name='$row[generic_field_name]'  class='form-control $fd_css_class' $readonly $length style='$fd_css_style'>";
			}else{
				  echo "<select name='$row[generic_field_name]'  class='form-control $fd_css_class' $readonly $length style='$fd_css_style'>";
			}
		  echo "<option></option>";
			if($isAllowedToAdd && !$readonly){
				echo '<option  >Add NEW</option>';
			}

        while ($res = $qry->fetch_assoc()) {

            $res2 = $res;
            unset($res2[$inviKey]);


            $data = implode(dropdownSeparator, $res2);

            if ($urow[$row[generic_field_name]] == $res[$key]) {

                echo "<option value='$res[$key]' selected >$data</option>";
            } else
                echo "???? <option value='$res[$key]'>$data</option>";
        }
        echo "</select>";

    }
}
















function multi_dropdown($row, $formatArray, $urow = 'false', $fieldValue = 'false', $page_editable = 'check') {
	// pr($urow);

	$selectLimit = isset($formatArray[1]) ? $formatArray[1]  : maxSelectLimit;
	$v = $urow[$row[generic_field_name]];

	echo "<input id='limitValue' hidden value=$selectLimit />";
	echo "<input id='multiSelectValues' hidden value=$v name='$row[generic_field_name]' />";
    $con = connect();

    $rs = $con->query("SELECT * FROM  data_dictionary where table_alias = '$row[dropdown_alias]'");

    $dd = $rs->fetch_assoc();
	// pr($row);

//print_r($row);die;
    $list_fields = explode(',', $dd['list_fields']);

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

    if (!empty($row['format_length']))
        $length = "style='width:$row[format_length]px'";
//        $length = "style='width:$row[format_length]em'";
	// pr($length);

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

		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"></script>';

		// pr($urow[$row[generic_field_name]]);

		echo "<div>";
        echo "<select multiple class='multiple_select form-control' $readonly $length>";

        while ($res = $qry->fetch_assoc()) {

            $res2 = $res;
            unset($res2[$inviKey]);


            $data = implode(dropdownSeparator, $res2);
            if ($urow[$row[generic_field_name]] == $res[$key]) {

                echo "<option value='$res[$key]' selected >$data</option>";
            } else
                echo "<option value='$res[$key]'>$data</option>";
        }
        echo "</select> </div>";


		echo '<script>
			var limit = $("#limitValue").val();
		    $(document).ready(function() {
				$(".multiple_select").multiselect({
					buttonWidth: "200px",
	            	onChange: function(option, checked, select) {
						var length = $(".multiple_select").val().length;
						var values = $(".multiple_select").val();
						console.log("length : " + length );
						console.log("limit : " + limit );
						if(length > limit){
							var deselect = [];
							deselect.push(option.val());
							deselectValues(deselect);
						}else{
							$("#multiSelectValues").val(values);
						}

	            	}
        		});

				var vv = $("#multiSelectValues").val();
				vv = vv.split(",");
				selectValues(vv);

				function deselectValues(value){
					$(".multiple_select").multiselect("deselect",value );

				}

				function selectValues(value){
					$(".multiple_select").multiselect("select",value );

				}

				$(".multiselect.dropdown-toggle").css("height","40px");
				$(".multiselect.dropdown-toggle").css("border-radius","2px");
				$(".multiselect.dropdown-toggle").css("padding-left","5px");

		    });
		</script>';
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
	$fd_css_class  =$row2['fd_css_class'];
	$fd_css_style = $row2['fd_css_code'];
  $con = connect();

	$rs = $con->query("SELECT * FROM  data_dictionary where table_alias = '$row2[dropdown_alias]'");
	$dd = $rs->fetch_assoc();
	$required_list_fileds = false;
	if(!empty(trim($dd['list_fields']))){
		$required_list_fileds =explode(',',trim($dd['list_fields']));
		$in_querry_params = '';
		foreach ($required_list_fileds as $key => $value) {
			$in_querry_params = $in_querry_params."'$value',";
		}
		$in_querry_params = substr($in_querry_params,0,strlen($in_querry_params)-1);
	}
	if($required_list_fileds){
		$rs = $con->query("SELECT * FROM  field_dictionary where table_alias = '$row2[dropdown_alias]' AND generic_field_name IN($in_querry_params)");
	}else{
		$rs = $con->query("SELECT * FROM  field_dictionary where table_alias = '$row2[dropdown_alias]'");
	}

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

    //$list_fields = explode(',', $dd['list_fields']);
		//print_r($list_fields);die;

    $query = get_listFragment_record($dd['database_table_name'], $dd['keyfield'], $dd['list_filter'], $dd['list_extra_options'], $fields);

    echo "<table class='list_fragment $fd_css_class' style='white-space: nowrap; $fd_css_style'>";

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
	if($query->num_rows > 0){
		while ($rec = $query->fetch_assoc()) {
			echo "<tr>";
			$i = 1;
			foreach ($rec as $val) {
				echo "<td class='list_td$i' >$val</td>";
				$i++;
			}
			echo "</tr>";
		}
	}else {
		echo "<tr><td align='center' colspan='".count($labels)."'>No Record Found</td></tr>";
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
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' style='$dimStyle' size=$dimWidth checked='checked'>";
				} else {
					echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' style='$dimStyle' size=$dimWidth >";
				}
			} else {
				echo "<input type='checkbox' name='$row[generic_field_name]' value='1' $disabled $required title='$row[help_message]' style='$dimStyle' size=$dimWidth>";
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
		$fd_css_class  =$row['fd_css_class'];
		$fd_css_style = $row['fd_css_code'];
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
	echo "<input type='text' id='datepicker_$row[generic_field_name]' value='".(isset($urow[$row['generic_field_name']]) ? $urow[$row['generic_field_name']] : '')."' name='$row[generic_field_name]' $row[strict_disabled] $disabled $required title='$row[help_message]' $dimStyle size=$dimWidth class='form-control $fd_css_class' style='$fd_css_style'>";
}

/*
 *
 * dateTimePicker FUNCTION
 */

function dateTimePicker($row, $formatArray,$dimWidth,$dimStyle, $urow = false, $page_editable = false) {
    $required = '';
    $disabled = '';
		$fd_css_class  =$row['fd_css_class'];
		$fd_css_style = $row['fd_css_code'];
    if ($_GET['addFlag'] == 'true'){
		 $row['dd_editable'] = '11';
	}
	if (( $row['dd_editable'] != '11' && $urow != 'false' ) || $page_editable == false){
		$disabled = 'disabled';
	}
    if (!empty($row['required'])){
		$required = 'required';
	}

	echo '
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />';

	echo "<script>
			$( function() {
				$('#dateTimepicker_$row[generic_field_name]').datetimepicker({
						format: 'YYYY-MM-DD HH:mm:ss',
				});
			});
		  </script>";
	echo "<input type='text' id='dateTimepicker_$row[generic_field_name]' value='".(isset($urow[$row['generic_field_name']]) ? $urow[$row['generic_field_name']] : '')."' name='$row[generic_field_name]' $row[strict_disabled] $disabled $required title='$row[help_message]' size=$dimWidth class='form-control $fd_css_class' style='$dimStyle;$dimWidth$fd_css_style'>";
}

//to detect width and height
function getImageDimension($string){
	$values = explode(',',trim($string));

	$result['width'] = '';
	$result['height'] = '';
	if(!empty($values[0])){
		$result['width']=$values[0];
	}
	if(isset($values[1]) && !empty($values[1])){
		$result['height'] = $values[1];
	}
	return $result;
}

function getFieldDimension($string){
	$values = explode(',',trim($string));

	$result['width'] = '';
	$result['height'] = '';
	$result['style'] = '';

	// die($values);
	if(!empty($values[0])){
		$result['width']=$values[0];
	}
	if(isset($values[1]) && !empty($values[1])){
		$result['height'] = $values[1].'em';
		$result['style'] = "height:" . $result['height'] . ";";
	}
	return $result;
}

function setTheVideoURL($rawURL){
	$rawURL = str_replace('watch?v=','embed/',$rawURL);
	$index = strpos($rawURL,'&feat');
	if($index !== false){
		$rawURL = substr($rawURL,0,$index);
	}
	return $rawURL;
}

function setThePptURL($rawURL){
	$index = strpos($rawURL,'/edit');
	if($index===false){
		$index = strpos($rawURL,'/view');
	}
	if($index !== false){
		$rawURL = substr($rawURL,0,$index);
		$rawURL = $rawURL.'/embed?start=false&loop=false&delayms=3000';
	}
	return $rawURL;
}

function getFieldFormatLength($formatLength){
	$values = explode(',',trim($formatLength));
	$result['width'] = '50px';
	$result['height'] = '10px';

	if(!empty(trim($values[0]))){
		$result['width'] = trim($values[0]).'px';
	}
	if(isset($values[1]) && !empty(trim($values[1]))){
		$result['height'] = trim($values[1]).'px';
	}
	return $result;
}

function getVideoFormatLength($formatLength){
	$values = explode(',',trim($formatLength));
	$result['width'] = 300;
	$result['height'] = 300;

	if(!empty(trim($values[0]))){
		$result['width'] = (int)trim($values[0]);
	}
	if(isset($values[1]) && !empty(trim($values[1]))){
		$result['height'] = (int)trim($values[1]);
	}else{
		$result['height'] = $result['width'];
	}
	return $result;
}

//to add red staric for identification that these fields are system related and dont mess with them
function isKeyField($row){
	$data = $row['generic_field_name'];
	$length = strlen($data);
	$result = substr($data,$length-3);
	if(strtoupper($result) ==="_ID" || strtoupper($data)==="ID" || strtoupper($row['field_identifier'])==="KEYFIELD"){
		return true;
	}
	return false;
}


function showVideo($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize){
	if($readonly){
		$row['format_type'] = "hidden";
	}
	$dimensions = getVideoFormatLength($row['format_length']);
	$iframeWidth = $dimensions['width'];
	$iframeHeight = $dimensions['height'];
	$videoInputFieldWidth = $dimensions['width'].'px';
	$videoInputFieldHeight = $dimensions['height'].'px';
	$inputSize = getDefaultLengthsByType($row);

	echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
	echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required size='$inputSize' title='$row[help_message]' class='form-control $fd_css_class'  style='$fd_css_style'>";
	$fieldValue = setTheVideoURL($fieldValue);
	$srcdoc = '';
	if(empty(trim($fieldValue))){
		$srcdoc = "srcdoc='<h3>No video attached!</h3>'";
	}
	echo "<iframe $srcdoc width='$iframeWidth' height='$iframeHeight' src='$fieldValue' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen class='$fd_css_class' style='$fd_css_style'></iframe></div><br>";

	echo "</div>";
}

function showPPT($row,$sigle_line_alignment,$fd_css_class,$fd_css_style,$field,$fieldValue,$readonly,$required,$inputSize){
	if($readonly){
		$row['format_type'] = "hidden";
	}
	$dimensions = getVideoFormatLength($row['format_length']);
	$iframeWidth = $dimensions['width'];
	$iframeHeight = $dimensions['height'];
	$videoInputFieldWidth = $dimensions['width'].'px';
	$videoInputFieldHeight = $dimensions['height'].'px';
	$inputSize = getDefaultLengthsByType($row);

	echo "<div class='new_form $sigle_line_alignment $fd_css_class' style='$fd_css_style'><div><label class='$fd_css_class' style='$fd_css_style'>$row[field_label_name]</label>";
	echo "<input type='$row[format_type]' name='$field' value='$fieldValue' $row[strict_disabled] $readonly $required size='$inputSize' title='$row[help_message]' class='form-control $fd_css_class'  style='$fd_css_style'>";
	if(isSlideShareURL($fieldValue)){
		echo "<br>";
		echo "$fieldValue";
		echo "<br>";
	}else{
		$fieldValue = setThePptURL($fieldValue);
		$srcdoc = '';
		if(empty(trim($fieldValue))){
			$srcdoc = "srcdoc='<h3>No Slides attached!</h3>'";
		}
		echo "<iframe $srcdoc width='$iframeWidth' height='$iframeHeight' src='$fieldValue' allowfullscreen='true' mozallowfullscreen='true' webkitallowfullscreen='true' class='$fd_css_class' style='$fd_css_style'></iframe><br>";
	}

	echo "</div></div>";
}

function isSlideShareURL(&$url){
	$index = strpos($url,'www.slideshare.net/slideshow');
	if($index !== false){
		$index2 = strpos($url,'</iframe>');
			$url = substr($url,0,$index2+9);
		return true;
	}
	return false;
}
