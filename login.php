<?php
if(isset($_COOKIE["user"])){
    $url = "http://home.localhost/GenericNew/GenericPlatform/main.php?display=home";
    header("Location: " . $url);
    die();
}
?>
<html>
<head>
    <link rel="stylesheet" href="CSS/LoginPage.css">
    <link rel="stylesheet" href="CSS/ButtonStyle.css">
</head>
<form id='loginForm' method='post' action="AjaxPhpPages/LoginCheck.php">
    <div class='loginPage' id=$loginPageId>
        <div class="container">
            <p class="loginText">Login Here</p>
        </div>
        <?php
            if(isset($_POST["login_error"])){
                echo "<p>Error: incorrect username or password.</p><br>";
            }
        ?>
        <div><label for='username'><b>Username</b></label>
            <input type='text' placeholder='Enter Username' name='username' required><br>
            <label for='password'><b>Password</b></label>
            <input type='password' placeholder='Enter Password' name='pword' required><br><br>
            <button class="button" type='submit'>Login</button>
        </div>
    </div>

</form>
</html>

