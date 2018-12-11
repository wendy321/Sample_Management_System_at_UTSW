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
$data=!empty($_POST["data"])?$_POST["data"]:null;
$output=null;
$errormsg="";
$uuids="";
$isfirst=1;
$data=!empty($_POST["data"])?$_POST["data"]:null;
if($data === null){
    $errormsg="You didn't select any study name. Please reselect it.";
}else{
    foreach ($data as $k => $v){
        $uuid=!empty($v["uuid"])?EscapeString::escape($v["uuid"]):null;
        if($uuid===null){
            $errormsg="The sample uuid can not be empty.";
            break;
        }
        $studyid=!empty($v["studyid"])?EscapeString::escape($v["studyid"]):null;
        if($studyid===null){
            $errormsg.="The study name can not be empty.";
            break;
        }

        if($isfirst===1){
            $uuids.=$uuid;
        }else{
            $uuids.=",".$uuid;
        }
        $isfirst=0;

        $oldstudyid=null;
        $sql="SELECT ID FROM EnrollStudy WHERE Study_ID=? AND Sample_UUID=?";
        if($result=$db->prepare($sql)){
            $result->bind_param('s',$uuid);
            $result->execute();
            $result->bind_result($oldstudyid);
            $result->fetch();
            $result->close();
        }

        if($oldstudyid===null){
            $sql="INSERT INTO EnrollStudy (Study_ID,Sample_UUID) VALUES (?,?)";
            if($result=$db->prepare($sql)){
                $result->bind_param('ss',$studyid,$uuid);
                $result->execute();
                $result->close();
            }
        }else{
            $changehistory=new ChangeHistory($db);
            $changehistory->recordChangeHistory("EnrollStudy","ID",$oldstudyid,"Study_ID",$studyid,$userid);
            $changehistory=null;

            $sql="UPDATE EnrollStudy SET Study_ID=? WHERE ID=? AND Sample_UUID=?";
            if($result=$db->prepare($sql)){
                $result->bind_param('sss',$studyid,$oldstudyid,$uuid);
                $result->execute();
                $result->close();
            }
        }
    }
}


if($errormsg!==""){
    $output=["stat"=>"Fail!","msg"=>$errormsg,"class"=>"alert-danger","goto"=>""];
}else{
    $output=["stat"=>"Success!","msg"=>"Successfully link sample and study.","class"=>"alert-success",
        "goto"=>"samplelist.php?operate=view&uuid=".$uuids];
}

$db->close();
echo json_encode($output);
