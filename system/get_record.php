<?php

/*
 *
 * function get_single_record($db_name, $pkey, $search) {
 * ***********
 *
 * function get_multi_record($db_name, $pkey, $search, $listFilter = 'false', $singleSort = 'false', $listCheck = 'false')
 *
 * **********************
 * **********************************
 *
 * function get_listFragment_record($db_name, $pkey, $listFilter = 'false', $limit = 'false', $fields = 'false')
 * ****
 * ***********************8
 */

function get_single_record($db_name, $pkey, $search) {

    $_SESSION['update_table']['search'] = $search;

    $con = connect();

//exit("select * from $db_name where $pkey='$search'");

    $user = $con->query("select * from $db_name where $pkey='$search'");
	if($user>num_rows){
		return $user->fetch_assoc();
	}
	return array();

}

/**
 * Get multi records for list display
 *
 * @param type $db_name
 * @param type $pkey
 * @param type $search
 * @param mixed $listFilter string if no parent-> child relationship of DD.table_type='child' then array('list_filter'=>DD.list_filter, 'child_filter'=>"'DD.database_table_name'.'DD.keyfield'=$search")
 * @param type $singleSort
 * @param type $listCheck
 * @return type
 */
function get_multi_record($db_name, $pkey, $search, $listFilter = 'false', $singleSort = 'false', $listCheck = 'false',&$isExistFilter,&$isExistField) {
    $_SESSION['update_table']['search'] = $search;
    $con = connect();

    if ($listFilter != 'false')
        $clause = listFilter($listFilter, $search,$isExistFilter,$isExistField);

    // exit("select * from $db_name $clause");
    if (!empty(trim($clause))){
      $clause ='WHERE ' . $clause;
    }

    if($singleSort !=='false' && !is_array( $singleSort ) ){
      $temp = strtoupper($singleSort);
      if($temp==='RANDOM'){
        $clause = $clause .' order by rand()';
      }else{
        $clause = $clause .' order by '.$singleSort;
      }
    }
	
	/*else if($singleSort !=='false' && is_array( $singleSort ) ){
		$key = 0;
		foreach($singleSort as $sorter) {
			if( $sorter ) {
				if( $key == 0 ) {					
					$clause = $clause .' order by '.$sorter; $key++;
				} 
				else {
					$clause = $clause .', '.$sorter; $key++;
					$key++;
				}
			}
		}
	}*/
	//echo "SELECT * FROM $db_name $clause ";
    $user = $con->query("SELECT * FROM $db_name $clause ");
    return $user;
}

function get_listFragment_record($db_name, $pkey, $listFilter = 'false', $limit = 'false', $fields = 'false') {


    $con = connect();


    $isExistFilter;$isExistField;
    if ($listFilter != 'false')
        $clause = listFilter($listFilter, $search,$isExistFilter,$isExistField);


    // exit("select * from $db_name $clause");

    if (!empty($clause))
        $clause = 'where ' . $clause;

    // exit("select * from $db_name $clause");

    if(!$fields)
        $fields = "*";

    if ($limit)
        $user = $con->query("select $fields from $db_name $clause limit 0, $limit");
    else
        $user = $con->query("select $fields from $db_name $clause");




    return $user;
}
