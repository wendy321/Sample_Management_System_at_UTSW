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
require ('class/EscapeString.inc');
require_once ('class/ChangeHistory.inc');
$output=null;
$errormsg="";
$uuids="";
$isfirst=1;
$data=!empty($_POST["data"])?$_POST["data"]:null;
if($data === null){
    $errormsg="You didn't select any patient. Please reselect it.";
}else {
    foreach ($data as $k => $v) {
        $uuid = !empty($v["uuid"]) ? EscapeString::escape($v["uuid"]) : null;
        if ($uuid === null) {
            $errormsg = "The sample uuid can not be empty.";
            break;
        }
        $pid = !empty($v["pid"]) ? EscapeString::escape($v["pid"]) : null;
        if ($pid === null) {
            $errormsg .= "The patient id can not be empty.";
            break;
        }

        /* record uuids for viewing of the page being redirected to */
        if ($isfirst === 1) {
            $uuids .= $uuid;
        } else {
            $uuids .= "," . $uuid;
        }
        $isfirst = 0;


        $sql = "SELECT  FROM Sample WHERE UUID=?";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('s', $uuid);
            $result->execute();
            $result->bind_result($path, $samclass);
            $result->fetch();
            $result->close();

            if (strlen((string)$path) === 1) {
                $path = "0" . ((string)$path);
            }

            if (strlen((string)$samclass) === 1) {
                $samclass = "0" . ((string)$samclass);
            }
        }

        /* Step1: update the newly-linked patient according to the original patient with [A-Z]{7} Patient_ID format
         * Step2: delete the original patient record, if this original Patient_ID has ONLY ONE Sample_ID referred to
         * Step3: update Sample_ID in Sample table because of adding newly-linked patient id
         * Step4: update Patient_ID to newly-linked one in EnrollStudy table
         */
        // record the sample's pathological status and sample class
        $sql = "SELECT Patient_ID, Pathological_Status, Sample_Class FROM Sample WHERE UUID=?";
        $oripid = $path = $samclass = "";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('s', $uuid);
            $result->execute();
            $result->bind_result($oripid, $path, $samclass);
            $result->fetch();
            $result->close();

            if (strlen((string)$path) === 1) {
                $path = "0" . ((string)$path);
            }

            if (strlen((string)$samclass) === 1) {
                $samclass = "0" . ((string)$samclass);
            }
        }

        // Step1: update the newly-linked patient according to the original patient
        // (1) Take original Local_Patient_ID, because it's a required field when creating a new sample
        //      and Local_Patient_ID of newly-linked one is usually UNKNOWN.
        // (2) Take newly-linked Original_Database_Admin
        // (3) Take newly-linked Data Contributor Center
        // (4) Other fields are mainly depends on original patient info, if they both have values
        $sql = "SELECT Local_Patient_ID, Data_Contributor_Clinical_Trial_Group, Age_At_Enrollment_In_Days," .
            " Age_At_First_Visit_In_Days, Relapsed_At_Enrollment, Relapsed_At_First_Visit, Age_At_Diagnosis_In_Days," .
            " Year_Of_Diagnosis, Dysgenetic_Gonad, Sex, Race, Ethnic, Vital_Status, COG_Stage, FIGO_Stage, AJCC_Stage," .
            " IGCCCG_RiskGroup, Note FROM Patient WHERE Patient_ID = ? AND isDelete=0";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('s', $oripid);
            $result->execute();
            $result->bind_result($ori_localpid, $ori_datacontrigrp, $ori_ageenroll, $ori_agefirvis, $ori_relapseenroll,
                $ori_relapsefirvis, $ori_agediag, $ori_yrdiag, $ori_isdysginad, $ori_sex, $ori_race, $ori_ethnic, $ori_vital,
                $ori_cog, $ori_figo, $ori_ajcc, $ori_igcccg, $ori_note);
            $result->fetch();
            $result->close();
        }

        $sql = "SELECT Data_Contributor_Clinical_Trial_Group, Age_At_Enrollment_In_Days," .
            " Age_At_First_Visit_In_Days, Relapsed_At_Enrollment, Relapsed_At_First_Visit, Age_At_Diagnosis_In_Days," .
            " Year_Of_Diagnosis, Dysgenetic_Gonad, Sex, Race, Ethnic, Vital_Status, COG_Stage, FIGO_Stage, AJCC_Stage," .
            " IGCCCG_RiskGroup, Note FROM Patient WHERE Patient_ID = ? AND isDelete=0";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('s', $pid);
            $result->execute();
            $result->bind_result($linked_datacontrigrp, $linked_ageenroll, $linked_agefirvis, $linked_relapseenroll,
                $linked_relapsefirvis, $linked_agediag, $linked_yrdiag, $linked_isdysginad, $linked_sex, $linked_race,
                $linked_ethnic, $linked_vital, $linked_cog, $linked_figo, $linked_ajcc, $linked_igcccg, $linked_note);
            $result->fetch();
            $result->close();
        }

        $final_datacontrigrp = (!empty($ori_datacontrigrp) ? $ori_datacontrigrp : (!empty($linked_datacontrigrp) ? $linked_datacontrigrp : NULL));
        $final_ageenroll = (!empty($ori_ageenroll) ? $ori_ageenroll : (!empty($linked_ageenroll) ? $linked_ageenroll : NULL));
        $final_agefirvis = (!empty($ori_agefirvis) ? $ori_agefirvis : (!empty($linked_agefirvis) ? $linked_agefirvis : NULL));
        $final_relapseenroll = (!empty($ori_relapseenroll) ? $ori_relapseenroll : (!empty($linked_relapseenroll) ? $linked_relapseenroll : NULL));
        $final_relapsefirvis = (!empty($ori_relapsefirvis) ? $ori_relapsefirvis : (!empty($linked_relapsefirvis) ? $linked_relapsefirvis : NULL));
        $final_agediag = (!empty($ori_agediag) ? $ori_agediag : (!empty($linked_agediag) ? $linked_agediag : NULL));
        $final_yrdiag = (!empty($ori_yrdiag) ? $ori_yrdiag : (!empty($linked_yrdiag) ? $linked_yrdiag : NULL));
        $final_isdysginad = (!empty($ori_isdysginad) ? $ori_isdysginad : (!empty($linked_isdysginad) ? $linked_isdysginad : NULL));
        $final_sex = (!empty($ori_sex) ? $ori_sex : (!empty($linked_sex) ? $linked_sex : NULL));
        $final_race = (!empty($ori_race) ? $ori_race : (!empty($linked_race) ? $linked_race : NULL));
        $final_ethnic = (!empty($ori_ethnic) ? $ori_ethnic : (!empty($linked_ethnic) ? $linked_ethnic : NULL));
        $final_vital = (!empty($ori_vital) ? $ori_vital : (!empty($linked_vital) ? $linked_vital : NULL));
        $final_cog = (!empty($ori_cog) ? $ori_cog : (!empty($linked_cog) ? $linked_cog : NULL));
        $final_figo = (!empty($ori_figo) ? $ori_figo : (!empty($linked_figo) ? $linked_figo : NULL));
        $final_ajcc = (!empty($ori_ajcc) ? $ori_ajcc : (!empty($linked_ajcc) ? $linked_ajcc : NULL));
        $final_igcccg = (!empty($ori_igcccg) ? $ori_igcccg : (!empty($linked_igcccg) ? $linked_igcccg : NULL));
        if(!empty($ori_note) && !empty($linked_note)){
            $final_note=$ori_note." ".$linked_note;
        }elseif(empty($ori_note) && !empty($linked_note)){
            $final_note=$linked_note;
        }elseif(!empty($ori_note) && empty($linked_note)){
            $final_note=$ori_note;
        }else{
            $final_note=NULL;
        }
        $sql = "UPDATE Patient SET Local_Patient_ID=?, Data_Contributor_Clinical_Trial_Group=?, Age_At_Enrollment_In_Days=?," .
            "Age_At_First_Visit_In_Days=?, Relapsed_At_Enrollment=?, Relapsed_At_First_Visit=?, Age_At_Diagnosis_In_Days=?,".
            " Year_Of_Diagnosis=?, Dysgenetic_Gonad=?, Sex=?, Race=?, Ethnic=?, Vital_Status=?, COG_Stage=?, FIGO_Stage=?, AJCC_Stage=?," .
            " IGCCCG_RiskGroup=?, Note=? WHERE Patient_ID=?";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('sssssssssssssssssss', $ori_localpid, $final_datacontrigrp, $final_ageenroll, $final_agefirvis,
                $final_relapseenroll, $final_relapsefirvis, $final_agediag, $final_yrdiag, $final_isdysginad, $final_sex, $final_race,
                $final_ethnic, $final_vital, $final_cog, $final_figo, $final_ajcc, $final_igcccg, $final_note, $pid);
            $result->execute();
            $result->close();
        }

        // Step2: delete the original patient record, if this original Patient_ID has ONLY ONE Sample_ID referring to
        $sql = "SELECT count(Sample_ID) FROM Sample WHERE Patient_ID=?";
        if($result = $db->prepare($sql)){
            $result->bind_param('s',$oripid);
            $result->execute();
            $result->bind_result($cntsample);
            $result->fetch();
            $result->close();

            if($cntsample==1){
                $sql = "UPDATE Patient SET isDelete=\"1\" WHERE Patient_ID=?";
                if($result = $db->prepare($sql)){
                    $result->bind_param('s',$oripid);
                    $result->execute();
                    $result->close();
                }
            }
        }

        // Step3: update Sample ID
        // check the max sample id with same pathological status code and sample class code.
        // (p.s. Since the original patient id with [A-Z]{7} format has been filtered out in the UI web page,
        //  we don't need to filter out it here again.)
        $maxsampleid = null;
        $sql = "SELECT max(Sample_ID) FROM Sample WHERE Patient_ID=? AND Patient_ID IS NOT NULL AND " .
            "Pathological_Status=(SELECT Pathological_Status FROM Sample WHERE UUID=?) AND " .
            "Sample_Class=(SELECT Sample_Class FROM Sample WHERE UUID=?)";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('sss', $pid, $uuid, $uuid);
            $result->execute();
            $result->bind_result($maxsampleid);
            $result->fetch();
            $result->close();
        }

        // if max sample id with same pathologocal status code and sample class code is NOT FOUND
        if ($maxsampleid == null) {
            // record change history
            $changehistory = new ChangeHistory($db);
            $changehistory->recordChangeHistory("Sample", "UUID", $uuid, "Sample_ID", $pid . $path . $samclass . "00", $userid);
            $changehistory->recordChangeHistory("Sample", "UUID", $uuid, "Patient_ID", $pid, $userid);
            $changehistory = null;

            // update Sample_ID and Patient_ID
            $sql = "UPDATE Sample SET Sample_ID=INSERT(INSERT(Sample_ID,1,7,?),12,2,\"00\"),Patient_ID=? WHERE UUID=?";
            if ($result = $db->prepare($sql)) {
                $result->bind_param('sss', $pid, $pid, $uuid);
                $result->execute();
                $result->close();
            }
            // if max sample id with same pathologocal status code and sample class code is FOUND
        } else {
            // calculate last 2-digit of new sample id
            $last2digit = (int)substr($maxsampleid, 10, 2);
            if ($last2digit < 99) {
                $last2digit += 1;
                if ($last2digit < 10) {
                    $last2digit = "0" . (string)$last2digit;
                }
            } else {
                $last2digit = "00";
            }

            // record change history
            $changehistory = new ChangeHistory($db);
            $changehistory->recordChangeHistory("Sample", "UUID", $uuid, "Sample_ID", $pid . $path . $samclass . $last2digit, $userid);
            $changehistory->recordChangeHistory("Sample", "UUID", $uuid, "Patient_ID", $pid, $userid);
            $changehistory = null;

            // update Sample_ID and Patient_ID
            $sql = "UPDATE Sample SET Sample_ID=INSERT(INSERT(Sample_ID,1,7,?),12,2,?),Patient_ID=? WHERE UUID=?";
            if ($result = $db->prepare($sql)) {
                $result->bind_param('ssss', $pid, $last2digit, $pid, $uuid);
                $result->execute();
                $result->close();
            }
        }

        // Step4: update Patient_ID to newly-linked one in EnrollStudy table
        $sql = "UPDATE EnrollStudy SET Patient_ID=? WHERE Sample_UUID=?";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('ss', $pid, $uuid);
            $result->execute();
            $result->close();
        }
    }
}

if($errormsg!==""){
    $output=["stat"=>"Fail!","msg"=>$errormsg,"class"=>"alert-danger","goto"=>""];
}else{
    $output=["stat"=>"Success!","msg"=>"Successfully link sample and patient.","class"=>"alert-success",
        "goto"=>"samplelist.php?operate=view&uuid=".$uuids];
}

$db->close();
echo json_encode($output);