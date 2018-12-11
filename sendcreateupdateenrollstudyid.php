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

// Utility classes
require_once ('class/InputFormat.inc');

$output=null;
$errormsg="";

// get and transform input variables
$enrollstudyid=!empty($_POST['enrollstudyid'])?$_POST['enrollstudyid']:null;
$studyid=!empty($_POST['studyid'])?$_POST['studyid']:null;
if($studyid===null){
    $errormsg.="Please fill in Source / Study Name.";
}
$studyarm=!empty($_POST['studyarm'])?$_POST['studyarm']:null;
$samuuid=!empty($_POST['samuuid'])?$_POST['samuuid']:null;
if($samuuid===null){
    $errormsg.=" Please fill in Sample UUID.";
}
$studysampleid=!empty($_POST['studysampleid'])?$_POST['studysampleid']:null;
if(!InputFormat::checkInputFormat($studysampleid,TRUE,'/^.{0,30}$/')){
    $errormsg.=" Source / Study Sample ID is too long.";
}
$patientid=!empty($_POST['patientid'])?$_POST['patientid']:null;
$studypatientid=!empty($_POST['studypatientid'])?$_POST['studypatientid']:null;
if(!InputFormat::checkInputFormat($studypatientid,TRUE,'/^.{0,30}$/')){
    $errormsg.=" Source / Study Patient ID is too long.";
}

require_once ('class/ChangeHistory.inc');
// check whether the patient_id of the input sample_uuid equals to input patient_id
if($patientid!==null){
    $sql = "SELECT Patient_ID,Pathological_Status,Sample_Class FROM Sample WHERE UUID=? AND isDelete=0";
    if ($result = $db->prepare($sql)) {
        $result->bind_param('s', $samuuid);
        $result->execute();
        $result->bind_result($pid,$path,$samclass);
        $result->fetch();
        $result->close();

        if($patientid!==$pid){
            $errormsg.=" The Patient ID is different from Patient ID of the input Sample UUID. Please select another Patient ID.";
        }else{
            // ALSO UPDATE Sample_ID with the linked patient_id => class static function
            $maxsampleid=null;
            $sql = "SELECT max(Sample_ID) FROM Sample WHERE Patient_ID=? AND Pathological_Status=? AND Sample_Class=?";
            if ($result = $db->prepare($sql)) {
                $result->bind_param('sss', $patientid, $path,$samclass);
                $result->execute();
                $result->bind_result($maxsampleid);
                $result->fetch();
                $result->close();
            }

            if($maxsampleid==null){
                $sampleid=$patientid.$path.$samclass."00";
            }else{
                $last2digit=(int)substr($maxsampleid,10,2);
                if($last2digit<99){
                    $last2digit+=1;
                    if($last2digit<10){
                        $last2digit="0".(string)$last2digit;
                    }
                }else{
                    $last2digit="00";
                }
                $sampleid=$patientid.$path.$samclass.$last2digit;
            }

            $changehistory=new ChangeHistory($db);
            $changehistory->recordChangeHistory("Sample","UUID",$samuuid,"Sample_ID",$sampleid,$userid);
            $changehistory->recordChangeHistory("Sample","UUID",$samuuid,"Patient_ID",$patientid,$userid);
            $changehistory=null;
            $sql="UPDATE Sample SET Sample_ID=?,Patient_ID=? WHERE UUID=? AND isDelete=0";
            if($result=$db->prepare($sql)){
                $result->bind_param('sss',$sampleid,$patientid,$samuuid);
                $result->execute();
                $result->close();
            }
        }
    }
}

if($errormsg===""){
    // if enroll study id is null, create a new enroll study
    if($enrollstudyid===null){
        // check whether the enroll study has already existed in database
        $sql = "SELECT ID FROM EnrollStudy WHERE Study_ID=? AND Sample_UUID=? AND isDelete=0";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('ss',$studyid,$samuuid);
            $result->execute();
            $result->bind_result($id);
            $result->fetch();
            $result->close();

            if($id===null){
                $sql = "INSERT INTO EnrollStudy (Study_ID,Study_Arm,Patient_ID,Within_Study_Patient_ID,Sample_UUID,
                Within_Study_Sample_ID,CreateTime) VALUES (?,?,?,?,?,?,NOW())";
                if ($result = $db->prepare($sql)) {
                    $result->bind_param('ssssss',$studyid,$studyarm,$patientid,$studypatientid,$samuuid,$studysampleid);
                    $result->execute();
                    $enrollstudyid=$result->insert_id;
                    $result->close();
                }
                $output=["stat"=>"Success!","msg"=>"Successfully add a new enroll study.","class"=>"alert-success","goto"
                =>"enrollstudylist.php?operate=view&enstudyid=".$enrollstudyid];
            }else{
                $output=["stat"=>"Info!","msg"=>"The enroll study with same study name and same sample uuid has already 
                existed in database. If you want to modify it, please click <a class='btn btn-xs btn-primary' 
                href='enrollstudy.php?operate=edit&enstudyid=".$id."'>Edit </a>","class"=>"alert-warning","goto"=>""];
            }
        }
    // if enroll study id is not null, update the enroll study info
    }else{
        $changehistory=new ChangeHistory($db);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Study_ID",$studyid,$userid);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Study_Arm",$studyarm,$userid);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Patient_ID",$patientid,$userid);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Within_Study_Patient_ID",$studypatientid,$userid);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Sample_UUID",$samuuid,$userid);
        $changehistory->recordChangeHistory("EnrollStudy","ID",$enrollstudyid,"Within_Study_Sample_ID",$studysampleid,$userid);
        $changehistory=null;

        if($patientid == null){
            $sql = "UPDATE EnrollStudy SET Study_ID=?,Study_Arm=?,Patient_ID=(SELECT Patient_ID FROM Sample WHERE UUID=?),Within_Study_Patient_ID=?,Sample_UUID=?,
                Within_Study_Sample_ID=? WHERE ID=?";
            if ($result = $db->prepare($sql)) {
                $result->bind_param('sssssss',$studyid,$studyarm,$samuuid,$studypatientid,$samuuid,$studysampleid,$enrollstudyid);
                $result->execute();
                $result->close();
            }
        }else{
            $sql = "UPDATE EnrollStudy SET Study_ID=?,Study_Arm=?,Patient_ID=?,Within_Study_Patient_ID=?,Sample_UUID=?,
                Within_Study_Sample_ID=? WHERE ID=?";
            if ($result = $db->prepare($sql)) {
                $result->bind_param('sssssss',$studyid,$studyarm,$patientid,$studypatientid,$samuuid,$studysampleid,$enrollstudyid);
                $result->execute();
                $result->close();
            }
        }
        $output=["stat"=>"Success!","msg"=>"Successfully update the enroll study.","class"=>"alert-success","goto"
        =>"enrollstudylist.php?operate=view&enstudyid=".$enrollstudyid];
    }
}else{
    $output=["stat"=>"Fail!","msg"=>$errormsg,"class"=>"alert-danger","goto"=>""];
}

$db->close();
echo json_encode($output);
