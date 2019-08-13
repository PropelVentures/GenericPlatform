<?php

/*
 *
 * FFFr_ICONS function for voting/rating
 */


function fffr_friend($row){
  $title = friendTitle;
  if(!empty(trim($row['tab_name']))){
    $title = trim($row['tab_name']);
  }

  $css_class = trim($row['list_style']);
  $dd_css_class = trim($row['dd_css_class']);
  $dd_css_code = trim($row['dd_css_code']);
  $_SESSION['fffr_search_id'] = $_GET['search_id'];
  echo "<div class=fffr ' $css_class $dd_css_class' style='$dd_css_code'>";
  $check = getWhere($row['database_table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));

  if(!iconOrButton($row,$check,'friend_me_icon')){
    echo "<button type='button' class='button friend_me_icon $css_class' id='$row[database_table_name]' title='" . $title . "'>"
    . (empty($check[0]) ? friendOn : friendOff) . "</button>";
    echo "<input type='hidden' id='friend_me_icon_selected' value='".friendOn."' />";
    echo "<input type='hidden' id='friend_me_icon_unselected' value='".friendOff."' />";
    echo "<input type='hidden' id='friend_me_icon_type' value='text' />";

  }
  echo "</div>";
}

function fffr_favorite($row){
  $title = favoriteTitle;
  if(!empty(trim($row['tab_name']))){
    $title = trim($row['tab_name']);
  }
  $css_class = trim($row['list_style']);
  $dd_css_class = trim($row['dd_css_class']);
  $dd_css_code = trim($row['dd_css_code']);

  $_SESSION['fffr_search_id'] = $_GET['search_id'];
  echo "<div class=fffr ' $css_class $dd_css_class' style='$dd_css_code'>";

  $check = getWhere($row['database_table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));
  if(!iconOrButton($row,$check,'favorite_me_icon')){
    echo " <span class='"
    . (empty($check[0]) ? "favorite_me_icon" : "favorite_me_icon_selected" ) . "' id='$row[database_table_name]' title='" . $title . "'></span>";
    echo "<input type='hidden' id='favorite_me_icon_selected' value='favorite_me_icon' />";
    echo "<input type='hidden' id='favorite_me_icon_unselected' value='favorite_me_icon_selected' />";
    echo "<input type='hidden' id='favorite_me_icon_type' value='icon' />";
  }

  echo "</div>";
}

function fffr_follow($row){
  $title = followTitle;
  if(!empty(trim($row['tab_name']))){
    $title = trim($row['tab_name']);
  }
  $css_class = trim($row['list_style']);
  $dd_css_class = trim($row['dd_css_class']);
  $dd_css_code = trim($row['dd_css_code']);

  $_SESSION['fffr_search_id'] = $_GET['search_id'];
  echo "<div class=fffr ' $css_class $dd_css_class' style='$dd_css_code'>";
  $check = getWhere($row['database_table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));
  if(!iconOrButton($row,$check,'follow_me_icon')){
    echo "<button type='button' class='button follow_me_icon $css_class' id='$row[database_table_name]' title='" . $title . "'>"
    . (empty($check[0]) ? followOn : followOff) . "</button>";
    echo "<input type='hidden' id='follow_me_icon_selected' value='".followOn."' />";
    echo "<input type='hidden' id='follow_me_icon_unselected' value='".followOff."' />";
    echo "<input type='hidden' id='follow_me_icon_type' value='text' />";


  }
  echo "</div>";
}

function fffr_contact($row){
  $title = 'Contact Me';
  if(!empty(trim($row['tab_name']))){
    $title = trim($row['tab_name']);
  }
  $css_class = trim($row['list_style']);
  $dd_css_class = trim($row['dd_css_class']);
  $dd_css_code = trim($row['dd_css_code']);
  $table = trim($row['database_table_name']);
  $_SESSION['fffr_search_id'] = $_GET['search_id'];
  $reciverId = $_SESSION['fffr_search_id'];
  echo "<div class=fffr ' $css_class $dd_css_class' style='$dd_css_code'>";
  echo "<button type='button'  class='button contact_me_icon $css_class'  data-table='$table' data-reciver='$reciverId' title='" . $title . "'>$title</button>";
  echo "</div>";
}

function fffr_rating($row){
  $_SESSION['fffr_search_id'] = $_GET['search_id'];
  $css_class = trim($row['list_style']);
  $dd_css_class = trim($row['dd_css_class']);
  $dd_css_code = trim($row['dd_css_code']);
  echo "<div class=fffr ' $css_class $dd_css_class' style='$dd_css_code'>";
  $icons_table = listExtraOptions($row['list_extra_options']);
  $check = getWhere($row['database_table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));
  $value = $check[0][value];
  //data-toggle='tooltip' data-placement='bottom' title='Tooltip on bottom'
  /*             * **coding the javascript function** */
  $disable_status = 'false';
  $dilog_msg = '';
  ////if voteChange is enable(true).....
  if (trim($icons_table['voteChange']) == 'false') {
      if (!empty($value)) {
          $disable_status = 'true';
          $dilog_msg = voteChangeOptionDisable;
      }
  }
  ///////////Voting limit checked (user allowed to vote on number of profiles//////
  if (!empty(trim($icons_table['userLimit']))) {
      $records = numOfRows($row['database_table_name'], array('user_id' => $_SESSION['uid']));
      if (( $icons_table['userLimit'] <= $records )) {
          $disable_status = 'true';
          $dilog_msg .= "<p>You can not cast vote on more than $icons_table[userLimit] Profiles</p>";
      }
  }
  ///////////total vote allowed for profile//////
  if (!empty(trim($icons_table['voteLimit']))) {
      $records = sumValues($row['database_table_name']);
      if ( $icons_table['voteLimit'] <= $records ) {
          $disable_status = 'true';
          $dilog_msg .= "<p>Total Vote Limit Of $icons_table[voteLimit] Has Been Reached</p>";
      }
  }
  ///////////total vote allowed for SINGLE USER//////
  if (!empty(trim($icons_table['userVoteLimit']))) {
      $records = sumValues($row['database_table_name'], array('user_id' => $_SESSION['uid']));
      //print_r($records);die;
      if ( $icons_table['userVoteLimit'] <= $records ) {
          $disable_status = 'true';
          $dilog_msg .= "<p>Your Total Vote Limit Of $icons_table[userVoteLimit] Has Been Reached</p>";
      }
  }
  /*
   * if to change vote is allowed then it will only let user to change the casted vote
   */
  if(!empty($value) && trim($icons_table['voteChange']) != 'false'){
      $dilog_msg = '';
      $disable_status = "false";
  }
  /*
   *
   * Actual Rating icons code goes here
   *
   *
   */
  if( trim($icons_table['votingType']) == 'number'){
    echo " <span class='fffr-rating-number'><span class='numberLabel'>$icons_table[numberLabel]</span>
            <input type='number' class='fffr-input'   min='$icons_table[lowerLimit]' max='$icons_table[upperLimit]'   value='" . (!empty($value) ?  $value : 0 ) . "'   " . ( ($disable_status == 'true') ? " readonly" : "" )  . ">
            <a href='#' class='button voting-number" . ( ($disable_status == 'true') ? " disabled" : "" )  . "' id='$icons_table[rating_tbl]'>". votingNoSubmitBtn . "</a>
          </span>";
  }else{
       echo "<div class='rating-container'><input type='number' id='$row[database_table_name]' class='rating rate_me' data-min='$icons_table[lowerLimit]' data-show-clear='". showClear ."' " . ( ($disable_status == 'true') ? " data-disabled='true'" : "" )  . "data-show-caption='false' data-max='$icons_table[upperLimit]' data-step='1' data-size='xs' data-stars='$icons_table[upperLimit]'"
  . (!empty($value) ? " value='$value'" : "" ) . "></div>  ";
  }
  echo "</div>";
  /*
   *
   * Javascript dialoge display scripts to display Alert msgs to the users.
   */
  if( !empty( $dilog_msg)  ){
        echo "<script>$(document).on('ready', function(){
          $('.rating-container, .fffr-rating-number').click(function () {
          $('.votingBody').html('$dilog_msg');
          $('#votingModal').modal('show');
        });
      });</script>";
  }
}

function fffr_icons($display_page){
    echo "<div>";
    $haveAnyFFFR = false;
    $con = connect();
    $fffr_rs = $con->query("SELECT * FROM  data_dictionary where display_page = '$display_page'" );
    while($row = $fffr_rs->fetch_assoc()){
    switch($row['table_type']){
      case 'friend':
        $haveAnyFFFR = true;
        fffr_friend($row);
        break;
      case 'follow':
      $haveAnyFFFR = true;
        fffr_favorite($row);
        break;
      case 'favorite':
      $haveAnyFFFR = true;
        fffr_follow($row);
        break;
      case 'rating':
      $haveAnyFFFR = true;
        fffr_rating($row);
        break;
      case 'contact_me':
      $haveAnyFFFR = true;
        fffr_contact($row);
        break;
      default:
        break;
    }
  }
  echo "</div>";
  if($haveAnyFFFR){
    echo "<br><br>";
  }
}

function iconOrButton($row,$check,$type){

  $display_type;
  $selected ;
  $unselected;
  $title = friendTitle;
  if(!empty(trim($row['tab_name']))){
    $title = trim($row['tab_name']);
  }
  $extraOptions = trim($row['list_extra_options']);
  if(empty($extraOptions)){
    return false;
  }
  $extraOptions = explode(':',$extraOptions);
  if(!empty(trim($extraOptions[0]))){
    $display_type = strtolower(trim($extraOptions[0]));
    $selectedUnselected = explode(',',$extraOptions[1]);
      if(!empty(trim($selectedUnselected[0])) && !empty(trim($selectedUnselected[1]))){
        $selected = trim($selectedUnselected[0]);
        $unselected = trim($selectedUnselected[1]);
        $unselected = str_replace(';','',$unselected);
      }else{
        return false;
      }
  }
  $selected_id = $type.'_selected';
  $unselected_id = $type.'_unselected';
  $type_value = $type.'_type';

  if($display_type =='icon'){
    echo " <span class='"
    . (empty($check[0]) ? "$selected" : "$unselected" ) . "' id='$row[database_table_name]' title='" . $title . "'></span>";
  }else if($display_type=='text'){
      echo "<button type='button' class='button $type $css_class' id='$row[database_table_name]' title='" . $title . "'>"
        .(empty($check[0]) ? $selected : $unselected) . "</button>";

  }
  echo "<input type='hidden' id='$selected_id' value='".$selected."' />";
  echo "<input type='hidden' id='$unselected_id' value='".$unselected."' />";
  echo "<input type='hidden' id='$type_value' value='$display_type' />";
  return true;

}
