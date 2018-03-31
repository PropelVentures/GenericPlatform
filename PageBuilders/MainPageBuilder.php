<?php

//include("EditDatabase.php");

class MainPageBuilder
{
    private $tabNum; //Current Tab Number
    private $table_alias; //Table alias associated with current display page (located in Data Dictionary and related to Field Dictionary)
    private $database_table_name; //database_table_name field inside the
    private $list_fields; // fields to be displayed in table retrieved from data dictionary
    private $list_filter; //
    private $list_sort; //way to sort fields retrieved from field dictionary (retieved from data dictionary)
    private $dataDictQuery; // Resulting array from querying data dictionary
    private $WHERE; // Where statement
    private $isEdit;
    private $BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";
    function LoadMainContent($displayPage, $menu_location, Factory $oFactory){
        if(empty($_GET["edit"])){
            $this->isEdit = false;
        }
        else{
            $this->isEdit = true;
        }
        if(!empty($_GET["tab_num"])){
            $this->tabNum = $_GET["tab_num"];
        }
        else{
            $this->tabNum = 1;
        }
        //$_SESSION["tab_num"] = $this->tabNum;
        // Query data dictionary for needed fields
        $this->dataDictQuery = $this->GetInfoFromDataDictionary($displayPage, $menu_location, $oFactory);
        // Create the main tabs for the body
        $this->CreateAndDisplayTabs($displayPage, $menu_location, $oFactory);
        if($this->isEdit){
            $this->LoadEditPage($displayPage, $menu_location, $oFactory);
        }
        else{
            // Get the list_view and create display field
            $list_view = explode(" ", $this->dataDictQuery[$this->tabNum - 1]["list_views"]);
            if ($list_view[0] == "listview"){
                $this->CreateTable($displayPage, $menu_location, $oFactory);
            }
            else if ($list_view[0] == "boxView"){
                $this->CreateBoxView($displayPage, $menu_location, $oFactory);
            }
            else if ($list_view[0] == "thumbView"){
                // To be made
                //  CreateThumbView($displayPage, $menu_location, $oFactory);
            }
            else if ($list_view[0] == "Cards"){
                // To be made
                // CreateCardVsiew($displayPage, $menu_location, $oFactory);
            }
            else if ($list_view[0] == "Xlist"){
                // To be made
                // CreateXListView($displayPage, $menu_location, $oFactory);
            }
            // Just display field_data
            else {
                $this->CreateDefaultView($oFactory);
            }
        }
    }
    // No styling or specific layout, just display data listed in field_dictionary
    function CreateDefaultView($oFactory){
        // Grab fields from field_dictionary
        // will need to change some queries
        $query = "";
        $query = "SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`";
        $data_fields = $oFactory->SQLHelper()->queryToDatabase($query);
        $data_fields_copy = $data_fields;
        $data_fields =$this->GetDataFields($oFactory);
        // Unused, not sure Creates tabs
        if(empty($this->list_fields) || $this->list_fields == ""){
            foreach($data_fields_copy as $key=>$value){
                if($key < count($data_fields_copy) - 1){
                    $this->list_fields .= $value["generic_field_name"] . ",";
                }
                else {
                    $this->list_fields .= $value["generic_field_name"];
                }
            }
        }
        // Iterate through and display data
        echo "<form>";
        foreach($data_fields as $result){
            // Iterate through each fields's column's name and value
            foreach($result as $key=>$value){
                // Foreach field row (field_dict copy)
                foreach($data_fields_copy as $field_return) {
                    // If the column name matches the generic_field_name format the data
                    if ($field_return["generic_field_name"] == $key) {
                        //$this->DisplayData($field_return["format_type"], $value);
                        $this->DisplayData2($field_return["format_type"], $value, $field_return["field_label_name"]);
                    }
                }
            }
        }
        //why do we do this?
        echo "</form>";
        //$this->DisplayData($data_fields, $oFactory);
    }
    // Creates html for a data field and retrieves the value to go inside
    // Creates html for a data field
    function DisplayData($formatType, $value){
        if ($formatType == "" || empty($formatType)){
            echo "<p> $value </p>";
        }
        else if ($formatType == "username"){
            echo "<input type='text' name='username'>";
        }
        else if ($formatType == "transaction_text"){
        }
        else if ($formatType == "transaction_execute"){
        }
        else if ($formatType == "transaction_confirmation"){
        }
        else if ($formatType == "transaction_cancel"){
        }
        else if ($formatType == "textbox"){
            echo "<input type='text' value='$value' readonly='$this->isEdit'>";
        }
        else if ($formatType == "tag"){
            echo "<input type='text' value='$value' data-role='tagsinput'></input>";
        }
        else if ($formatType == "pdf_inline"){
        }
        else if ($formatType == "password"){
            echo "<input type='password' name='username'>";
        }
        else if ($formatType == "list_fragment"){
        }
        else if ($formatType == "image"){
            echo "<img src='" . $value . "' >";
        }
        else if ($formatType == "email"){
        }
        else if ($formatType == "dropdown"){
        }
        else if ($formatType == "crf"){
        }
    }
    function LoadEditPage($displayPage, $menu_location, Factory $oFactory){
        // Get the current tab number selected
        if(!empty($_GET["tab_num"])){
            $this->tabNum = $_GET["tab_num"];
        }
        else{
            $this->tabNum = 1;
        }
        $data_id = $_GET["search_id"];
        // Create the tabs
        //$this->CreateAndDisplayTabs($displayPage, $menu_location, $oFactory);
        // Generate globals for display page
        //$this->dataDictQuery = $this->GetInfoFromDataDictionary($displayPage, $menu_location, $oFactory);
        // Grab the selected cell
        $primary_key = "SHOW KEYS FROM $this->database_table_name WHERE Key_name = 'PRIMARY'";
        $primary_key = $oFactory->SQLHelper()->queryToDatabase($primary_key);
        $primary_key = $primary_key[0]["Column_name"];
        $data_result_query = "SELECT * from $this->database_table_name WHERE $primary_key = '$data_id'";
        $data_result = $oFactory->SQLHelper()->queryToDatabase($data_result_query);
        //echo "<form action='Helpers/EditDatabase.php' method='post'>";
        $location = $_SERVER['PHP_SELF'] . "?display=" . $_GET['display'];
        //might not be necessary since doing this in javascript now
        echo "<form action='$location' method='post'>";
        $data_result = $data_result[0];

        foreach($data_result as $key=>$value) {
            // If the column name matches the generic_field_name format the data
            //$this->DisplayData($field_return["format_type"], $value);
            $this->CreateEditView($key, $value);
            $_SESSION['$key'] = $value;
        }
        $_SESSION['oFactory'] = $oFactory;
        // Should return only one row
        echo "<br><input type='button' value='Submit Changes' onclick='ajaxTesting(\"$this->database_table_name\", \"$primary_key\", \"$data_id\")'>";
        echo "</form>";
    }
    function CreateEditView($key, $value){
        echo "<div style='display: inline-table; margin-right: 20px; margin-top:19px;' id='$key' class='form-group row'>";
        echo "<label style='display:inline-block;'> $key </label>";
        echo "<input type='text' name='$key' value='$value' class='form-control'>";
//        $oldValue = $value;
//        $oldKey = "old_" . $key;
//        echo "<input type='hidden' name='$oldKey' value='$oldValue'>
        echo "</div>";
    }
    function DisplayData2($formatType, $value, $field_display_name){
        if ($formatType == "" || empty($formatType)){
            echo "<div style='display: inline-table; margin-right: 20px; margin-top:19px;' id='$field_display_name' class='form-group row'>";
            echo "<label style='display:inline-block;'> $field_display_name </label>";
            echo "<input type='text' value='$value' class='form-control'></div>";
        }
        else if ($formatType == "username"){
            echo "<input type='text' name='username'>";
        }
        else if ($formatType == "transaction_text"){
        }
        else if ($formatType == "transaction_execute"){
        }
        else if ($formatType == "transaction_confirmation"){
        }
        else if ($formatType == "transaction_cancel"){
        }
        else if ($formatType == "textbox"){
            echo "<input type='text' value='$value' readonly='$this->isEdit'>";
        }
        else if ($formatType == "tag"){
            echo "<input type='text' value='$value' data-role='tagsinput'></input>";
        }
        else if ($formatType == "pdf_inline"){
        }
        else if ($formatType == "password"){
            echo "<input type='password' name='username'>";
        }
        else if ($formatType == "list_fragment"){
        }
        else if ($formatType == "image"){
            echo "<img src='$value' class='img-thumbnail img-responsive user_thumb' >";
        }
        else if ($formatType == "email"){
        }
        else if ($formatType == "dropdown"){
        }
        else if ($formatType == "crf"){
        }
    }
    // Creates the HTML tab tags to be displayed
    function CreateAndDisplayTabs($displayPage, $menu_location, Factory $oFactory){
        // Create tabs
        echo "<ul class='nav nav-tabs'>";
        $BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";
        // Iterate through each entry
        foreach($this->dataDictQuery as $key=>$value){
            // If current tab is current active tab
            if(!empty($this->tabNum) && $key + 1 == $this->tabNum){
                echo "<li class='active'><a href='$BASE_URL?display=$displayPage&tab_num=" . ($key + 1) . "'>" . $value["tab_name"] . "</a> </li>";
            } else{ // Else, Inactive tab
                echo "<li><a href='$BASE_URL?display=$displayPage&tab_num=" . ($key + 1) . "'>" . $value["tab_name"] . "</a> </li>";
            }
        }
        echo "</ul>";
    }
    // Grab the Relevant information from data dictionary to prepare query for field_dictionary
    function GetInfoFromDataDictionary($displayPage, $menu_location, Factory $oFactory){
        // Grab Tabs
        $query = "SELECT * FROM `data_dictionary` WHERE `display_page` = '$displayPage'  ORDER BY `tab_num`";
        $resArr = $oFactory->SQLHelper()->queryToDatabase($query);
        // Set current tab to be tab 0
        $this->table_alias = $resArr[$this->tabNum - 1]["table_alias"];
        // Set reference to database_table name to current tab's reference
        $this->database_table_name = $resArr[$this->tabNum - 1]["database_table_name"];
        // Grab the list fields
        $this->list_fields = $resArr[$this->tabNum - 1]["list_fields"]; //explode(",", $resArr[$this->tabNum - 1]["list_fields"]);
        $this->list_fields = str_replace('*', '', $this->list_fields);
        // Grab list sort method
        $this->list_sort = $resArr[$this->tabNum - 1]["list_sort"];
        // Get WHERE statement
        if ($resArr[$this->tabNum-1]["list_filter"] != "" && !empty($resArr[$this->tabNum-1]["list_filter"])){
            $where = $resArr[$this->tabNum-1]['list_filter'];
            $this->WHERE = "WHERE $where";
        }
        else {
            $this->WHERE = "";
        }
        return $resArr;
    }
    function CreateTable($displayPage, $menu_location,$oFactory){
        // Create the table headers
        $this->CreateTableHeadersFromFieldDictionary($displayPage, $menu_location, $oFactory);
        // Grab field data and populate the table
        $this->PopulateTable($displayPage, $menu_location, $oFactory);
    }
    // Iterate through the field_dictionary to grab the table headers
    Function CreateTableHeadersFromFieldDictionary($displayPage, $menu_location, Factory $oFactory){
        $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`");
        if(empty($this->list_fields) || $this->list_fields == ""){
            foreach($resArr as $key=>$value){
                if($key < count($resArr) - 1){
                    $this->list_fields .= $value["generic_field_name"] . ",";
                }
                else {
                    $this->list_fields .= $value["generic_field_name"];
                }
            }
        }
        echo "<table class='table table-bordered table-striped' id='example'><thead><tr>";
        foreach($resArr as $result){
            echo "<th>" . $result["field_label_name"] . "</th>";
        }
        echo "</thead></tr>";
    }
    // Grab all the data for the field_data and populate the table
    Function PopulateTable($displayPage, $menu_location, Factory $oFactory){
        // Not sure?
        if(empty($this->list_sort)){
            $this->list_sort = "\"\"";
        }
        // Grab the fields from field dictionary
        $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`");
        // Create a copy of the fields
        $resArrCopy = $resArr;
        $keyfield = "";
        foreach($resArr as $myresult){
            if($myresult["field_identifier"] == "KEYFIELD"){
                $keyfield = $myresult["generic_field_name"];
            }
        }
        if(empty($keyfield)){
            $keyfield = $resArr[0]["generic_field_name"];
        }
        // Check for specific fields and if no fields specified, grab them all
        $resArr = $this->GetDataFields($oFactory);
        ?>
        <script>
            $(document).ready(function(){
                $(".odd").click(function() {
                    var base_url = "http://home.localhost/GenericNew/GenericPlatform/main.php?";
                    var displayPage = $(this).attr("displaypage");
                    var tabNum = $(this).attr("tabnum");
                    var search_id = $(this).attr("search_id");
                    var totalURI = base_url + "display=" + displayPage + "&tab_num=" + tabNum + "&edit=true&search_id=" + search_id;
                    window.location = totalURI;
                });
                $(".even").click(function() {
                    var base_url = "http://home.localhost/GenericNew/GenericPlatform/main.php?";
                    var displayPage = $(this).attr("displaypage");
                    var tabNum = $(this).attr("tabnum");
                    var search_id = $(this).attr("search_id");
                    var totalURI = base_url + "display=" + displayPage + "&tab_num=" + tabNum + "&edit=true&search_id=" + search_id;
                    window.location = totalURI;
                });
            });
        </script>
        <?php
        // Iterate through each field (database_table row)
        echo "<tbody>";
        foreach($resArr as $result){
            echo "<tr id='clickable-row' search_id='$result[$keyfield]' displaypage='$displayPage' tabnum='$this->tabNum'>";
            // Iterate through each fields's column's name and value
            foreach($result as $key=>$value){
                echo "<td>";
                // Foreach field row (field_dict copy)
                foreach($resArrCopy as $field_return) {
                    // If the column name matches the generic_field_name format the data
                    if ($field_return["generic_field_name"] == $key) {
                        $this->DisplayData($field_return["format_type"], $value);
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    function GetDataFields($oFactory){
        // Check for specific fields and if no fields specified, grab them all
        if($this->list_fields == null || $this->list_fields == ""){ //shouldnt ever happen anymore
            $query = "SELECT * FROM `$this->database_table_name` ORDER BY '$this->list_sort'";
            $field_list = $oFactory->SQLHelper()->queryToDatabase($query);
        }
        elseif(is_numeric($this->list_fields)){
            //if is number grab the first four fields from field dictionary and populate only those?
            //They don't even do this. Note table_alias transactions... For now just getting everything
            $field_list = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `$this->database_table_name` ORDER BY `'$this->list_sort'`");
        }
        else{
            $query = "SELECT $this->list_fields FROM `$this->database_table_name` ORDER BY $this->list_sort";
            $field_list = $oFactory->SQLHelper()->queryToDatabase($query);
        }
        return $field_list;
    }

    //This gets an object from the field dictionary and displays it with a box around it.
    function CreateBoxView($displayPage, $menu_location, Factory $oFactory){
        $this->CreateBoxViewFromFieldDictionary($displayPage, $menu_location, $oFactory);
    }

    function CreateBoxViewFromFieldDictionary($displayPage, $menu_location, Factory $oFactory){
        //Get the object from the field dictionary.
        $query = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`");
        $dataFields =$this->GetDataFields($oFactory);

        if(empty($this->list_fields) || $this->list_fields == ""){
            foreach($query as $key=>$value){
                if($key < count($query) - 1){
                    $this->list_fields .= $value["generic_field_name"] . ",";
                }
                else {
                    $this->list_fields .= $value["generic_field_name"];
                }
            }
        }
        //Display the object from the field dictionary with a box around it.
        foreach($dataFields as $field) {
            foreach($field as $key=>$value) {
                foreach ($query as $result) {
                    if($result["generic_field_name"] == $key) {
                        //The formatting should probably be edited.
                        //It currently displays as a vertical list with one element per row.
                        echo '<div style="border: 2px solid grey;">';
                        echo $result["field_label_name"];
                        echo "<br>";
                        echo "$value";
                        echo "</div>";
                    }
                }
            }
        }
    }
}

