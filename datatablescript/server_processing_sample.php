<?php
session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

require_once ("../class/EscapeString.inc");
require_once ("../class/SampleID.inc");
require_once ("../class/dbencryt.inc");
require_once ("../dbsample.inc");

$operate=!empty($_GET['operate'])?EscapeString::escape($_GET['operate']):null;
$item=!empty($_GET['item'])?EscapeString::escape($_GET['item']):null;
$uuid=!empty($_GET['uuid'])?EscapeString::escape($_GET['uuid']):null;

// For XLSX batch upload version, get batch sample upload result from remoter database
$new_uuids_str = '';
$new_uuids_patids_studyids_dic = null;
if($uuid==="allnew") {
    $db_remoter = new mysqli(Encryption::decrypt($hostname), Encryption::decrypt($username), Encryption::decrypt($password), Encryption::decrypt($dbname_remoter));
    if ($db_remoter->connect_error) {
        die('Unable to connect to database: ' . $db_remoter->connect_error);
    }

    $sql = "SELECT S.NewSampleUUID, S.NewPatientID, S.NewEnrollStudyID FROM SMSParameters AS P LEFT JOIN SMSSuccessResults AS S ON P.JobID = S.JobID WHERE P.AccountID=? AND P.Tag='1'";
    if ($result = $db_remoter->prepare($sql)) {
        $result->bind_param("i", $userid);
        $result->execute();
        $result->bind_result($newuuid, $newpatid, $newstudyid);
        $isfirst = 0;
        while ($row = $result->fetch()) {
            $new_uuids_patids_studyids_dic[$newuuid]['newpatid'] = $newpatid;
            $new_uuids_patids_studyids_dic[$newuuid]['newstudyidtid'] = $newstudyid;
            if($isfirst == 0){
                $new_uuids_str .= "\"".$newuuid."\"";
                $isfirst = 1;
            }else{
                $new_uuids_str .= ",\"".$newuuid."\"";
            }
        }
        $result->close();
    }
    $db_remoter->close();
}

/*
* DataTables example server-side processing script.
*
* Please note that this script is intentionally extremely simply to show how
* server-side processing can be implemented, and probably shouldn't be used as
* the basis for a large complex system. It is suitable for simple use cases as
* for learning.
*
* See http://datatables.net/usage/server-side for full details on the server-
* side processing requirements of DataTables.
*
* @license MIT - http://datatables.net/license_mit
*/

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* Easy set variables
*/
// DB table to use
$table = 'Sample';

// Table's primary key
$primaryKey = 'UUID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns=null;
$resultpid=null;
if($uuid==="allnew") {
    $columns = array(
        array(
            'db' => '`Sample`.`UUID`',
            'dt' => 0,
            'field' => 'UUID',
            'formatter' => function ($d, $row) {
                global $operate,$item;
                return getUUIDFormat($operate,$item,$d);
            }
        ),
        array(
            'db' => '`Sample`.`Sample_ID`',
            'dt' => 1, 'field' => 'Sample_ID',
            'formatter' => function ($d, $row) {
                global $hostname, $username,$password,$dbname_sample,$resultpid;
                $db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),
                    Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
                if($db->connect_error){
                    die('Unable to connect to database: ' . $db->connect_error);
                }
                $samid=SampleID::getConvertedSampleID($db,$row[0]);
                $db->close();
                $resultpid = substr($samid, 0, -6);
                $pathological = substr($samid, -6, 2);
                $sampleclass = substr($samid, -4, 2);
                $autoincre = substr($samid, -2, 2);
                return $resultpid . $pathological . $sampleclass . $autoincre;
            }
        ),
        array(
            'db' => '`Sample`.`Local_Sample_ID`',
            'dt' => 2, 'field' => 'Local_Sample_ID',
        ),
        array(
            'db' => '`Sample`.`Parent_UUID`',
            'dt' => 3, 'field' => 'Parent_UUID'
        ),
        array(
            'db' => '`Sample`.`Date_Derive_From_Parent`',
            'dt' => 4, 'field' => 'Date_Derive_From_Parent',
        ),
        array(
            'db' => '`Sample`.`Patient_ID`',
            'dt' => 5, 'field' => 'Patient_ID',
            'formatter' => function ($d, $row) {
                global $new_uuids_patids_studyids_dic;
                if($row[36]==='1') {
                    return '';
                }else{
                    if(!empty($new_uuids_patids_studyids_dic[$row[0]]['newpatid'])){
                        return '<u>'.$d.'</u> <a class="btn btn-xs btn-success editpid" href="patient.php?operate=edit&pid='.$d.'" target="_blank"> Edit </a>';
                    }else{
                        if ($d !== null) {
                            return '<a href="patientlist.php?operate=view&pid='.$d.'"><u>'.$d.'</u></a>';
                        }else{
                            return '';
                        }
                    }
                }
            }
        ),
        array(
            'db' => '`pat`.`Local_Patient_ID`',
            'dt' => 6, 'field' => 'Local_Patient_ID',
        ),
        array(
            'db' => 'Initial_CodeStudy',
            'dt' => 7, 'field' => 'Initial_CodeStudy',
            'formatter' => function ($d, $row) {
                global $new_uuids_patids_studyids_dic;
                if($row[35]==='1'){
                    return '';
                }else{
                    if(!empty($new_uuids_patids_studyids_dic[$row[0]]['newstudyidtid'])){
                        return '<u>'.$d.'</u> <a class="btn btn-xs btn-success editenstudyid" href="enrollstudy.php?operate=edit&enstudyid='.$row[37].'" target="_blank"> Edit </a>';
                    }else{
                        if ($d !== null) {
                            return '<a href="enrollstudylist.php?operate=view&enstudyid='.$row[37].'"><u>'.$d.'</u></a>';
                        }else{
                            return '';
                        }
                    }
                }
            }
        ),
        array(
            'db' => '`enrollstudy`.`Within_Study_Sample_ID`',
            'dt' => 8, 'field' => 'Within_Study_Sample_ID',
            'formatter' => function($d, $row){
                if($row[35]==='1'){
                    return '';
                }else{
                    return $d;
                }
            }
        ),
        array(
            'db' => '`enrollstudy`.`Within_Study_Patient_ID`',
            'dt' => 9, 'field' => 'Within_Study_Patient_ID',
            'formatter' => function ($d, $row) {
                if($row[35]==='1'){
                    return '';
                }else{
                    return $d;
                }
            }
        ),
        array(
            'db' => 'Initial_CodeDataContributorClinicalTrialGroup',
            'dt' => 10, 'field' => 'Initial_CodeDataContributorClinicalTrialGroup',
        ),
        array(
            'db' => 'Initial_SampleContributorInstitute',
            'dt' => 11, 'field' => 'Initial_SampleContributorInstitute',
        ),
        array(
            'db' => 'Initial_ProcedureType',
            'dt' => 12, 'field' => 'Initial_ProcedureType',
        ),
        array(
            'db' => '`Sample`.`Date_Procedure`',
            'dt' => 13, 'field' => 'Date_Procedure',
        ),
        array(
            'db' => 'Initial_PathologicalStatus',
            'dt' => 14, 'field' => 'Initial_PathologicalStatus',
        ),
        array(
            'db' => 'Initial_SampleClass',
            'dt' => 15, 'field' => 'Initial_SampleClass',
        ),
        array(
            'db' => 'Initial_SampleType',
            'dt' => 16, 'field' => 'Initial_SampleType',
        ),
        array(
            'db' => 'Initial_SpecimenType',
            'dt' => 17, 'field' => 'Initial_SpecimenType',
        ),
        array(
            'db' => 'Initial_NucleotideSizeGroup200',
            'dt' => 18, 'field' => 'Initial_NucleotideSizeGroup200',
        ),
        array(
            'db' => 'Initial_AnatomicalSite',
            'dt' => 19, 'field' => 'Initial_AnatomicalSite',
        ),
        array(
            'db' => 'Initial_AnatomicalLaterality',
            'dt' => 20, 'field' => 'Initial_AnatomicalLaterality',
        ),
        array(
            'db' => 'Initial_StorageRoom',
            'dt' => 21, 'field' => 'Initial_StorageRoom',
        ),
        array(
            'db' => 'Initial_StorageCabinetType',
            'dt' => 22, 'field' => 'Initial_StorageCabinetType',
        ),
        array(
            'db' => 'Initial_Temperature',
            'dt' => 23, 'field' => 'Initial_Temperature',
        ),
        array(
            'db' => '`Sample`.`Cabinet_Number`',
            'dt' => 24, 'field' => 'Cabinet_Number',
        ),
        array(
            'db' => '`Sample`.`Shelf_Number`',
            'dt' => 25, 'field' => 'Shelf_Number',
        ),
        array(
            'db' => '`Sample`.`Rack_Number`',
            'dt' => 26, 'field' => 'Rack_Number',
        ),
        array(
            'db' => '`Sample`.`Box_Number`',
            'dt' => 27, 'field' => 'Box_Number',
        ),
        array(
            'db' => '`Sample`.`Position_Number`',
            'dt' => 28, 'field' => 'Position_Number',
        ),
        array(
            'db' => '`Sample`.`Quantity_Value`',
            'dt' => 29, 'field' => 'Quantity_Value',
        ),
        array(
            'db' => 'Initial_AmountUnit',
            'dt' => 30, 'field' => 'Initial_AmountUnit',
        ),
        array(
            'db' => '`Sample`.`Concentration_Value`',
            'dt' => 31, 'field' => 'Concentration_Value',
        ),
        array(
            'db' => '`Sample`.`Concentration_Unit`',
            'dt' => 32, 'field' => 'Concentration_Unit',
        ),
        array(
            'db' => '`Sample`.`Notes`',
            'dt' => 33, 'field' => 'Notes',
        ),
        array(
            'db' => '`Sample`.`CreateTime`',
            'dt' => 34, 'field' => 'CreateTime',
        ),
        array(
            'db' => '`enrollstudy`.`isDelete`',
            'dt' => 35, 'field' => 'isDelete',
        ),
        array(
            'db' => '`pat`.`isDelete`',
            'dt' => 36, 'field' => 'isDelete',
        ),
        array(
            'db' => '`enrollstudy`.`ID`',
            'dt' => 37, 'field' => 'ID'
        ),
//    ,array(
//        'db'=> '',
//        'dt'=>22,
//        'formatter' => function($d,$row){
//            return "<img src='../idautomation-datamatrix-demo-static.php'>";
//        }
//    )
    );
}else{
    $columns = array(
        array(
            'db' => '`Sample`.`UUID`',
            'dt' => 0, 'field' => 'UUID',
            'formatter' => function ($d, $row) {
                global $operate,$item;
                return getUUIDFormat($operate,$item,$d);
            }
        ),
        array(
            'db' => '`Sample`.`Sample_ID`',
            'dt' => 1, 'field' => 'Sample_ID',
            'formatter' => function ($d, $row) {
                global $hostname, $username,$password,$dbname_sample,$resultpid;
                $db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),
                    Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
                if($db->connect_error){
                    die('Unable to connect to database: ' . $db->connect_error);
                }
                $samid=SampleID::getConvertedSampleID($db,$row[0]);
                $db->close();
                $resultpid = substr($samid, 0, -6);
                $pathological = substr($samid, -6, 2);
                $sampleclass = substr($samid, -4, 2);
                $autoincre = substr($samid, -2, 2);
                return $resultpid . $pathological . $sampleclass . $autoincre;
            }
        ),
        array(
            'db' => '`Sample`.`Local_Sample_ID`',
            'dt' => 2, 'field' => 'Local_Sample_ID',
            ),
        array(
            'db' => '`Sample`.`Parent_UUID`',
            'dt' => 3, 'field' => 'Parent_UUID',
            'formatter' => function ($d, $row) {
                global $operate, $item;
                if ($d === null) {
                    if (strpos($operate,'edit') !== FALSE && $item === "parentsample") {
                        return "<label for='ddl_parentuuid'>
                                    <input type='text' class='form-control' id='ddl_parentuuid' name='parentuuid[]' 
                                    value='' data-toggle='modal' data-target='#sample_modal'/>
                                </label>";
                    }
                }
                return $d;
            }
        ),
        array(
            'db' => '`Sample`.`Date_Derive_From_Parent`',
            'dt' => 4, 'field' => 'Date_Derive_From_Parent',
        ),
        array(
            'db' => '`Sample`.`Patient_ID`',
            'dt' => 5, 'field' => 'Patient_ID',
            'formatter' => function ($d, $row) {
                global $operate, $item;
                if($row[36]==='1'){
                    return '';
                }else{
                    if (strpos($operate, 'edit') !== FALSE && $item === "patient") {
                        return "<label for='ddl_patientid'>
                                    <input type='text' class='form-control text-center' name='patientid' 
                                    value='" . $d . "' data-toggle='modal' data-target='#patient_modal'/>
                                </label>";
                    } else {
                        if ($d !== null) {
                            return '<a href="patientlist.php?operate=view&pid='.$d.'"><u>'.$d.'</u></a>';
                        }else{
                            return '';
                        }
                    }
                }
            }
        ),
        array(
            'db' => '`pat`.`Local_Patient_ID`',
            'dt' => 6, 'field' => 'Local_Patient_ID',
        ),
        array(
            'db' => 'Initial_CodeStudy',
            'dt' => 7, 'field' => 'Initial_CodeStudy',
            'formatter' => function ($d, $row) {
                global $operate, $item;
                if($row[35]==='1'){
                    return '';
                }else{
                    if (strpos($operate,'edit') !== FALSE && $item === "study") {
                        return "<label for='ddl_studyname'>
                                    <select class=\"form-control\" id=\"ddl_studyname\" name=\"studyid\">
                                        <option label=\"Please select ...\" value=\"99\">Please select ...</option>
                                        <option value=\"2\">P9749</option>
                                        <option value=\"6\">AGCT0132</option>
                                        <option value=\"4\">AGCT01P1</option>
                                        <option value=\"14\">AGCT0521</option>
                                        <option value=\"9\">GC 1</option>
                                        <option value=\"8\">GC 2</option>
                                        <option value=\"5\">GOG 0078</option>
                                        <option value=\"1\">GOG 0090</option>
                                        <option value=\"11\">GOG 0116</option>
                                        <option value=\"7\">INT-0097</option>
                                        <option value=\"10\">INT-0106</option>
                                        <option value=\"3\">OPTF</option>
                                        <option value=\"13\">TCG_99</option>
                                        <option value=\"12\">TGM95</option>
                                        <option value=\"98\">Other</option>
                                        <option value=\"99\">Unknown</option>
                                        <option value=\"100\">Not In Clinical Trial</option>
                                    </select>
                                </label>";
                    }else{
                        return $d;
                    }
                }
            }
        ),
        array(
            'db' => '`enrollstudy`.`Within_Study_Sample_ID`',
            'dt' => 8, 'field' => 'Within_Study_Sample_ID',
            'formatter' => function ($d, $row) {
                if($row[35]==='1'){
                    return '';
                }else{
                    return $d;
                }
            }
        ),
        array(
            'db' => '`enrollstudy`.`Within_Study_Patient_ID`',
            'dt' => 9, 'field' => 'Within_Study_Patient_ID',
            'formatter' => function ($d, $row) {
                if($row[35]==='1'){
                    return '';
                }else{
                    return $d;
                }
            }
        ),
        array(
            'db' => 'Initial_CodeDataContributorClinicalTrialGroup',
            'dt' => 10, 'field' => 'Initial_CodeDataContributorClinicalTrialGroup',
        ),
        array(
            'db' => 'Initial_SampleContributorInstitute',
            'dt' => 11, 'field' => 'Initial_SampleContributorInstitute',
        ),
        array(
            'db' => 'Initial_ProcedureType',
            'dt' => 12, 'field' => 'Initial_ProcedureType',
        ),
        array(
            'db' => '`Sample`.`Date_Procedure`',
            'dt' => 13, 'field' => 'Date_Procedure',
        ),
        array(
            'db' => 'Initial_PathologicalStatus',
            'dt' => 14, 'field' => 'Initial_PathologicalStatus',
        ),
        array(
            'db' => 'Initial_SampleClass',
            'dt' => 15, 'field' => 'Initial_SampleClass',
        ),
        array(
            'db' => 'Initial_SampleType',
            'dt' => 16, 'field' => 'Initial_SampleType',
        ),
        array(
            'db' => 'Initial_SpecimenType',
            'dt' => 17, 'field' => 'Initial_SpecimenType',
        ),
        array(
            'db' => 'Initial_NucleotideSizeGroup200',
            'dt' => 18, 'field' => 'Initial_NucleotideSizeGroup200',
        ),
        array(
            'db' => 'Initial_AnatomicalSite',
            'dt' => 19, 'field' => 'Initial_AnatomicalSite',
        ),
        array(
            'db' => 'Initial_AnatomicalLaterality',
            'dt' => 20, 'field' => 'Initial_AnatomicalLaterality',
        ),
        array(
            'db' => 'Initial_StorageRoom',
            'dt' => 21, 'field' => 'Initial_StorageRoom',
        ),
        array(
            'db' => 'Initial_StorageCabinetType',
            'dt' => 22, 'field' => 'Initial_StorageCabinetType',
        ),
        array(
            'db' => 'Initial_Temperature',
            'dt' => 23, 'field' => 'Initial_Temperature',
        ),
        array(
            'db' => '`Sample`.`Cabinet_Number`',
            'dt' => 24, 'field' => 'Cabinet_Number',
        ),
        array(
            'db' => '`Sample`.`Shelf_Number`',
            'dt' => 25, 'field' => 'Shelf_Number',
        ),
        array(
            'db' => '`Sample`.`Rack_Number`',
            'dt' => 26, 'field' => 'Rack_Number',
        ),
        array(
            'db' => '`Sample`.`Box_Number`',
            'dt' => 27, 'field' => 'Box_Number',
        ),
        array(
            'db' => '`Sample`.`Position_Number`',
            'dt' => 28, 'field' => 'Position_Number',
        ),
        array(
            'db' => '`Sample`.`Quantity_Value`',
            'dt' => 29, 'field' => 'Quantity_Value',
        ),
        array(
            'db' => 'Initial_AmountUnit',
            'dt' => 30, 'field' => 'Initial_AmountUnit',
        ),
        array(
            'db' => '`Sample`.`Concentration_Value`',
            'dt' => 31, 'field' => 'Concentration_Value',
        ),
        array(
            'db' => '`Sample`.`Concentration_Unit`',
            'dt' => 32, 'field' => 'Concentration_Unit',
        ),
        array(
            'db' => '`Sample`.`Notes`',
            'dt' => 33, 'field' => 'Notes',
        ),
        array(
            'db' => '`Sample`.`CreateTime`',
            'dt' => 34, 'field' => 'CreateTime',
        ),
        array(
            'db' => '`enrollstudy`.`isDelete`',
            'dt' => 35, 'field' => 'isDelete',
        ),
        array(
            'db' => '`pat`.`isDelete`',
            'dt' => 36, 'field' => 'isDelete',
        )
//    ,array(
//        'db'=> '',
//        'dt'=>22,
//        'formatter' => function($d,$row){
//            return "<img src='../idautomation-datamatrix-demo-static.php'>";
//        }
//    )
    );
}

// SQL server connection information
$sql_details = array(
    'user' => Encryption::decrypt($username),
    'pass' => Encryption::decrypt($password),
    'db'   => Encryption::decrypt($dbname_sample),
    'host' => Encryption::decrypt($hostname)
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* If you just want to use the basic configuration for DataTables with PHP
* server-side, there is no need to edit below this line.
*/

require( '../class/SSP_customized.php' );

$joinQuery="FROM `{$table}`";
// For CSV batch upload version
//if($uuid==="allnew") {
//    $joinQuery = " INNER JOIN `BatchUploadSampleLog` AS `busl` ON (`{$table}`.`UUID`=`busl`.`Sample_UUID`) ";
//}
$joinQuery .= " LEFT JOIN `EnrollStudy` AS `enrollstudy` ON (`{$table}`.`UUID` = `enrollstudy`.`Sample_UUID`)" .
    " LEFT JOIN `Patient` AS `pat` ON `{$table}`.`Patient_ID`=`pat`.`Patient_ID`" .
    " LEFT JOIN `CodeStudy` AS `cs` ON `enrollstudy`.`Study_ID`=`cs`.`ID`".
    " LEFT JOIN `CodeDataContributorClinicalTrialGroup` AS `cdcctg` ON `{$table}`.`Sample_Contributor_Consortium_ID`=`cdcctg`.`ID`" .
    " LEFT JOIN `CodeSampleContributorInstitute` AS `csct` ON `{$table}`.`Sample_Contributor_Institute_ID`=`csct`.`ID`" .
    " LEFT JOIN `CodeProcedureType` AS `cpt` ON `{$table}`.`Procedure_Type`=`cpt`.`ID`" .
    " LEFT JOIN `CodePathologicalStatus` AS `cps` ON `{$table}`.`Pathological_Status`=`cps`.`ID`" .
    " LEFT JOIN `CodeSampleClass` AS `csc` ON `{$table}`.`Sample_Class`=`csc`.`ID`" .
    " LEFT JOIN `CodeSampleType` AS `cst` ON `{$table}`.`Sample_Type`=`cst`.`ID`" .
    " LEFT JOIN `CodeSpecimenType` AS `cspt` ON `{$table}`.`Specimen_Type`=`cspt`.`ID`" .
    " LEFT JOIN `CodeNucleotideSizeGroup200` AS `cnsg200` ON `{$table}`.`Nucleotide_Size_Group_200`=`cnsg200`.`ID`" .
    " LEFT JOIN `CodeAnatomicalSite` AS `cas` ON `{$table}`.`Anatomical_Site`=`cas`.`ID`" .
    " LEFT JOIN `CodeAnatomicalLaterality` AS `cal` ON `{$table}`.`Anatomical_Laterality`=`cal`.`ID`" .
    " LEFT JOIN `CodeStorageRoom` AS `csr` ON `{$table}`.`Storage_Room`=`csr`.`ID`" .
    " LEFT JOIN `CodeStorageCabinetType` AS `cscabt` ON `{$table}`.`Cabinet_Type`=`cscabt`.`ID`" .
    " LEFT JOIN `CodeTemperature` AS `ct` ON `{$table}`.`Cabinet_Temperature`=`ct`.`ID`" .
    " LEFT JOIN `CodeAmountUnit` AS `cau` ON `{$table}`.`Quantity_Unit`=`cau`.`ID`";

// sql condition for linking unlinked-patient_id to sample uuid
$patientIsNullCondition="";
if(strpos($operate,'edit') !== FALSE && $item==="patient"){
    $patientIsNullCondition=" AND `{$table}`.`Patient_ID` REGEXP '^[A-Z]{7}$' OR `{$table}`.`Patient_ID` IS NULL ";
}

// sql condition for add parent sample
$parentIsNullCondition="";
if (strpos($operate,'edit') !== FALSE && $item === "parentsample") {
    $parentIsNullCondition=" AND `{$table}`.`Parent_UUID` IS NULL ";
}

// sql condition for linking enroll study id to sample uuid
$studyIsNullCondition="";
if(strpos($operate,'edit') !== FALSE && $item==="study"){
    $studyIsNullCondition=" AND (`enrollstudy`.`Study_ID` = 99 OR `enrollstudy`.`Study_ID` IS NULL) ";
}

$extraCondition=null;
if($uuid==="allnew"){
    if($new_uuids_str !== ''){
        $extraCondition = "`{$table}`.`{$primaryKey}` IN ({$new_uuids_str}) AND `{$table}`.`isDelete` = 0 ";
    }else{
        $extraCondition = "`{$table}`.`{$primaryKey}` is NULL AND `{$table}`.`isDelete` = 0 ";
    }

}elseif(strpos($uuid,',') !== FALSE){
    $uuidarr=explode(",",$uuid);
    $uuidstr="";
    $isfirst=1;
    foreach ($uuidarr as $k=>$v){
        if($isfirst===1){
            $uuidstr.="\"".$v."\"";
            $isfirst=0;
        }else{
            $uuidstr.=",\"".$v."\"";
        }
    }
    $extraCondition = "`{$table}`.`{$primaryKey}` IN (".$uuidstr.") AND `{$table}`.`isDelete` = 0 ";
}elseif($uuid!==null){
    $extraCondition = "`{$table}`.`{$primaryKey}` =\"".$uuid."\" AND `{$table}`.`isDelete` = 0 ";
}else{
    $extraCondition ="`{$table}`.`isDelete` = 0 ";
}

$extraCondition.=$patientIsNullCondition.$parentIsNullCondition.$studyIsNullCondition;

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraCondition)
);


function getUUIDFormat($operate,$item,$d){
    $format='';
    if(strpos($operate,'edit') !== FALSE ){
        $format.="<a class='btn btn-xs btn-success editsampleid' href='sample.php?operate=edit&uuid=".$d."'> Edit </a>";
        if($item !== null){
            $format.="<input type='radio' class='hidden' name='sampleuuid[]' value='".$d."' checked/>";
        }
    }

    if(strpos($operate,'delete') !== FALSE ){
        $format.="<button type='button' class='btn btn-xs btn-danger deletesample' 
                 data-toggle='modal' data-target='#deletesample_modal'> Delete </button>";
    }

    if(strpos($operate,'select') !== FALSE && $item === null){
        $format.="<input type='radio' name='sampleuuid' value='".$d."'/>";
    }

    return $format.$d;
}