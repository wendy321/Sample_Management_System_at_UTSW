<?php
session_start();

$user = null;
if (!empty($_SESSION["user"])) {
    $user = $_SESSION["user"];
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sample Management System - UT Southwestern Medical Center | Department of Pediatrics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="manage medical samples including sample id generation, sample barcode generation,
		and linkage sample with patient information and clinical trial/study information"/>
    <meta name="keywords" content="medical sample, sample barcodes"/>
    <meta name="author" content="UTSW - QBRC"/>

    <!-- Facebook integration -->
    <meta property="og:title" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content=""/>
    <meta property="og:description" content=""/>

    <!-- <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,600,400italic,700' rel='stylesheet' type='text/css'> -->

    <!-- Animate.css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Theme style  -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Modernizr JS -->
    <script src="js/modernizr-2.6.2.min.js"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

    <header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
    </header>

    <?php include("nav.php"); ?>

    <div id="fh5co-about">
        <div class="container">
            <div class="about-content">
                <div class="row animate-box">
                    <div class="col-md-8 col-md-offset-2 text-center fh5co-heading">
                        <span></span>
                        <h2>Goal</h2>
                    </div>
                </div>
                <div class="row">
                    <!--					<div class="col-md-6 col-md-push-6">-->
                    <div class="col-md-12">
                        <div class="desc animate-box" data-animate-effect="fadeIn">
                            <h3 class="text-center">Generate Sample ID designed for barcode</h3>
                        </div>
                        <div class="desc animate-box" data-animate-effect="fadeIn">
                            <h3 class="text-center">Manage Sample, Patient, and Study (Clinical Trial) Information</h3>
                        </div>
                        <div class="desc animate-box" data-animate-effect="fadeIn">
                            <h3 class="text-center">Link Sample to Patient and Study</h3>
                        </div>
                        <div class="desc animate-box" data-animate-effect="fadeIn">
                            <h3 class="text-center">Summarize Sample, Patient, and Study Information</h3>
                        </div>
                    </div>
                    <!--					<div class="col-md-6 col-md-pull-6">-->
                    <!--						<img class="img-responsive" src="images/img_bg_1.jpg" alt="about">-->
                    <!--						<img class="img-responsive" src="images/img_bg_2.jpg" alt="about">-->
                    <!--					</div>-->

                </div>
            </div>
            <div class="row animate-box">
                <div class="col-md-8 col-md-offset-2 text-center fh5co-heading">
                    <span></span>
                    <h2>Meet Our Team</h2>
                    <!--					<p>Dignissimos asperiores vitae velit veniam totam fuga molestias accusamus alias autem provident. Odit ab aliquam dolor eius.</p>-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 animate-box" data-animate-effect="fadeIn">
                    <div class="fh5co-staff">
                        <img src="images/people/james.jpg" alt="Free HTML5 Templates by gettemplates.co">
                        <h3>James Amatruda</h3>
                        <strong class="role">M.D., Ph.D.</strong>
                        <strong class="role">Associate Professor</strong>
                        <strong class="dep">PD-Hematology and Oncology, UTSW</strong>
                        <ul class="fh5co-social-icons">
                            <li><a href="mailto:James.Amatruda@UTSouthwestern.edu"><i class="icon-email"></i></a></li>
                            <li><a href="https://www.utsouthwestern.edu/labs/amatruda/"><i class="icon-home"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 animate-box" data-animate-effect="fadeIn">
                    <div class="fh5co-staff">
                        <img src="images/people/yang.jpg" alt="Free HTML5 Templates by gettemplates.co">
                        <h3>Yang Xie</h3>
                        <strong class="role">M.D., Ph.D.</strong>
                        <strong class="role">Associate Professor & Director of QBRC</strong>
                        <strong class="dep">Department of Clinical Sciences, UTSW</strong>
                        <ul class="fh5co-social-icons">
                            <li><a href="mailto:yang.xie@utsouthwestern.edu"><i class="icon-email"></i></a></li>
                            <li><a href="https://qbrc.swmed.edu/labs/xielab"><i class="icon-home"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 animate-box" data-animate-effect="fadeIn">
                    <div class="fh5co-staff">
                        <img src="images/people/shinyi-lin.jpg" alt="Free HTML5 Templates by gettemplates.co">
                        <h3>Shin-Yi Lin</h3>
                        <strong class="role">M.S.</strong>
                        <strong class="role">Scientific Programmer</strong>
                        <strong class="dep">QBRC, UTSW</strong>
                        <ul class="fh5co-social-icons">
                            <li><a href="mailto:Shinyi.Lin@utsouthwestern.edu"><i class="icon-email"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>

<div class="gototop js-top">
    <a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
</div>

<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!-- jQuery Easing -->
<script src="js/jquery.easing.1.3.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.min.js"></script>
<!-- Waypoints -->
<script src="js/jquery.waypoints.min.js"></script>
<!-- Main -->
<script src="js/main.js"></script>

<script>
    $(function () {
        $(".menu-1 #navabout").addClass("active");
    });
</script>

</body>
</html>

