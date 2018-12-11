<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
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

    <link rel="icon" href="images/utsw_logo_icon.jpg"/>
    <!-- Animate.css -->
    <link rel="stylesheet" href="css/animate.css"/>
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- FONTAWESOME STYLE CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"/>
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="css/bootstrap.css"/>
    <!-- JQuery UI CSS -->
    <link href="css/jquery-ui.css" rel="stylesheet"/>
    <!-- DataTable -->
    <link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="css/responsive.dataTables.min.css"/>
    <!-- FILE INPUT CSS -->
    <link href="css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="css/style1.css" rel="stylesheet"/>
    <link href="css/style.css" rel="stylesheet"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
        #method1 span,#method2 span{
            font-size: 2.3rem;
            color: #000000;
            cursor: pointer;
        }
        #method1 input,#method2 input{
            margin-top:2%;
        }
        #seemore{
            margin-left: 37.5%;
            cursor: pointer;
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

    <div id="fh5co-services">
        <div class="container">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8 text-justify" style="color:black;border: #9a9a9b solid 1px; padding: 20px;">
                    <div class="text-center" style="font-size: 1.2em;font-weight: 600;">
                        How to print out sample barcode(s) automatically after creating new sample(s) ?
                        <div id="instruction" class="text-justify" style="display: none;font-size: 1em;font-weight: 500;padding:20px;">
                            <br>
                            If you would like to use <b> (BarTender Software- Enterprise Automation Edition) </b>
                            to print out the barcodes, please <a href = "mailto: shinyi.lin@utsouthwestern.edu">Send Email</a> to us
                            to ask for the database account and password, and follow
                            <a href="./instruction/BarTender/connectBarTenderToDatabase.php" target="_blank">
                            Connect BarTender Software to SMS Database Instruction </a>.
                            Then, you will be able to print out the sample barcode(s) after creating new sample(s) in this web page.
                        </div>
                        <br><br>
                        <a id="instructionBottom" class="btn btn-default btn-xs" style="font-size: 0.8em;font-weight: 500;">Read More ..</a>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8 text-center">
                    <h2> Select an input method</h2>
                </div>
                <div class="col-md-2"></div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="form-group">
                        <form method="post" action="sendcreatesample_Single_and_CSV.php">
                            <div id="method1" class="radio">
                                <label class="radio-inline" for="method1input"></label>
                                <input id="method1input" type="radio" name="method" value="1">
                                <span> <b>( Method 1 ) Input Single Sample Information </b> </span>
                                <p>All samples should have same attribute values.</p>
                            </div>

                            <br>
                            <div class="alert alert-info">

                                <div class="border-effect">
                                    <h3 class="text-center"> SOURCE ID </h3>
                                    <label for="ddl_studyname">Source ( Study ) Name
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

                                    <label for="ddl_studysampleid">Source ( Study ) Sample ID (max 30 characters)
                                        <i class="fa fa-question-circle"
                                           title="Sample ID in data source">
                                        </i>
                                    </label>
                                    <input type="text" class="form-control" id="ddl_studysampleid" name="studysampleid"
                                           maxlength="30" placeholder="e.g. T95-1C3"/><br>

                                    <label for="ddl_studypatient">Source ( Study ) Patient ID (max 30 characters)
                                        <i class="fa fa-question-circle"
                                           title="Patient ID in data source">
                                        </i>
                                    </label>
                                    <input type="text" class="form-control" id="ddl_studypatient" name="studypatientid"
                                           maxlength="30" placeholder="e.g. XNL24G"/>
                                </div>

                                <hr>

                                <div class="border-effect">
                                    <h3 class="text-center"> LOCAL ID </h3>
                                    <label for="ddl_localsampleid">Local Sample ID (max 30 characters)
                                        <i class="fa fa-question-circle"
                                           title="Sample ID in your local system">
                                        </i>
                                        <em><span class="fa fa-asterisk fa-fw"></span></em>
                                    </label>
                                    <input type="text" class="form-control" id="ddl_localsampleid" name="localsampleid"
                                           required pattern=".{1,30}" placeholder="e.g. G000001-T1"/><br>

                                    <label for="ddl_localpatientid">Local Patient ID (max 30 characters)
                                        <i class="fa fa-question-circle"
                                           title="Patient ID in your local system">
                                        </i>
                                        <em><span class="fa fa-asterisk fa-fw"></span></em>
                                    </label>
                                    <input type="text" class="form-control" id="ddl_localpatientid" name="localpatientid"
                                           required pattern=".{1,30}" placeholder="e.g. G000001"/><br>
                                </div>

                                <hr>

                                <div class="border-effect">
                                    <h3 class="text-center"> SYSTEM ID</h3>
                                    <label for="ddl_patientid">System Patient ID (max 7 characters)
                                        <i class="fa fa-question-circle"
                                           title="You can link the sample to the System Patient ID which has already existed in the sample management system.">
                                        </i>
                                    </label>
                                    <input type="text" class="form-control" id="ddl_patientid" name="patientid"
                                           pattern=".{0,7}" data-toggle="modal" data-target="#patient_modal"/><br>
                                </div>

                                <div id="detail" style="display: none">
                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> SAMPLE </h3>
                                        <label for="ddl_sampleclass">Sample Class (for labeling) </label>
                                        <select class="form-control" id="ddl_sampleclass" name="sampleclass">
                                            <option label="Please select ..." value="99">Please select ...</option>
                                            <option value="01">DNA</option>
                                            <option value="02">RNA</option>
                                            <option value="03">Protein</option>
                                            <option value="04">Tissue</option>
                                            <option value="05">Cell</option>
                                            <option value="06">Fluid</option>
                                            <option value="98">Other</option>
                                            <option value="99">Unknown</option>
                                        </select><br>

                                        <label for="ddl_sampletype">Sample Type (for labeling)
                                            <i class="fa fa-question-circle"
                                               title="You must select Sample Class first before selecting Sample Type.">
                                            </i>
                                        </label>
                                        <select class="form-control" id="ddl_sampletype" name="sampletype">
                                            <option label="Please select ..." value="">Please select ...</option>
                                            <optgroup label="DNA">
                                                <option value="11">DNA, Whole Genome Amplified DNA</option>
                                                <option value="14">DNA, Genomic DNA</option>
                                                <option value="12">DNA, cDNA</option>
                                                <option value="13">DNA, ctDNA</option>
                                                <option value="19">DNA, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="RNA">
                                                <option value="21">RNA, poly-A enriched</option>
                                                <option value="22">RNA, Nuclear</option>
                                                <option value="23">RNA, Cytoplasmic</option>
                                                <option value="24">RNA, Total RNA</option>
                                                <option value="29">RNA, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="Protein">
                                                <option value="39">Protein, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="Tissue">
                                                <option value="41">Tissue, Tissue Block</option>
                                                <option value="42">Tissue, Tissue Slide</option>
                                                <option value="43">Tissue, Microdissected</option>
                                                <option value="49">Tissue, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="Cell">
                                                <option value="51">Cell, Pleural Effusion All Cells</option>
                                                <option value="52">Cell, Pleural Effusion White Blood Cells</option>
                                                <option value="53">Cell, Peripheral Blood All Cells</option>
                                                <option value="54">Cell, Peripheral Blood White Cells</option>
                                                <option value="55">Cell, Peripheral Blood Mononuclear Cell (PBMC)</option>
                                                <option value="56">Cell, Cell Pellet</option>
                                                <option value="59">Cell, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="Fluid">
                                                <option value="61">Fluid, Whole Blood</option>
                                                <option value="62">Fluid, Plasma</option>
                                                <option value="63">Fluid, Serum</option>
                                                <option value="64">Fluid, Bone Marrow</option>
                                                <option value="65">Fluid, Urine</option>
                                                <option value="66">Fluid, Saliva</option>
                                                <option value="67">Fluid, Cerebrospinal Fluid</option>
                                                <option value="68">Fluid, Pleural Fluid</option>
                                                <option value="69">Fluid, Ascites</option>
                                                <option value="610">Fluid, Lavage</option>
                                                <option value="611">Fluid, Body Cavity Fluid</option>
                                                <option value="612">Fluid, Milk</option>
                                                <option value="613">Fluid, Vitreous Fluid</option>
                                                <option value="614">Fluid, Gastric Fluid</option>
                                                <option value="615">Fluid, Amniotic Fluid</option>
                                                <option value="616">Fluid, Bile</option>
                                                <option value="617">Fluid, Synovial Fluid</option>
                                                <option value="618">Fuild, Sweat</option>
                                                <option value="619">Fuild, Feces</option>
                                                <option value="620">Fuild, Buffy Coat</option>
                                                <option value="621">Fuild, Sputum</option>
                                                <option value="699">Fluid, Not Specified</option>
                                            </optgroup>
                                            <optgroup label="Other">
                                                <option value="98">Other</option>
                                            </optgroup>
                                            <optgroup label="Unknown">
                                                <option value="99">Unknown</option>
                                            </optgroup>
                                        </select><br>

                                        <label for="ddl_pathological">Pathological Status (for labeling)</label>
                                        <select class="form-control" id="ddl_pathological" name="pathological">
                                            <option value="99">Please select ...</option>
                                            <option value="01">Primary Solid Tumor</option>
                                            <option value="02">Recurrent Solid Tumor</option>
                                            <option value="03">Metastatic Tumor</option>
                                            <option value="10">Blood Derived</option>
                                            <option value="11">Solid Tissue Normal</option>
                                            <option value="98">Other</option>
                                            <option value="99">Unknown</option>
                                        </select><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> STORAGE </h3>
                                        <label for="ddl_room">Storage Room</label>
                                        <select class="form-control" id="ddl_room" name="room">
                                            <option label="Please select ..." value="99">Please select ...</option>
                                            <option value="1">Amatruda Lab (K4.110/K4.112)</option>
                                            <option value="2">Cold Room (K4.118D)</option>
                                        </select><br>

                                        <label for="ddl_cabinettype">Cabinet Type</label>
                                        <select class="form-control" id="ddl_cabinettype" name="cabinettype">
                                            <option value="99">Please select ...</option>
                                            <option value="1">Freezer</option>
                                            <option value="2">Storage Cabinet</option>
                                        </select><br>

                                        <label for="ddl_cabinettemp">Cabinet Temperature</label>
                                        <select class="form-control" id="ddl_cabinettemp" name="cabinettemp">
                                            <option value="99">Please select ...</option>
                                            <option value="1">-20 &#8451;</option>
                                            <option value="2">-80 &#8451;</option>
                                        </select><br>

                                        <label for="ddl_cabinetnum">Cabinet Number</label>
                                        <input type="number" class="form-control" id="ddl_cabinetnum" name="cabinetnum" min="1" max="99"/><br>

                                        <label for="ddl_shelfnum">Shelf Number</label>
                                        <input type="number" class="form-control" id="ddl_shelfnum" name="shelfnum" min="1" max="99"/><br>

                                        <label for="ddl_racknum">Rack Number</label>
                                        <input type="number" class="form-control" id="ddl_racknum" name="racknum" min="1" max="99"/><br>

                                        <label for="ddl_boxnum">Box Number</label>
                                        <input type="number" class="form-control" id="ddl_boxnum" name="boxnum" min="1" max="99"/><br>

                                        <label for="ddl_posnum">Position Number</label>
                                        <input type="number" class="form-control" id="ddl_posnum" name="posnum" min="1" max="100"/><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> AMOUNT </h3>
                                        <label for="ddl_quantitynum">Amount Value</label>
                                        <input type="number" class="form-control" id="ddl_quantitynum" name="quantitynum"
                                               step="0.00001" max="99999.99999"/><br>

                                        <label for="ddl_quantityunit">Amount Unit</label>
                                        <select class="form-control" id="ddl_quantityunit" name="quantityunit">
                                            <option value="">Please select ...</option>
                                            <option value="1">μg</option>
                                            <option value="2">mg</option>
                                            <option value="3">g</option>
                                            <option value="4">μL</option>
                                            <option value="5">mL</option>
                                            <option value="6">scrolls</option>
                                            <option value="7">cassettes</option>
                                            <option value="8">slides</option>
                                            <option value="9">blocks</option>
                                            <option value="10">unspecified</option>
                                        </select><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> CONCENTRATION </h3>
                                        <label for="ddl_concennum">Concentration Value</label>
                                        <input type="number" class="form-control" id="ddl_concennum" name="concennum"
                                               step="0.00001" max="99999.99999"/><br>

                                        <label for="ddl_concenunit">Concentration Unit (max 30 characters)</label>
                                        <input type="text" class="form-control" id="ddl_concenunit" name="concenunit"
                                               maxlength="30" value="ng/μL"/><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> SPECIMEN </h3>
                                        <label>Specimen Type
                                            <i class="fa fa-question-circle"
                                               title="Describe how the speciemen was processed. Choose all that apply.">
                                            </i></label>
                                        <div class="radio">
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="1">Flash Frozen
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="2">Frozen with OCT
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="3">FFPE
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="4">Fresh
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="98">Other
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="specimentype" value="99">Unknown
                                            </label>
                                        </div><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> ANATOMY </h3>
                                        <label for="ddl_anatomicalsite">Anatomical Site
                                            <i class="fa fa-question-circle"
                                               title="Anatomical site of the human body where the sample was obtained.">
                                            </i></label>
                                        <select class="form-control" id="ddl_anatomicalsite" name="anatomicalsite">
                                            <option value="">Please select ...</option>
                                            <optgroup label="Central Nervous System">
                                                <option value="1.a">Suprasellar/Neurohypophyseal</option>
                                                <option value="1.b">Pineal</option>
                                                <option value="1.c">Bifocal (Suprasellar/Neurohypophyseal + Pineal)</option>
                                                <option value="1.d">Thalamic</option>
                                                <option value="1.e.a">Cerebral Cortex: Frontal</option>
                                                <option value="1.e.b">Cerebral Cortex: Temporal</option>
                                                <option value="1.e.c">Cerebral Cortex: Parietal</option>
                                                <option value="1.e.d">Cerebral Cortex: Occipital</option>
                                                <option value="1.e.y">Cerebral Cortex: Other</option>
                                                <option value="1.f">Spinal</option>
                                                <option value="1.y">Other</option>
                                            </optgroup>
                                            <option value="2">Head, neck (not CNS)</option>
                                            <option value="3">Liver</option>
                                            <option value="4">Mediastinum</option>
                                            <option value="5">Ovary</option>
                                            <option value="6">Retroperitoneum</option>
                                            <option value="7">Sacrococcygeal</option>
                                            <option value="8">Testis</option>
                                            <option value="9">Vagina (female only)</option>
                                            <option value="98">Other</option>
                                            <option value="99">Unknown</option>
                                        </select><br>

                                        <label for="ddl_anatomicallaterality">Anatomical Laterality</label>
                                        <select class="form-control" id="ddl_anatomicallaterality" name="anatomicallaterality">
                                            <option value="">Please select ...</option>
                                            <option value="1">Left</option>
                                            <option value="2">Right</option>
                                            <option value="3">Bilateral</option>
                                            <option value="99">Unknown</option>
                                        </select><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> SIZE </h3>
                                        <label for="ddl_nucleotidesize">Nucleotide Size (only for DNA or RNA)</label>
                                        <select class="form-control" id="ddl_nucleotidesize" name="nucleotidesize">
                                            <option value="">Please select ...</option>
                                            <option value="1">> 200bp</option>
                                            <option value="2"><= 200bp</option>
                                            <option value="99">Unknown</option>
                                            <option value="100">Not Applicable</option>
                                        </select><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> PROCEDURE </h3>
                                        <label for="ddl_proceduretype">Procedure Type
                                            <i class="fa fa-question-circle"
                                               title="The type of the procedure in which the sample was obtained from human body.">
                                            </i>
                                        </label>
                                        <select class="form-control" id="ddl_proceduretype" name="proceduretype">
                                            <option value="">Please select ...</option>
                                            <option value="1">Biopsy</option>
                                            <option value="2">Surgery</option>
                                            <option value="3">Blood collection</option>
                                            <option value="4">Saliva collection</option>
                                            <option value="5">Skin biopsy</option>
                                            <option value="6">Lumbar puncture</option>
                                            <option value="98">Other</option>
                                            <option value="99">Unknown</option>
                                        </select><br>

                                        <label for="txt_proceduredate">Procedure Date
                                            <i class="fa fa-question-circle"
                                               title="The date on which the sample was obtained from human body.">
                                            </i>
                                        </label>
                                        <input type="date" class="form-control" id="txt_proceduredate" name="proceduredate" placeholder="mm/dd/yyyy"/><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> CONTRIBUTOR </h3>
                                        <label for="ddl_consortium">Sample Contributor Consortium</label>
                                        <select class="form-control" id="ddl_consortium" name="consortium">
                                            <option value="">Please select ...</option>
                                            <option value="1">COG, Children's Oncology Group</option>
                                            <option value="2">CCLG, Children’s Cancer and Leukaemia Group</option>
                                            <option value="3">MRC, Medical Research Concil</option>
                                            <option value="4">NRG Oncology</option>
                                            <option value="98">Other</option>1
                                            <option value="99">Unknown</option>
                                        </select><br>

                                        <label for="ddl_institute">Sample Contributor Institute</label>
                                        <select class="form-control" id="ddl_institute" name="institute">
                                            <option value="">Please select ...</option>
                                            <option value="1">UT Southwestern Medical Center</option>
                                            <option value="2">Biopathology Center</option>
                                            <option value="3">Boston Children’s Hospital</option>
                                            <option value="4">Children's Medical Center at Dallas</option>
                                            <option value="5">Cooperative Human Tissue Network</option>
                                            <option value="6">Erasmus Medical Center</option>
                                            <option value="7">Indiana University</option>
                                            <option value="8">Sant Joan de Déu Barcelona Children’s Hospital</option>
                                            <option value="98">Other (Please specify institute name in the NOTES field)</option>
                                            <option value="99">Unknown</option>
                                        </select><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> SOURCE SAMPLE</h3>
                                        <label for="ddl_hasparent">
                                            Any Parent Sample this sample was derived from? (Parent sample should be created first.)
                                            <i class="fa fa-question-circle"
                                               title="The parent sample is the sample which this sample is derived from.">
                                            </i>
                                        </label>
                                        <select class="form-control" id="ddl_hasparent" name="hasparent">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select><br>

                                        <div id="deriveddatediv" style="display: none;">
                                            <label for="ddl_deriveddate">Date Derive From Source Sample</label>
                                            <input type="date" class="form-control" id="ddl_deriveddate" name="deriveddate"
                                                   placeholder="mm/dd/yyyy" />
                                        </div><br>
                                    </div>

                                    <hr>

                                    <div class="border-effect">
                                        <h3 class="text-center"> NOTE </h3>
                                        <label>Notes</label>
                                        <textarea id="txt_notes" class="form-control" name="notes"
                                                  placeholder="Enter Your Notes (max 100 characters)"
                                                  rows="5" maxlength="100"></textarea>
                                    </div>
                                </div>
                                <br/>
                                <div id="seemore" class="btn btn-info">See More Variables</div>
                                <br/>
                                <input type="submit" class="btn btn-primary" value="Submit"/>
                                <sub><em><i class="fa fa-asterisk fa-fw"></i></em> as required field</sub>
                            </div>

                            <?php
                            if(isset($_GET['exist']) && $_GET['exist']==='1'){
                                $uuid=isset($_GET['uuid'])?$_GET['uuid']:'';
                                echo "<div class='alert alert-info' style='margin-top:10px;'>
                                       The sample has already been in database. If you want to edit it, please click on
                                       <a class='btn btn-xs btn-info' href='sample.php?operate=edit&uuid=".$uuid."'>Edit Sample</a>
                                  </div>";
                            }
                            ?>

                            <div id="method2" class="radio">
                                <label class="radio-inline" for="method2input"></label>
                                <input id="method2input" type="radio" name="method" value="2">
                                <span><b>( Method 2 ) Upload Multiple Sample Information Excel File </b> <br>
                                    <small style="padding-left:1.5em;">(xlsx file, <= 1 Mb)</small></span>
                            </div>
                            Download
                            <a href="example/sample_batch_upload_example_V1.xlsx"> Template </a> and
                            <a href="example/Sample_Codebook_V4_20180329.xlsx">Sample Codebook</a>
                            <br>
                            <div class="alert alert-warning">
                                <div class="file-loading">
                                    <!--<input id="inputfile" name="inputfile" type="file" accept="application/vnd.ms-excel;text/csv" size="1">-->
                                    <input id="inputfile" name="inputfile" type="file" accept="application/vnd.openxmlformats-
                            officedocument.spreadsheetml.sheet,application/vnd.ms-excel" size="1">
                                </div>
                                <div id="filesuccessmsg" class="alert alert-success" style="margin-top:10px;display:none"><ul></ul></div>
                                <div id="filefailmsg" class="alert alert-danger" style="margin-top:10px;display:none"><ul></ul></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-2"></div>

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
    </div>
    <!--FOOTER SECTION END-->
    <?php include("footer.php"); ?>

</div>

<div class="gototop js-top">
    <a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
</div>

<!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
<!-- CORE JQUERY  -->
<script src="js/jquery-3.3.1.js"></script>
<script src="js/jquery-ui.js"></script>
<!-- jQuery Easing -->
<script src="js/jquery.easing.1.3.js"></script>
<!-- BOOTSTRAP SCRIPTS  -->
<script src="js/bootstrap.min.js"></script>
<!-- POPPER JS-->
<script src="js/popper.min.js" type="text/javascript"></script>
<!-- Waypoints -->
<script src="js/jquery.waypoints.min.js"></script>
<!-- DataTable -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.responsive.min.js"></script>
<!-- FILE INPUT JS-->
<script src="js/fileinput.js" type="text/javascript"></script>
<!-- Main -->
<script src="js/main.js"></script>
<!-- CUSTOM SCRIPTS  -->
<script src="js/custom.js"></script>

<script type="text/javascript">
    $(function () {
        $(".menu-1 #navcreate").addClass("active");

        /**
         *  Toggle instruction section
         */
        $('#instructionBottom').on('click',function(){
            $('#instruction').slideToggle("slow");
            if($(this).text() == "Read More .."){
                $(this).text("Close");

            }else{
                $(this).text("Read More ..");
            }
        });

        /**
         *  Toggle effect on method1 (single sample input) and method2 (batch sample input) block
         */
        var method1tag=$("#method1");
        var method1detail=$(method1tag).next().next();
        var method2tag=$("#method2");
        var method2detail=$(method2tag).next().next().next();
        $(method1detail).toggle();
        $(method1tag).on('click',function(){
            $(this).find("input").prop("checked",true);
            $(method1detail).toggle("fast","linear");
            if($(method2detail).css('display')=="block"){
                $(method2detail).toggle("fast","linear");
            }
        });

        $(method2tag).on('click',function(){
            $(this).find("input").prop("checked",true);
            $(method2detail).toggle("fast","linear");
            if($(method1detail).css('display')=="block"){
                $(method1detail).toggle("fast","linear");
            }
        });


        /**
         *  Method 1: Single sample input interaction
         */
            // make date work in all browsers
        var dateinput='[type="date"]';
        if ( $(dateinput).prop('type') != 'date' ) {
            $(this).datepicker();
        }

        // see more and see less in method1 (single sample input) block
        $('#seemore').on('click',function(){
            $('#detail').toggle('fast','linear');
            if($(this).text()=='See More Variables'){
                $(this).text('Less ..');
            }else{
                $(this).text('See More Variables');
            }
        });

        // mouse enter and leave section effect in method 1 block
        var border_effect=$('.border-effect');
        var title=$(border_effect).find('h3');
        $(border_effect).css({"border":"0.5px solid #d9edf7", "border-radius":"3px", "padding":"10px", "margin":"0"});
        $(title).css({"color":"#31708f","font-weight":"650"});
        $(border_effect).on('mouseenter',function(){
            $(this).css({"border-color":"#31708f","box-shadow":"3px 3px 8px 3px #31708f"});
        });
        $(border_effect).on('mouseleave',function(){
            $(this).css({"border-color":"#d9edf7","box-shadow":"none"});
        });

        // show or hide the sample type opt based on the selected sample class
        function toggleRelateOptgrpInput(mainopt,relateoptgrp){
            var main=$(mainopt).find('option:selected').text();
            if(main==='Please select ...'){
                $(relateoptgrp).find('optgroup').hide();
                $(relateoptgrp).find('option').show();
            }else{
                $(relateoptgrp).find('option[label=\'Please select ...\']').hide();
                $(relateoptgrp).find('optgroup').hide();
                $(relateoptgrp).find('optgroup[label='+main+']').show();
            }
        }
        toggleRelateOptgrpInput('#ddl_sampleclass','#ddl_sampletype');
        $('#ddl_sampleclass').on('change',function(){
            toggleRelateOptgrpInput(this,'#ddl_sampletype');
        });

        // change storage variable values based on the selected sample type
        function changeStorageVar(sampletype){
            var unknowntype=["","43","49","98","99"];
            var tissuetype=["41"];
            var slidetype=["42"];
            // mix
            if(unknowntype.indexOf(sampletype)>=0){
                $('#ddl_room').val('');
                $('#ddl_cabinettype').val('');
                $('#ddl_shelfnum').val('').show().prev('label').show();
                $('#ddl_racknum').val('').show().prev('label').show();
                $('#ddl_posnum').attr('min',1).attr('max',100).val('').show().prev('label').show();
            }// tissue block
            else if(tissuetype.indexOf(sampletype)>=0){
                $('#ddl_room').val('1');
                $('#ddl_cabinettype').val('1');
                $('#ddl_shelfnum').val('').show().prev('label').show();
                $('#ddl_racknum').val('').show().prev('label').show();
                $('#ddl_posnum').val('').hide().prev('label').hide();
            }// tissue slide
            else if(slidetype.indexOf(sampletype)>=0){
                $('#ddl_room').val('2');
                $('#ddl_cabinettype').val('2');
                $('#ddl_shelfnum').val('').hide().prev('label').hide();
                $('#ddl_racknum').val('').hide().prev('label').hide();
                $('#ddl_posnum').attr('min',1).attr('max',100).val('').show().prev('label').show();
            }// tube
            else{
                $('#ddl_room').val('1');
                $('#ddl_cabinettype').val('1');
                $('#ddl_shelfnum').val('').show().prev('label').show();
                $('#ddl_racknum').val('').show().prev('label').show();
                $('#ddl_posnum').attr('min',1).attr('max',81).val('').show().prev('label').show();
            }
            $('#ddl_boxnum').val('')
        }
        var ddl_sampletype=$('#ddl_sampletype');
        changeStorageVar($(ddl_sampletype).val());
        $(ddl_sampletype).on('change',function(){
            changeStorageVar($(this).val());
        });

        // show or hide parent-sample and derived-date inputs based on the selected has-parent input
        $('#ddl_hasparent').on('click',function(){
            var deriveddate=$('#deriveddatediv');
            if($(this).val()==="1"){
                deriveddate.show('fast','linear');
            }else{
                $("#ddl_deriveddate").val("");
                deriveddate.hide('fast','linear');
            }

        });

        // patient DataTable in model
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

        // display modal after clicking on patient input
        var patientinput;
        $('#ddl_patientid').on('click',function () {
            patientinput=$(this);
            $(patientinput).trigger('blur');
            setTimeout(function(){
                patientdatatable.columns.adjust().responsive.recalc();
            },190);

            $(patientinput).on('keydown',function(){
                $(this).trigger('blur');
            });
        });

        // fill the selected patient of model into the patient input */
        $('#patient_modal').find('button[data-dismiss="modal"]').on('click',function(){
            var pid=$(patienttable).find('tbody tr td:first-child input:checked').val();
            $(patientinput).val(pid);
        });

        /**
         *  Method 2: Batch sample input interaction
         */
        // input file upload
        $("#inputfile").fileinput({
            previewFileType: "any",
            previewFileIconSettings: {'xlsx': '<i class="fa fa-file-excel-o text-success"></i>'},
            previewSettings:{office: {width: "213px", height: "160px"}},
            previewSettingsSmall:{office: {width: "100%", height: "160px"}},
            previewZoomButtonIcons:{
                toggleheader: '<i class="fa fa-arrows-v"></i>',
                fullscreen: '<i class="fa fa-arrows"></i>',
                borderless: '<i class="fa fa-expand"></i>',
                close: '<i class="fa fa-times"></i>'
            },
            allowedFileTypes:["office"],
            allowedFileExtensions: ["xlsx"],
            fileTypeSettings: {
                office: function (vType, vName) {
                    return vType.match(/(excel)$/i) ||
                        vName.match(/\.(xlsx?)$/i);
                }
            },
            fileActionSettings:{
                removeClass: 'btn btn-kv btn-default btn-outline-secondary icon-file-subtract',
                zoomClass: 'hidden'},
            previewClass: "bg-warning",
            browseClass: "btn btn-success",
            browseLabel: "Search File",
            browseIcon: "<i class=\"icon-search\"></i> ",
            removeClass: "btn btn-danger",
            removeLabel: "Delete",
            removeIcon: "<i class=\"icon-bin\"></i> ",
            uploadClass: "btn btn-info",
            uploadLabel: "Upload",
            uploadIcon: "<i class=\"icon-upload2\"></i> ",
            uploadUrl: "sendcreatesample_XLSX.php",
            uploadAsync: true, // boolean whether the batch upload will be asynchronous/in parallel.
            minFileCount:1,
            maxFileCount: 1,
            maxFileSize:1000, // 1 Mb
            uploadExtraData: function() {
                return {
                    method: $("#method2input").val()
                };
            }
        }).on('fileloaded', function(event) {
            $('.kv-file-upload').addClass('icon-file-add');
            $('.kv-file-remove').addClass('icon-file-subtract');
            $('.kv-file-zoom').addClass('icon-zoom-in');
            $('#method2input').prop("checked",true);
        }).on('filedeleted', function(event) {
            $('#method2input').prop("checked",false);
        }).on('filereset', function(event) {
            $('#method2input').prop("checked",false);
            // syn
//        }).on('filebatchuploadsuccess', function(event, data) {
//            console.log(data);
            // asyn
        }).on('fileuploaded', function(event,data,previewId,index) {
            var fname = data.files[index].name;
            var fgoto= data.response['goto'];
            var ferror= data.response['error'];
            if(fgoto != '' && ferror == ''){
                $('#filesuccessmsg').find('ul').append('<li>' + 'Uploaded file '  +  fname + ' successfully.' + '</li>')
                    .fadeIn('slow').fadeOut(1000);
                window.location.href=fgoto;
            }else{
                $('#filefailmsg').find('ul').append('<li>' + 'Uploaded file '  +  fname + ' fail.' + '</li>')
                    .fadeIn('slow').fadeOut(1000);
            }
        });
    });
</script>

</body>
</html>
