<?php

/*
 *  @sidebar function
 */

function sidebar($sidebar, $both_sidebar, $display_page, $sidebar_width) {
    $con = connect();
    if ($sidebar == 'right') {
        if (!empty($sidebar_width)) {///////checking if manual width is set for sidebar
            echo "  <div style='width:$sidebar_width%;float:left;'  class='right-sidebar'>";
        } else {
            if ($both_sidebar == 'both') {
                echo "  <div class='col-lg-2 right-sidebar'>";
            } else {
                echo " <div class='col-3 col-sm-3 col-lg-3 right-sidebar'>";
            }
        }
		/* Side Bar Navigation Start*/
		GetSideBarNavigation($display_page,'body-right');
		/* Side Bar Navigation End*/

		/* Tab Navigation Start*/
		Get_Tab_Links($display_page,'right');
		/* Tab Navigation End*/
        $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND table_type NOT REGEXP 'header|banner|slider|content|url|text|subheader' and tab_num LIKE 'R%' order by tab_num");
        while ($row = $rs->fetch_assoc()) {

    			/* Check From table_type == header1 or header2 Start */
    			//ShowTableTypeHeaderContent($display_page,$row['tab_num']);
    			/* Check For table_type == header1 or header2 End */
    			/* Check From table_type == subheader1 or subheader2 Start */
    			//ShowTableTypeSubHeaderContent($display_page,$row['tab_num']);
    			/* Check For table_type == subheader1 or subheader2 End */
          if(isAllowedToShowByPrivilegeLevel($row)){
            Get_Data_FieldDictionary_Record($row['table_alias'], $display_page, $tab_status = 'bars', $row['tab_num']);
          }
        }
        echo "</div>";
    } else if ($sidebar == 'left') {
        if (!empty($sidebar_width)) {///////checking if manual width is set for sidebar
            echo "  <div style='width:$sidebar_width%;float:left;' class='left-sidebar'>";
        } else {
            if ($both_sidebar) {
                echo "  <div class='col-lg-2 left-sidebar'>";
            } else {
                echo " <div class='col-3 col-sm-3 col-lg-3 left-sidebar'>";
            }
        }
		/* Side Bar Navigation Start*/
		GetSideBarNavigation($display_page,'body-left');
		/* Side Bar Navigation End*/
		/* Tab Navigation Start*/
		Get_Tab_Links($display_page,'left');
		/* Tab Navigation End*/
        $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND table_type NOT REGEXP 'header|banner|slider|content|url|text|subheader' and tab_num LIKE 'L%' order by tab_num");
        while ($row = $rs->fetch_assoc()) {
    			/* Check From table_type == header1 or header2 Start */
    			//ShowTableTypeHeaderContent($display_page,$row['tab_num']);
    			/* Check For table_type == header1 or header2 End */
    			/* Check From table_type == subheader1 or subheader2 Start */
    			//ShowTableTypeSubHeaderContent($display_page,$row['tab_num']);
    			/* Check For table_type == subheader1 or subheader2 End */
          if(isAllowedToShowByPrivilegeLevel($row)){
            Get_Data_FieldDictionary_Record($row['table_alias'], $display_page, $tab_status = 'bars', $row['tab_num']);
          }
        }
        echo "</div>";
    }
}

///end of sidebar function
