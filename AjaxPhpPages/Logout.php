<?php
/**
 * Created by PhpStorm.
 * User: Doug Porter
 * Date: 4/1/2018
 * Time: 8:44 PM
 */

/*
 * This is just a prototype version to check if it works
 */

unset($_COOKIE["user"]);
session_destroy();
session_start();

header("Location: http://genericnew.cjcornell.net/GenericPlatform/main.php?display=home");
die();