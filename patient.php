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

$item=$pid=$exist=null;
if(strpos($operate,'edit') !== FALSE){
    $item=(!empty($_GET['item']))?EscapeString::escape($_GET['item']):null;
    $pid=(!empty($_GET['pid']))?EscapeString::escape($_GET['pid']):null;
    $exist=(!empty($_GET['exist']))?EscapeString::escape($_GET['exist']):null;
}

if(strpos($operate,'view') !== FALSE){
    $pid=(!empty($_GET['pid']))?EscapeString::escape($_GET['pid']):null;
}

if($pid!==null) {
    require_once("class/dbencryt.inc");
    require_once("dbsample.inc");
    $db = new mysqli(Encryption::decrypt($hostname), Encryption::decrypt($username), Encryption::decrypt($password), Encryption::decrypt($dbname_sample));
    if ($db->connect_error) {
        die('Unable to connect to database: ' . $db->connect_error);
    }

    $localpid=$datacontriclini=$datacontricenter=$ageenroll=$agefirstvisit=$relapseenroll=$relapsefirstvisit=$agediag=$yrdiag=$dysgonad
        =$sex=$race=$ethical=$vitalstatus=$cog=$figo=$ajcc=$igcccg=$notes=$histology='';

    $sql = "SELECT Local_Patient_ID,Data_Contributor_Clinical_Trial_Group,Data_Contributor_Center,Age_At_Enrollment_In_Days,Age_At_First_Visit_In_Days,
            Relapsed_At_Enrollment,Relapsed_At_First_Visit,Age_At_Diagnosis_In_Days,Year_Of_Diagnosis,Dysgenetic_Gonad,
            Sex,Race,Ethnic,Vital_Status,COG_Stage,FIGO_Stage,AJCC_Stage,IGCCCG_RiskGroup, Note, 
            (SELECT Overall_Histology_Legacy FROM Histology WHERE His_Patient_ID = ?) FROM Patient WHERE Patient_ID=? AND isDelete=0";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('ss', $pid,$pid);
        $result->execute();
        $result->bind_result($localpid,$datacontriclini,$datacontricenter,$ageenroll,$agefirstvisit,$relapseenroll,$relapsefirstvisit,
            $agediag,$yrdiag,$dysgonad,$sex,$race,$ethical,$vitalstatus,$cog,$figo,$ajcc,$igcccg,$notes,$histology);
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
    <!-- Modernizr JS -->
    <script src="js/modernizr-2.6.2.min.js"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
        #seemore{
            margin-left: 37.5%;
            cursor: pointer;
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
                Patient
            </li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">
            <?php
                if ($operate==='edit' && $exist==="1"){
                    echo "<div class=\"row\">                
                                    <div class=\"col-md-12 text-center\">
                                        <div class=\"alert alert-info\">
                                            <i class=\"icon-circle-cross\" style=\"float:right;\"></i>
                                            <strong>Info Message!</strong>";
                    if($exist!==''){
                        echo "<br>This patient has already been in database according to your Local Patient ID input.".
                            " You can edit it in the following fields.";
                    }
                    echo "</div></div></div>";
                }
            ?>
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
                        Patient Information
                    </h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="form-group">
                        <form method="post" action="sendcreateupdatepatient.php">
                            <div class="alert alert-info">
                            <?php
                                if((strpos($operate,'edit') !== FALSE || strpos($operate,'view') !== FALSE) && ($pid !=='')){
                                    echo "<label for='ddl_pid'>System Patient ID</label>
                                            <input id='ddl_pid' name='pid' class='form-control' 
                                            type='text' value ='".$pid."' readonly/><br>";
                                }
                            ?>
                                <label for="ddl_localpatientid">Local Patient ID (max 30 characters)
                                    <i class="fa fa-question-circle"
                                       title="Patient ID in your local system">
                                    </i>
                                    <em><span class="fa fa-asterisk fa-fw"></span></em>
                                </label>
                                <input type="text" class="form-control" id="ddl_localpatientid" name="localpatientid"
                                       required pattern=".{1,30}" placeholder="e.g. G000001"/><br>

                                <label for="ddl_sex">Sex</label>
                                <select id="ddl_sex" name="sex" class="form-control">
                                    <option value="">Please select ...</option>
                                    <option value="1">Female</option>
                                    <option value="2">Male</option>
                                    <option value="3">Undifferntiated</option>
                                    <option value="99">Unknown</option>
                                </select><br>

                                <label for="ddl_race">Race</label>
                                <select id="ddl_race" name="race" class="form-control">
                                    <option value="">Please select ...</option>
                                    <option value="1">American Indian or Alaska Native</option>
                                    <option value="2">Asian</option>
                                    <option value="3">Black or African American</option>
                                    <option value="4">Native Hawaiian or Other Pacific Islander</option>
                                    <option value="5">White</option>
                                    <option value="6">Multiple</option>
                                    <option value="98">Other</option>
                                    <option value="99">Unknown</option>
                                </select><br>

                                <label for="ddl_ethic">Ethic</label>
                                <select id="ddl_ethic" name="ethic" class="form-control">
                                    <option value="">Please select ...</option>
                                    <option value="1">Hispanic or Latino</option>
                                    <option value="2">Not Hispanic Latino</option>
                                    <option value="99">Unknown</option>
                                </select><br>

                                <label for="ddl_agediag">Age At Diagnosis ( Days )
                                    <i class="fa fa-question-circle"
                                       title="Age at diagnosis in days relative to date of birth (DOB).">
                                    </i>
                                </label>
                                <input id="ddl_agediag" name="agediag" class="form-control" type="number"
                                       min="0" step="1" max="73000" value=""/><br>

                                <label for="ddl_yrdiag">Year Of Diagnosis ( A.D. )
                                    <i class="fa fa-question-circle"
                                       title="Year at diagnosis.">
                                    </i>
                                </label>
                                <input id="ddl_yrdiag" name="yrdiag" class="form-control" type="number"
                                       min="1" step="1" max="9999" placeholder="e.g. 1976"/><br>

                                <label for="ddl_death">Vital Status
                                    <i class="fa fa-question-circle"
                                       title="Age at diagnosis in days relative to date of birth (DOB).">
                                    </i>
                                </label>
                                <select id="ddl_death" name="death" class="form-control">
                                    <option value="">Please select ...</option>
                                    <option value="0">Alive</option>
                                    <option value="1">Death</option>
                                    <option value="99">Unknown</option>
                                </select><br>


                                <label for="ddl_histology">Overall Histology
                                    <i class="fa fa-question-circle"
                                       title="Overall histology of a patient.">
                                    </i>
                                </label>
                                <select id="ddl_histology" name="histology" class="form-control">
                                    <option value="">Please select ...</option>
                                    <option value="1">Seminoma</option>
                                    <option value="2">Dysgerminoma</option>
                                    <option value="3">Germinoma</option>
                                    <option value="4">Embryonal Carcinoma</option>
                                    <option value="5">Yolk Sac Tumor</option>
                                    <option value="6">Choriocarcinoma</option>
                                    <option value="7">Mature Teratoma</option>
                                    <option value="8">Immature Teratoma</option>
                                    <option value="9">Teratoma, NOS</option>
                                    <option value="10">Gonadoblastoma</option>
                                    <option value="11">Mixed Germ Cell Tumor</option>
                                    <option value="98">Other</option>
                                    <option value="99">Unknown</option>
                                </select><br>

                                <div id="detail" style="display: none">
                                    <hr>

                                    <label for="ddl_contrigrp">Data Contributor  ( Clinical Trial Group )
                                        <i class="fa fa-question-circle"
                                           title="The ID of the contributor consortium.">
                                        </i>
                                    </label>
                                    <select id="ddl_contrigrp" name="contrigrp" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">COG, Childrens Oncology Group</option>
                                        <option value="2">CCLG, Children’s Cancer and Leukaemia Group</option>
                                        <option value="3">MRC, Medical Research Concil</option>
                                        <option value="4">NRG Oncology</option>
                                        <option value="98">Other</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <label for="ddl_contricenter">Data Contributor  ( Center )
                                        <i class="fa fa-question-circle"
                                           title="The ID of the contributor institute.">
                                        </i>
                                    </label>
                                    <select id="ddl_contricenter" name="contricenter" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">DFCI, Dana-Farber Cancer Institute</option>
                                        <option value="2">Barretos Cancer Hosptial</option>
                                        <option value="3">UTSW, UT Southwestern Medical Center</option>
                                        <option value="4">Gustave Roussy Institute of Oncology</option>
                                        <option value="98">Other</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <hr>

                                    <label for="ddl_ageenroll">Age At Enrollment  ( Days )
                                        <i class="fa fa-question-circle"
                                           title="Age at enrollment in clinical trial in days relative to date of birth (DOB).">
                                        </i>
                                    </label>
                                    <input id="ddl_ageenroll" name="ageenroll" class="form-control" type="number"
                                           min="0" step="1" max="73000" value=""/><br>

                                    <label for="ddl_relapseenroll">Relapse At Enrollment
                                        <i class="fa fa-question-circle"
                                           title="Whether the patient had relapsed tumor or not at enrollment.">
                                        </i>
                                    </label>
                                    <select id="ddl_relapseenroll" name="relapseenroll" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <hr>

                                    <label for="ddl_agevisit">Age At First Visit  ( Days )
                                        <i class="fa fa-question-circle"
                                           title="Age at first visit to the cancer insititure / center in days relative to date of birth (DOB).">
                                        </i>
                                    </label>
                                    <input id="ddl_agevisit" name="agevisit" class="form-control" type="number"
                                           min="0" step="1" max="73000" value=""/><br>

                                    <label for="ddl_relapsevisit">Relapse At First Visit
                                        <i class="fa fa-question-circle"
                                           title="Whether the patient had relapsed tumor or not at first visit.">
                                        </i>
                                    </label>
                                    <select id="ddl_relapsevisit" name="relapsevisit" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <hr>

                                    <label for="ddl_dysgonad">Dysgenetic Gonad
                                        <i class="fa fa-question-circle"
                                           title="Whether the gonad of the patient is dysgentic or not.">
                                        </i>
                                    </label>
                                    <select id="ddl_dysgonad" name="dysgonad" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select><br>

                                    <hr>

                                    <label for="ddl_cog">COG Stage</label>
                                    <select id="ddl_cog" name="cog" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">I</option>
                                        <option value="2">II</option>
                                        <option value="3">III</option>
                                        <option value="4">IV</option>
                                        <option value="5">Staging procedure is incomplete</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <label for="ddl_figo">FIGO Stage</label>
                                    <select id="ddl_figo" name="figo" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">I</option>
                                        <option value="2">IA</option>
                                        <option value="3">IB</option>
                                        <option value="4">IC1</option>
                                        <option value="5">IC2</option>
                                        <option value="6">IC3</option>
                                        <option value="7">II</option>
                                        <option value="8">IIA</option>
                                        <option value="9">IIB</option>
                                        <option value="10">III</option>
                                        <option value="11">IIIA1</option>
                                        <option value="12">IIIA2</option>
                                        <option value="13">IIIB</option>
                                        <option value="14">IIIC</option>
                                        <option value="15">IVA</option>
                                        <option value="16">IVB</option>
                                        <option value="17">Staging procedure is incomplete</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <label for="ddl_ajcc">AJCC Stage</label>
                                    <select id="ddl_ajcc" name="ajcc" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">0</option>
                                        <option value="2">I</option>
                                        <option value="3">IA</option>
                                        <option value="4">IB</option>
                                        <option value="5">IS</option>
                                        <option value="6">II</option>
                                        <option value="7">IIA</option>
                                        <option value="8">IIB</option>
                                        <option value="9">IIC</option>
                                        <option value="10">III</option>
                                        <option value="11">IIIA</option>
                                        <option value="12">IIIB</option>
                                        <option value="13">IIIC</option>
                                        <option value="14">Staging procedure is incomplete</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <label for="ddl_igcccg">IGCCCG RisGroup</label>
                                    <select id="ddl_igcccg" name="igcccg" class="form-control">
                                        <option value="">Please select ...</option>
                                        <option value="1">Good</option>
                                        <option value="2">Intermediate</option>
                                        <option value="3">Poor</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <hr>

                                    <label>Notes</label>
                                    <textarea id="txt_notes" class="form-control" name="notes"
                                              placeholder="Enter Your Notes (max 100 characters)"
                                              rows="5" maxlength="100"></textarea>

                                </div>
                                <br/>
                                <div id="seemore" class="btn btn-info">See More Variables</div>
                                <br/>
                                <input type="submit" class="btn btn-primary" value="Submit"/>
                                <sub><em><i class="fa fa-asterisk fa-fw"></i></em> as required field</sub>
                            </div>
                        </form>
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
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function(){
            /* menu animation*/
            $(".menu-1 #navmgn").addClass("active");
            $(".menu-1 #navpat a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();

            /* see more and see less in method1 block*/
            $('#seemore').on('click',function(){
                $('#detail').toggle('fast','linear');
                if($(this).text()=='See More Variables'){
                    $(this).text('Less ..');
                }else{
                    $(this).text('See More Variables');
                }
            });

            $('#ddl_localpatientid').val('<?php echo $localpid;?>');
            $('#ddl_sex').find('option[value="<?php echo $sex;?>"]').prop('selected',true);
            $('#ddl_race').find('option[value="<?php echo $race;?>"]').prop('selected',true);
            $('#ddl_ethic').find('option[value="<?php echo $ethical;?>"]').prop('selected',true);
            $('#ddl_death').find('option[value="<?php echo $vitalstatus;?>"]').prop('selected',true);
            $('#ddl_histology').find('option[value="<?php echo $histology;?>"]').prop('selected',true);
            $('#ddl_contrigrp').find('option[value="<?php echo $datacontriclini;?>"]').prop('selected',true);
            $('#ddl_contricenter').find('option[value="<?php echo $datacontricenter;?>"]').prop('selected',true);
            $('#ddl_agediag').val('<?php echo $agediag;?>');
            $('#ddl_yrdiag').val('<?php echo $yrdiag;?>');
            $('#ddl_ageenroll').val('<?php echo $ageenroll;?>');
            $('#ddl_relapseenroll').find('option[value="<?php echo $relapseenroll;?>"]').prop('selected',true);
            $('#ddl_agevisit').val('<?php echo $agefirstvisit;?>');
            $('#ddl_relapsevisit').find('option[value="<?php echo $relapsefirstvisit;?>"]').prop('selected',true);
            $('#ddl_dysgonad').find('option[value="<?php echo $dysgonad;?>"]').prop('selected',true);
            $('#ddl_cog').val('<?php echo $cog;?>');
            $('#ddl_figo').val('<?php echo $figo;?>');
            $('#ddl_ajcc').val('<?php echo $ajcc;?>');
            $('#ddl_igcccg').val('<?php echo $igcccg;?>');
            $('#txt_notes').val('<?php echo $notes;?>');

        });
    </script>
</body>
</html>

