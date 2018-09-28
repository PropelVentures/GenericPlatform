
<?php

require_once 'config.php';

?>

<div id="loading-msg"
     style="position: static !important; display: none;">
    <div style="float: left; padding-top: 7px;">Loading Page..</div>
    <div style="float: left;">
        <img alt="" style="height: 40px !important;"
             src="<?php echo BASE_URL ?>application/images/wait.gif">
    </div>
</div>
<div class="footer">
  <div class="container">
    <p class="text-muted pull-left">&#169;2001-2014 All Rights Reserved. </p>
    <div class="social pull-right">
      <ul>
        <li><a href="https://www.facebook.com/" class="fb" target="_blank"><i class="fa fa-facebook"></i></a></li>
        <li><a href="https://twitter.com/" class="tweet" target="_blank"><i class="fa fa-twitter"></i></a></li>
        <li><a href="https://plus.google.com" class="g-plus" target="_blank"><i class="fa fa-google-plus"></i></a></li>
        <li><a href="http://www.youtube.com/" class="you-tube" target="_blank"><i class="fa fa-youtube"></i></a></li>
        <div class="clr"></div>
      </ul>
    </div>
    <div class="clr"></div>
  </div>
</div>


<script src="<?=BASE_JS_URL?>bootstrap.min.js"></script>


</body>
</html>