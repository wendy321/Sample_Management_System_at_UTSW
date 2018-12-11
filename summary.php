<?php
session_start();

$user=null;
if(!empty($_SESSION["user"])){
	$user=$_SESSION["user"];
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

// Connect to database
require_once("class/dbencryt.inc");
require_once("dbsample.inc");
$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
if($db->connect_error){
	die('Unable to connect to database: ' . $db->connect_error);
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

	<!-- Facebook integration -->
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
	<style>
		.pie_panel {
			width: 24%;float: left;margin-right: 1%;
		}

		@media screen and (max-width: 768px){
			.pie_panel {
				width: 32%;
			}
		}

		@media screen and (max-width: 768px) and (min-width: 414px){
			.pie_panel {
				width: 49%;
			}
		}

		@media screen and (max-width: 414px){
			.pie_panel {
				width: 99%;
			}
		}

	</style>

</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

	<header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
		<div class="overlay"></div>
	</header>

	<?php include("nav.php"); ?>

	<div id="fh5co-services">
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-8 text-center">
					<h2> Sample Summary</h2>
				</div>
			</div>
			<hr class="hr-set"/>
			<br/>
			<div class="row">
				<div class="trendcharts col-sm-12">
					<div class="panel panel-default animate-box"
						 style="">
						<div class="panel-heading">
							Sample Number Inventory Trend Chart
						</div>
						<div class="panel-body">
							<div id="sampleinventrendchart" style="width:100%;"></div>
						</div>
					</div>
				</div>
				<div class="piecharts col-sm-12">
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Contributor Consortium
						</div>
						<div class="panel-body">
							<div id="consortiumpiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Contributor Institute
						</div>
						<div class="panel-body">
							<div id="institutepiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Sample Class
						</div>
						<div class="panel-body">
							<div id="sampleclasspiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Pathological Status
						</div>
						<div class="panel-body">
							<div id="pathologicalpiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Specimen Type
						</div>
						<div class="panel-body">
							<div id="specimentypepiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Anatomical Site
						</div>
						<div class="panel-body">
							<div id="anatomsitepiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Anatomical Laterality
						</div>
						<div class="panel-body">
							<div id="anatomlateralitypiechart" style="width:100%;"></div>
						</div>
					</div>
				</div>
			</div>
			<br/>

			<div class="row">
				<div class="col-md-offset-2 col-md-8 text-center">
					<h2> Patient Summary</h2>
				</div>
			</div>
			<hr class="hr-set"/>
			<br/>
			<div class="row">
				<div class="piecharts col-sm-12">
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Sex
						</div>
						<div class="panel-body">
							<div id="sexpiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Race
						</div>
						<div class="panel-body">
							<div id="racepiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Age at Diagnosis
						</div>
						<div class="panel-body">
							<div id="agediagpiechart" style="width:100%;"></div>
						</div>
					</div>
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Vital Status
						</div>
						<div class="panel-body">
							<div id="vitalpiechart" style="width:100%;"></div>
						</div>
					</div>
				</div>
			</div>
			<br/>

			<div class="row">
				<div class="col-md-offset-2 col-md-8 text-center">
					<h2> Study Summary</h2>
				</div>
			</div>
			<hr class="hr-set"/>
			<br/>
			<div class="row">
				<div class="piecharts col-md-offset-4 col-sm-12">
					<div class="panel panel-default animate-box pie_panel">
						<div class="panel-heading">
							Patient Distribution by Study / Clinical Trial
						</div>
						<div class="panel-body">
							<div id="studypiechart" style="width:100%;"></div>
						</div>
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
<!-- Highchart -->
<script src="js/highcharts.js"></script>
<script src="js/data.js"></script>
<script src="js/drilldown.js"></script>
<!-- Main -->
<script src="js/main.js"></script>

<script>
	$(function () {
		$(".menu-1 #navsum").addClass("active");
	});

	function drawtrend(el,seriedata){
		$(el).highcharts({
			chart: {
				type: 'column'
			},

			title: {
				text: 'Total fruit consumtion, grouped by gender'
			},

			xAxis: {
				categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
			},

			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: 'Number of fruits'
				}
			},

			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': ' + this.y + '<br/>' +
						'Total: ' + this.point.stackTotal;
				}
			},

			plotOptions: {
				column: {
					stacking: 'normal'
				}
			},

			series: [{
				name: 'John',
				data: [5, 3, 4, 7, 2],
				stack: 'male'
			}, {
				name: 'Joe',
				data: [3, 4, 4, 2, 5],
				stack: 'male'
			}, {
				name: 'Jane',
				data: [2, 5, 6, 2, 1],
				stack: 'female'
			}, {
				name: 'Janet',
				data: [3, 0, 4, 4, 3],
				stack: 'female'
			}
			]
		});
	}

	function drawpie(title,el,value,piecolors){
		$(el).highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: title
			},
			tooltip: {
				headerFormat: '<span style="font-size:1em">{point.key}</span><br>',
				pointFormat: '<span style="font-size: 1em">{series.name}: <b>{point.percentage:.1f}%</b></span>'
			},
			credits: {
				enabled: false
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					colors: piecolors,
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: [{
				name: 'Percentage',
				colorByPoint: true,
				data: value
			}]
		});
	}

	//	function formatTrend(){
	//		var defaultPointInterval = 86400000; // one day
	//		var defaultPointStart = Date.UTC(2009, 1, 1, 0, 0, 0); // milliseconds
	//		var seriedata = null;
	//
	//	}
	//
		var sample_trend_chart_info=[
			{
				element: '#sampleinventrendchart',
				<?php
				$cnttotal = 0;
				$pointinterval = 86400000; // default pointInterval (one day)
				$pointstart = mktime(0, 0, 0, 1, 1, 2009)*1000; // default pointStart (2009/01/01)
				$mindateMysql = "";
				$maxdateMysql = "";
				$timeresult = "";
				$sql="SELECT count(*),MIN(Date_Procedure),MAX(Date_Procedure) FROM Sample WHERE Date_Procedure IS NOT NULL AND isDelete = 0";
				if($result=$db->prepare($sql)){
					$result->execute();
					$result->bind_result($cnttotal,$mindateMysql,$maxdateMysql);
					$result->fetch();
					$mindate=strtotime($mindateMysql);
					$maxdate=strtotime($maxdateMysql);
					if($cnttotal == 0){
						$timeresult.="pointInterval:".$pointinterval.",".
							"pointStart:".$pointstart.",";
					}elseif($cnttotal == 1){
						$pointstart = $mindate-$pointinterval/2;
						$timeresult.="pointInterval:".$pointinterval.",".
							"pointStart:".$pointstart.",";
					}else{
						$pointinterval=($maxdate-$mindate)/12;
						$pointstart = $mindate-$pointinterval/2;
						$timeresult.="pointInterval:".$pointinterval.",".
							"pointStart:".$pointstart.",";
					}
					$result->close();
				}

//				$serieresult = "";
//				if($cnttotal == 0){
//					$serieresult.="[{name: null, data: null}]";
//				}elseif($cnttotal == 1){
//					$sql="SELECT count(*) AS cnt, T.sample_class, T.date_ran
//							FROM (
//									SELECT CASE
//									WHEN S.Date_Procedure Between DATE_SUB(\"".$mindateMysql."\",INTERVAL 15 DAY) AND \"".$mindateMysql."\" THEN '2001-6'
//									WHEN S.Date_Procedure Between \"2001-7-1\" AND \"2001-7-31\" THEN '2001-7'
//									ELSE '>=01-8'
//									END
//									AS date_ran,
//									C.Initial_SampleClass AS sample_class
//									FROM Sample as S
//									LEFT JOIN CodeSampleClass as C
//									ON S.Sample_Class = C.ID ) T
//							GROUP BY T.sample_class ,T.date_ran
//							ORDER BY T.sample_class ASC ,T.date_ran ASC";
//
//				}else{
//					$sql="SELECT count(*) AS cnt, T.sample_class, T.date_ran
//							FROM (
//									SELECT CASE
//									WHEN S.Date_Procedure Between DATE_SUB(\"2001-6-30\",INTERVAL 30 DAY) AND \"2001-6-30\" THEN '2001-6'
//									WHEN S.Date_Procedure Between \"2001-7-1\" AND \"2001-7-31\" THEN '2001-7'
//									ELSE '>=01-8'
//									END
//									AS date_ran,
//									C.Initial_SampleClass AS sample_class
//									FROM Sample as S
//									LEFT JOIN CodeSampleClass as C
//									ON S.Sample_Class = C.ID ) T
//							GROUP BY T.sample_class ,T.date_ran
//							ORDER BY T.sample_class ASC ,T.date_ran ASC";
//				}

//				$sql="(SELECT count(*), Code.Initial_SampleClass FROM Sample AS S".
//					" RIGHT JOIN CodeSampleClass AS Code".
//					" ON S.Sample_Class=Code.ID".
//					" WHERE S.isDelete = 0 AND S.Date_Procedure BETWEEN ? AND ? GROUP BY S.Sample_Class)".
//					" UNION".
//					"(SELECT count(*), Code.Initial_SampleClass, S.Date_Procedure FROM Sample AS S".
//					" LEFT JOIN CodeSampleClass AS Code".
//					" ON S.Sample_Class=Code.ID".
//					" WHERE S.isDelete = 0 AND S.Date_Procedure BETWEEN ? AND ? GROUP BY S.Sample_Class)";
//				$isfirst=1;
//				if($result=$db->prepare($sql)){
//					$result->execute();
//					$result->bind_result($cnt,$name,$datetick);
//					while($result->fetch()){
//						if($name === null) $name="Not Avaliable";
//						if($isfirst!==1) echo ",";
//						$isfirst=0;
//						echo "{name:\"".$name."\",y:".$cnt."}";
//					}
//					$result->close();
//				}

				?>
				seriedata: []
			}
		];
	//	sample_trend_chart_info.forEach(function(v){
	//		drawtrend(v.element);
	//	});

	drawtrend('#sampleinventrendchart');

	//	var defaultPieColors = ['#f45642','#f49541','#f4c741','#f4f141','#d6f441','#a6f441','#70f441','#3c8c59','#4beaed','#0eacaf',
	//		'#146ece','#084689','#8278e2','#e27791','#843548','#de77e2','#e710ed','#6b1a55','#bc0f4e','#e8c2c2'];

	<?php

	function getPieValueCnt($db,$sql){
		$value='';
		$cntrow=0;
		if($result=$db->prepare($sql)){
			$cnt=0;
			$name='';
			$result->execute();
			$result->bind_result($cnt,$name);
			while($result->fetch()){
				if($name === null) $name="Not Avaliable";
				if($cntrow!==0) $value.=",";
				$cntrow++;
				$value.="{name:\"".$name."\",y:".$cnt."}";
			}
			$result->close();
		}
		return [$value,$cntrow];
	}

	function getPieColorStr($coloropt,$piecnt){
		if($piecnt == 0){
			return "null";
		}else{
			return "(function () { 
							var colors = [],
        					base = Highcharts.getOptions().colors[".$coloropt."],
        					i;
							for (i = 0; i < ".$piecnt."; i += 1) {
								colors.push(Highcharts.Color(base).brighten((i - ".($piecnt/2).") / ".$piecnt.").get());
							}
							return colors;
						}())";
		}
	}

	$coloroption = 0;
	?>

	var sample_pie_info=[
		{
			title:'',
			element:'#consortiumpiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_CodeDataContributorClinicalTrialGroup AS CodeGrp FROM Sample AS S".
					" RIGHT JOIN CodeDataContributorClinicalTrialGroup AS Code".
					" ON S.Sample_Contributor_Consortium_ID=Code.ID group by (CodeGrp))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_CodeDataContributorClinicalTrialGroup AS CodeGrp FROM Sample AS S".
					" LEFT JOIN CodeDataContributorClinicalTrialGroup AS Code".
					" ON S.Sample_Contributor_Consortium_ID=Code.ID group by (CodeGrp))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr($coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#institutepiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_SampleContributorInstitute AS CodeInst FROM Sample AS S".
					" RIGHT JOIN CodeSampleContributorInstitute AS Code".
					" ON S.Sample_Contributor_Institute_ID=Code.ID group by (CodeInst))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_SampleContributorInstitute AS CodeInst FROM Sample AS S".
					" LEFT JOIN CodeSampleContributorInstitute AS Code".
					" ON S.Sample_Contributor_Institute_ID=Code.ID group by (CodeInst))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php ++$coloroption; echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#sampleclasspiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_SampleClass AS CodeSClass FROM Sample AS S ".
					"RIGHT JOIN CodeSampleClass AS Code ".
					"ON S.Sample_Class=Code.ID group by (CodeSClass))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_SampleClass AS CodeSClass FROM Sample AS S ".
					"LEFT JOIN CodeSampleClass AS Code ".
					"ON S.Sample_Class=Code.ID group by (CodeSClass))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#pathologicalpiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_PathologicalStatus AS CodePath FROM Sample AS S ".
					"RIGHT JOIN CodePathologicalStatus AS Code ".
					"ON S.Pathological_Status=Code.ID group by (CodePath))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_PathologicalStatus AS CodePath FROM Sample AS S ".
					"LEFT JOIN CodePathologicalStatus AS Code ".
					"ON S.Pathological_Status=Code.ID group by (CodePath))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#specimentypepiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_SpecimenType AS CodeSpec FROM Sample AS S ".
					"RIGHT JOIN CodeSpecimenType AS Code ".
					"ON S.Specimen_Type=Code.ID group by (CodeSpec))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_SpecimenType AS CodeSpec FROM Sample AS S ".
					"LEFT JOIN CodeSpecimenType AS Code ".
					"ON S.Specimen_Type=Code.ID group by (CodeSpec))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#anatomsitepiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_AnatomicalSite AS CodeAnat FROM Sample AS S ".
					"RIGHT JOIN CodeAnatomicalSite AS Code ".
					"ON S.Anatomical_Site=Code.ID group by (CodeAnat))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_AnatomicalSite AS CodeAnat FROM Sample AS S ".
					"LEFT JOIN CodeAnatomicalSite AS Code ".
					"ON S.Anatomical_Site=Code.ID group by (CodeAnat))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#anatomlateralitypiechart',
			value:[<?php

				$sql="(SELECT count(S.UUID),Code.Initial_AnatomicalLaterality AS CodeAnatLater FROM Sample AS S ".
					"RIGHT JOIN CodeAnatomicalLaterality AS Code ".
					"ON S.Anatomical_Laterality=Code.ID group by (CodeAnatLater))".
					" UNION ".
					"(SELECT count(S.UUID),Code.Initial_AnatomicalLaterality AS CodeAnatLater FROM Sample AS S ".
					"LEFT JOIN CodeAnatomicalLaterality AS Code ".
					"ON S.Anatomical_Laterality=Code.ID group by (CodeAnatLater))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		}
	];

	sample_pie_info.forEach(function(v){
		drawpie(v.title,v.element,v.value,v.piecolors);
	});

	<?php $coloroption=0; ?>
	var patient_pie_info=[
		{
			title:'',
			element:'#sexpiechart',
			value:[<?php

				$sql="(SELECT count(P.Patient_ID),Code.Initial_Sex AS CodeSex FROM Patient AS P RIGHT JOIN CodeSex AS Code".
					" ON P.Sex=Code.ID group by (CodeSex))".
					" UNION ".
					"(SELECT count(P.Patient_ID),Code.Initial_Sex AS CodeSex FROM Patient AS P LEFT JOIN CodeSex AS Code".
					" ON P.Sex=Code.ID group by (CodeSex))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr($coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#racepiechart',
			value:[<?php

				$sql="(SELECT count(P.Patient_ID),Code.Initial_Race AS CodeRace FROM Patient AS P RIGHT JOIN CodeRace AS Code".
					" ON P.Race=Code.ID group by (CodeRace))".
					" UNION ".
					"(SELECT count(P.Patient_ID),Code.Initial_Race AS CodeRace FROM Patient AS P LEFT JOIN CodeRace AS Code".
					" ON P.Race=Code.ID group by (CodeRace))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php ++$coloroption;echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#agediagpiechart',
			value:[<?php

				$sql="select count(*) as cnt, t.ran
					from (  
					  select case    
						when Age_At_Diagnosis_In_Days between 0 and 3650 then ' 0-10'  
						when Age_At_Diagnosis_In_Days between 3651 and 7300 then ' 10-20'  
						when Age_At_Diagnosis_In_Days between 7301 and 10950 then ' 20-30'  
						when Age_At_Diagnosis_In_Days between 10951 and 14600 then ' 30-40'  
						when Age_At_Diagnosis_In_Days between 14601 and 18250 then ' 40-50'  
						when Age_At_Diagnosis_In_Days between 18251 and 21900 then ' 50-60'  
						else '> 60'   
						end
						as ran
					  from Patient) t  
					group by t.ran";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors: <?php echo getPieColorStr(++$coloroption,$piecnt);?>
		},
		{
			title:'',
			element:'#vitalpiechart',
			value:[<?php

				$sql="(SELECT count(P.Patient_ID),Code.Initial_VitalStatus AS CodeVital FROM Patient AS P RIGHT JOIN CodeVitalStatus AS Code".
					" ON P.Vital_Status=Code.ID group by (CodeVital))".
					" UNION ".
					"(SELECT count(P.Patient_ID),Code.Initial_VitalStatus AS CodeVital FROM Patient AS P LEFT JOIN CodeVitalStatus AS Code".
					" ON P.Vital_Status=Code.ID group by (CodeVital))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		}
	];

	patient_pie_info.forEach(function(v){
		drawpie(v.title,v.element,v.value,v.piecolors);
	});

	studyenroll_pie_info=[
		{
			title:'',
			element:'#studypiechart',
			value:[<?php

				$sql="(SELECT count(ES.Patient_ID),Code.Initial_CodeStudy AS CodeStudy FROM EnrollStudy AS ES RIGHT JOIN CodeStudy AS Code ".
					" ON ES.Study_ID=Code.ID group by (CodeStudy))".
					" UNION ".
					"(SELECT count(ES.Patient_ID),Code.Initial_CodeStudy AS CodeStudy FROM EnrollStudy AS ES LEFT JOIN CodeStudy AS Code ".
					" ON ES.Study_ID=Code.ID group by (CodeStudy))";
				$pievaluecnt = getPieValueCnt($db,$sql);
				$pievalue = $pievaluecnt[0];
				$piecnt = $pievaluecnt[1];
				echo $pievalue;
				?>],
			piecolors:<?php echo getPieColorStr(++$coloroption,$piecnt);?>
		}
	];

	studyenroll_pie_info.forEach(function(v){
		drawpie(v.title,v.element,v.value,v.piecolors);
	});

</script>
</body>
</html>

