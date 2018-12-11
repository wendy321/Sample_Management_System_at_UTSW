<?php

session_start();

$userid=null;
if(!empty($_SESSION["userid"])){
    $userid=$_SESSION["userid"];
}else{
    header("location:login.php");
}

// Connect to database
require_once("class/dbencryt.inc");
require_once("dbsample.inc");
$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
if($db->connect_error){
    die('Unable to connect to database: ' . $db->connect_error);
}

$db->set_charset("utf8");

// Escape special characters in inputs
require_once ("class/EscapeString.inc");
foreach ($_POST as $k => $v){
    $_POST[$k]=EscapeString::escape($v);
}

// Utility classes
require_once ('class/ChangeHistory.inc');
require_once ('class/PatientID.inc');
require_once ('class/SampleID.inc');
require_once ('class/UUID.inc');
require_once ('class/InputFormat.inc');

$method=(!empty($_POST['method']))?$_POST['method']:null;
// If input single sample info
if($method=== "1"){
    /* check and convert inputs */
    $studyid=!empty($_POST['studyid'])?$_POST['studyid']:null;
    if($studyid === null){
        $db->close();
        die('Study ID can not be empty.');
    }
    $studysampleid=!empty($_POST['studysampleid'])?$_POST['studysampleid']:null;
    $studypatientid=!empty($_POST['studypatientid'])?$_POST['studypatientid']:null;
    $localsampleid=!empty($_POST['localsampleid'])?$_POST['localsampleid']:null;
    if($localsampleid === null){
        $db->close();
        die('Local Sample ID can not be empty.');
    }
    $localpatientid=!empty($_POST['localpatientid'])?$_POST['localpatientid']:null;
    if($localpatientid === null){
        $db->close();
        die('Local Patient ID can not be empty.');
    }
    $patientid=!empty($_POST['patientid'])?$_POST['patientid']:null;
    $sampleclass=!empty($_POST['sampleclass'])?$_POST['sampleclass']:null;
    $sampletype=!empty($_POST['sampletype'])?$_POST['sampletype']:null;
    if($sampletype===null){
        switch($sampleclass){
            case "1": $sampletype="19";break;
            case "2": $sampletype="29";break;
            case "3": $sampletype="39";break;
            case "4": $sampletype="49";break;
            case "5": $sampletype="59";break;
            case "6": $sampletype="699";break;
            case "98": $sampletype="98";break;
            case "99":$sampletype="99"; break;
            default:$sampletype=null;
        }
    }
    $pathological=!empty($_POST['pathological'])?$_POST['pathological']:null;

    $room=!empty($_POST['room'])?$_POST['room']:null;
    $cabinettype=!empty($_POST['cabinettype'])?$_POST['cabinettype']:null;
    $cabinettemp=!empty($_POST['cabinettemp'])?$_POST['cabinettemp']:null;
    $cabinetnum=!empty($_POST['cabinetnum'])?$_POST['cabinetnum']:null;
    $shelfnum=!empty($_POST['shelfnum'])?$_POST['shelfnum']:null;
    $racknum=!empty($_POST['racknum'])?$_POST['racknum']:null;
    $boxnum=!empty($_POST['boxnum'])?$_POST['boxnum']:null;
    $posnum=!empty($_POST['posnum'])?$_POST['posnum']:null;

    // change values of storage variables based on sample type
    $inputerrmsg="";
    $unknowntype=["","43","49","98","99"];
    $tissuetype=["41"];
    $slidetype=["42"];
    // mix
    if(array_search($sampletype,$unknowntype)!==false){
        if(!empty($posnum) && ($posnum < 1 || $posnum > 100)){
            $inputerrmsg.=" Storage position number is out of the range, 1 ~ 100.";
        }
    }// tissue block
    else if(array_search($sampletype,$tissuetype)!==false){
        if($room != "1" || $cabinettype != "1"){
            $inputerrmsg.=" Storage room or cabinet type is wrong based on the selected sample type.";
        }
        if($posnum != null){
            $inputerrmsg.=" Storage position number shouldn't have value based on the selected sample type.";
        }
    }// tissue slide
    else if(array_search($sampletype,$slidetype)!==false){
        if($room != "2" || $cabinettype != "2"){
            $inputerrmsg.=" Storage room or cabinet type is wrong based on the selected sample type.";
        }
        if($shelfnum != null){
            $inputerrmsg.=" Storage shelf number shouldn't have value based on the selected sample type.";
        }
        if($racknum != null){
            $inputerrmsg.=" Storage rack number shouldn't have value based on the selected sample type.";
        }
        if(!empty($posnum) && ($posnum < 1 || $posnum > 100)){
            $inputerrmsg.=" Storage position number is out of the range, 1 ~ 100.";
        }
    }// tube
    else{
        if($room != "1" || $cabinettype != "1"){
            $inputerrmsg.=" Storage room or cabinet type is wrong based on the selected sample type.";
        }
        if(!empty($posnum) && ($posnum < 1 || $posnum > 81)){
            $inputerrmsg.=" Storage position number is out of the range, 1 ~ 81.";
        }
    }

    if($inputerrmsg!==""){
        $db->close();
        die($inputerrmsg);
    }

    $quantitynum=!empty($_POST['quantitynum'])?$_POST['quantitynum']:null;
    $quantityunit=!empty($_POST['quantityunit'])?$_POST['quantityunit']:null;
    $concennum=!empty($_POST['concennum'])?$_POST['concennum']:null;
    $concenunit=!empty($_POST['concenunit'])?$_POST['concenunit']:null;


    $specimentype=!empty($_POST['specimentype'])?$_POST['specimentype']:null;
    $anatomicalsite=!empty($_POST['anatomicalsite'])?$_POST['anatomicalsite']:null;
    $anatomicallaterality=!empty($_POST['anatomicallaterality'])?$_POST['anatomicallaterality']:null;
    $nucleotidesize=!empty($_POST['nucleotidesize'])?$_POST['nucleotidesize']:null;
    $proceduretype=!empty($_POST['proceduretype'])?$_POST['proceduretype']:null;
    $proceduredate=!empty($_POST['proceduredate'])?$_POST['proceduredate']:null;
    $consortium=!empty($_POST['consortium'])?$_POST['consortium']:null;
    $institute=!empty($_POST['institute'])?$_POST['institute']:null;
    $hasparent=!empty($_POST['hasparent'])?$_POST['hasparent']:null;
    $deriveddate=!empty($_POST['deriveddate'])?$_POST['deriveddate']:null;
    $notes=!empty($_POST['notes'])?$_POST['notes']:null;

    $resultarr=operateInDB($db,$method,$studyid,$studysampleid,$studypatientid,$localsampleid,$localpatientid,$patientid,
        $sampleclass,$sampletype,$pathological,
        $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
        $concennum,$concenunit,
        $specimentype,$anatomicalsite,$anatomicallaterality,$nucleotidesize,
        $proceduretype,$proceduredate,$consortium,$institute,$deriveddate,$notes);
    $errmsg=$resultarr["errmsg"];
    $uuid=$resultarr["uuid"];
    $redirect=$resultarr["redirect"];
    $existuuid=$resultarr["existuuid"];

    if($errmsg!==null){
        $db->close();
        die($errmsg);
    }else{
        if(!empty($existuuid)){
            header("Location:samplelist.php?operate=edit&uuid=".$existuuid."&exist=1");
        }else{
            // ps. There's an issue of generating URL-encoded query string by using http_build_query() on lce-test server.
            $http_build_query="";
            if(!empty($redirect)){
                foreach ($redirect as $key => $value){
                    $http_build_query.="&".$key."=".$value;
                }
            }
            if($hasparent=="1"){
                header("Location:samplelist.php?operate=edit_select&item=parentsample&uuid=".$uuid."&exist=0".$http_build_query);
            }else{
                header("Location:samplelist.php?operate=view&uuid=".$uuid."&exist=0".$http_build_query);
            }
        }
        $db->close();
    }

// If input batch sample info
}else if($method === "2"){
    if (empty($_FILES['inputfile'])) {
        echo json_encode(['error'=>'No files found for upload.']);
    }

    $file = $_FILES['inputfile'];
    $paths=[];
    $success=null;
    $errormsg='';
    $output = [];
    $filename = $file['name'];
    $type = $file['type'];
    if($type !=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
        $success=false;
        $errormsg='Wrong file type. Please import XSLX file.';
    }
    $error=$file['error'];
    if($error > 0){
        $success=false;
        $errormsg=' Fail to upload file.';
    }
    $size = $file['size'];
    if($size > 1000000){
        $success=false;
        $errormsg.=' File size is too large. File size should be <= 1 MB.';
    }
    $ext = explode('.', basename($filename));
    // ps. In lce-test server, $target= "../../../../tmp" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
    $target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
    if(move_uploaded_file($file['tmp_name'], $target)) {
        $success = true;
        $paths[] = $target;
    } else {
        $success = false;
        $errormsg.=' Error while uploading file. Contact the system administrator.';
    }

    if ($success === true) {
        batchUpLoadSample($db,$method,$target,$userid);

        $output = ['goto'=>'samplelist.php?operate=view&uuid=allnew&userid='.$userid];
        foreach ($paths as $file) {
            unlink($file);
        }
    } elseif ($success === false) {
        $output = ['error'=>$errormsg];
        foreach ($paths as $file) {
            unlink($file);
        }
    } else {
        $output = ['error'=>'No files were processed.'];
    }
    echo json_encode($output);
    $db->close();
// No input method is selected
}else{
    $db->close();
    die('No input method is selected.');
}

/**************************************************************************************************
 * Check input patient_id with found patient_id by source/study_id and source/study_patient_id.
 **************************************************************************************************/
function checkInputPatientIDWiPatientIDInEnrollStudy($db,$patientid,$studyid,$studypatientid){
    $msg=null;
    $finalpid=null;
    if($studypatientid!==null){
        $dbpatientid2=null;
        $sql="SELECT Patient_ID FROM EnrollStudy WHERE Study_ID=? AND Within_Study_Patient_ID=? AND isDelete=0";
        if($result = $db->prepare($sql))
        {
            $result->bind_param('ss',$studyid,$studypatientid);
            $result->execute();
            $result->bind_result($dbpatientid2);
            $result->fetch();
            $result->close();
        }

        if($dbpatientid2!==null){
            if($patientid!==null && $dbpatientid2!==$patientid){
                $msg="There is a Patient ID conflict. You selected Patient_ID, ".$patientid.
                    ". However, Patient ID, ".$dbpatientid2." is found in database according to your selected ".
                    "Source Name and Source Patient ID.";
            }else{
                $finalpid=$dbpatientid2;
            }
        }else{
            $finalpid=$patientid;
        }
    }else{
        $finalpid=$patientid;
    }
    return array($finalpid,$msg);
}
/******************************************
 * Update patient info in database
 ******************************************/
function updatePatient($db,$userid,$patientid,$localpatientid){
    $changehistory=new ChangeHistory($db);
    $changehistory->recordChangeHistory("Patient","Patient_ID",$patientid,"Local_Patient_ID",$localpatientid,$userid);
    $changehistory=null;

    $sql="Update Patient SET Local_Patient_ID=? WHERE Patient_ID=? AND isDelete=0";
    if($result = $db->prepare($sql))
    {
        $result->bind_param('ss',$localpatientid,$patientid);
        $result->execute();
        $result->close();
    }
}
/******************************************
 * Handle patient id info
 ******************************************/
function operatePatient($db,$userid,$method,$localpatientid,$patientid,$studyid,$studypatientid){
    $finalpid=null;
    $msg=null;
    $redirect=null;

    $dbpatientid1=null;
    $sql="SELECT Patient_ID FROM Patient WHERE Local_Patient_ID=? AND isDelete=0";
    if($result = $db->prepare($sql))
    {
        $result->bind_param('s',$localpatientid);
        $result->execute();
        $result->bind_result($dbpatientid1);
        $result->fetch();
        $result->close();
    }
    if($dbpatientid1!==null){
        if($patientid!==null){
            if($dbpatientid1!==$patientid){
                $msg="There is a Patient ID conflict. You selected Patient_ID, ".$patientid.
                    ". However, Patient ID, ".$dbpatientid1." is found in database according to your selected Local Patient ID.";
            }else{
                $resultpid=checkInputPatientIDWiPatientIDInEnrollStudy($db,$patientid,$studyid,$studypatientid);
                $finalpid=$resultpid[0];
                $msg=$resultpid[1];
            }
        }else{
            $finalpid=$dbpatientid1;
        }

        if($msg===null){
            updatePatient($db,$userid,$finalpid,$localpatientid);
        }
    }else{
        if($patientid!==null){
            $resultpid=checkInputPatientIDWiPatientIDInEnrollStudy($db,$patientid,$studyid,$studypatientid);
            $finalpid=$resultpid[0];
            $msg=$resultpid[1];

            if($msg===null){
                updatePatient($db,$userid,$finalpid,$localpatientid);
            }
        }else{
            $resultpid=checkInputPatientIDWiPatientIDInEnrollStudy($db,$patientid,$studyid,$studypatientid);
            $finalpid=$resultpid[0];
            $msg=$resultpid[1];

            if($msg===null){
                if($finalpid!==null){
                    updatePatient($db,$userid,$finalpid,$localpatientid);
                }else{
                    // generate fake pid
                    $finalpid=PatientID::generatePatientID($db,TRUE);

                    // insert a new fake pid into database
                    if($result = $db->prepare("INSERT INTO Patient (Patient_ID,Local_Patient_ID, CreateTime) VALUES (?,?,NOW())"))
                    {
                        $result->bind_param('ss',$finalpid,$localpatientid);
                        $result->execute();
                        $result->close();
                    }

                    if($method==="1"){
                        $redirect=$finalpid;
                    }
                }
            }
        }
    }

    if($method==="2"){
        $redirect=$finalpid;
    }

    return array($finalpid,$msg,$redirect);
}
/****************************************
 * Add a sample record into the database
 *****************************************/
function insertSample($db,$pid,$localsampleid,$sampleclass,$sampletype,$pathological,
                      $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
                      $concennum,$concenunit,
                      $specimentype,$anatomicalsite,
                      $anatomicallaterality,$nucleotidesize,$proceduretype,$proceduredate,$consortium,
                      $institute,$deriveddate,$notes){

    // generate an uuid for the sample
    // ps. UUID might be duplicate due to NTP adjustment.
    // $uuid = uniqid("", TRUE); // generate 23-character uuid
    $uuid = UUID::generate36DigitUUID(); // generate 36-character uuid
    // generate sample_id
    $arr=SampleID::generateSampleID($db,$pid,$pathological,$sampleclass);
    $sampleid=$arr[0];
    $pid=$arr[1];

    // insert a new sample record
    $sql="INSERT INTO Sample (UUID,Sample_ID,Patient_ID,Local_Sample_ID,Sample_Contributor_Consortium_ID,Sample_Contributor_Institute_ID,"
        ."Procedure_Type,Date_Procedure,Parent_UUID,Date_Derive_From_Parent,Pathological_Status,Sample_Type,Sample_Class,"

        ."Storage_Room,Cabinet_Type,Cabinet_Temperature,Cabinet_Number,Shelf_Number,Rack_Number,Box_Number,Position_Number,"
        ."Quantity_Value,Quantity_Unit,Concentration_Value,Concentration_Unit,"

        ."Specimen_Type,Nucleotide_Size_Group_200,Anatomical_Site,Anatomical_Laterality,Notes,CreateTime) VALUES (?,?,?,?,"
        ."?,?,?,?,NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, NOW())";
    if($result = $db->prepare($sql)){
        $result->bind_param("sssssssssssssssssssssssssssss",$uuid,$sampleid,$pid,$localsampleid,$consortium,$institute,
            $proceduretype,$proceduredate,$deriveddate,$pathological,$sampletype,$sampleclass,
            $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
            $concennum,$concenunit,
            $specimentype,$nucleotidesize,$anatomicalsite,$anatomicallaterality,$notes);
        $result->execute();
        $result->close();
    }
    return $uuid;
}

/********************************************
 * Add an enrollstudy record into the database
 ********************************************/
function insertEnrollStudy($db,$studyid,$uuid,$studysampleid,$pid,$studypatientid){

    $sql="INSERT INTO EnrollStudy (Study_ID,Patient_ID,Within_Study_Patient_ID,Sample_UUID,Within_Study_Sample_ID,CreateTime) VALUES (?,?,?,?,?,NOW())";
    if($result = $db->prepare($sql)){
        $result->bind_param("sssss", $studyid,$pid,$studypatientid,$uuid,$studysampleid);
        $result->execute();
        $result->close();
    }
    $enrollstudyid=$db->insert_id;
    return $enrollstudyid;
}

/***********************************************
 * Handle Patient, Sample, and EnrollStudy info
 **********************************************/
function operatePatientSampleEnrollStudy($db,$userid,$method,$studyid,$studypatientid,$studysampleid,$localpatientid,$patientid,$localsampleid,
                                         $sampleclass,$sampletype,$pathological,
                                         $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
                                         $concennum,$concenunit,
                                         $specimentype,$anatomicalsite,$anatomicallaterality,
                                         $nucleotidesize,$proceduretype,$proceduredate,$consortium,$institute,
                                         $deriveddate,$notes){
    //operate patient info and get final patient_id & related information
    $resultpatient=operatePatient($db,$userid,$method,$localpatientid,$patientid,$studyid,$studypatientid);
    $finalpid=$resultpatient[0];
    $msg=$resultpatient[1];
    $redirect["edit_Patient"]=$resultpatient[2];


    $uuid=null;
    if($msg===null){
        // operate sample info and get final sample_uuid & related information
        $uuid=insertSample($db,$finalpid,$localsampleid,$sampleclass,$sampletype,$pathological,
            $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
            $concennum,$concenunit,
            $specimentype,$anatomicalsite,
            $anatomicallaterality,$nucleotidesize,$proceduretype,$proceduredate,$consortium,
            $institute,$deriveddate,$notes);

        // operate enrollstudy info and get enrollstudy_id & related information
        $enrollstudyid=insertEnrollStudy($db,$studyid,$uuid,$studysampleid,$finalpid,$studypatientid);
        $redirect["edit_EnrollStudy"]=$enrollstudyid;
    }

    return array($uuid,$msg,$redirect);
}
/***************************************************************************
 * Handle single sample record and update or insert it into the database
 * The user input data might result in generating new Patient_ID or Enroll_Study_ID
 **************************************************************************/
function operateInDB($db,$method,$studyid,$studysampleid,$studypatientid,$localsampleid,$localpatientid,$patientid,$sampleclass,
                     $sampletype,$pathological,
                     $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
                     $concennum,$concenunit,
                     $specimentype,$anatomicalsite,$anatomicallaterality,$nucleotidesize,
                     $proceduretype,$proceduredate,$consortium,$institute,$deriveddate,$notes){
    global $userid;
    // store error message
    $errmsg=null;

    // store redirect messages, if new Patient or EnrollStudy record is added in database
    $redirect=null;

    // store sample_uuid, if the input samples have already existed in database
    $existedsampleuuid=null;

    // check whether local_sample_id exists in Sample table (local_sample_id should be unique)
    $uuid=null;
    if($result = $db->prepare("SELECT UUID FROM Sample WHERE Local_Sample_ID=? AND isDelete=0"))
    {
        $result->bind_param('s',$localsampleid);
        $result->execute();
        $result->bind_result($uuid);
        $result->fetch();
        $result->close();
    }
    //if local_sample_id exists
    if($uuid!==null){
        $existedsampleuuid=$uuid;
     //else local_sample_id doesn't exist
    }else{
        // if $studyname is not null
        if($studyid!==null){
            // if $studysampleid is not null
            if($studysampleid!==null){
                $dbsampleuuid=null;
                if($result = $db->prepare("SELECT Sample_UUID FROM EnrollStudy WHERE Study_ID=? AND ".
                    "Within_Study_Sample_ID=? AND isDelete=0 limit 1"))
                {
                    $result->bind_param('ss',$studyid,$studysampleid);
                    $result->execute();
                    $result->bind_result($dbsampleuuid);
                    $result->fetch();
                    $result->close();

                }
                // if existed sample_uuid is not null
                if($dbsampleuuid!==null){
                    $changehistory=new ChangeHistory($db);
                    $changehistory->recordChangeHistory("Sample","UUID",$dbsampleuuid,"Local_Sample_ID",$localsampleid,$userid);
                    $changehistory=null;

                    if($result = $db->prepare("UPDATE Sample SET Local_Sample_ID=? WHERE UUID=?"))
                    {
                        $result->bind_param('ss',$localsampleid,$dbsampleuuid);
                        $result->execute();
                        $result->close();

                    }
                    $existedsampleuuid=$dbsampleuuid;
                // else existed sample_uuid is null
                }else{
                    $resultall=operatePatientSampleEnrollStudy($db,$userid,$method,$studyid,$studypatientid,$studysampleid,$localpatientid,$patientid,$localsampleid,
                        $sampleclass,$sampletype,$pathological,
                        $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
                        $concennum,$concenunit,
                        $specimentype,$anatomicalsite,$anatomicallaterality,
                        $nucleotidesize,$proceduretype,$proceduredate,$consortium,$institute,
                        $deriveddate,$notes);
                    $uuid=$resultall[0];
                    $errmsg=$resultall[1];
                    $redirect=$resultall[2];
                }
            // else $studysampleid is null
            }else{
                $resultall=operatePatientSampleEnrollStudy($db,$userid,$method,$studyid,$studypatientid,$studysampleid,$localpatientid,$patientid,$localsampleid,
                    $sampleclass,$sampletype,$pathological,
                    $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
                    $concennum,$concenunit,
                    $specimentype,$anatomicalsite,$anatomicallaterality,
                    $nucleotidesize,$proceduretype,$proceduredate,$consortium,$institute,
                    $deriveddate,$notes);
                $uuid=$resultall[0];
                $errmsg=$resultall[1];
                $redirect=$resultall[2];
            }
        }
    }

    return array("errmsg"=>$errmsg,"uuid"=>$uuid,"redirect"=>$redirect,"existuuid"=>$existedsampleuuid);

}

/********************************************
 * Batch upload samples into database
 ********************************************/
function batchUpLoadSample($db,$method,$target,$userid){
    // Method 1: Asynchronous, similar to remote r
    // store process job to database
//    $jobid = uniqid("", TRUE);
//    if($result = $db->prepare("INSERT INTO Jobs (JobID,Status,Software,Analysis,CreateTime) VALUES (?,\"0\",\"samplemanagementsystem\",\"batchsampleupload\",NOW())"))
//    {
//        $result->bind_param("s", $jobid);
//        $result->execute();
//        $result->close();
//    }
//
//    if($result = $db->prepare("INSERT INTO SMSParameters (JobID, FileLocation, UserAccount) VALUES (?,?,?)"))
//    {
//        $result->bind_param("sss", $jobid,$target,$userid);
//        $result->execute();
//        $result->close();
//    }
//    return "<script>location.href='batchsamplelist.php?jobid=".$jobid."'</script>";

    // Method 2: Synchronous
    // change previous batch upload tag to 2
    $sql="UPDATE BatchUploadSampleLog SET Tag='2' WHERE Account=? AND Tag='1'";
    if($result = $db->prepare($sql)){
        $result->bind_param("i",$userid);
        $result->execute();
        $result->close();
    }
    $sql="UPDATE BatchUploadSampleErrorLog SET Tag='2' WHERE Account=? AND Tag='1'";
    if($result = $db->prepare($sql)){
        $result->bind_param("i",$userid);
        $result->execute();
        $result->close();
    }
    // start to process new batch upload XLSX file
    
    $file=fopen($target,"r");
    fgetcsv($file);
    $allexistuuid=null;
    $isfirst=1;
    $errormsg="";
    $isrollback=0;
    $db->autocommit(FALSE);
    while($line=fgetcsv($file)){
        /* convert inputs */
        $studyid=(!empty($line[0]) && $line[0]!="NA" && $line[0]!="N/A")?$line[0]:null;
        if($studyid === null){
            $db->rollback();
            $isrollback=1;
            $errormsg.="Study_ID can't be empty.";
            break;
        }
        $studysampleid=(!empty($line[1]) && $line[1]!="NA" && $line[1]!="N/A")?$line[1]:null;
        $studypatientid=(!empty($line[2]) && $line[2]!="NA" && $line[2]!="N/A")?$line[2]:null;
        $localsampleid=(!empty($line[3]) && $line[3]!="NA" && $line[3]!="N/A")?$line[3]:null;
        if($localsampleid === null){
            $db->rollback();
            $isrollback=1;
            $errormsg.=" Local_Sample_ID can't be empty.";
            break;
        }
        $localpatientid=(!empty($line[4]) && $line[4]!="NA" && $line[4]!="N/A")?$line[4]:null;
        if($localpatientid === null){
            $db->rollback();
            $isrollback=1;
            $errormsg.=" Local_Patient_ID can't be empty.";
            break;
        }
        $patientid=(!empty($line[5]) && $line[5]!="NA" && $line[5]!="N/A")?$line[5]:null;
        $sampleclass=(!empty($line[6]) && $line[6]!="NA" && $line[6]!="N/A")?$line[6]:null;
        switch($sampleclass){
            case "1": case "01":$sampleclass="01";break;
            case "2": case "02":$sampleclass="02";break;
            case "3": case "03":$sampleclass="03";break;
            case "4": case "04":$sampleclass="04";break;
            case "5": case "05":$sampleclass="05";break;
            case "6": case "06":$sampleclass="06";break;
            case "98": $sampleclass="98";break;
            case "99":$sampleclass="99"; break;
            default:$sampleclass=null;
        }

        $sampletype=(!empty($line[7]) && $line[7]!="NA" && $line[7]!="N/A")?$line[7]:null;
        if($sampletype===null){
            switch($sampleclass){
                case "01": $sampletype="19";break;
                case "02": $sampletype="29";break;
                case "03": $sampletype="39";break;
                case "04": $sampletype="49";break;
                case "05": $sampletype="59";break;
                case "06": $sampletype="699";break;
                case "98": $sampletype="98";break;
                default:$sampletype="99";
            }
        }

        if($sampleclass===null){
            switch($sampletype){
                case "11":case "12":case "13":case "19":$sampleclass="01";break;
                case "21":case "22":case "23":case "29":$sampleclass="02";break;
                case "39":$sampleclass="03";break;
                case "41":case "42":case "43":case "49":$sampleclass="04";break;
                case "51":case "52":case "53":case "54":case "55":case "56":case "59":$sampleclass="05";break;
                case "61":case "62":case "63":case "64":case "65":case "66":case "67":case "68":case "69":
                case "610":case "611":case "612":case "613":case "614":case "615":case "616":case "617":case "618":case "619":
                case "620":case "621":$sampleclass="06";break;
                case "98": $sampleclass="98";break;
                default:$sampleclass="99";
            }
        }

        $pathological=(!empty($line[8]) && $line[8]!="NA" && $line[8]!="N/A")?$line[8]:null;
        switch($pathological){
            case "1": case "01": $pathological="01";break;
            case "2": case "02": $pathological="02";break;
            case "3": case "03": $pathological="03";break;
            case "10": $pathological="10";break;
            case "11": $pathological="11";break;
            case "98": $pathological="98";break;
            default:$pathological="99";
        }

        $room=(!empty($line[9]) && $line[9]!="NA" && $line[9]!="N/A")?$line[9]:null;
        $cabinettype=(!empty($line[10]) && $line[10]!="NA" && $line[10]!="N/A")?$line[10]:null;
        $cabinettemp=(!empty($line[11]) && $line[11]!="NA" && $line[11]!="N/A")?$line[11]:null;
        $cabinetnum=(!empty($line[12]) && $line[12]!="NA" && $line[12]!="N/A")?$line[12]:null;
        $shelfnum=(!empty($line[13]) && $line[13]!="NA" && $line[13]!="N/A")?$line[13]:null;
        $racknum=(!empty($line[14]) && $line[14]!="NA" && $line[14]!="N/A")?$line[14]:null;
        $boxnum=(!empty($line[15]) && $line[15]!="NA" && $line[15]!="N/A")?$line[15]:null;
        $posnum=(!empty($line[16]) && $line[16]!="NA" && $line[16]!="N/A")?$line[16]:null;
        $quantitynum=(!empty($line[17]) && $line[17]!="NA" && $line[17]!="N/A")?$line[17]:null;
        $quantityunit=(!empty($line[18]) && $line[18]!="NA" && $line[18]!="N/A")?$line[18]:null;
        $concennum=(!empty($line[19]) && $line[19]!="NA" && $line[19]!="N/A")?$line[19]:null;
        $concenunit=(!empty($line[20]) && $line[20]!="NA" && $line[20]!="N/A")?$line[20]:null;

        $specimentype=(!empty($line[21]) && $line[21]!="NA" && $line[21]!="N/A")?$line[21]:null;
        $anatomicalsite=(!empty($line[22]) && $line[22]!="NA" && $line[22]!="N/A")?$line[22]:null;
        $anatomicallaterality=(!empty($line[23]) && $line[23]!="NA" && $line[23]!="N/A")?$line[23]:null;
        $nucleotidesize=(!empty($line[24]) && $line[24]!="NA" && $line[24]!="N/A")?$line[24]:null;
        $proceduretype=(!empty($line[25]) && $line[25]!="NA" && $line[25]!="N/A")?$line[25]:null;
        $proceduredate=(!empty($line[26]) && $line[26]!="NA" && $line[26]!="N/A")?$line[26]:null;
        $consortium=(!empty($line[27]) && $line[27]!="NA" && $line[27]!="N/A")?$line[27]:null;
        $institute=(!empty($line[28]) && $line[28]!="NA" && $line[28]!="N/A")?$line[28]:null;
        $deriveddate=(!empty($line[29]) && $line[29]!="NA" && $line[29]!="N/A")?$line[29]:null;
        $notes=(!empty($line[30]) && $line[30]!="NA" && $line[30]!="N/A")?$line[30]:null;

        /* check input codes, types, length */
        $iscorrectinput=TRUE;
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($studyid,FALSE,'/^1$|^2$|^3$|^4$|^5$|^6$|^7$|^8$|^9$|^10$|^11$|^12$|^13$|^98$|^99$|^100$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Study code.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($studysampleid,TRUE,'/^.{0,30}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Study Sample ID should be 0 ~ 30 characters.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($studypatientid,TRUE,'/^.{0,30}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Study Patient ID should be 0 ~ 30 characters.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($localsampleid,FALSE,'/^.{1,30}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Local Sample ID format should be 1 ~ 30 characters.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($localpatientid,FALSE,'/^.{1,30}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Local Patient ID should be 1 ~ 30 characters.";

        if($patientid!==null){
            $dbpatientid=null;
            $sql="SELECT Patient_ID FROM Patient WHERE Patient_ID=? AND isDelete=0";
            if($result = $db->prepare($sql))
            {
                $result->bind_param('s',$patientid);
                $result->execute();
                $result->bind_result($dbpatientid);
                $result->fetch();
                $result->close();

                if($dbpatientid==null){
                    $iscorrectinput=FALSE;
                    $errormsg.=" The Patient_ID, ".$patientid.", is not in database or has been deleted.";
                }
            }
        }

        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampleclass,TRUE,'/^01$|^02$|^03$|^04$|^05$|^06$|^98$|^99$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Sample Class Code.";
        switch($sampleclass){
            case "01": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^11$|^12$|^13$|^19$/'); break;
            case "02": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^21$|^22$|^23$|^29$/'); break;
            case "03": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^39$/'); break;
            case "04": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^41$|^42$|^43$|^49$/'); break;
            case "05": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^51$|^52$|^53$|^54$|^55$|^56$|^59$/'); break;
            case "06": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^(6)([1-9]|1\d|2[01]|9{2})$/'); break;
            case "98": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^98$/'); break;
            case "99": $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($sampletype,TRUE,'/^99$/'); break;
            case null: $iscorrectinput=$iscorrectinput&&($sampletype==null); break;
            default: $iscorrectinput=$iscorrectinput&&FALSE;
        }
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Sample Type Code.";

        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($pathological,TRUE,'/^01$|^02$|^03$|^10$|^11$|^98$|^99$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Pathological Code.";

        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($room,TRUE,'/^01$|^1$|^02$|^2$|^99$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Room Code.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($cabinettype,TRUE,'/^01$|^1$|^02$|^2$|^99$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Cabinet Type Code.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($cabinettemp,TRUE,'/^01$|^1$|^02$|^2$|^99$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Cabinet Temperature Code.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($cabinetnum,TRUE,'/^[0-9]{0,2}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Cabinet Number.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($shelfnum,TRUE,'/^[0-9]{0,2}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Shelf Number.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($racknum,TRUE,'/^[0-9]{0,2}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Rack Number.";
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($boxnum,TRUE,'/^[0-9]{0,2}$/');
        $errormsg.=$iscorrectinput?"":$localsampleid." Wrong Box Number.";

        $unknowntype=["","43","49","98","99"];
        $tissuetype=["41"];
        $slidetype=["42"];
        // mix
        if(array_search($sampletype,$unknowntype)!==false){
            if(!empty($posnum) && ($posnum < 1 || $posnum > 100)){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage position number is out of the range, 1 ~ 100.";
            }
        }// tissue block
        else if(array_search($sampletype,$tissuetype)!==false){
            if($room != "1" || $cabinettype != "1"){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage room or cabinet type is wrong based on the selected sample type.";
            }
            if($posnum != null){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage position number shouldn't have value based on the selected sample type.";
            }
        }// tissue slide
        else if(array_search($sampletype,$slidetype)!==false){
            if($room != "2" || $cabinettype != "2"){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage room or cabinet type is wrong based on the selected sample type.";
            }
            if($shelfnum != null){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage shelf number shouldn't have value based on the selected sample type.";
            }
            if($racknum != null){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage rack number shouldn't have value based on the selected sample type.";
            }
            if($posnum != null && ($posnum < 1 || $posnum > 100)){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage position number is out of the range, 1 ~ 100.";
            }
        }// tube
        else{
            if($room != "1" || $cabinettype != "1"){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage room or cabinet type is wrong based on the selected sample type.";
            }
            if($posnum != null && ($posnum < 1 || $posnum > 81)){
                $iscorrectinput=FALSE;
                $errormsg.=$localsampleid." Storage position number is out of the range, 1 ~ 81.";
            }
        }

        if(!empty($quantitynum) && ($quantitynum < 0 || $quantitynum > 99999.99999)){
            $iscorrectinput=FALSE;
            $errormsg.=$localsampleid." Quantity Number is out of range 0 ~ 99999.99999";
        }
        $iscorrectquantityunit=InputFormat::checkInputFormat($quantityunit,TRUE,'/^[a-zA-Z0-9.\-\/]{0,30}$/');
        $errormsg.=$iscorrectquantityunit?"":$localsampleid." Amount Unit character length is out of range 0 ~ 30.";
        $iscorrectinput&=$iscorrectquantityunit;

        if(!empty($concennum) && ($concennum < 0 || $concennum > 99999.99999)){
            $iscorrectinput=FALSE;
            $errormsg.=$localsampleid." Concentration Value is out of range 0 ~ 99999.99999";
        }
        $iscorrectconcenunit=InputFormat::checkInputFormat($concenunit,TRUE,'/^[a-zA-Z0-9.\-\/]{0,30}$/');
        $errormsg.=$iscorrectconcenunit?"":$localsampleid." Concentration Unit character length is out of range 0 ~ 30.";
        $iscorrectinput&=$iscorrectconcenunit;

        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($pathological,TRUE,'/^01$|^02$|^03$|^10$|^11$|^98$|^99$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($specimentype,TRUE,'/^1$|^2$|^3$|^4$|^98$|^99$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($anatomicalsite,TRUE,'/^[2-9]$|^98$|^99$|^(1.[a-f,y])$|^(1.e.[a-d,y])$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($anatomicallaterality,TRUE,'/^1$|^2$|^3$|^99$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($nucleotidesize,TRUE,'/^1$|^2$|^99$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($proceduretype,TRUE,'/^1$|^2$|^3$|^4$|^5$|^6$|^98$|^99$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($proceduredate,TRUE,'/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])\s*$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($consortium,TRUE,'/^([1-4]?|98|99)$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($institute,TRUE,'/^([1-3]?|98|99)$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($deriveddate,TRUE,'/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])\s*$/');
        $iscorrectinput=$iscorrectinput&&InputFormat::checkInputFormat($notes,TRUE,'/^.{0,100}$/');

        if($iscorrectinput===FALSE){
            $db->rollback();
            $isrollback=1;
            break;
        }

        $resultarr=operateInDB($db,$method,$studyid,$studysampleid,$studypatientid,$localsampleid,$localpatientid,$patientid,
            $sampleclass,$sampletype,$pathological,
            $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
            $concennum,$concenunit,
            $specimentype,$anatomicalsite,$anatomicallaterality,$nucleotidesize,
            $proceduretype,$proceduredate,$consortium,$institute,$deriveddate,$notes);

        $errormsg.=($resultarr["errmsg"]!==null)?$resultarr["errmsg"]:"";
        $uuid=$resultarr["uuid"];
        $redirect_pid=$resultarr["redirect"]["edit_Patient"];
        $redirect_enstudyid=$resultarr["redirect"]["edit_EnrollStudy"];
        $existuuid=$resultarr["existuuid"];

        if($errormsg!==""){
            $db->rollback();
            $isrollback=1;
            break;
        }else{
            if(!empty($existuuid)){
                if($isfirst===1){
                    $allexistuuid.=$existuuid;
                }else{
                    $allexistuuid.=",".$existuuid;
                }
                $isfirst=0;
            }else{
                $sql="INSERT INTO BatchUploadSampleLog (Account,Sample_UUID,Redirect_PatientID,Redirect_EnrollStudyID,Tag,CreateTime) VALUES (?,?,?,?,1,NOW())";
                if($result = $db->prepare($sql)){
                    $result->bind_param("ssss",$userid,$uuid,$redirect_pid,$redirect_enstudyid);
                    $result->execute();
                    $result->close();
                }
            }
        }
    }
    $db->commit();
    $db->autocommit(TRUE);
    fclose($file);

    if($allexistuuid!==null || ($isrollback===1 && $errormsg!=="")){
        $sql="INSERT INTO BatchUploadSampleErrorLog (Account,Tag,Exist_Sample_UUID,Error_Msg,CreateTime) VALUES (?,1,?,?,NOW())";
        if($result = $db->prepare($sql)){
            $result->bind_param("sss",$userid,$allexistuuid,$errormsg);
            $result->execute();
            $result->close();
        }
    }

}