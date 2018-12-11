<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
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
	and linkage sample with patient information and clinical trial/study information" />
    <meta name="keywords" content="medical sample, sample barcodes" />
    <meta name="author" content="UTSW - QBRC" />

    <!-- Facebook and Twitter integration -->
    <meta property="og:title" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content=""/>
    <meta property="og:description" content=""/>
    <meta name="twitter:title" content="" />
    <meta name="twitter:image" content="" />
    <meta name="twitter:url" content="" />
    <meta name="twitter:card" content="" />

    <link rel="icon" href="images/utsw_logo_icon.jpg">

    <!-- Animate.css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Circle Number Counter CSS -->
    <link href="css/jquery.circliful.css" rel="stylesheet"/>
    <!-- Theme style  -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Modernizr JS -->
    <script src="js/modernizr-2.6.2.min.js"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->

</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

    <?php include "nav.php";?>

    <header id="fh5co-header" class="fh5co-cover" role="banner" style="background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    <div class="display-t">
                        <div class="display-tc animate-box" data-animate-effect="fadeIn">
                            <h1 style=" font-size: 30px;">Search Sample</h1>
                            <h2>by Sample ID or Barcode</h2>
                            <div class="row">
                                <form class="form-inline" id="fh5co-header-subscribe" method="post" action="searchsample.php">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="sampleid" name="sampleid" placeholder="Input Sample ID or Scan Barcode"
                                                   onblur="if(this.value==' ') this.value='Input Sample ID or Scan Barcode';"
                                                   onFocus="this.value=' '"/>
                                            <button type="submit" class="btn btn-default">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div id="fh5co-services" class="fh5co-bg-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    <div class="feature-center animate-box" data-animate-effect="fadeIn">
                        <h1 style=" font-size: 30px;">Search Sample</h1>
                        <h3>by multiple variables</h3>
                        <div class="form-group">
                            <form method="post" action="">

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php";?>

<div class="gototop js-top">
    <a href="#" class="js-gotop"><i class="fa fa-2x fa-arrow-circle-up"></i></a>
</div>

<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!-- jQuery Easing -->
<script src="js/jquery.easing.1.3.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.min.js"></script>
<!-- Waypoints -->
<script src="js/jquery.waypoints.min.js"></script>
<!-- Circle Number Counter SCRIPTS  -->
<script src="js/jquery.circliful.min.js"></script>
<!-- Main -->
<script src="js/main.js"></script>
<script>
    $(document).ready(function(){
        $('.menu-1 #navsearch').addClass('active');
    });
</script>
</body>
</html>

