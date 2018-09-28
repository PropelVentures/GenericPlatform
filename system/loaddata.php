<?php

if ($_SERVER['HTTP_HOST'] === 'localhost')

{ define('APP_DIR', $_SERVER['DOCUMENT_ROOT'].'generic-platforms/'); // Base Root or Directory Path For Application

  $GLOBALS['APP_DIR'] = $_SERVER['DOCUMENT_ROOT'] . 'generic-platforms/'; // Base Root or Directory Path For Application

}
else
{
  define('APP_DIR', $_SERVER['SUBDOMAIN_DOCUMENT_ROOT']); // Base Root or Directory Path For Application
  $GLOBALS['APP_DIR'] = $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] . '/'; // Base Root or Directory Path For Application
}

d("inside Loaddata - 1");
require $GLOBALS['APP_DIR'] . 'application/config.php';
d("inside Loaddata - 2");
require $GLOBALS['APP_DIR'] . 'socprox3.0/config/dbConfig.php';
d("inside Loaddata - 3");
require $GLOBALS['APP_DIR'] . 'socprox3.0/lib/functions/appFunctions.php';
d("inside Loaddata - 4");
require $GLOBALS['APP_DIR'] . 'system/special_config.php';
d("inside Loaddata - 5");


// Getting and setting Paramas
$pagenum = $_REQUEST ['pagenum'];
$pagesize = $_REQUEST ['pagesize'];
$tableAlias = $_REQUEST ['tblrequest'];
$pagenum = $_REQUEST ['pagenum'];
$pagesize = $_REQUEST ['pagesize'];
$list_view = $_REQUEST['list_view'];
$time_start = microtime(true);
$finalQuery = "";
$where = "";
$tableSORT = 'DESC';
$sortID = 'default';
$dbname = $config ['db_name'];
$rows = array();
$columns = array();
$count = 0;
$tblHead = '';
$sortImg = '';
$hasDatePicker = "";
$dateField = array();
$all_where = "where ";

// Setting default value for Query where clause
if (isset($_REQUEST ['whereval']))
{
  $where = $_REQUEST ['whereval'];
}

// Setting default value for table sort Order
if (isset($_REQUEST ['sortorder']) && $_REQUEST ['sortid'])
{
  $sortID = $_REQUEST ['sortid'];
  $tableSORT = $_REQUEST ['sortorder'];
}

// Setting start param for Pagination
$start = $pagenum * $pagesize;

// Getting dynamic column names for tables
$rs = $mysqli->query("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE,COLUMN_KEY
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE table_name = '$tableAlias'
  AND table_schema = '$dbname'
  ");
  // CJ-NOTE 1-20-15  .. uh oh ...search and replace may have the abive incorrect ... tablealias may be incorrect



// Genaration of table header Start
$html = '<div id="tablecontent"><table id="testgrid" class="testgrid"><thead><tr><th>Edit</th>';
while ($row = $rs->fetch_assoc())
{
  $sortImg = '';
  $colName = $row ['COLUMN_NAME'];
  $sortImg = sortImg($colName, $sortID, $count, $tableSORT); // getting sort image as arraow up or down
  $newSortOrder = "";
  $newSortOrder = getNewSortOrder($tableSORT);
  $tblHead = $tblHead . '<th><a style="cursor: pointer;" onclick="toggleSortMe(\'' . $pagenum . '\',\'' . $tableAlias . '\',\'' . $colName . '\',\'' . $newSortOrder . '\',\'' . $where . '\');">' . $colName . $sortImg . '</a></th>';
  $rows [$count] [0] = $colName;

  if ($row ['DATA_TYPE'] == 'datetime')
  {
    $dateField [$colName] = 'DATE';
  }
  $columns [$count] = $colName;
  $all_where = $all_where . getNewAllWhere($where, $count, $colName);
  $count ++;
}
$html = $html . $tblHead . '<th>Delete</th></tr></thead>';
// Genaration of table header End

$tblidx = $rows [0] ['0']; // Setting the Index for the selected table

if (isset($where))
{
  $totrs = $mysqli->query("SELECT COUNT(*) as count FROM $tableAlias $all_where");
}
else
{
  $totrs = $mysqli->query("SELECT COUNT(*) as count FROM $tableAlias");
}

$totRow = $totrs->fetch_array();
$totalRecord = $totRow [0];

// Setting up Final query for grid data depending on where
if (isset($where))
{
  $finalQuery = "SELECT * FROM $tableAlias $all_where";
}
else
{
  $finalQuery = "SELECT * FROM $tableAlias";
}

// Setting up Final query for grid data depending on Sorting
if ($sortID != 'default')
{
  $finalQuery = $finalQuery . " order by $sortID $tableSORT";
}
else
{
  if (isset($GLOBALS['SORTMAP'][$tableAlias]))
  {
    $SortColumn = $GLOBALS['SORTMAP'][$tableAlias];
    $finalQuery = $finalQuery . " order by $SortColumn DESC";
  }
  elseif ($_REQUEST['list_sort'] != null || $_REQUEST['list_sort'] != "")
  {
    $SortColumn = $_REQUEST['list_sort'];
    $finalQuery = $finalQuery . " order by $SortColumn DESC";
  }
  else
  {
    $SortColumn = $_REQUEST['list_sort'];
    $finalQuery = $finalQuery . " order by $tblidx ASC";
  }
}

// Setting up pagination LIMIT
$limit = " LIMIT $start, $pagesize";
$finalQuery = $finalQuery . $limit;
//exit;
// Execution of final data Query
$result = $mysqli->query($finalQuery);
// Copy result for Other Views - Start

$boxViewResult = $mysqli->query($finalQuery);
$thumbViewResult = $mysqli->query($finalQuery);

// Copy result for Other Views - End

$html = $html . '<tbody>';
$tblBody = '';
$tbltd = '';
$row = '';
$rowCount = $mysqli->field_count;
$count = 0;

// Generation of grid data Start
while ($row = $result->fetch_array())
{
  $hasDatePicker = "";
  $tblBody = $tblBody . "<tr id='tr-$count'>";
  $tbltd = "";
  $i = 0;
  $editHtml = "<td>
	<div style='float: left; width: 20px;'>
	<img title='Edit row' alt='Edit' onclick=\"editRow($row[0],'$tableAlias','$pagenum','$sortID','$tableSORT','$where');\" src='" . $GLOBALS['app_plugin_prefix'] . "resources/images/edit.png' class='icon' style='float: left;'>
	</div>
	</td>";
  while ($rowCount > $i)
  {
    $hasDatePicker = "";
    $relatonalData = "";

    // Check if relational data needs to be generated Start
    if($GLOBALS ['TABLEMAP'] [$tableAlias]):
    if (array_key_exists($columns [$i], $GLOBALS ['TABLEMAP'] [$tableAlias]))
    {
      $targetTable = $GLOBALS ['TABLEMAP_TO_FIELD'] [$columns [$i]];
      if ($columns [$i] == "UserIDs")
      {
        $targetTable = $GLOBALS ['TABLEMAP_TO_FIELD'] [$columns [$i]];
        $Q1 = "";
        $userIDS = explode(";", $row [$i]);
        $w = 0;
        if (count($userIDS) > 0)
        {
          $showResult = "  ";
          while (count($userIDS) > $w)
          {
            $Q2 = "select User.UserID,User.Username from User where User.UserID = $userIDS[$w]";
            $rs1 = $mysqli->query($Q2);
            $row23 = $rs1->fetch_array();
            $showResult = $showResult . "  <br><a href='#' style='float:right' onclick='showRelationalData(\"$columns[$i]\",\"$row23[0]\",\"$tableAlias\",\"$row[0]\",\"$targetTable\",\"$sortID\",\"$tableSORT\",\"$where\",\"$pagenum\");'>" . $row23 [0] . "  :  " . $row23 [1] . "</a>";
            $w ++;
          }
          $relatonalData = $showResult;
        }
        else
        {
          if (getRelationalData($columns [$i], $row [$i], $tableAlias) != '')
            $relatonalData = getRelationalData($columns [$i], $row [$i], $tableAlias);
        }
      } elseif ($columns [$i] == "ChallengeInstID")
      {
        if (getRelationalData($columns [$i], $row [$i], $tableAlias) != '')
        {
          $relatonalData = getRelationalData($columns [$i], $row [$i], $tableAlias);
        }
      }
      else
      {
        if (getRelationalData($columns [$i], $row [$i], $tableAlias) != '')
        {
          $relatonalData = getRelationalData($columns [$i], $row [$i], $tableAlias);
        }
      }
    }
    endif;
    // Check if relational data needs to be generated End
    $showResult = "";

    // IF the column has a date field put an identifier for calender
    if (isset($dateField [$columns [$i]]) && $dateField [$columns [$i]] == 'DATE')
    {
      $hasDatePicker = "hasDatepicker";
    }

    // Special Case for ID mismatch Start
    if ($relatonalData != '' && $columns [$i] == "UserIDs")
    { // Generation for combined USER IDS
      $showResult = $relatonalData;

      if ($i == 0)
      {
        $tbltd = $tbltd . "<td class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span>$showResult<input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
      else
      {
        $tbltd = $tbltd . "<td ondblclick='updateCell(\"$count\",\"$i\");' class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span>$showResult<input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
    }
    elseif ($relatonalData != '' && $columns [$i] == "ChallengeInstID")
    { // Generation for Challenge ID
      $tmpColumn = 'ChallengeInstanceID';
      $showResult = "";
      $rs12 = $mysqli->query($relatonalData);
      $row223 = $rs12->fetch_array();
      $rowCount2 = $mysqli->field_count;
      $n = 0;
      while ($rowCount2 > $n)
      {
        if ($n == 0)
        {
          $showResult = $showResult . $row223 [0];
        }
        else
        {
          $showResult = $showResult . ":" . $row223 [0];
        }

        $n ++;
      }
      if ($i == 0)
      {
        $tbltd = $tbltd . "<td class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><a href='#' onclick='showRelationalData(\"$tmpColumn\",\"$row[$i]\",\"$tableAlias\",\"$row[0]\",\"$targetTable\",\"$sortID\",\"$tableSORT\",\"$where\",\"$pagenum\");'>$showResult</a><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
      else
      {
        $tbltd = $tbltd . "<td ondblclick='updateCell(\"$count\",\"$i\");' class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><a style='float:right' href='#' onclick='showRelationalData(\"$tmpColumn\",\"$row[$i]\",\"$tableAlias\",\"$row[0]\",\"$targetTable\",\"$sortID\",\"$tableSORT\",\"$where\",\"$pagenum\");'>$showResult</a><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
    }
    elseif ($relatonalData != '' && $columns [$i] != "UserIDs")
    { //// Generation for general IDS relational
      $rs12 = $mysqli->query($relatonalData);
      socx_debug($relatonalData);
      $row223 = $rs12->fetch_array();
      $rowCount2 = $mysqli->field_count;
      $n = 0;
      while ($rowCount2 > $n)
      {
        if ($n == 0)
        {
          $showResult = $showResult . $row223 [0];
        }
        else
        {
          $showResult = $showResult . ":" . $row223 [0];
        }

        $n ++;
      }
      if ($i == 0)
      {
        $tbltd = $tbltd . "<td class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><a href='#' onclick='showRelationalData(\"$columns[$i]\",\"$row[$i]\",\"$tableAlias\",\"$row[0]\",\"$targetTable\",\"$sortID\",\"$tableSORT\",\"$where\",\"$pagenum\");'>$showResult</a><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
      else
      {
        $tbltd = $tbltd . "<td ondblclick='updateCell(\"$count\",\"$i\");' class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><a style='float:right' href='#' onclick='showRelationalData(\"$columns[$i]\",\"$row[$i]\",\"$tableAlias\",\"$row[0]\",\"$targetTable\",\"$sortID\",\"$tableSORT\",\"$where\",\"$pagenum\");'>$showResult</a><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
    }
    else
    { // Generation for general DATA non-relational
      if ($i == 0)
      {
        $tbltd = $tbltd . "<td class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
      else
      {
        $tbltd = $tbltd . "<td ondblclick='updateCell(\"$count\",\"$i\");' class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i] </span><input class='$hasDatePicker' inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
      }
    }
    // Special Case for ID mismatch End
    $i ++;
  }
  $deleteHtml = "<td><div style='float: left; width: 20px;'>
									<img title='Delete row' alt='delete'
										onclick=\"deleteRow($row[0],'$tableAlias','$pagenum');\"
										src='" . $GLOBALS['app_plugin_prefix'] . "resources/images/delete.png' class='icon'
										style='float: left;'>
								</div></td>";
  $tblBody = $tblBody . $editHtml . $tbltd . $deleteHtml . '</tr>';
  $count ++;
}
$html = $html . $tblBody . '</tbody></table></div>';
// Generation of grid data End
$html = $html . updatePaginator($totalRecord, $pagesize, $pagenum, $tableAlias, $sortID, $tableSORT, $where);

if ($totalRecord < 1)
{
  $html = "<div id=\"tablecontent\">  No record found !! </div>";
}
//echo "<br><br><br><br> BOX VIEW START <br><br><br><br>";
$BOX_HTML = "";
// BOX view Setup Start
$BOX_HTML = $BOX_HTML . "<div style='display:none;' id='card-view-container'>";
$BOX_HTML = $BOX_HTML . "<h2>Popular $tableAlias </h2>";
while ($row = $boxViewResult->fetch_array())
{
  // echo "Inside loop";
  $img = ($row[$list_view[$tableAlias]['img']] == "") ? 'defaultImageIcon.png' : $row[$list_view[$tableAlias]['img']];
  $BOX_HTML = $BOX_HTML . "<a href='projectDetails.php?pid=" . $row[$list_view[$tableAlias]['pid']] . "' class='project-details-wrapper'>";
  $BOX_HTML = $BOX_HTML . "    <div data-scroll-reveal='enter bottom over 1s and move 100px' class='col-6 col-sm-6 col-lg-3'>
             <div class='project-detail'>
                 <span class='profile-image'>
                 <img class='img-responsive' alt='' src='" .  BASE_URL . "project_uploads/" . $img . "'></span>
                 <span data-active='0' data-id='2' class='relationship like'><i class='fa fa-heart-o fa-2x'></i></span>
                 <div class='star-rating rating-sm rating-active'>
                     <div title='Clear' class='clear-rating clear-rating-active'>
                        <i class='glyphicon glyphicon-minus-sign'></i>
                     </div>
                     <div class='rating-container rating-gly-star' data-content='?????'>
                         <div class='rating-stars' data-content='?????' style='width: 0%;'></div>
                            <input type='number' data-size='sm' step='0.2' max='5' min='0' data-from='project' data-id='' class='rating form-control' value='' id='input-21b' style='display: none;'>
                     </div>
                     <div class='caption'><span class='label label-default'>Not Rated</span></div>
                 </div>
                 <div class='project-info'>
                    <h3>" . $row[$list_view[$tableAlias]['pname']] . "</h3>
                        <p> <span>
                                    <strong>Created -</strong>
                            </span>
                            <span class='date'>" . $row[$list_view[$tableAlias]['created']] . "</span>
                        </p>
                        <div style='clear:both'>
                        </div>
                 </div>
             </div>
         </div>
     </a>";
}
$BOX_HTML = $BOX_HTML . "</div>";
echo $BOX_HTML;
// BOX view setup End
// Thumb view setup Start
$BOX_HTML = "";
// BOX view Setup Start
$BOX_HTML = $BOX_HTML . "<div style='display:none;' id='xlist-view-container'>";
$BOX_HTML = $BOX_HTML . "<h2>Popular $tableAlias </h2>";
while ($row = $thumbViewResult->fetch_array())
{
  // echo "Inside loop";
  $img = ($row[$list_view[$tableAlias]['img']] == "") ? 'defaultImageIcon.png' : $row[$list_view[$tableAlias]['img']];
  $BOX_HTML = $BOX_HTML . "<a href='projectDetails.php?pid=" . $row[$list_view[$tableAlias]['pid']] . "' class='project-details-wrapper thumbView'>";
  $BOX_HTML = $BOX_HTML . "    <div data-scroll-reveal='enter bottom over 1s and move 100px' class='col-12 col-sm-12 col-lg-12'>
             <div class='project-detail'>
                 <span class='profile-image'>
                 <img class='img-responsive' alt='' src='" . BASE_URL . "project_uploads/" . $img . "'></span>
                 <span data-active='0' data-id='2' class='relationship like'>
                    <i class='fa fa-heart-o fa-2x user_liked'></i>
                 </span>
                 <div class='star-rating rating-sm rating-active'>
                     <div title='Clear' class='clear-rating clear-rating-active'>
                        <i class='glyphicon glyphicon-minus-sign'></i>
                     </div>
                     <div class='rating-container rating-gly-star' data-content='?????'>
                         <div class='rating-stars' data-content='?????' style='width: 78%;'></div>
                            <input type='number' data-size='sm' step='0.2' max='5' min='0' data-from='project' data-id='' class='rating form-control' value='' id='input-21b' style='display: none;'>
                     </div>
                     <div class='caption'><span class='label label-default'>Not Rated</span></div>
                 </div>
                 <div class='project-info'>
                    <h3>" . $row[$list_view[$tableAlias]['pname']] . "</h3>
                        <p> <span>
                                    <strong>Created -</strong>
                            </span>
                            <span class='date'>" . $row[$list_view[$tableAlias]['created']] . "</span>
                        </p>
                        <div style='clear:both'>
                        </div>
                 </div>
             </div>
         </div>
     </a>";
}
$BOX_HTML = $BOX_HTML . "</div>";
echo $BOX_HTML;
//  Thumb view setup END



echo $html;
?>