<?php
include("../application/header.php");

if (isset($_GET["uid"]) && $_GET["uid"] != "")
{
  $userId = $_GET["uid"];
  $result = mysql_query("SELECT * FROM {$userTblArray['table_alias']} where {$userTblArray['uid_fld']} =" . $userId);
  $projects = mysql_query("SELECT * FROM {$projectTblArray['tableAlias']} where {$projectTblArray['uid_fld']} =" . $userId . " and {$projectTblArray['isLive_fld']}=1 and {$projectTblArray['isBought_fld']}=0");
  $users = mysql_fetch_array($result);
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style>
  .pretyImg{
    padding: 1px;
    border: 1px solid #ccc;
    box-shadow: 1px 1px #999999;
  }
  .userDetailsWrapper {
    margin: 0px 50px;
    /*float: left;*/
    padding: 0px;
    width: 60%;
  }
  #pdwrapper ul li{width:40%}
  .userImage img{border-radius:0px !important; border:1px solid #ddd; padding:2px !important; margin-left:15px !important;}
  .comment-info{width:80% !important;}
</style>
<div id="projectWrapper">
  <div class="contentWrapper">
    <!--mainContentArea starts here-->
    <div class="jumbotron search-form">
      <div class="container">
        <div class="row row-offcanvas row-offcanvas-right">
          <div class="row">
            <div class="col-6 height2"> </div>
          </div>
          <div class="col-6 col-sm-6 col-lg-3">
            <div class="left-content userdetail" >
              <?php
              if ($users[$userTblArray['image_fld']] == "")
              {
                echo '<img class="img-thumbnail" src="' . BASE_URL . 'users_uploads/defaultImageIcon.png">';
              }
              else
              {
                if (file_exists(MYPATH_USERS_DIR_THUMB . $users[$userTblArray['image_fld']]))
                {
                  echo '<img class="img-thumbnail" src="' . BASE_URL . 'users_uploads/thumbs/' . $users[$userTblArray['image_fld']] . '">';
                }
                else
                {
                  echo '<img class="img-thumbnail" src="' . BASE_URL . 'users_uploads/' . $users[$userTblArray['image_fld']] . '">';
                }
              }
              ?>
            </div>
            <?php
            //Follow button
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] != $_GET['uid']))
            {

              $sql = "SELECT * FROM user_follow WHERE user_id = " . $_SESSION['uid'] . " and target_user_id=" . $_GET['uid'];
              $result = mysql_query($sql);
              if (mysql_num_rows($result) > 0)
              {
                echo '<div>
						<input type="button" name="userFollow" class="relationship follow" value="Unfollow" data-id="' . $_GET['uid'] . '" data-action="1">
					</div>';
              }
              else
              {
                echo '<div>
						<input type="button" name="userFollow" class="relationship follow" value="Follow" data-id="' . $_GET['uid'] . '" data-action="0">
					</div>';
              }
            }
            ?>
          </div>
          <div class="col-6 col-sm-6 col-lg-9 right-content user-profile">
            <?php
            if (isset($_SESSION["messages"]))
            {
              # end progressbar code by me
              echo "<div class='alert alert-info'>";
              echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";
              echo "<p>" . FlashMessage::render() . "</p>";
              echo "</div>";
            }
            ?>
            <ul class="nav nav-tabs" role="tablist">
              <li <?php echo (!isset($_GET['tab']) && $_GET['tab'] == "") ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $userId ?>"><?php echo USER_INFO ?></a></li>
              <li <?php echo (isset($_GET['tab']) && $_GET['tab'] == "about") ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $userId ?>&tab=about"><?php echo USER_DESCRIPTION ?></a></li>
              <li <?php echo (isset($_GET['tab']) && $_GET['tab'] == "projects" ) ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $userId ?>&tab=projects"><?php echo PROJECT . 's' ?></a></li>
              <li <?php echo (isset($_GET['tab']) && $_GET['tab'] == "userTransactions" ) ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $userId ?>&tab=userTransactions"><?php echo TRANSACTION . 's' ?></a></li>
              <li <?php echo (isset($_GET['tab']) && $_GET['tab'] == "comments" ) ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $userId ?>&tab=comments"><?php echo COMMENT . 's' ?></a></li>
            </ul>
            <div style="border:1px solid #ddd; padding:5px 10px 5px 10px; margin-top:-1px;">
              <?php
              if (!isset($_GET['tab']) || $_GET['tab'] == "userDetails")
              {
                include_once($GLOBALS['APP_DIR'] . "tabs/userDetails.php");
              }
              if ($_GET['tab'] == "about")
              {
                echo "<br />" . stripslashes($users[$userTblArray['description_fld']]) . "<br />";
              }
              if ($_GET['tab'] == "comments")
              {
                include_once($GLOBALS['APP_DIR']."tabs/comments.php");
              }
              if ($_GET['tab'] == "userTransactions")
              {
                include_once($GLOBALS['APP_DIR']."tabs/userTransactions.php");
              }
              if ($_GET['tab'] == "projects")
              {
                include_once($GLOBALS['APP_DIR']."tabs/user_projects.php");
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $("#myprojects_tab").click(function () {
        $("#myprojects_tab").toggleClass("open");
      });

      $(document).ready(function () {

        //Follow User
        jQuery(document).on('click', '.relationship', function (e) {
          //alert("sure you want to like this fellow?");
          e.preventDefault();
          var currentObject = $(this);
          var userFollowed = $(this).attr('data-id');
          //var getClass = $(this).attr('class');
          var action = $(this).attr('data-action');
          $.post(
                  '<?php echo BASE_URL ?>ajax-actions.php',
                  {userFollowed: userFollowed, relationship_action: action},
          function (data) {
            if (data) {
              currentObject.attr('data-action', action == 0 ? '1' : '0');
              currentObject.attr('value', action == 0 ? 'Unfollow' : 'Follow');
            } else {
              alert("Sorry you cant follow this user.");
            }
          }
          );
        });

      });

    </script>
    <script>
      jQuery("#myprojects_tab").click(function () {
        jQuery("#myprojects_tab").toggleClass("open");
      });
    </script>
    <?php include("../application/footer.php"); ?>