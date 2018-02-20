<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 2/19/2018
 * Time: 11:42 AM
 */

class MainPageBuilder
{

    private $tabNum; //Current Tab Number
    private $table_alias; //Table alias associated with current display page (located in Data Dictionary and related to Field Dictionary)
    private $database_table_name; //database_table_name field inside the
    private $list_fields; //fields to be displayed in table retrieved from data dictionary
    private $list_filter; //
    private $list_sort; //way to sort fields retrieved from field dictionary (retieved from data dictionary)

    function LoadMainContent($displayPage, $menu_location, Factory $oFactory){
        if(!empty($_GET["tab_num"])){
            $this->tabNum = $_GET["tab_num"];
        }
        else{
            $this->tabNum = 1;
        }
        //$_SESSION["tab_num"] = $this->tabNum;
        $this->GetInfoFromDataDictionary($displayPage, $menu_location, $oFactory);
        $this->CreateAndDisplayTabs($displayPage, $menu_location, $oFactory);
        $this->CreateTableHeadersFromFieldDictionary($displayPage, $menu_location, $oFactory);
        $this->PopulateTable($displayPage, $menu_location, $oFactory);
    }
    function CreateAndDisplayTabs($displayPage, $menu_location, Factory $oFactory){
        //Create tabs
        echo "<ul class='nav nav-tabs'>";
        //Query from data dictionary for everything that matches the display page in URL
        $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `data_dictionary` WHERE `display_page` = \"$displayPage\"");
        $BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";

        foreach($resArr as $key=>$value){
            if(!empty($this->tabNum) && $key + 1 == $this->tabNum){ //active tab
                echo "<li class='active'><a href='$BASE_URL?display=$displayPage&tab_num=" . ($key + 1) . "'>" . $value["tab_name"] . "</a> </li>";
            } else{ //Inactive tab
                echo "<li><a href='$BASE_URL?display=$displayPage&tab_num=" . ($key + 1) . "'>" . $value["tab_name"] . "</a> </li>";
            }
        }
        echo "</ul>";
    }

    function GetInfoFromDataDictionary($displayPage, $menu_location, Factory $oFactory){
        $resArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM `data_dictionary` WHERE `display_page` = \"$displayPage\"");
        $this->table_alias = $resArr[$this->tabNum - 1]["table_alias"];
        $this->database_table_name = $resArr[$this->tabNum - 1]["database_table_name"];
        $this->list_fields = $resArr[$this->tabNum - 1]["list_fields"]; //explode(",", $resArr[$this->tabNum - 1]["list_fields"]);
        $this->list_sort = $resArr[$this->tabNum - 1]["list_sort"];
    }

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