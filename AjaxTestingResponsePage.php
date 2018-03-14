<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/13/2018
 * Time: 8:51 PM
 */
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<?php
    echo "<label id='test123'>testing</label>";
?>
<input type="button" id="addButton" value="Add" onclick="ajaxTesting()">
<script>
    function ajaxTesting(){
        $.ajax({
            url: "http://home.localhost/GenericNew/GenericPlatform/ajaxquerytestingrequest.php",
            type: 'post',
            data: {'action': 'test2'},
            success: function(data, status){
                if(data == "ok"){
                    console.log("worked");
                    $("#test123").text("yay");
                }
                else{
                    console.log("???");
                }
            },
            error: function(xhr, desc, err){
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

</script>
