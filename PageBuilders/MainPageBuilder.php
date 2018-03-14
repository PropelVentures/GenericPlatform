<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehana
 * Date: 2/19/2018
 * Time: 11:42 AM
 */

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

    function LoadMainContent($displayPage, $menu_location, Factory $oFactory){
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
        
        
        // Get the list_view and create display field
        $list_view = explode(" ", $this->dataDictQuery[$this->tabNum - 1]["list_views"]);
        if ($list_view[0] == "listview"){
            $this->CreateTable($displayPage, $menu_location, $oFactory);
        }
        else if ($list_view[0] == "boxView"){
            // To be made
            // CreateBoxView($displayPage, $menu_location, $oFactory);
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
    
    // No styling or specific layout, just display data listed in field_dictionary
    function CreateDefaultView($oFactory){
        
        // Grab fields from field_dictionary
        $query = "SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`";
        $data_fields = $oFactory->SQLHelper()->queryToDatabase($query);
        $data_fields_copy = $data_fields;
        $data_fields =$this->GetDataFields($oFactory);

        // Unused, not sure
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
        foreach($data_fields as $result){
            // Iterate through each fields's column's name and value
            foreach($result as $key=>$value){
                // Foreach field row (field_dict copy)
                foreach($data_fields_copy as $field_return) {
                    // If the column name matches the generic_field_name format the data
                    if ($field_return["generic_field_name"] == $key) {
                        $this->DisplayData($field_return["format_type"], $value);
                    }
                }
            }
        }
        $this->DisplayData($data_fields, $oFactory);

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
            echo "<input type='text' value='$value'>";

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
        echo "<table class='table table-bordered table-striped'><tr>";
        foreach($resArr as $result){
            echo "<th>" . $result["field_label_name"] . "</th>";
        }
        echo "</tr>";
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
        // Check for specific fields and if no fields specified, grab them all
        $resArr = $this->GetDataFields($oFactory);

        // Iterate through each field (database_table row)
        foreach($resArr as $result){
            echo "<tr>";
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
        echo "</table>";
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
}
