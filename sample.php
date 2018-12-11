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

$uuid=null;
if(strpos($operate,'edit') !== FALSE){
    $uuid=(!empty($_GET['uuid']))?EscapeString::escape($_GET['uuid']):null;
}

$err=null;
$err=(!empty($_GET['err']))?EscapeString::escape($_GET['err']):null;

if(strpos($operate,'view') !== FALSE){
    $uuid=(!empty($_GET['uuid']))?EscapeString::escape($_GET['uuid']):null;
}

if($uuid!==null) {
    require_once("class/SampleID.inc");
    require_once("class/dbencryt.inc");
    require_once("dbsample.inc");
    $db = new mysqli(Encryption::decrypt($hostname), Encryption::decrypt($username), Encryption::decrypt($password), Encryption::decrypt($dbname_sample));
    if ($db->connect_error) {
        die('Unable to connect to database: ' . $db->connect_error);
    }
    $db->set_charset("utf8");
    $sampleid=$localsampleid=$pid=$consortium=$institute=$proceduretype=$proceduredate=$parentuuid=$deriveddate
            =$pathological=$sampleclass=$sampletype=$room=$cabinettype=$cabinettemp=$cabinetnum=$shelfnum=$racknum=$boxnum
            =$posinum=$quantityval=$quantityunit=$concenval=$concenunit
            =$specimentype=$nucleotidesize=$anatomicalsite=$anatomicallaterality=$notes="";
    $sql = "SELECT Sample_ID,Patient_ID,Local_Sample_ID,Sample_Contributor_Consortium_ID,Sample_Contributor_Institute_ID,
            Procedure_Type,Date_Procedure,Parent_UUID,Date_Derive_From_Parent,Pathological_Status,Sample_Class,Sample_Type,
            Storage_Room,Cabinet_Type,Cabinet_Temperature,Cabinet_Number,Shelf_Number,Rack_Number,Box_Number,Position_Number,
            Quantity_Value,Quantity_Unit,Concentration_Value,Concentration_Unit,
            Specimen_Type,Nucleotide_Size_Group_200,Anatomical_Site,Anatomical_Laterality,
            Notes FROM Sample WHERE UUID=? AND isDelete=0";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('s', $uuid);
        $result->execute();
        $result->bind_result($sampleid,$pid,$localsampleid,$consortium,$institute,$proceduretype,
            $proceduredate,$parentuuid,$deriveddate,$pathological,$sampleclass,$sampletype,
            $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,
            $posinum,$quantityval,$quantityunit,$concenval,$concenunit,
            $specimentype,$nucleotidesize,$anatomicalsite,$anatomicallaterality,$notes);
        $result->fetch();
        $result->close();
    }

    $sampleid=SampleID::getConvertedSampleID($db,$uuid);
    $db->close();
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
    <link rel="stylesheet" href="css/responsive.dataTables.min.css"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="css/style.css" rel="stylesheet"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
        #method1 div{
            font-size: 2.3rem;
            color: #000000;
            cursor: pointer;
        }
        #method1 input{
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

    <header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
    </header>

    <?php include("nav.php"); ?>

    <div class="container-fluid" style="padding: 0;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item col-md-offset-3"><a href="manage.php">Manage</a></li>
            <li class="breadcrumb-item active"><?php if(strpos($operate,'edit') !== FALSE ){echo "Edit";} ?> Sample</li>
        </ol>
    </div>

    <div id="fh5co-services">
        <div class="container">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8 text-center">
                    <h2> Edit Sample</h2>
                </div>
                <div class="col-md-2"></div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="form-group">
                        <form method="post" action="sendupdatesample.php">
                            <div class="alert alert-info">
                                <label for="ddl_sampleuuid">System Sample UUID
                                    <i class="fa fa-question-circle"
                                       title="Universal Unique Sample ID in sample management system">
                                    </i>
                                </label>
                                <input type="text" class="form-control" id="ddl_sampleuuid" name="sampleuuid"
                                       required pattern=".{23}"
                                       value="<?php echo $uuid;?>" readonly/><br>

                                <label for="ddl_sampleid">System Sample ID
                                    <i class="fa fa-question-circle"
                                       title="Sample ID in sample management system">
                                    </i>
                                </label>
                                <input type="text" class="form-control" id="ddl_sampleid" name="sampleid"
                                       required pattern=".{1,30}"
                                       value="<?php echo $sampleid;?>" readonly/><br>

                                <label for="ddl_studylocalsampleid">Local Sample ID (max 30 characters)
                                    <i class="fa fa-question-circle"
                                       title="Sample ID in your local system">
                                    </i>
                                    <em><span class="fa fa-asterisk fa-fw"></span></em>
                                </label>
                                <input type="text" class="form-control" id="ddl_studylocalsampleid"
                                       name="studylocalsampleid" required pattern=".{1,30}"/><br>
                                <hr>

                                <label for="ddl_sampleclass">Sample Class </label>
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

                                <label for="ddl_sampletype">Sample Type </label>
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

                                <label for="ddl_pathological">Pathological Status</label>
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

                                <hr>

                                <div class="form-group has-feedback">
                                    <label class="control-label" for="ddl_pid">System Patient ID
                                        <span style="font-size: small"></span>
                                    </label>
                                    <input type="text" class="col-sm-8 form-control" id="ddl_pid" name="pid"
                                           data-toggle='modal' data-target='#patient_modal'/>
                                    <span class="col-sm-2 form-control-feedback"></span>
                                </div><br/><br/>

                                <div id="detail" style="display: none">
                                    <hr>
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

                                    <hr>
                                        <label for="ddl_quantitynum">Amount Value</label>
                                        <input type="number" class="form-control" id="ddl_quantitynum" name="quantitynum"
                                               step="0.00001" max="99999.99999"/><br>

                                        <label for="ddl_quantityunit">Amount Unit </label>
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
                                    <hr>
                                        <label for="ddl_concennum">Concentration Value</label>
                                        <input type="number" class="form-control" id="ddl_concennum" name="concennum"
                                               step="0.00001" max="99999.99999"/><br>

                                        <label for="ddl_concenunit">Concentration Unit (max 30 characters)</label>
                                        <input type="text" class="form-control" id="ddl_concenunit" name="concenunit"
                                               maxlength="30" value="ng/μl"/><br>
                                    <hr>

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

                                    <hr>

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

                                    <hr>

                                    <label for="ddl_nucleotidesize">Nucleotide Size (only for DNA or RNA)</label>
                                    <select class="form-control" id="ddl_nucleotidesize" name="nucleotidesize">
                                        <option value="">Please select ...</option>
                                        <option value="1">> 200bp</option>
                                        <option value="2"><= 200bp</option>
                                        <option value="99">Unknown</option>
                                    </select><br>

                                    <hr>

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
                                    <input type="date" class="form-control" id="txt_proceduredate" name="proceduredate"
                                           placeholder="mm/dd/yyyy"/><br>

                                    <hr>

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

                                    <hr>

                                    <div class="form-group has-feedback">
                                        <label class="control-label" for="ddl_parentuuid">Parent Sample UUID
                                              <span style="font-size: small"></span>
                                        </label>
                                        <input type="text" class="col-sm-8 form-control" id="ddl_parentuuid" name="parentuuid"
                                               pattern=".{23,23}|.{0}" data-toggle='modal' data-target='#sample_modal'/>
                                        <span class="col-sm-2 form-control-feedback"></span>
                                    </div><br>

                                    <label for="ddl_deriveddate">Date Derive From Parent Sample</label>
                                    <input type="date" class="form-control" id="ddl_deriveddate" name="deriveddate"
                                               placeholder="mm/dd/yyyy" />
                                    <br>

                                    <hr>

                                    <label>Notes</label>
                                    <textarea id="txt_notes" class="form-control" name="notes"
                                              placeholder="Enter Your Notes (max 100 characters)"
                                              rows="5" maxlength="100"></textarea>
                                </div>
                                <br/>
                                <div id="seemore" class="btn btn-info">See More Variables</div>
                                <br/>
                                <input id="updatesamplebtn" type="submit" class="btn btn-primary" value="Submit"/>
                                <sub><em><i class="fa fa-asterisk fa-fw"></i></em> as required field</sub>
                            </div>
                        </form>

                        <div id="patient_modal" class="modal fade" role="dialog">
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

                        <div id="sample_modal" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content text-center">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h2 class="modal-title">Select a Sample</h2>
                                    </div>
                                    <div class="modal-body" style="padding:24px;">
                                        <br/>
                                        <table id="parentsampletable" class="display responsive" style="width: 100%;">
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
                                        <button class="btn btn-primary" data-dismiss="modal" type="button"> Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>
</div>

<!--FOOTER SECTION END-->
<?php include("footer.php"); ?>

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
<!-- Main -->
<script src="js/main.js"></script>
<!-- CUSTOM SCRIPTS  -->
<script src="js/custom.js"></script>

<script>
    $(function () {
        $(".menu-1 #navmgn").addClass("active");

        /* make date work in all browsers */
        var dateinput='[type="date"]';
        if ( $(dateinput).prop('type') != 'date' ) {
            $(this).datepicker();
        }

        /* see more and see less in method1 block*/
        $('#seemore').on('click',function(){
            $('#detail').toggle('fast','linear');
            if($(this).text()=='See More Variables'){
                $(this).text('Less ..');
            }else{
                $(this).text('See More Variables');
            }
        });

        /* show, hide sample type input */
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
        var ddlsampleclass='#ddl_sampleclass';
        var ddlsampletype='#ddl_sampletype';
        toggleRelateOptgrpInput(ddlsampleclass,ddlsampletype);
        $(ddlsampleclass).on('change',function(){
            toggleRelateOptgrpInput(this,ddlsampletype);
        });

        /* fill in sample info into inputs */
        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
        $('#ddl_studylocalsampleid').val('<?php echo $localsampleid;?>');
        var sampleclass=pad(<?php echo $sampleclass;?>,2);
        $(ddlsampleclass).find('option[value="'+sampleclass+'"]').prop('selected',true);
        $(ddlsampletype).find('option[value="<?php echo $sampletype;?>"]').prop('selected',true);
        var pathological=pad(<?php echo $pathological;?>,2);
        $('#ddl_pathological').find('option[value="'+pathological+'"]').prop('selected',true);
        var patientinput='#ddl_pid';
        $(patientinput).val('<?php echo $pid;?>');
        $('#ddl_room').find('option[value="<?php echo $room;?>"]').prop('selected',true);
        $('#ddl_cabinettype').find('option[value="<?php echo $cabinettype;?>"]').prop('selected',true);
        $('#ddl_cabinettemp').find('option[value="<?php echo $cabinettemp;?>"]').prop('selected',true);
        $('#ddl_cabinetnum').val('<?php echo $cabinetnum;?>');
        $('#ddl_shelfnum').val('<?php echo $shelfnum;?>');
        $('#ddl_racknum').val('<?php echo $racknum;?>');
        $('#ddl_boxnum').val('<?php echo $boxnum;?>');
        $('#ddl_posnum').val('<?php echo $posinum;?>');
        $('#ddl_quantitynum').val('<?php echo $quantityval;?>');
        $('#ddl_quantityunit').val('<?php echo $quantityunit;?>');
        $('#ddl_concennum').val('<?php echo $concenval;?>');
        $('#ddl_concenunit').val('<?php echo $concenunit;?>');
        $('input[name="specimentype"][value="<?php echo $specimentype;?>"]').prop('checked',true);
        $('#ddl_anatomicalsite').find('option[value="<?php echo $anatomicalsite;?>"]').prop('selected',true);
        $('#ddl_anatomicallaterality').find('option[value="<?php echo $anatomicallaterality;?>"]').prop('selected',true);
        $('#ddl_nucleotidesize').find('option[value="<?php echo $nucleotidesize;?>"]').prop('selected',true);
        $('#ddl_proceduretype').find('option[value="<?php echo $proceduretype;?>"]').prop('selected',true);
        $('#txt_proceduredate').val('<?php echo $proceduredate;?>');
        $('#ddl_consortium').val('<?php echo $consortium;?>');
        $('#ddl_institute').val('<?php echo $institute;?>');
        var ddlparentuuid='#ddl_parentuuid';
        $(ddlparentuuid).val('<?php echo $parentuuid;?>');
        $('#ddl_deriveddate').val('<?php echo $deriveddate;?>');
        $('#txt_notes').val('<?php echo $notes;?>');

        /* parent sample DataTable in model */
        var parentsampletable=$('#parentsampletable');
        if (!($.fn.DataTable.isDataTable(parentsampletable))){
            var parentddtable=$(parentsampletable).DataTable( {
                'responsive': true,
                'retrieve': true,
                'processing': true,
                'serverSide': true,
                'ajax': {
                    'url': 'datatablescript/server_processing_sample.php',
                    'type': 'GET',
                    'data': function ( d ) {
                        d.operate='select';
                    }
                },
                'deferRender': true,
                'searching': true
            });
        }

        /* select parent sample in model */
        var suuid;
        $(ddlparentuuid).on('click',function(){
            $(this).trigger('blur');
            suuid=$('#ddl_sampleuuid').val();
            setTimeout(function(){
                parentddtable.columns.adjust().responsive.recalc();
            },190);
            $(this).on('keydown',function(){
                $(this).trigger('blur');
            });
        });

        /* confirm selected parent sample in model */
        $('#sample_modal').find('button[data-dismiss="modal"]').on('click',function(){
            var psuuid=$(parentsampletable).find('tbody tr td:first-child input:checked').val();
            if(psuuid===suuid){
                $('#updatesamplebtn').prop('disabled',true);
                $(ddlparentuuid).val('');
                $(ddlparentuuid).prev('label').find('span').text('(Sample UUID can\'t equal to Parent Sample UUID.)');
                $(ddlparentuuid).closest('.form-group').addClass('has-error');
                $(ddlparentuuid).next('span').addClass('icon-cross');
            }else{
                $('#updatesamplebtn').prop('disabled',false);
                $(ddlparentuuid).val(psuuid);
                $(ddlparentuuid).prev('label').find('span').text('');
                $(ddlparentuuid).closest('.form-group').removeClass('has-error');
                $(ddlparentuuid).next('span').removeClass('icon-cross');
            }
        });

        /* patient DataTable in model */
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
        $(patientinput).on('click',function () {
            patientinput=$(this);
            $(patientinput).trigger('blur');
            setTimeout(function(){
                patientdatatable.columns.adjust().responsive.recalc();
            },190);

            $(patientinput).on('keydown',function(){
                $(this).trigger('blur');
            });
        });

        /* confirm selected patient in model */
        $('#patient_modal').find('button[data-dismiss="modal"]').on('click',function(){
            var pid=$(patienttable).find('tbody tr td:first-child input:checked').val();
            $(patientinput).val(pid);
        });
    });
</script>

</body>
</html>