<?php

    session_start();

    require_once ("class/EscapeString.inc");
    $inputuname=(isset($_POST["username"]) && ($_POST["username"]!==""))?EscapeString::escape($_POST["username"]):null;
    $inputpword=(isset($_POST["password"]) && ($_POST["password"]!==""))?EscapeString::escape($_POST["password"]):null;

    if($inputuname==null || $inputpword==null){
        header("Location:login.php");
    }else{
        require_once ("class/dbencryt.inc");
        require_once("dbsample.inc");
        $db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
//        $db = new mysqli("localhost","wendy","Dianalin","Test");
        if($db->connect_error) {
            die('Unable to connect to database: ' . $db->connect_error);
        }

        if($result=$db->prepare("SELECT ID,Account,Password,Role FROM CodeAccount WHERE Account=? AND Password=?")){
            $result->bind_param("ss",$inputuname,$inputpword);
            $result->execute();
            $result->bind_result($userid,$outputname,$outputpword,$outputrole);
            $result->fetch();
            $result->close();

            if ($outputname===null && $outputpword===null){
                header("Location:login.php?error=1");
            }
            else{
                $_SESSION["userid"]=$userid;
                $_SESSION["user"]=$outputname;
                $_SESSION["role"]=$outputrole;

                if($result=$db->prepare("SELECT DO.View, DO.Add, DO.Modify, DO.Delete, DOP.Project FROM DataOpsPermission AS DOP LEFT JOIN CodeDataOps AS DO".
                    " ON DOP.DataOps=DO.ID WHERE Role=?")){
                    $result->bind_param("s",$outputrole);
                    $result->execute();
                    $result->bind_result($outview,$outadd,$outmod,$outdel,$outproject);
                    $result->fetch();
                    $result->close();

                    if ($outproject===null){
                        $_SESSION["public_private"]="public";
                        $_SESSION["view"]="1";
                        $_SESSION["add"]="0";
                        $_SESSION["modify"]="0";
                        $_SESSION["delete"]="0";
                        $_SESSION["project"]="";
                    }else{
                        $_SESSION["public_private"]="private";
                        $_SESSION["view"]=$outview;
                        $_SESSION["add"]=$outadd;
                        $_SESSION["modify"]=$outmod;
                        $_SESSION["delete"]=$outdel;
                        $_SESSION["project"]=$outproject;
                    }
                }

                header("Location:index.php");
            }
        }

        $db->close();
    }
?>