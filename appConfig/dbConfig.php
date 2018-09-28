<?php



$config['db_name'] = "genericplatform";

#$config['db_user'] = "genericinternal";

#$config['db_password'] = "Upwork0814!!";

$config['db_user'] = "root";

$config['db_password'] = "";

$config['db_host'] = "localhost";





if(!empty($config['db_name'])){

    $_SESSION['config'] = $config;
}else{

    unset($_SESSION['config']);
}

?>