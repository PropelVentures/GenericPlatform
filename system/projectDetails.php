<?php
include("../application/header.php");
include($GLOBALS['APP_DIR'] . "system/special_config.php");

if (isset($_GET['pid']) && !empty($_GET['pid']))
{
  //echo "<br><br><br><br><br><br>";
  $sql = "SELECT * FROM {$projectTblArray['tableAlias']}
	 LEFT JOIN {$userTblArray['table_alias']} ON {$projectTblArray['tableAlias']}.{$projectTblArray['uid_fld']}={$userTblArray['table_alias']}.{$userTblArray['uid_fld']}
	 LEFT JOIN tags ON {$projectTblArray['tableAlias']}.{$projectTblArray['pid_fld']}=tags.pid
	 LEFT JOIN project_categories ON {$projectTblArray['tableAlias']}.{$projectTblArray['cid_fld']} = project_categories.project_category_id
	 WHERE {$projectTblArray['tableAlias']}.{$projectTblArray['pid_fld']}=" . $_GET['pid'];


      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...
  $sql = "SELECT * FROM {$projectTblArray['databas_tabl_name']}
	 LEFT JOIN {$userTblArray['table_alias']} ON {$projectTblArray['tableAlias']}.{$projectTblArray['uid_fld']}={$userTblArray['table_alias']}.{$userTblArray['uid_fld']}
	 LEFT JOIN tags ON {$projectTblArray['tableAlias']}.{$projectTblArray['pid_fld']}=tags.pid
	 LEFT JOIN project_categories ON {$projectTblArray['tableAlias']}.{$projectTblArray['cid_fld']} = project_categories.project_category_id
	 WHERE {$projectTblArray['tableAlias']}.{$projectTblArray['pid_fld']}=" . $_GET['pid'];


  $result = mysql_query($sql);
  $project = mysql_fetch_array($result);

  $sqlConfig = "SELECT * FROM `project_config` WHERE pid=" . $_GET['pid'];
  $resultConfig = mysql_query($sqlConfig);
  $projectConfig = mysql_fetch_array($resultConfig);
}
?>
<script src="<?php echo BASE_JS_URL ?>jquery.min.js"></script>
<script>
  $(document).ready(function () {

    //Subscribe Project
    $(document).on('click', '.relationship', function (e) {
      //alert("sure you want to like this fellow?");
      e.preventDefault();
      var currentObject = $(this);
      var projectSubscribe = $(this).attr('data-id');
      //var getClass = $(this).attr('class');
      var action = $(this).attr('data-action');
      $.post(
              '<?php echo BASE_URL ?>ajax-actions.php',
              {projectSubscribe: projectSubscribe, relationship_action: action},
      function (data) {
        if (data) {
          currentObject.attr('data-action', action == 0 ? '1' : '0');
          currentObject.attr('value', action == 0 ? 'Unsubscribe' : 'Subscribe');
        } else {
          alert("Sorry you cant subscribe this user.");
        }
      }
      );
    });

  });

</script>
<div class="jumbotron search-form" >
  <div class="container">
    <div class="row">
      <div class="col-6 height2">
        <?php
        if (isset($_SESSION["messages"]))
        {
          echo "<div class='alert alert-info'>";
          echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";
          echo FlashMessage::render();
          echo "</div>";
        }
        ?>
      </div>
      <div class="row top-add-tab">
        <div class="col-md-6 col-lg-8 col-sm-6 add-tab">
          <div class="row">
            <div class="row top-add-tab">
              <div class="col-md-10 col-lg-12 col-sm-10 add-tab">
                <div class="left-content" style="float:left; width:25%;"> <img class="img-thumbnail" src="<?php echo BASE_URL ?><?php echo ($project[$projectTblArray['projectImage_fld']] != "") ? ("project_uploads/thumbs/" . $project['projectImage']) : 'images/thumb_defaultImageIcon.png' ?>"> </div>
                <div class="right-content" style="float:left; width:75%; padding-left:5px;">
                  <h2>
                    <?php echo isset($project[$projectTblArray['pname_fld']]) ? $project[$projectTblArray['pname_fld']] : 'Project Name' ?>
                  </h2>
                  <span>(
                    <?php echo isset($project['project_categeory_name']) ? $project['project_categeory_name'] : 'No Categories for this Project.' ?>
                    )</span> </div>
                <?php
                //Follow button
                if (isset($_SESSION['uid']) && $project[$projectTblArray['uid_fld']] != $_SESSION['uid'])
                {

                  $sql = "SELECT * FROM project_subscribe WHERE user_id = " . $_SESSION['uid'] . " and project_id=" . $_GET['pid'];
                  $result = mysql_query($sql);
                  if (mysql_num_rows($result) > 0)
                  {
                    echo '<div>
													<input type="button" name="projectSubscribe" class="relationship follow" value="Unsubscibe" data-id="' . $_GET['pid'] . '" data-action="1">
												</div>';
                  }
                  else
                  {
                    echo '<div>
													<input type="button" name="projectSubscribe" class="relationship follow" value="Subscribe" data-id="' . $_GET['pid'] . '" data-action="0">
												</div>';
                  }
                }
                ?>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="row top-add-tab">
              <div class="col-md-10 col-lg-12 col-sm-10 add-tab">
                <ul class="nav nav-tabs">
                  <?php
                  if ($projectConfig['description'] == 1)
                  {
                    ?>
                    <li class="active"> <a href="#description" data-toggle="tab"> <i class="fa fa-file-text-o"></i><br />
                        <span>Description</span> </a> </li>
                    <?php
                  }
                  if ($projectConfig['transaction'] == 1)
                  {
                    ?>
                    <li><a href="#transaction" data-toggle="tab"> <i class="fa fa-tags"></i><br />
                        <span>Transactions</span> </a></li>
                    <?php
                  }
                  if ($projectConfig['comments'] == 1)
                  {
                    ?>
                    <li><a href="#comments" data-toggle="tab"> <i class="fa fa-file-text-o"></i><br />
                        <span>Comments</span> </a></li>
                    <?php
                  }
                  if ($projectConfig['image_gallery'] == 1)
                  {
                    ?>
                    <li><a href="#image_gallery" data-toggle="tab"> <i class="fa fa-tags"></i><br />
                        <span>Image Gallery</span> </a></li>
                    <?php
                  }
                  ?>
                </ul>
                <div class="tab-content">
                  <?php
                  if ($projectConfig['description'] == 1)
                  {
                    ?>
                    <div class="tab-pane active" id="description">
                      <div class="col-lg-12 right-content user-profile set-img" >
                        <?php echo isset($project[4]) ? stripslashes($project[4]) : 'Project Does Not Have Any Description' ?>
                        <br/>
                      </div>
                    </div>
                    <?php
                  }
                  if ($projectConfig['transaction'] == 1)
                  {
                    ?>
                    <!-- @end #hello -->
                    <div class="tab-pane" id="transaction">
                      <div class="col-lg-12 right-content user-profile set-img">
                        <div>Transaction</div>
                        <div class="displayTags">
                          show  transactions here
                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  if ($projectConfig['comments'] == 1)
                  {
                    ?>
                    <!-- @end #empty -->
                    <div class="tab-pane" id="comments">
                      <div class="col-lg-12 right-content user-profile set-img">
                        <h3>Comments</h3>
                        Show Comments here
                      </div>
                    </div>
                    <?php
                  }
                  if ($projectConfig['image_gallery'] == 1)
                  {
                    ?>
                    <!-- @end #templates -->
                    <div class="tab-pane" id="image_gallery">
                      <div class="col-lg-12 right-content user-profile set-img">
                        <h3>Image Gallery</h3>
                        Show Image Gallery here
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                </div>
                <!-- @end .tab-content -->
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 col-sm-6 p-owner">
          <div class="project-detail" >
            <div class="row side-set">
              <div class="col-md-5 col-sm-5"> <span class="profile-image"> <img class="img-responsive" src="<?php echo BASE_URL ?><?php echo ($project[$userTblArray['image_fld']] != "") ? ("users_uploads/thumbs/" . $project[$userTblArray['image_fld']]) : 'images/thumb_defaultImageIcon.png' ?>"> </span> </div>
              <div class="col-md-7 col-sm-7">
                <div class="project-info ownername" >
                  <h3>Project Owner <a href="<?php echo BASE_URL_SYSTEM ?>userDetails.php?uid=<?php echo $project[$projectTblArray['uid_fld']] ?>" style="text-decoration:underline;">
                      <?php echo $project[$userTblArray['uname_fld']] ?>
                    </a></h3>
                </div>
              </div>
            </div>
            <div class="bootom-cont">
              <h2 class="left-set"><span>Quantity</span> <span class="val-amt">
                  <?php echo $project[$projectTblArray['quantity_fld']] ?>
                </span> </h2>
              <h2 class="left-set"> <span>Amount </span> <span class="val-amt">
                  <?php echo $project[$projectTblArray['amount_fld']] ?>
                </span> </h2>
              <?php
              if ($project[$projectTblArray['quantity_fld']] > 0)
              {

                $exp_date = strtotime($project[$projectTblArray['expiry_date_fld']]);
                if ($exp_date < time())
                {
                  echo '<p> <span><strong>Expired</strong> </span></p>';
                }
                else
                {
                  echo '<p> <span><strong>' . PROJECT_EXPIRY_DATE . '-</strong> </span> <span class="date">' . $project[$projectTblArray['expiry_date_fld']] . '</span> </p>';
                }
              }
              ?>
              <div class="clear"></div>
              <?php
              if ($project[$projectTblArray['uid_fld']] != $_SESSION["uid"])
              {
                if (($exp_date > time()) && $project[$projectTblArray['quantity_fld']] > 0)
                {
                  ?>
                          <h2><!--<a href="<?php echo BASE_URL ?>transactionActions.php?action=buyProject&pid=<?php echo $_GET['pid'] ?>">
                            <button class="submit btn btn-primary " type="button" name="buyProject">Buy Project</button>
                            </a>--></h2>
                  <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#buyProject">
                    Buy Project
                  </button>
                  <?php
                }
                else
                {
                  echo "This Project Not Available.";
                }
              }
              else
              {
                echo "This is your project.";
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="buyProject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL ?>transactionActions.php" method="get">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Buy This Project</h4>
        </div>
        <div class="modal-body" style="float:left; width:100%;">
          <div style="float:left; width:100%;" >
            <div>
              <label> Project Name </label>
              <span>
                <?php echo $project[$projectTblArray['pname_fld']] ?>
              </span>
            </div>
            <div>
              <label>Quantity </label>
              <span> 1 </span>
            </div>
            <div>
              <label> Amount</label>
              <span>
                <?php echo $project[$projectTblArray['amount_fld']] ?>
              </span>
            </div>
            <div>
              <label> Transaction Type </label>
              <span>
                <select name="ttype">
                  <option value=''> --Select-- </option>
                  <?php
                  if ($projectConfig['copy'] == 1)
                  {
                    echo "<option value='copy'> Copy </option>";
                  }
                  if ($projectConfig['fork'] == 1)
                  {
                    echo "<option value='fork'> Fork </option>";
                  }
                  if ($projectConfig['subscribe'] == 1)
                  {
                    echo "<option value='subscribe'> Subscribe </option>";
                  }
                  if ($projectConfig['share'] == 1)
                  {
                    echo "<option value='share'> Share </option>";
                  }
                  ?>
                </select>
              </span>
            </div>
          </div>
          <input type="hidden" name="action" value="buyProject" />
          <input type="hidden" name="pid" value="<?php echo $_GET['pid'] ?>" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input class="submit btn btn-primary " type="submit" name="buyProject" value="Buy Project">
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  jQuery("#myprojects_tab").click(function () {
    jQuery("#myprojects_tab").toggleClass("open");
  });
</script>
<?php include("../application/footer.php"); ?>
