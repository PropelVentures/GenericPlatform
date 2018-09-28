<?php


function serial_layout($display_page, $style) {

    $con = connect();

    $rs = $con->query("SELECT * FROM  data_dictionary where display_page = '$display_page' and tab_num != 0 order by tab_num");

    $editable = 'false';
    while ($row = $rs->fetch_assoc()) {
        echo "<form class='$style'>
  <fieldset>
    <legend>$row[tab_name]:</legend>";
        Get_Data_FieldDictionary_Record($row['table_alias'], $display_page, $editable);
        echo "</fieldset>
</form>";
    }
}
