<?php



$config['db_name'] = "genericsandbox2";

$config['db_user'] = "genericsandbox2";

$config['db_password'] = "Upwork0703!**";

/* $config['db_user'] = "root";

$config['db_password'] = ""; */

$config['db_host'] = "localhost";





if(!empty($config['db_name'])){

    $_SESSION['config'] = $config;
}else{

    unset($_SESSION['config']);
}

?>