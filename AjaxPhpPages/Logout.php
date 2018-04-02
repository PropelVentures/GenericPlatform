<?php
/**
 * Created by PhpStorm.
 * User: Doug Porter
 * Date: 4/1/2018
 * Time: 8:44 PM
 */

unset($_SESSION["user_id"]);
if(!isset($_SESSION["user_id"])){
    echo "success";
}
//header("Location: http://home.localhost/GenericNew/GenericPlatform/main.php?display=home");
//die();