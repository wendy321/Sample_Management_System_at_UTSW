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

$db->set_charset("utf8");

// Escape special characters in inputs
require ('class/EscapeString.inc');
foreach ($_POST as $k => $v){
    $_POST[$k]=EscapeString::escape($v);
}

// get and transform input variables
$sampleuuid=!empty($_POST['sampleuuid'])?$_POST['sampleuuid']:null;
if($sampleuuid === null){
    $db->close();
    die('Sample UUID can not be empty.');
}
$sampleid=!empty($_POST['sampleid'])?$_POST['sampleid']:null;
$pid=!empty($_POST['pid'])?$_POST['pid']:null;
$studylocalsampleid=!empty($_POST['studylocalsampleid'])?$_POST['studylocalsampleid']:null;
if($studylocalsampleid === null){
    $db->close();
    die('Local Sample ID can not be empty.');
}
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
$parentuuid=!empty($_POST['parentuuid'])?$_POST['parentuuid']:null;
$deriveddate=!empty($_POST['deriveddate'])?$_POST['deriveddate']:null;
$notes=!empty($_POST['notes'])?$_POST['notes']:null;

// jump out of script if patient_ids of sample_uuid and parent_sample_uuid are different
if($parentuuid!==null){
    $sql="SELECT Patient_ID FROM Sample WHERE UUID=?";
    if($result = $db->prepare($sql)) {
        $result->bind_param("s",$parentuuid);
        $result->execute();
        $result->bind_result($parentpid);
        $result->fetch();
        $result->close();

        if($parentpid!==$pid){
            $db->close();
            header("Location:sample.php?operate=edit&uuid=".$sampleuuid."&err=1"."&parentpid=".$parentpid."&pid=".$pid."&psuuid=".$parentuuid);
            die("Error: The input Patient ID and the Patient ID of the input Parent Sample UUID are different");
        }
    }
}
// jump out of script if there is a cycle between sample_uuid and parent_sample_uuid
// p.s. what if the loop length is too long, which takes a lot of computing time?

// calculate new sample id
require ('class/SampleID.inc');
$arr=SampleID::generateSampleID($db,$pid,$pathological,$sampleclass);
$newsampleid=$arr[0];
$newpid=$arr[1];

// update sample information
require_once ('class/ChangeHistory.inc');
if($newsampleid==null){
    $db->close();
    die('Error: Please contact developer.');
}else{

    $changehistory=new ChangeHistory($db);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Sample_ID",$newsampleid,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Patient_ID",$newpid,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Local_Sample_ID",$studylocalsampleid,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Sample_Contributor_Consortium_ID",$consortium,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Sample_Contributor_Institute_ID",$institute,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Storage_Room",$room,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Cabinet_Type",$cabinettype,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Cabinet_Temperature",$cabinettemp,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Cabinet_Number",$cabinetnum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Shelf_Number",$shelfnum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Rack_Number",$racknum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Box_Number",$boxnum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Position_Number",$posnum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Quantity_Value",$quantitynum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Quantity_Unit",$quantityunit,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Concentration_Value",$concennum,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Concentration_Unit",$concenunit,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Procedure_Type",$proceduretype,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Date_Procedure",$proceduredate,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Parent_UUID",$parentuuid,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Date_Derive_From_Parent",$deriveddate,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Pathological_Status",(string)((int)$pathological),$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Sample_Class",(string)((int)$sampleclass),$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Sample_Type",$sampletype,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Specimen_Type",$specimentype,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Nucleotide_Size_Group_200",$nucleotidesize,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Anatomical_Site",$anatomicalsite,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Anatomical_Laterality",$anatomicallaterality,$userid);
    $changehistory->recordChangeHistory("Sample","UUID",$sampleuuid,"Notes",$notes,$userid);

    // destroy objects
    // p.s. (1) won't destroy object if explicitly call destructor
    //      (2) won't destroy object if $changehistory=null and another object points to the same memory
    $changehistory=null;


    $sql="UPDATE Sample SET Sample_ID=?,Patient_ID=?,Local_Sample_ID=?,Sample_Contributor_Consortium_ID=?,
          Sample_Contributor_Institute_ID=?,
          Storage_Room=?,Cabinet_Type=?,Cabinet_Temperature=?,Cabinet_Number=?,Shelf_Number=?,Rack_Number=?,Box_Number=?,Position_Number=?,
          Quantity_Value=?,Quantity_Unit=?,Concentration_Value=?,Concentration_Unit=?,
          Procedure_Type=?,Date_Procedure=?,Parent_UUID=?,Date_Derive_From_Parent=?,Pathological_Status=?,Sample_Class=?,Sample_Type=?,
          Specimen_Type=?,Nucleotide_Size_Group_200=?,Anatomical_Site=?,Anatomical_Laterality=?,Notes=? WHERE UUID=?;";
    if($result = $db->prepare($sql)) {
        $result->bind_param("ssssssssssssssssssssssssssssss",$newsampleid,$newpid,$studylocalsampleid,$consortium,$institute,
            $room,$cabinettype,$cabinettemp,$cabinetnum,$shelfnum,$racknum,$boxnum,$posnum,$quantitynum,$quantityunit,
            $concennum,$concenunit,
            $proceduretype,$proceduredate,$parentuuid,$deriveddate,$pathological,$sampleclass,$sampletype,
            $specimentype,$nucleotidesize,$anatomicalsite,$anatomicallaterality,$notes,$sampleuuid);
        $result->execute();
        $result->close();
    }
}

header("Location:samplelist.php?operate=view&uuid=".$sampleuuid);
$db->close();