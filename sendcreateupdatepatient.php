<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

// Connect to database
require_once("class/dbencryt.inc");
require_once("dbsample.inc");
$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
if($db->connect_error){
    die('Unable to connect to database: ' . $db->connect_error);
}

// Escape special characters in inputs
require_once ("class/EscapeString.inc");
foreach ($_POST as $k => $v){
    $_POST[$k]=EscapeString::escape($v);
}

// get and transform input variables
$pid=!empty($_POST['pid'])?$_POST['pid']:null;
$localpid=!empty($_POST['localpatientid'])?$_POST['localpatientid']:null;
if($localpid === null){
    $db->close();
    die('Local Patient ID can not be empty.');
}
$sex=!empty($_POST['sex'])?$_POST['sex']:null;
$race=!empty($_POST['race'])?$_POST['race']:null;
$ethic=!empty($_POST['ethic'])?$_POST['ethic']:null;
// empty("0") == true
$vitalstatus=(!empty($_POST['death']) or $_POST['death']!=="")?$_POST['death']:null;
$histology=!empty($_POST['histology'])?$_POST['histology']:null;
$contrigrp=!empty($_POST['contrigrp'])?$_POST['contrigrp']:null;
$contricenter=!empty($_POST['contricenter'])?$_POST['contricenter']:null;
$agediag=!empty($_POST['agediag'])?$_POST['agediag']:null;
$yrdiag=!empty($_POST['yrdiag'])?$_POST['yrdiag']:null;
$ageenroll=!empty($_POST['ageenroll'])?$_POST['ageenroll']:null;
$relapseenroll=(isset($_POST['relapseenroll']) && $_POST['relapseenroll']!=="")?$_POST['relapseenroll']:null;
$agevisit=!empty($_POST['agevisit'])?$_POST['agevisit']:null;
$relapsevisit=(isset($_POST['relapsevisit']) && $_POST['relapsevisit']!=="")?$_POST['relapsevisit']:null;
$dysgonad=(isset($_POST['dysgonad']) && $_POST['dysgonad']!=="")?$_POST['dysgonad']:null;
$cog=!empty($_POST['cog'])?$_POST['cog']:null;
$figo=!empty($_POST['figo'])?$_POST['figo']:null;
$ajcc=!empty($_POST['ajcc'])?$_POST['ajcc']:null;
$igcccg=!empty($_POST['igcccg'])?$_POST['igcccg']:null;
$notes=!empty($_POST['notes'])?$_POST['notes']:null;


require_once ('class/ChangeHistory.inc');
require_once ('class/PatientID.inc');
$exist=0;
// if patient id is null, create a new patient
if($pid===null){
    $dbpid=null;
    $sql = "SELECT Patient_ID FROM Patient WHERE Local_Patient_ID=? AND isDelete=0";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('s',$localpid);
        $result->execute();
        $result->bind_result($dbpid);
        $result->fetch();
        $result->close();
    }

    if($dbpid!==null){
        $exist=1;
        $pid=$dbpid;
    }else{
        $pid=PatientID::generatePatientID($db,false);
        $sql = "INSERT INTO Patient (Patient_ID,Local_Patient_ID,Data_Contributor_Clinical_Trial_Group,Data_Contributor_Center,Age_At_Enrollment_In_Days,
            Age_At_First_Visit_In_Days,Relapsed_At_Enrollment,Relapsed_At_First_Visit,Age_At_Diagnosis_In_Days,
            Year_Of_Diagnosis,Dysgenetic_Gonad,Sex,Race,Ethnic,Vital_Status,COG_Stage,FIGO_Stage,
            AJCC_Stage,IGCCCG_RiskGroup, Note, CreateTime) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('ssssssssssssssssssss',$pid,$localpid,$contrigrp,$contricenter,$ageenroll,$agevisit,$relapseenroll,$relapsevisit,$agediag,$yrdiag,
                $dysgonad,$sex,$race,$ethic,$vitalstatus,$cog,$figo,$ajcc,$igcccg,$notes);
            $result->execute();
            $result->close();
        }

        $sql = "INSERT INTO Histology (His_Patient_ID,Overall_Histology_Legacy) VALUES (?,?)";
        if($result = $db->prepare($sql)){
            $result->bind_param('ss',$pid,$histology);
            $result->execute();
            $result->close();
        }
    }
// if patient id is not null, update the patient info
}else{
    $changehistory=new ChangeHistory($db);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Local_Patient_ID",$localpid,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Data_Contributor_Clinical_Trial_Group",$contrigrp,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Data_Contributor_Center",$contricenter,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Age_At_Enrollment_In_Days",$ageenroll,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Age_At_First_Visit_In_Days",$agevisit,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Relapsed_At_Enrollment",$relapseenroll,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Relapsed_At_First_Visit",$relapsevisit,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Age_At_Diagnosis_In_Days",$agediag,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Year_Of_Diagnosis",$yrdiag,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Dysgenetic_Gonad",$dysgonad,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Sex",$sex,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Race",$race,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Ethnic",$ethic,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Vital_Status",$vitalstatus,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"COG_Stage",$cog,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"FIGO_Stage",$figo,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"AJCC_Stage",$ajcc,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"IGCCCG_RiskGroup",$igcccg,$userid);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$pid,"Note",$notes,$userid);
    $changehistory=null;

    $sql = "UPDATE Patient SET Local_Patient_ID=?,Data_Contributor_Clinical_Trial_Group=?,Data_Contributor_Center=?,Age_At_Enrollment_In_Days=?,
            Age_At_First_Visit_In_Days=?,Relapsed_At_Enrollment=?,Relapsed_At_First_Visit=?,Age_At_Diagnosis_In_Days=?,
            Year_Of_Diagnosis=?,Dysgenetic_Gonad=?,Sex=?,Race=?,Ethnic=?,Vital_Status=?,COG_Stage=?,FIGO_Stage=?,
            AJCC_Stage=?,IGCCCG_RiskGroup=?, Note=? WHERE Patient_ID=?";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('ssssssssssssssssssss',$localpid,$contrigrp,$contricenter,$ageenroll,$agevisit,$relapseenroll,$relapsevisit,$agediag,$yrdiag,
            $dysgonad,$sex,$race,$ethic,$vitalstatus,$cog,$figo,$ajcc,$igcccg,$notes,$pid);
        $result->execute();
        $result->close();
    }

    $dbhisid=null;
    $sql = "SELECT Histology_ID FROM Histology WHERE His_Patient_ID = ?";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('s',$pid);
        $result->execute();
        $result->bind_result($dbhisid);
        $result->fetch();
        $result->close();
    }
    // if the overall histology of a patient is null, create a new overall histology record
    if($dbhisid===null){
        $sql = "INSERT INTO Histology (His_Patient_ID,Overall_Histology_Legacy) VALUES (?,?)";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('ss',$pid,$histology);
            $result->execute();
            $result->close();
        }
    // if the overall histology of a patient is NOT null, update the the histology record
    }else{
        $changehistory=new ChangeHistory($db);
        $changehistory->recordChangeHistory("Histology","Histology_ID",$dbhisid,"Overall_Histology_Legacy",$histology,$userid);
        $changehistory=null;
        $sql = "UPDATE Histology SET Overall_Histology_Legacy=? WHERE Histology_ID=?";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('ss',$histology,$dbhisid);
            $result->execute();
            $result->close();
        }
    }
}

if($exist===0){
    header("Location:patientlist.php?operate=view&pid=".$pid);
}else{
    header("Location:patient.php?operate=edit&pid=".$pid."&exist=1");
}


$db->close();


