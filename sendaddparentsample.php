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
    $errormsg="The sent data can not be empty.";
}else{
    foreach ($data as $k => $v){
        $sampleuuid=!empty($v["sampleuuid"])?EscapeString::escape($v["sampleuuid"]):null;
        if($sampleuuid===null){
            $errormsg="The sample uuid can not be empty.";
            break;
        }
        $parentuuid=!empty($v["parentuuid"])?EscapeString::escape($v["parentuuid"]):null;
        if($sampleuuid===null){
            $errormsg.="The parent sample uuid can not be empty.";
            break;
        }

        if($isfirst===1){
            $uuids.=$sampleuuid;
        }else{
            $uuids.=",".$sampleuuid;
        }
        $isfirst=0;

        // Patient ids of sample_uuid and parent_sample_uuid should be the same.
        $pid=null;
        $parentpid=null;
        $sql="SELECT Patient_ID, (SELECT Patient_ID FROM Sample WHERE UUID=?) FROM Sample WHERE UUID=?";
        if($result=$db->prepare($sql)){
            $result->bind_param('ss',$sampleuuid,$parentuuid);
            $result->execute();
            $result->bind_result($pid,$parentpid);
            $result->fetch();
            $result->close();

            // Samples and parent samples relationship shouldn't form a cycle.(Don't know how to check it. Will solve it later)
            if($pid===$parentpid){
                $changehistory=new ChangeHistory($db);
                $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Parent_UUID",$parentuuid,$userid);
                $changehistory=null;

                $sql="UPDATE Sample SET Parent_UUID=? WHERE UUID=?";
                if($result=$db->prepare($sql)){
                    $result->bind_param('ss',$parentuuid,$sampleuuid);
                    $result->execute();
                    $result->close();
                }
            }else{
                $errormsg="The Patient_ID of Sample_UUID and Parent_Sample_UUID are different. 
                        Patient_ID should be the same. Please reselect Parent_Sample_UUID.";
            }
        }
    }
}

if($errormsg!==""){
    $output=["stat"=>"Fail!","msg"=>$errormsg,"class"=>"alert-danger","goto"=>""];
}else{
    $output=["stat"=>"Success!","msg"=>"Successfully add parent sample.","class"=>"alert-success",
        "goto"=>"samplelist.php?operate=view&uuid=".$uuids];
}

$db->close();
echo json_encode($output);
