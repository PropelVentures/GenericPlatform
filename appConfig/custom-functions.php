<?php

/* 
 * Custom Functions Goes here
 * 
 */


session_start();

require_once("appConfig.php");
require_once("../application/database/db.php");
require_once '../application/config.php';
require_once '../application/functions.php';
require_once '../DDICT/masterFunctions.php';



/*
 * *************
 * ************************
 * ************************************
 * 
 * @Execute Transaction Ajax Code goes here
 */

if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'execute_trans') {



    $check = getWhere('data_dictionary', array('table_alias' => $_GET['ta'], 'display_page' => $_GET['display']));

    if (!empty($check[0])) {

        if (!empty(trim($check[0]['keyfield'])))
            $primary_key = trim($check[0]['keyfield']);
        else
            $primary_key = firstFieldName(trim($check[0]['database_table_name']));



        $record = getWhere(trim($check[0]['database_table_name']), array($primary_key => $_GET['project_id']));


        $date = date("Y-m-d") . ' ' . date("h:i:s");

        $arr = array();
        
        $arr = array('user_id' => $_SESSION['uid'], 'owner_id' => $record[0]['user_id'], 'project_id' => $record[0]
        [$primary_key], 'transactionType' => $_GET['trans_id'], 'amount' => $record[0]['amount'], 'transaction_datetime' => $date);
        
        
        $check = getWhere('data_dictionary', array('dict_id' => $_GET['dd_id']));
        
        
        $fd_record = getWhere('field_dictionary', array('table_alias' => $check[0][list_select] ), "order by display_field_order");
                
        
        $fd_collector = array();
        
        foreach( $fd_record as $val){   
         
            
           // $val['table_type'] = 'transaction';
            
        $fd_collector[] = formating_Update($val, $method = 'transaction', $urow, $image_display = 'true', $page_editable = 'true') ;
                
        }
        
        $fd_collector[] = "<input type='hidden' name='insertRecord' class='insertRecord' value='$arr[user_id],$arr[owner_id],$arr[project_id],$arr[transactionType],$arr[amount],$arr[transaction_datetime]' />";
        
        
        
        exit(json_encode($fd_collector));
      
       
        
    } else {

        exit('invalid');
    }
}




/*
 * *************
 * ************************
 * ************************************
 * 
 * @Confirm Transaction Ajax Code goes here
 */

if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'confirm_trans') {



    $arr = explode(',', $_GET['impData']);
    
    
    $check = insert('transactions', array('user_id' => $arr[0],'owner_id' => $arr[1], 'project_id' => $arr[2], 'transactionType' => $arr[3], 'amount' => $arr[4], 'transaction_datetime' => $arr[5]));
    

   if($check)
       exit('inserted');
    
}



