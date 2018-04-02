<html>
<form id='loginForm' method='post' action="AjaxPhpPages/LoginCheck.php">
    <div id=$loginPageId class='loginPage'>
        <p>Login Here</p>
        <?php
            if(isset($_POST["login_error"])){
                echo "<p>Error: incorrect username or password.</p><br>";
            }
        ?>
        <div><label for='username'><b>Username</b></label>
            <input type='text' placeholder='Enter Username' name='username' required><br>
            <label for='password'><b>Password</b></label>
            <input type='password' placeholder='Enter Password' name='pword' required><br>
            <button type='submit'>Login</button>

</form>
</html>

