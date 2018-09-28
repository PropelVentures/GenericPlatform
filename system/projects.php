<?php
include("../application/header.php");
include_once($GLOBALS['APP_DIR'] . "actions/CustomHtml.php");
require($GLOBALS['APP_DIR'] . "system/special_config.php");
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="<?php echo BASE_URL ?>bootstrap-validator/css/bootstrapValidator.css"/>
<script type="text/javascript" src="<?php echo BASE_URL ?>bootstrap-validator/js/jquery-1.10.2.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="https://ucarecdn.com/widget/1.3.1/uploadcare/uploadcare-1.3.1.min.js"></script>

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
        <div class="col-md-10 col-lg-12 col-sm-10 add-tab">
          <div class="row">
            <div class="row top-add-tab">
              <div class="col-md-10 col-lg-12 col-sm-10 add-tab">
                <ul class="nav nav-tabs">


                  <li <?php echo (!isset($_GET['tab'])) ? 'class="active"' : '' ?>>
                    <a href="<?php echo BASE_URL_SYSTEM ?>projects.php?tab=<?php echo $projectTblArray['tableAlias']; ?>&pname=<?php echo $projectTblArray['tableAlias']; ?>">
                      <span>
                        <?php echo $Tbl_TAB_NAME_Array[$projectTblArray['tableAlias']]; ?>
                      </span> </a> </li>
                  <?php
                  //echo "<pre>";
                  //print_r($projsTblChildArray);
                  foreach ($projsTblChildArray as $k => $v)
                  {
                    $class = "";
                    if ($_REQUEST['tab'])
                    {
                      if ($_REQUEST['tab'] == $v)
                      {
                        $class = "active";
                      }
                      echo "<li class='" . $class . "'><a href='" . BASE_URL_SYSTEM . "projects.php?tab=" . $v . "&pname=" . $v . "'><span>" . $Tbl_TAB_NAME_Array[$v] . "</span></a></li>";
                    }
                    else
                    {
                      echo "<li class='" . $class . "'><a href='" . BASE_URL_SYSTEM . "projects.php?tab=" . $v . "&pname=" . $v . "'><span>" . $Tbl_TAB_NAME_Array[$v] . "</span></a></li>";
                    }
                  }
                  ?>
                  <li <?php echo ($_GET['tab'] == 'otherProjects') ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>projects.php?tab=otherProjects" > <!--<i class="fa fa-tags"></i><br />-->
                      <span>
                        <?php echo OTHERS_PROJECTS_TITLE ?>
                      </span> </a></li>
                    <li <?php echo ($_GET['tab'] == 'listprojects') ? 'class="active"' : '' ?>><a href="<?php echo BASE_URL_SYSTEM ?>projects.php?tab=listprojects" > <!--<i class="fa fa-file-text-o"></i><br />-->
                      <span>
                        Projects List
                      </span> </a></li>
                </ul>
                <div class="tab-content">
                  <!-- @start #myProjects -->

                  <?php
                  if ($_REQUEST['pname'] == $projectTblArray['tableAlias'] || (!isset($_REQUEST['pname']) && !isset($_REQUEST['tab'])))
                  {
                    $_REQUEST['tab'] = $projectTblArray['tableAlias'];
                    $_REQUEST['pname'] = $projectTblArray['tableAlias'];
                    $listParam = GenericDBFunctions::getListParams($projectTblArray['tableAlias']);
                    $list_view_array = array();
                    $list_view_array = explode(",", $listParam['list_view']);
                    foreach ($list_view_array as $k => $v)
                    {
                      if (strpos($v, '*') !== false)
                      {
                        $default_list_view = explode('*', $v);
                        $default_list_view = $default_list_view[1];
                      }
                    }
                    $_REQUEST['default_list_view'] = strtolower($default_list_view);
                    $_REQUEST['list_view'] = strtolower(str_replace('*', '', $listParam['list_view']));
                    //$_REQUEST['list_view']=
                    $_REQUEST['list_filter'] = $listParam['list_filter'];
                    $_REQUEST['list_sort'] = $listParam['list_sort'];
                    $_REQUEST['list_field'] = $listParam['list_field'];
                    if (count($list_view_array) > 0)
                    {
                      echo "<div class='col-6 col-sm-6 col-lg-6 grid-type'>";
                      foreach ($list_view_array as $k => $v)
                      {
                        echo " <span id='" . ltrim(rtrim(strtolower(str_replace('*', '', $v)))) . "' title='" . strtolower(str_replace('*', '', $v)) . "' class='projectList glyphicon glyphicon-align-right'></span>";
                      }
                      echo "</div>";
                    }
                    include($GLOBALS['APP_DIR'] . "socprox3.0/views/table.view.php");
                  }
                  ?>
                  <!-- @end #myProjects -->
                  <?php
                  if (in_array($_REQUEST['tab'], $projsTblChildArray))
                  {
                    include($GLOBALS['APP_DIR'] . "socprox3.0/views/table.view.php");
                  }
                  ?>
                  <!-- @start #otherProjects -->
                  <div class="tab-pane <?php echo ($_GET['tab'] == 'otherProjects') ? 'active' : '' ?>" id="otherProjects">
                    <div class="col-lg-12 right-content user-profile set-img">
                      <div id="wrapper-other-proj">
                        <div class="">
                          <div class="container">
                            <!--	<div class="search1">Search Project</div> -->
                            <br/>
                            <div id="">
                              <input type='text'  placeholder="Search Other Project" class="input-bg form-control" id='searchOthersProjects' onkeyup='searchProjects()'/>
                            </div>
                            <div class="row" id="pdowrapper">
                              <?php
                              $sql = "SELECT * FROM {$userTblArray['table_alias']} u, {$projectTblArray['tableAlias']} p
																WHERE  u.{$userTblArray['uid_fld']} = p.{$projectTblArray['uid_fld']}
																AND p.{$projectTblArray['uid_fld']} != " . $_SESSION['uid'] . " AND u.{$userTblArray['isActive_fld']} = 1
																AND p.{$projectTblArray['isLive_fld']}=1";
      // CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_alias (the key field in DD) ...
      // temporarily changing to ...  database_table_name
                              $sql = "SELECT * FROM {$userTblArray['database_table_name']} u, {$projectTblArray['tableAlias']} p
																WHERE  u.{$userTblArray['uid_fld']} = p.{$projectTblArray['uid_fld']}
																AND p.{$projectTblArray['uid_fld']} != " . $_SESSION['uid'] . " AND u.{$userTblArray['isActive_fld']} = 1
																AND p.{$projectTblArray['isLive_fld']}=1";


                              $result = mysql_query($sql);
                              if (mysql_num_rows($result) < 0)
                              {
                                echo "<div class='noProjects'>" . OTHERS_PROJECTS_NOT_AVAILABLE_MESSAGE . "</div>";
                                exit;
                              }

                              while ($row = mysql_fetch_array($result))
                              {
                                echo "<a href='" . BASE_URL_SYSTEM . "projectDetails.php?pid=" . $row[$projectTblArray['pid_fld']] . "'><div class='col-6 col-sm-6 col-lg-3' data-scroll-reveal='enter bottom over 1s and move 100px'><div class='project-detail'><span class='profile-image'>";
                                if ($row[$projectTblArray['projectImage_fld']] != "")
                                {
                                  echo "<img src='TimThumb.php?src=" . BASE_URL . "project_uploads/thumbs/" . $row[$projectTblArray['projectImage_fld']] . "&w=650&h=450'>";
                                }
                                else
                                {
                                  echo "<img src='" . BASE_URL . "project_uploads/defaultImageIcon.png'>";
                                }
                                echo "</span>
																	<div class='project-info projDetails'>
																		<h3>" . $row[$projectTblArray['pname_fld']] . "</h3>
																		<p> <span><strong></strong></span> <span class='name'>" . $row[$projectTblArray['create_date_fld']] . "</span></p>
																		<p> <span><strong></strong></span> <span class='name'>" . $row[$projectTblArray['expiry_date_fld']] . "</span></p>
																	</div>
																	</div>
																	</div>
																	</a>";
                              }
                              ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- @end #otherProjects -->
                  <!-- @start #createProject -->
                  <div class="tab-pane <?php echo ($_GET['tab'] == 'createProject') ? 'active' : '' ?>" id="createProject">
                    <div class="col-lg-12 right-content user-profile set-img">
                      <div id="wrapper-create-proj"> <br/>
                        <?php include($GLOBALS['APP_DIR'] . "tabs/projectForm.php"); ?>
                      </div>
                    </div>
                  </div>
                  <!-- @end #createProject -->

                  <div class="tab-pane <?php echo ($_GET['tab'] == 'listprojects') ? 'active' : '' ?>" id="listProjects">
                    <div class="col-lg-12 right-content user-profile set-img">
                      <div id="wrapper-create-proj"> <br/>
                        <?php
                        if ($_GET['tab'] == 'listprojects')
                        {
                          $GLOBALS['sqlWhereConditions'] = 'where uid = ' . $_SESSION['uid'] . ' ';
                          $GLOBALS['table_count'] = 'project_count';
                          $GLOBALS['table'] = 'project_config';
                          include($GLOBALS['APP_DIR'] . "grid_widget/grid.php");
                        }
                        ?>
                      </div>
                    </div>
                  </div>

                </div>
                <!-- @end .tab-content -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo BASE_JS_URL ?>bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL ?>bootstrap-validator/js/bootstrapValidator.js"></script>
<script src="<?php echo BASE_JS_URL ?>my_projects.js"></script>
<script src="<?php echo BASE_JS_URL ?>projects.js"></script>
<script src="<?php echo BASE_URL ?>ckeditor/ckeditor.js"></script>
<script type="text/javascript">
  CKEDITOR.replace('description', {
    toolbarGroups: [
      {name: 'document', groups: ['mode']}, // Line break - next group will be placed in new line.
      {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
      {name: 'styles', groups: ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor']},
      {name: 'insert', groups: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'uploadcare', 'youtube']}
    ]},
  {
    allowedContent: true
  });
</script>
<script>
  $("#myprojects_tab").click(function () {
    $("#myprojects_tab").toggleClass("open");
  });

</script>
<?php include("../application/footer.php"); ?>