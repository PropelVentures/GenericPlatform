<?php
// session_start();

require_once 'functions_loader.php';

echo $_SESSION['child_return_url'];
unset($_SESSION['child_return_url']);
exit;

?>
