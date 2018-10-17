<?php

/* 
 * 
 * FFFr_ICONS function for voting/rating
 */



function fffr_icons() {

    
    
    $con = connect();

    $fffr_rs = $con->query("SELECT * FROM  data_dictionary where display_page = '$_GET[display]' and tab_num = '$_GET[tabNum]' and table_alias = '$_GET[ta]'");
    $fffr_row = $fffr_rs->fetch_assoc();
    
    
    $icons_table = listExtraOptions($fffr_row['list_extra_options']);

    
    if ( !empty($icons_table['friend_tbl'] ) || !empty($icons_table['follow_tbl'] )  || !empty($icons_table['favorite_tbl'] ) || !empty($icons_table['rating_tbl'] ) || !empty($icons_table['voting_tbl'] )) {

       
        /////target_id
        $_SESSION['fffr_search_id'] = $_GET['search_id'];

        ///////////decoding table & icons////////////


        //print_r($icons_table);die;
        ////fffr icons div starts here
        echo "<div class='fffr'>";

        /*
         * Favorite
         */

        if (!empty($icons_table['favorite_tbl'])) {

            $check = getWhere($icons_table['favorite_tbl'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));

            echo " <span class='"
            . (empty($check[0]) ? "favorite_me_icon" : "favorite_me_icon_selected" ) . "' id='$icons_table[favorite_tbl]' title='" . favoriteTitle . "'></span>";
        }

        /*
         * Friends
         */

        if (!empty($icons_table['friend_tbl'])) {

            $check = getWhere($icons_table['friend_tbl'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));

            echo "<button type='button' class='button friend_me_icon' id='$icons_table[friend_tbl]' title='" . friendTitle . "'>"
            . (empty($check[0]) ? friendOn : friendOff) . "</button>";
        }

        /*
         * Followed
         */

        if (!empty($icons_table['follow_tbl'])) {

            $check = getWhere($icons_table['follow_tbl'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));

            echo " <button type='button' class='button follow_me_icon' id='$icons_table[follow_tbl]' title='" . followTitle . "'>"
            . (empty($check[0]) ? followOn : followOff) . "</button>&nbsp;&nbsp";
        }


        /*
         * Ratings
         * 
         * 
         */

        if (!empty($icons_table['rating_tbl'])) {


            $check = getWhere($icons_table['rating_tbl'], array('user_id' => $_SESSION['uid'], 'target_id' => $_SESSION['fffr_search_id']));

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



                $records = numOfRows($icons_table['rating_tbl'], array('user_id' => $_SESSION['uid']));



                if (( $icons_table['userLimit'] <= $records )) {

                    $disable_status = 'true';

                    $dilog_msg .= "<p>You can not cast vote on more than $icons_table[userLimit] Profiles</p>";
                } 
                
            }





            ///////////total vote allowed for profile//////


            if (!empty(trim($icons_table['voteLimit']))) {


                $records = sumValues($icons_table['rating_tbl']);


                if ( $icons_table['voteLimit'] <= $records ) {

                    $disable_status = 'true';

                    $dilog_msg .= "<p>Total Vote Limit Of $icons_table[voteLimit] Has Been Reached</p>";
                } 
            }



            ///////////total vote allowed for SINGLE USER//////


            if (!empty(trim($icons_table['userVoteLimit']))) {


                $records = sumValues($icons_table['rating_tbl'], array('user_id' => $_SESSION['uid']));

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
            
                 echo "<div class='rating-container'><input type='number' id='$icons_table[rating_tbl]' class='rating rate_me' data-min='$icons_table[lowerLimit]' data-show-clear='". showClear ."' " . ( ($disable_status == 'true') ? " data-disabled='true'" : "" )  . "data-show-caption='false' data-max='$icons_table[upperLimit]' data-step='1' data-size='xs' data-stars='$icons_table[upperLimit]'"
            . (!empty($value) ? " value='$value'" : "" ) . "></div>  ";
            
            }
           
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


        ////fffr icons divs ends here
        echo "</div>";
    }
}
