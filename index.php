<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
	$user=$_SESSION["user"];
}

///*
// * Record visit history
// * */
//$webname = "sample_management_system";
////Get the user's proxy ip address and ip address if a proxy server is used
//if (getenv('HTTP_X_FORWARDED_FOR', true) ?: getenv('HTTP_X_FORWARDED_FOR'))
//{
//	$pipaddress = getenv('HTTP_X_FORWARDED_FOR', true) ?: getenv('HTTP_X_FORWARDED_FOR');
//	$ipaddress = getenv('REMOTE_ADDR', true) ?: getenv('REMOTE_ADDR');
//}
////Get the user's ip address if no proxy server is used
//else
//{
//	$pipaddress = "";
//	$ipaddress = getenv('REMOTE_ADDR', true) ?: getenv('REMOTE_ADDR');
//}
//
//include "dbsample.inc";
//include "class/dbencryt.inc";
//
//$db_conn = new mysqli(Encryption::decrypt($host), Encryption::decrypt($usr), Encryption::decrypt($pwd),Encryption::decrypt($visithistorydbname));
//if ($db_conn->connect_error) {}
//else {
//	date_default_timezone_set('America/Chicago');
//	$logintime = date('Y-m-d H:i:s');
//
//	if ($stmt = $db_conn->prepare("INSERT INTO access (website, ipaddress, proxyaddress, visittime) VALUES (?, ?, ?, ?);")) {
//		$stmt->bind_param("ssss", $webname, $ipaddress, $pipaddress, $logintime);
//		$stmt->execute();
//		$stmt->close();
//	}
//
//	$db_conn->close();
//}
//?>

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

<!--<div class="fh5co-loader"></div>-->

<div id="page">

	<?php include "nav.php";?>

	<header id="fh5co-header" class="fh5co-cover" role="banner" style="background-image:url(images/img_bg_2.jpg);">
		<div class="overlay"></div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 text-center">
					<div class="display-t">
						<div class="display-tc animate-box" data-animate-effect="fadeIn">
							<h1 style=" font-size: 40px;">Sample Management System</h1>
							<h2>Provide the solution for sample labeling. <br> Link sample labels to barcode reader.</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div id="fh5co-services" class="fh5co-bg-section">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-sm-4 text-center">
					<div class="feature-center animate-box" data-animate-effect="fadeIn">
						<div class="circle-container">
							<h2 class="center-block">Samples</h2>
							<div class="circlestat center-block" data-dimension="200" data-text="370" data-width="20"
								 data-fontsize="38" data-percent="100"
								 data-fgcolor="#0000cc" data-bgcolor="#eee"
								 data-fill="#ddd">
<!--								--><?php
//							require_once("class/dbencryt.inc");
//							include "dbsample.inc";
//							$db = new mysqli(Encryption::decrypt($hostname), Encryption::decrypt($username),
//								Encryption::decrypt($password), Encryption::decrypt($dbname_sample));
//							if ($db->connect_error) {
//								die('Unable to connect to database: ' . $db->connect_error);
//							}
//
//							if ($result = $db->prepare("SELECT count(UUID) FROM Sample WHERE isDelete=0;")) {
//								$result->execute();
//								$result->bind_result($samplenum);
//								$result->fetch();
//								$result->close();
//								echo $samplenum;
//							}
//							?>

								  </div>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 text-center">
					<div class="feature-center animate-box" data-animate-effect="fadeIn">
						<div class="circle-container">
							<h2 class="center-block">Patients</h2>
							<div class="circlestat center-block" data-dimension="200" data-text="1435" data-width="20"
								 data-fontsize="38" data-percent="100" data-fgcolor="#0000cc" data-bgcolor="#eee"
								 data-fill="#ddd">
<!--									--><?php
//							require_once("class/dbencryt.inc");
//							include "dbsample.inc";
//							$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),
//								Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
//							if($db->connect_error){
//								die('Unable to connect to database: ' . $db->connect_error);
//							}
//
//							if($result = $db->prepare("SELECT count(Patient_ID) FROM Patient WHERE isDelete=0;"))
//							{
//								$result->execute();
//								$result->bind_result($patientnum);
//								$result->fetch();
//								$result->close();
//								echo $patientnum;
//							}
//							?>
								 </div>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-4 text-center">
					<div class="feature-center animate-box" data-animate-effect="fadeIn">
						<div class="circle-container">
							<h2 class="center-block">Studies</h2>
							<div class="circlestat center-block" data-dimension="200" data-text="1048" data-width="20"
								 data-fontsize="38" data-percent="100" data-fgcolor="#0000cc" data-bgcolor="#eee"
								 data-fill="#ddd">
<!--									--><?php
//							require_once("class/dbencryt.inc");
//							include "dbsample.inc";
//							$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),
//								Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
//							if($db->connect_error){
//								die('Unable to connect to database: ' . $db->connect_error);
//							}
//
//							if($result = $db->prepare("SELECT count(ID) FROM CodeStudy WHERE isDelete=0;"))
//							{
//								$result->execute();
//								$result->bind_result($studynum);
//								$result->fetch();
//								$result->close();
//								echo $studynum;
//							}
//							$db->close();
//							?>
								 </div>
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
		$('.menu-1 #navhome').addClass('active');
		$('.circlestat').circliful();
	});
</script>
</body>
</html>

