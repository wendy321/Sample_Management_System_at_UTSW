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
$uuid=!empty($_POST["uuid"])?EscapeString::escape($_POST["uuid"]):null;

if($uuid!==null){
    $sql="UPDATE Sample SET isDelete=1 WHERE UUID=?;";
    if($result = $db->prepare($sql)) {
        $result->bind_param("s",$uuid);
        $result->execute();
        $result->close();
    }
    echo json_encode(array("stat"=>"Success! ","msg"=>"Success to delete the sample.", "class"=>"alert-success"));
}else{
    echo json_encode(array("stat"=>"Fail! ","msg"=>"Fail to delete the sample.", "class"=>"alert-danger"));
}

$db->close();