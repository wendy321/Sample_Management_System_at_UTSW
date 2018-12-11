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
$db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_remoter));
if($db->connect_error){
    die('Unable to connect to database: ' . $db->connect_error);
}

// Escape special characters in inputs
require_once ("class/EscapeString.inc");
foreach ($_POST as $k => $v){
    $_POST[$k]=EscapeString::escape($v);
}

// Utility classes
require_once ('class/ChangeHistory.inc');
require_once ('class/PatientID.inc');
require_once ('class/SampleID.inc');
require_once ('class/InputFormat.inc');

$method=(!empty($_POST['method']))?$_POST['method']:null;

// if batch upload
$issuccess=true;
$errormsg='';
$output = [];
if($method === "2"){
    /* check file existence, file type, file error, file size */
    if (empty($_FILES['inputfile'])) {
        $errormsg.='No file is uploaded. Please import a file.';
    }
    $file = $_FILES['inputfile'];
    $filename = $file['name'];
    $type = $file['type'];
//    if($type !=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || $type != "application/vnd.ms-excel"){
    if($type !=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
        $issuccess=false;
        $errormsg.='Wrong file type. Please import XSLX file.';
    }
    $error=$file['error'];
    if($error > 0){
        $issuccess=false;
        $errormsg.=' Fail to upload file.';
    }
    $size = $file['size'];
    if($size > 1000000){
        $issuccess=false;
        $errormsg.=' File size is too large. File size should be <= 1 MB.';
    }

    if($issuccess == false){
        echo json_encode(['goto'=>'','error'=>$errormsg]);
    }else{
        /* create a remoter job and store the file into the database */
        // change previous batch upload tag to 2
        $sql="UPDATE SMSParameters SET Tag='2' WHERE Account=? AND Tag='1'";
        if($result = $db->prepare($sql)){
            $result->bind_param("i",$userid);
            $result->execute();
            $result->close();
        }

        // insert new job
        $jobid = uniqid("", TRUE);
        $sql="INSERT INTO Jobs(JobID,Software,Analysis,Status,CreateTime) VALUES (?,\"samplemanagementsystem\",\"samplebatchupload\",0,now())";
        if($result = $db->prepare($sql)){
            $result->bind_param("s",$jobid);
            $result->execute();
            $result->close();
        }

        // insert new job parameter with user account id, batch upload tag 1, data type, and blob xlsx file
        $sql="INSERT INTO SMSParameters(JobID,AccountID,Tag,DataType,XLSXFile) VALUES (?,?,1,\"sample\",?)";
        if($result = $db->prepare($sql)){
            $result->bind_param("sib",$jobid,$userid,$blob);

            $fp = fopen($file['tmp_name'], "rb");
            while (!feof($fp)) {
                $result->send_long_data(2, fread($fp,filesize($file['tmp_name'])));
            }
            fclose($fp);

            $result->execute();
            $result->close();
        }
        echo json_encode(['goto'=>'samplelist.php?operate=view&uuid=allnew&userid='.$userid,'error'=>'']);
    }
// No input method is selected
}else{
    echo json_encode(['goto'=>'','error'=>'No input method is selected. Please select single sample input or batch sample input.']);
}

$db->close();

