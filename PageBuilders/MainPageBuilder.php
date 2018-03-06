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
        foreach($this->dataDictQuery as $key => $row){
            $row["list_views"];
            $list_view = explode(" ", $row);
            foreach ($list_view as $key => 
            if ($list_view[0] == "listView"){
                CreateTable($displayPage, $menu_location, $oFactory);
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
                CreateDefaultView($displayPage, $menu_location, $oFactory);
            }
        }
        

    }
    
    // No styling or specific layout, just display data listed in field_dictionary
    function CreateDefaultView($displayPage, $menu_location, $oFactory){
        
        // Grab fields from field_dictionary
        $data_fields = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`");
        if(empty($this->list_fields) || $this->list_fields == ""){
            foreach($data_fields as $key=>$value){
                if($key < count($data_fields) - 1){
                    $this->list_fields .= $value["generic_field_name"] . ",";
                }
                else {
                    $this->list_fields .= $value["generic_field_name"];
                }
            }
        }
        foreach($data_fields as $field){
            // Generate html code based off format_type
            GetAndDisplayData($field);        
    }
    
    
    // Creates html for a data field and retrieves the value to go inside
    function GetAndDisplayData($field){
        $tag_data = $oFactory->SQLHelper()->queryToDatabase("SELECT $field FROM `$this->database_table_name` ORDER BY $this->list_sort");
        
        if ($field["format_type"] == "" || empty($field["format_type"]){
            echo "<p> $tag_data </p>";
        }
        else if ($field["format_type"] == "username"){
            echo "<input type='text' name='username'>";
        }
        else if ($field["format_type"] == "transaction_text"){
            
        }
        else if ($field["format_type"] == "transaction_execute"){
            
        }
        else if ($field["format_type"] == "transaction_confirmation"){
            
        }
        else if ($field["format_type"] == "transaction_cancel"){
            
        }
        else if ($field["format_type"] == "textbox"){
            echo "<input type='text' name='$field['generic_field_name'>";

        }
        else if ($field["format_type"] == "tag"){
            echo "<input type='text' value='html,input,tag' data-role='tagsinput'></input>";
        }
        else if ($field["format_type"] == "pdf_inline"){
            
        }
        else if ($field["format_type"] == "password"){
            echo "<input type='password' name='username'>";
        }
        else if ($field["format_type"] == "list_fragment"){
            
        }
        else if ($field["format_type"] == "image"){
            echo "<img src='" . $oFactory->SQLHelper()->queryToDatabase("SELECT $field['generic_field_name' FROM `$this->database_table_name` ORDER BY $this->list_sort $this->WHERE "); . "' >";
        }
        else if ($field["format_type"] == "email"){
            
        }
        else if ($field["format_type"] == "dropdown"){
            
        }
        else if ($field["format_type"] == "crf"){
            
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
        $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `data_dictionary` WHERE `display_page` = '$displayPage'  ORDER BY `tab_num`");
        // Set current tab to be tab 0
        $this->table_alias = $resArr[$this->tabNum - 1]["table_alias"];
        // Set reference to database_table name to current tab's reference
        $this->database_table_name = $resArr[$this->tabNum - 1]["database_table_name"];
        // Grab the list fields
        $this->list_fields = $resArr[$this->tabNum - 1]["list_fields"]; //explode(",", $resArr[$this->tabNum - 1]["list_fields"]);
        // Grab list sort method
        $this->list_sort = $resArr[$this->tabNum - 1]["list_sort"];
        // Get WHERE statement
        if ($resArr[$this->tabNum-1]["list_filter"] != "" && !empty($resArr[$this->tabNum-1]["list_filter"]){
            $this->WHERE = "WHERE $resArr[$this->tabNum-1]['list_filter']";
        }
        else {
            $this->WHERE = "";
        }
        
        
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
        if(empty($this->list_sort)){
            $this->list_sort = "\"\"";
        }
        //$resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `field_dictionary` WHERE `table_alias` = \"$this->table_alias\" ORDER BY `display_field_order`");
        if($this->list_fields == null || $this->list_fields == ""){ //shouldnt ever happen anymore
            $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `$this->database_table_name` ORDER BY $this->list_sort");
        }
        elseif(is_numeric($this->list_fields)){
            //if is number grab the first four fields from field dictionary and populate only those?
            //They don't even do this. Note table_alias transactions... For now just getting everything
            $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `$this->database_table_name` ORDER BY $this->list_sort");
        }
        else{
            $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT $this->list_fields FROM `$this->database_table_name` ORDER BY $this->list_sort");
        }
        foreach($resArr as $result){
            echo "<tr>";
            foreach($result as $index){
                echo "<td>$index</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
