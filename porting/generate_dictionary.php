    <?php
    /**
     * Created by Susmit.
     * User: work
     * Date: 10/12/14
     * Time: 3:25 AM
     */
    require('dictionaryConfig.php');
    function getLabelName($name){
        $new = explode("_",$name);
        $newval="";
        foreach($new as $k => $v){
            $newval=$newval." ".$v;
        }
       return $newval;
    }
    $con=mysqli_connect($host,$user,$pass,$dbname);

    if($con->connect_errno){
        printf("Connect failed: %s\n", $con->connect_error);
        exit();
    }
    $database_table_names=array();
    $con->query("DROP TABLE IF EXISTS `$datadictTABLE`;");
    $con->query("DROP TABLE IF EXISTS `$fieldDefTABLE`;");
    $rs=$con->query("show tables;");
    $i=0;
    while ( $row = $rs->fetch_array()) {
        $database_table_names[$i]= $row[0];

        $i++;
    }
    echo "<h3>GENERATE DATA DICTIONARY<h3>";
    echo "<h3><a target='_blank' href=\"generate_fd.php\"> CLICK HERE TO GENERATE FIELD DEFINITION</a></h3>";
    echo "<pre>";
    $con->query("USE `$dbname`;");

    // CJ NOTE 1/17/2015 -->
    // not sure if we should DROP the table if exists
    $con->query("DROP TABLE IF EXISTS `$datadictTABLE`;");


    // CJ NOTE 1/17/2015 -->
    // fields / structure updated below

    $con->query("

     CREATE TABLE `$datadictTABLE` (
	`dict_id` INT(10) NOT NULL AUTO_INCREMENT,
	`table_alias` VARCHAR(35) NULL DEFAULT NULL,
	`database_table_name` VARCHAR(35) NULL DEFAULT NULL,
	`table_type` ENUM('user','project','transaction','child','crossref','system') NULL DEFAULT NULL,
	`parent_table` VARCHAR(50) NULL DEFAULT NULL,
	`keyfield` VARCHAR(50) NULL DEFAULT NULL,
	`display_page` VARCHAR(50) NULL DEFAULT NULL,
	`tab_num` INT(3) NULL DEFAULT NULL,
	`tab_name` VARCHAR(50) NULL DEFAULT NULL,
	`visibility` INT(5) NULL DEFAULT NULL,
	`user_type` VARCHAR(50) NULL DEFAULT NULL,
	`privilege_level` INT(5) NULL DEFAULT NULL,
	`list_views` VARCHAR(50) NULL DEFAULT NULL,
	`list_filter` VARCHAR(50) NULL DEFAULT NULL,
	`list_sort` VARCHAR(50) NULL DEFAULT NULL,
	`tab_num` INT(3) NULL DEFAULT NULL,
	`desc` TEXT NULL,
	`created` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`dict_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;




");

     $string= "insert into `$datadictTABLE`(`database_table_name`,`created`) values ";
         foreach($database_table_names as $k => $v){
             if($k==0){
                 $string=$string. "('".$v."',now())";
             }else{
                 $string=$string. ",('".$v."',now())";
             }
         }
    $con->query($string);
    echo "<pre> Data Dictionary Generated Successfully";
    $con->close();