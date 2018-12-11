<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

require ('class/EscapeString.inc');
$operate=!empty($_GET['operate'])?EscapeString::escape($_GET['operate']):null;

$enstudyid=null;
if(strpos($operate,'edit') !== FALSE){
    $enstudyid=(!empty($_GET['enstudyid']))?EscapeString::escape($_GET['enstudyid']):null;
}

if(strpos($operate,'view') !== FALSE){
    $enstudyid=(!empty($_GET['enstudyid']))?EscapeString::escape($_GET['enstudyid']):null;
}

if($enstudyid!==null) {
    require_once("class/dbencryt.inc");
    require_once("dbsample.inc");
    $db = new mysqli(Encryption::decrypt($hostname), Encryption::decrypt($username), Encryption::decrypt($password), Encryption::decrypt($dbname_sample));
    if ($db->connect_error) {
        die('Unable to connect to database: ' . $db->connect_error);
    }

    $studyid=$arm=$pid=$studypid=$sampleuuid=$studysampleid='';

    $sql = "SELECT Study_ID,Study_Arm,Patient_ID,Within_Study_Patient_ID,Sample_UUID,Within_Study_Sample_ID 
            FROM EnrollStudy WHERE ID=? AND isDelete=0";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('s',$enstudyid);
        $result->execute();
        $result->bind_result($studyid,$arm,$pid,$studypid,$sampleuuid,$studysampleid);
        $result->fetch();
        $result->close();
    }
    $db->close();
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
    <link href="css/style1.css" rel="stylesheet"/>
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
            <li class="breadcrumb-item active">
                <?php
                switch($operate){
                    case 'edit': echo "Edit";break;
                    case 'add': echo "Add";break;
                    default: echo "View"; break;
                }
                ?>
                Enroll Study
            </li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>
                        <?php
                        switch($operate){
                            case 'edit': echo "Edit";break;
                            case 'add': echo "Add";break;
                            default: echo "View"; break;
                        }
                        ?>
                        Enroll Study Information
                    </h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="form-group">
                        <form action="">
                            <div class="alert alert-info">
                                <?php
                                if(strpos($operate,'edit') !== FALSE && ($enstudyid !=='')){
                                    echo "<label for='ddl_enrollstudyid' class='hidden'>Enroll Study ID</label>
                                            <input id='ddl_enrollstudyid' name='enrollstudyid' class='form-control hidden' 
                                            type='text' value ='".$enstudyid."' readonly/><br>";
                                }
                                ?>
                                <label for="ddl_studyname">Source / Study Name
                                    <i class="fa fa-question-circle" title="Sample data source"></i>
                                    <em><span class="fa fa-asterisk fa-fw"></span></em>
                                </label>
                                <select class="form-control" id="ddl_studyname" name="studyid" required>
                                    <option value="2">P9749</option>
                                    <option value="6">AGCT0132</option>
                                    <option value="4">AGCT01P1</option>
                                    <option value="14">AGCT0521</option>
                                    <option value="9">GC 1</option>
                                    <option value="8">GC 2</option>
                                    <option value="5">GOG 0078</option>
                                    <option value="1">GOG 0090</option>
                                    <option value="11">GOG 0116</option>
                                    <option value="7">INT-0097</option>
                                    <option value="10">INT-0106</option>
                                    <option value="3">OPTF</option>
                                    <option value="13">TCG_99</option>
                                    <option value="12">TGM95</option>
                                    <option value="98">Other</option>
                                    <option value="99">Unknown</option>
                                    <option value="100">Not In Clinical Trial</option>
                                </select><br>

                                <div id="studyarmblock">
                                    <label for="ddl_studyarm">Study Arm</label>
                                    <select id="ddl_studyarm" name="studyarm" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">Experimental Arm</option>
                                        <option value="2">Control Arm</option>
                                    </select><br>
                                </div>

                                <hr>

                                <label for="ddl_samuuid">System Sample UUID
                                    <em><span class="fa fa-asterisk fa-fw"></span></em>
                                </label>
                                <input id="ddl_samuuid" name="samuuid" class="form-control" type="text"
                                       pattern=".{23}" data-toggle="modal" data-target="#sample_modal"/><br>

                                <label for="ddl_studysampleid">Source / Study Sample ID (max 30 characters)
                                    <i class="fa fa-question-circle"
                                       title="Sample ID in data source">
                                    </i>
                                </label>
                                <input type="text" class="form-control" id="ddl_studysampleid" name="studysampleid"
                                       pattern=".{1,30}" placeholder="e.g. T95-1C3"
                                /><br>

                                <hr>

                                <label for="ddl_patientid">System Patient ID</label>
                                <input id="ddl_patientid" name="patientid" class="form-control" type="text"
                                       pattern=".{6}" data-toggle="modal" data-target="#patient_modal"/><br>

                                <label for="ddl_studypatient">Source / Study Patient ID (max 30 characters)
                                    <i class="fa fa-question-circle"
                                       title="Patient ID in data source">
                                    </i>
                                </label>
                                <input type="text" class="form-control" id="ddl_studypatient" name="studypatientid"
                                       pattern=".{1,30}"/><br>

                                <input type="submit" class="btn btn-primary" value="Submit"/>
                                <sub><em><i class="fa fa-asterisk fa-fw"></i></em> as required field</sub>
                            </div>
                        </form>
                    </div>



                    <div id="sample_modal" class="modal fade text-center" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h2 class="modal-title">Select a Sample</h2>
                                </div>
                                <div class="modal-body" style="padding:24px;">
                                    <br/><br/>
                                    <table id="sampletable" class="display responsive" style="width: 100%;">
                                        <thead> <tr>
                                            <th>System Sample UUID</th>
                                            <th>System Sample ID</th>
                                            <th>Local Sample ID</th>
                                            <th>Parent Sample UUID</th>
                                            <th>Date Derived From Parent</th>
                                            <th>System Patient ID</th>
                                            <th>Local Patient ID</th>
                                            <th>Source Name</th>
                                            <th>Source Sample ID</th>
                                            <th>Source Patient ID</th>
                                            <th>Sample Contributor Consortium</th>
                                            <th>Sample Contributor Institute</th>
                                            <th>Procedure Type</th>
                                            <th>Procedure Date</th>
                                            <th>Pathological Status</th>
                                            <th>Sample Class</th>
                                            <th>Sample Type</th>
                                            <th>Specimen Type</th>
                                            <th>Nucleotide Size</th>
                                            <th>Anatomical Site</th>
                                            <th>Anatomical Laterality</th>
                                            <th>Storage Room</th>
                                            <th>Cabinet Type</th>
                                            <th>Cabinet Temperature</th>
                                            <th>Cabinet Number</th>
                                            <th>Shelf Number</th>
                                            <th>Rack Number</th>
                                            <th>Box Number</th>
                                            <th>Position Number</th>
                                            <th>Amount Value</th>
                                            <th>Amount Unit</th>
                                            <th>Concentration Value</th>
                                            <th>Concentration Unit</th>
                                            <th>Notes</th>
                                            <th>CreateTime</th>
                                            </tr> </thead>
                                        <tbody></tbody>
                                    </table>
                                </div><br/>
                                <div class="modal-footer">
                                    <button id="selectsamplebtn" class="btn btn-primary" data-dismiss="modal"
                                            type="button"> Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="patient_modal" class="modal fade text-center" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h2 class="modal-title">Select a Patient</h2>
                                </div>
                                <div class="modal-body" style="padding:24px;">
                                    <br/><br/>
                                    <table id="patienttable" class="display responsive" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>System Patient ID</th>
                                            <th>Local Patient ID</th>
                                            <th>Data Contributor Clinical Trial_Group</th>
                                            <th>Data Contributor Center</th>
                                            <th>Age at Enrollment in Days</th>
                                            <th>Relapsed at Enrollment</th>
                                            <th>Age at First Visit in Days</th>
                                            <th>Relapsed at First Visit</th>
                                            <th>Age at Diagnosis in Days</th>
                                            <th>Year of Diagnosis</th>
                                            <th>Has Dysgenetic Gonad</th>
                                            <th>Sex</th>
                                            <th>Race</th>
                                            <th>Ethnic</th>
                                            <th>Vital Status</th>
                                            <th>Histology</th>
                                            <th>COG Stage</th>
                                            <th>FIGO Stage</th>
                                            <th>AJCC Stage</th>
                                            <th>IGCCCG RiskGroup</th>
                                            <th>Notes</th>
                                            <th>Create Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="modal-footer">
                                    <button id="selectpatientbtn" type="button" class="btn btn-primary"
                                            data-dismiss="modal">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="addupdateenrollstudymsg" class="row"></div>
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
        $(document).ready(function(){
            /* menu animation*/
            $(".menu-1 #navmgn").addClass("active");
            $(".menu-1 #navstudy a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();

            /* study and study arm interaction */
            var studyname='#ddl_studyname';
            var studyarm='#ddl_studyarm';

            <?php
                if(strpos($operate,'edit') !== FALSE){
                    echo "$(studyname).find('option[value=\"".$studyid."\"]').prop('selected',true);
                    $(studyarm).find('option[value=\"".$arm."\"]').prop('selected',true);
                    $('#ddl_samuuid').val('".$sampleuuid."');
                    $('#ddl_studysampleid').val('".$studysampleid."');
                    $('#ddl_patientid').val('".$pid."');
                    $('#ddl_studypatient').val('".$studypid."');";
                }
            ?>

            function interactStudyStudyarm(){
                if($(studyname).val()==='100'){
                    $(studyarm).val('');
                    $('#studyarmblock').hide();
                }else{
                    $('#studyarmblock').show();
                }
            }
            interactStudyStudyarm();
            $(studyname).on('change',function(){
                interactStudyStudyarm();
            });

            /* Sample Data Table in model */
            var sampletable=$('#sampletable');
            if (!($.fn.DataTable.isDataTable(sampletable))){
                var sampleddtable=$(sampletable).DataTable( {
                    "responsive": true,
                    "retrieve": true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": 'datatablescript/server_processing_sample.php',
                        "type": 'GET',
                        "data": function ( d ) {
                            d.operate="select";
                        }
                    },
                    "deferRender": true,
                    "searching": true
                });
            }

            /* select sample in model */
            var samuuidinput='#ddl_samuuid';
            $(samuuidinput).on('click',function(){
                $(this).trigger('blur');
                setTimeout(function(){
                    sampleddtable.columns.adjust().responsive.recalc();
                },190);

                $(this).on('keydown',function(){
                    $(this).trigger('blur');
                });
            });

            $('#sample_modal').on('click','button[data-dismiss="modal"]',function(){
                var sid=$(sampletable).find('tbody tr td:first-child input:checked').val();
                $(samuuidinput).val(sid);
            });

            /* Patient Data Table in model */
            var patienttable=$('#patienttable');
            if (!($.fn.DataTable.isDataTable(patienttable))){
                var patientdatatable=$(patienttable).DataTable( {
                    "responsive": true,
                    "retrieve": true,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": 'datatablescript/server_processing_patient.php',
                        "type": 'GET',
                        "data":function ( d ) {
                            d.operate="select";
                        }
                    },
                    "deferRender": true,
                    "searching": true
                });
            }

            /* select patient in model */
            var patientidinput='#ddl_patientid';
            $(patientidinput).on('click',function () {
                $(this).trigger('blur');
                setTimeout(function(){
                    patientdatatable.columns.adjust().responsive.recalc();
                },190);

                $(this).on('keydown',function(e){
                    $(this).trigger('blur');
                });
            });

            /* confirm selected patient in model */
            $('#patient_modal').on('click','button[data-dismiss="modal"]',function(){
                var pid=$(patienttable).find('tbody tr td:first-child input:checked').val();
                $(patientidinput).val(pid);
            });

            /* submit create or update enroll study */
            $('input[type="submit"]').on('click',function(e){
                e.preventDefault();
                var inputs=$("form").serialize();
                $.ajax({
                    async: true,
                    cache: true,
                    type: "POST",
                    url: "sendcreateupdateenrollstudyid.php",
                    data: inputs,
                    success: function(result,status,xhr){
                        var str="<div class=\"col-md-12 text-center alert-dismissible alert "+result.class+" fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>"+result.stat+"</strong><br>"+result.msg+"</div></div>";
                        $('#addupdateenrollstudymsg').empty().append(str);
                        if(result.goto!==""){
                            window.location.href=result.goto;
                        }
                    },
                    error: function(xhr,status,error){
                        var str="<div class=\"col-md-12 text-center alert-dismissible alert alert-danger fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>Fail! </strong><br>Please contact developer.</div></div>";
                        $('#addupdateenrollstudymsg').empty().append(str);
                    },
                    dataType: "json"
                });
            });

        });
    </script>
</body>
</html>

