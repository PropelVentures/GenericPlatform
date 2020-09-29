<?php

/**
 * Render sidebar
 * 
 * @param string $sidebar Sidebar position, left or right
 * @param string $both_sidebar Denotes if the page will have both left and right sidebars
 * @param string $page_name Page name where the sidebar will be rendered
 * @param float $sidebar_width Width of the sidebar in percentage, if custom width needed
 * 
 * @author ph
 * 
 * @return void
 */
function sidebar($sidebar, $both_sidebar, $page_name, $sidebar_width) {
    $con = connect();

    if (!empty($sidebar_width)) {
        echo " <div style='width:$sidebar_width%;float:left;' class='{$sidebar}-sidebar'>";
    } else {
        $classes = $both_sidebar == 'both' ? 'col-lg-2' : 'col-3 col-sm-3 col-lg-3';
        echo " <div class='{$classes} {$sidebar}-sidebar'>";
    }

    GetSideBarNavigation($page_name, "body-{$sidebar}");

    Get_Serial_Tab_Links($page_name, $sidebar);

    $column = strtoupper($sidebar[0]);
    $sql = "SELECT * FROM data_dictionary where page_name='$page_name' AND component_type NOT REGEXP 'header|banner|slider|content|url|text|subheader' and component_column LIKE '{$column}%' order by component_column";
    $rs = $con->query($sql);

    while ($row = $rs->fetch_assoc()) {
        if (isAllowedToShowByPrivilegeLevel($row)) {
            component_display_loop('', $row['table_alias'], $page_name, 'sidebars', $row['component_column']);
        }
    }
    echo "</div>";
}
