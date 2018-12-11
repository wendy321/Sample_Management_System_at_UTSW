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

	<!-- Modernizr JS -->
	<script src="js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->

	<style>
		.rowwrapper{
			margin-bottom: 5rem;
			padding: 5rem;
			background-color: #eaeaea;
			border-radius: 1rem;
		}
		.hr-set{
			margin-bottom: 7rem;
		}
		i:hover{
			opacity: 0.7;
		}
		a:hover h3{
			color: #00b3ee;
		}
	</style>
</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

	<?php include("nav.php"); ?>

	<header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
		<div class="overlay"></div>
	</header>

	<div id="fh5co-project">
		<div class="container">
			<div class="rowwrapper">
				<div class="row">
					<div class="col-md-2"></div>
					<div class="col-md-8 text-center">
						<h2>Sample-Related Information</h2>
					</div>
					<div class="col-md-2"></div>
				</div>
				<hr class="hr-set"/>

				<br/>

				<div class="row">
					<div class="col-md-6 animate-box">
						<div class="feature-center">
						<span class="icon icon2">
							<a href="samplelist.php?operate=add_edit_delete"><i class="fa fa-th-large"></i></a>
						</span>
							<div class="desc">
								<a href="samplelist.php?operate=add_edit_delete"><h3>Search & Edit Sample</h3></a>
								<p>You can add, modify, and delete sample information.</p>
							</div>
						</div>
					</div>

					<div class="col-md-6 animate-box">
						<div class="feature-center">
						<span class="icon icon2">
							<a href="linksamplepatient.php"><i class="fa fa-link"></i></a>
						</span>
							<div class="desc">
								<a href="linksamplepatient.php"><h3>Link Sample to Patient</h3></a>
								<p>You can link sample and patient information together.</p>
							</div>
						</div>
					</div>

					<div class="col-md-6 animate-box">
						<div class="feature-center">
						<span class="icon icon2">
							<a href="linksamplestudy.php"><i class="fa fa-external-link-square"></i></a>
						</span>
							<div class="desc">
								<a href="linksamplestudy.php"><h3>Link Sample to Study</h3></a>
								<p>You can link sample and study (clinical trial) together.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="rowwrapper">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 text-center">
						<h2>More Information</h2>
					</div>
				</div>
				<hr class="hr-set"/>
				<br/>

				<div class="row">
					<div class="col-md-6 animate-box">
						<div class="feature-center">
							<span class="icon icon2">
								<a href="patientlist.php?operate=add_edit_delete"><i class="fa fa-user"></i></a>
							</span>
							<div class="desc">
								<a href="patientlist.php?operate=add_edit_delete"><h3>Search & Edit Patient</h3></a>
								<p>You can add, modify, delete patient information.</p>
							</div>
						</div>
					</div>

					<div class="col-md-6 animate-box">
						<div class="feature-center">
							<span class="icon icon2">
								<a href="enrollstudylist.php?operate=add_edit_delete"><i class="icon-brush"></i></a>
							</span>
							<div class="desc">
								<a href="enrollstudylist.php?operate=add_edit_delete"><h3>Search & Edit Enroll Study</h3></a>
								<p>You can add, modify, and delete enroll study information, and enroll patient and
									sample to study.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="rowwrapper">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 text-center">
						<h2>Trace Modification Record</h2>
					</div>
				</div>
				<hr class="hr-set"/>
				<br/>

				<div class="row">
					<div class="col-md-6 animate-box">
						<div class="feature-center">
								<span class="icon icon2">
									<a href="changehistorylist.php?operate=view"><i class="icon-time-slot"></i></a>
								</span>
							<div class="desc">
								<a href="changehistorylist.php?operate=view"><h3 class="text-center">View Change History</h3></a>
								<p>You can view all record change history.</p>
							</div>
						</div>
					</div>
				</div>
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
<!-- Main -->
<script src="js/main.js"></script>

<script>
	$(function () {
		$(".menu-1 #navmgn").addClass("active");
	});
</script>
</body>
</html>

