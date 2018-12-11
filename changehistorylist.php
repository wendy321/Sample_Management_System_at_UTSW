<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

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

    <!-- <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,600,400italic,700' rel='stylesheet' type='text/css'> -->

    <link rel="icon" href="images/utsw_logo_icon.jpg">
    <!-- Animate.css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- FONTAWESOME STYLE CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"/>
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Theme style  -->
    <link rel="stylesheet" href="css/style.css">
    <!-- DataTable -->
    <link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="css/responsive.dataTables.min.css"/>
    <!-- Modernizr JS -->
    <script src="js/modernizr-2.6.2.min.js"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
    </style>
</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

    <header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
    </header>

    <?php include("nav.php"); ?>

    <div class="container-fluid" style="padding: 0;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item col-md-offset-3"><a href="manage.php">Manage</a></li>
            <li class="breadcrumb-item active">View Change History</li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>View Change History Information</h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-12 text-center">
                    <table id="changehistorytable" class="display responsive" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Table Name</th>
                            <th>Primary Key</th>
                            <th>Field Name</th>
                            <th>Field Value Before Change</th>
                            <th>Field Value After Change</th>
                            <th>Account</th>
                            <th>Change Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <br/>
                </div>
            </div>
        </div>

        <?php include("footer.php"); ?>

    </div>

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
    <!-- DataTable -->
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/buttons.flash.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="js/pdfmake.min.js"></script>
    <script src="js/vfs_fonts.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <!-- Main -->
    <script src="js/main.js"></script>

    <script>
        $(function () {
            $(".menu-1 #navmgn").addClass("active");
            $(".menu-1 #navchangehistory a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();
        });

        $(document).ready(function(){

            /* Data Table */
            var changehistorytable=$('#changehistorytable');
            if (!($.fn.DataTable.isDataTable(changehistorytable))){
                $(changehistorytable).DataTable( {
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    "responsive": true,
                    "retrieve": true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": 'datatablescript/server_processing_changehistory.php',
                        "type": 'GET',
                        "data": function ( d ) {
                            d.operate="select";
                        }
                    },
                    "deferRender": true,
                    "searching": true
                });
            }

        });
    </script>
</body>
</html>

