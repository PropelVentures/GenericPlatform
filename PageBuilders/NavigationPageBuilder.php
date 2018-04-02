<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 2/19/2018
 * Time: 10:37 AM
 */

class NavigationPageBuilder
{
/**
 * ~~GenerateNavigation~~
 * Description: Creates the navigation at the top of any page.
 * @param $displayPage
 * @param $menu_location
 * @param $oFactory
 */
    function GenerateNavigation($displayPage, $menu_location, Factory $oFactory)
    {
        //URL to be reused. Will need to be changed on the live page.
        $BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";
        ?>
        <!--   Creation of necessary HTML for bootstraps navigation.     -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container fluid">
                <div class="navbar-header">
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <?php
                        //Query to database. Go to the navigation table and select all entries with displaypage = $displaypage and those whose item number is not 0
                        //0 means that we don't want it in at the moment. Sorted by item_number and then sorted by dropdown_order.
                        $resultArr = $oFactory->SQLHelper()->queryToDatabase("SELECT * FROM navigation where display_page='$displayPage' and item_number != 0 and menu_location='$menu_location' order by item_number ASC, dropdown_order ASC");
                        $currentIndex = 0;
                        while ($currentIndex < count($resultArr)) {
                            //Target display page is the display page we switch to if the text is clicked. (i.e. ?display=home in the url)
                            $targetDisplayPage = $resultArr[$currentIndex]["target_display_page"];
                            if ($resultArr[$currentIndex]["dropdown_order"] == 0) { //not a drop down
                                //create HTML for a list item.
                                echo "<li><a href=" . $BASE_URL . "?display=" . $targetDisplayPage . ">" . $resultArr[$currentIndex]["item_label"] . "</a></li>";
                                $currentIndex++;
                            } else { //is a drop down.
                                //Create HTML to setup a dropdown in bootstrap.
                                echo "
                                                    <li class = 'dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $resultArr[$currentIndex]['item_label'] . "<span class='caret'></span></a>
                                                        <ul class='dropdown-menu'>";
                                $currentIndex++; //increment our current index because we set up the dropdown to display the previous index.
                                //while we are inside the bounds of the array and the item_numbers previous is equal to the current (meaning that the
                                //we are still adding to the dropdown)
                                while ($currentIndex < count($resultArr) && $resultArr[$currentIndex]["item_number"] == $resultArr[$currentIndex - 1]["item_number"]) {
                                    $targetDisplayPage = $resultArr[$currentIndex]["target_display_page"];
                                    echo "<li><a href=" . $BASE_URL . "?display=" . $targetDisplayPage . ">" . $resultArr[$currentIndex]["item_label"] . "</a></li>";
                                    $currentIndex++;
                                }
                                //ending HTML
                                echo "</ul></li>";
                            }
                        }
                        //Check if the user is logged in.
                        //If the user is not logged in, display the "Login" link
                        //If the user is logged in, display the "Logout" link
                        $sessionId = session_id();
                        echo "<p>$sessionId</p>";
                        /*if(!isset($_SESSION["user_id"])){
                            //echo "<li><a href=" . $BASE_URL . "?display=login>Login</a></li>";
                            $displayType = "block";
                            $loginPageId = "theLoginPage";
                            echo "<button id='login' class='button' onclick='document.getElementById(\"$loginPageId\").style.display=\"$displayType\"'>Login</button>";
                            echo "<div id=$loginPageId class='loginPage'><p>";
                            //echo "<p>";
                            $this->LoginBuilder();
                            echo "</p></div>";
                        }
                        else{
                            //echo "<li><a href=" . $BASE_URL . "?display=logout>Logout</a></li>";
                            //echo "<li><input type='button' value='Login'></li>";
                            echo "<form method='post' action='AjaxPhpPages/Logout.php'>";
                            echo "<button type='submit' id='logout' class='button' onclick=''>Logout</button>";
                            echo "</form>";
                        }*/
                        ?>
        <!--        ENDING HTML              -->
                    </ul>
                </div>
            </div>
            </div>
        </nav>
        <?php
    }

    function LoginBuilder(){
        $location = $_SERVER['PHP_SELF'] . "?display=" . $_GET['display'];
        echo "<form id='loginForm' method='post' action='$location'>";
        echo "<div><label for='username'><b>Username</b></label>";
        echo "<input type='text' placeholder='Enter Username' name='username' required><br>";
        echo "<label for='password'><b>Password</b></label>";
        echo "<input type='password' placeholder='Enter Password' name='pword' required><br>";
        echo "<button type='submit' onclick='login()'>Login</button>";
        echo "<button type='button' onclick='document.getElementById(\"loginPageId\").style.display=\"none\"'>Cancel</button>";
        echo "</div>";
        echo "</form>";
    }
}