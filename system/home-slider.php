<div class="slider">
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="item active"> <img src="<?php echo BASE_URL . HOME_SLIDER_IMAGES_URL ?>slide1.jpg" alt="" class="img-responsive">
                <div class="container">
                    <div class="carousel-caption slide1">
                        <h1>
                            <?php echo HOME_SLIDER_TITLE1 ?>
                        </h1>
                        <p>
                            <?php echo HOME_SLIDER_CONTENT1 ?>
                        </p>
                        <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                <?php echo HOME_SLIDER_BUTTON_TEXT1 ?>
                            </a></p>
                    </div>
                </div>
            </div>
            <div class="item"> <img src="<?php echo BASE_URL . HOME_SLIDER_IMAGES_URL ?>slide2.jpg" alt="" class="img-responsive">
                <div class="container">
                    <div class="carousel-caption slide2">
                        <h1>
                            <?php echo HOME_SLIDER_TITLE2 ?>
                        </h1>
                        <p>
                            <?php echo HOME_SLIDER_CONTENT2 ?>
                        </p>
                        <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                <?php echo HOME_SLIDER_BUTTON_TEXT2 ?>
                            </a></p>
                    </div>
                </div>
            </div>
            <div class="item"> <img src="<?php echo BASE_URL . HOME_SLIDER_IMAGES_URL ?>slide3.jpg" alt="" class="img-responsive">
                <div class="container">
                    <div class="carousel-caption slide3">
                        <h1>
                            <?php echo HOME_SLIDER_TITLE3 ?>
                        </h1>
                        <p>
                            <?php echo HOME_SLIDER_CONTENT3 ?>
                        </p>
                        <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                <?php echo HOME_SLIDER_BUTTON_TEXT3 ?>
                            </a></p>
                    </div>
                </div>
            </div>
        </div>
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a> </div>
    <!-- /.carousel -->
</div>