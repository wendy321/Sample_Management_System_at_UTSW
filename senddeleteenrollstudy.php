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
$enrollstudyid=!empty($_POST["enrollstudyid"])?EscapeString::escape($_POST["enrollstudyid"]):null;

if($enrollstudyid!==null){
    $sql="UPDATE EnrollStudy SET isDelete=1 WHERE ID=?;";
    if($result = $db->prepare($sql)) {
        $result->bind_param("s",$enrollstudyid);
        $result->execute();
        $result->close();
    }
    echo json_encode(array("stat"=>"Success! ","msg"=>"Success to delete the enroll study.", "class"=>"alert-success"));
}else{
    echo json_encode(array("stat"=>"Fail! ","msg"=>"Fail to delete the enroll study.", "class"=>"alert-danger"));
}

$db->close();