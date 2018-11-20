<?php

/*
 * USE FOR LOGIN PAGE
 * 
 * 
 * function Select_Data_FieldDictionary_Record($alias) {
 * *************************************
 * 
 * function formating_Select($row) {
 * ************************************
 */

function Select_Data_FieldDictionary_Record($alias) {

    $con = connect();

    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$alias' and table_type='users' order by field_dictionary.display_field_order");

    $row = $rs->fetch_assoc();

    $_SESSION['select_table']['database_table_name'] = $row['database_table_name'];

    $_SESSION['select_table']['keyfield'] = firstFieldName($row['database_table_name']);


    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_type='users' and data_dictionary.table_alias = '$alias'  order by field_dictionary.display_field_order");





    while ($row = $rs->fetch_assoc()) {


        formating_Select($row);
    }//// end of while loop
}

function formating_Select($row) {


    $field = $row[generic_field_name];


    $readonly = '';
    $required = '';



    if ($row['editable'] == 'false')
        $readonly = 'readonly';


    if (!empty($row['required']))
        $required = 'required';

    if ($row['format_type'] == 'email') {


        echo "<input type='text' name='$field' value='' $readonly $required title='$row[help_message]' class='form-control' placeholder='Enter your Username or Email'>";
    } else if ($row['format_type'] == 'password') {

        echo "<input type='password' name='$field' value='' $readonly $required title='$row[help_message]' class='form-control' placeholder='Enter your $row[field_label_name]'>";
    } else {

        // echo "<input type='hidden' name='$field' >";
        $_SESSION['select_table']['username'] = $field;
    }
}
