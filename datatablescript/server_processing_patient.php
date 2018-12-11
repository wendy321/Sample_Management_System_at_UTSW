<?php
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

require_once ("../class/dbencryt.inc");
require_once ("../class/EscapeString.inc");
require_once ("../dbsample.inc");

// DB table to use
$table = 'Patient';

// Table's primary key
$primaryKey = 'Patient_ID';

// Operation for patients decides style in column formatter
$operate=!empty($_GET['operate'])?EscapeString::escape($_GET['operate']):null;
$item=!empty($_GET['item'])?EscapeString::escape($_GET['item']):null;
$pid=!empty($_GET['pid'])?EscapeString::escape($_GET['pid']):null;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$result='';
$columns = array(
    array(
        'db' => 'Patient_ID',
        'dt' => 0,
        'field' => 'Patient_ID',
        'formatter' => function( $d, $row ) {
            global $operate;
            return getPatientIDFormat($operate,$d);
        }
    ),
    array(
        'db' => 'Local_Patient_ID',
        'dt' => 1,
        'field' => 'Local_Patient_ID',
        /* Can't search data by inputting new formatted string into DataTable search API,
            if enable the server-side processing */
//        'formatter' => function( $d, $row ) {
//            if ($d == '') {
//                return 'Unknown';
//            } else {
//                return $d;
//            }
//        }
    ),
    array(
        'db' => 'Initial_CodeDataContributorClinicalTrialGroup',
        'dt' => 2,
        'field' => 'Initial_CodeDataContributorClinicalTrialGroup'
    ),
    array(
        'db' => 'Initial_DataContributorCenter',
        'dt' => 3,
        'field' => 'Initial_DataContributorCenter',
    ),
    array(
        'db' => 'Age_At_Enrollment_In_Days',
        'dt' => 4 ,
        'field' => 'Age_At_Enrollment_In_Days',
    ),
    array(
        'db' => 'Relapsed_At_Enrollment',
        'dt' => 5 ,
        'field' => 'Relapsed_At_Enrollment',
        'formatter' => function( $d, $row ) {
            switch ($d){
                case null: $result='';break;
                case 1: $result='Yes';break;
                case 0: $result='No';break;
                default: $result='';
            }
            return $result;
        }
    ),
    array(
        'db' => 'Age_At_First_Visit_In_Days',
        'dt' => 6 ,
        'field' => 'Age_At_First_Visit_In_Days',
    ),
    array(
        'db' => 'Relapsed_At_First_Visit',
        'dt' => 7 ,
        'field' => 'Relapsed_At_First_Visit',
        'formatter' => function( $d, $row ) {
            switch ($d){
                case null: $result='';break;
                case 1: $result='Yes';break;
                case 0: $result='No';break;
                default: $result='';
            }
            return $result;
        }
    ),
    array(
        'db' => 'Age_At_Diagnosis_In_Days',
        'dt' => 8 ,
        'field' => 'Age_At_Diagnosis_In_Days',
    ),
    array(
        'db' => 'Year_Of_Diagnosis',
        'dt' => 9 ,
        'field' => 'Year_Of_Diagnosis',
    ),
    array(
        'db' => 'Dysgenetic_Gonad',
        'dt' => 10 ,
        'field' => 'Dysgenetic_Gonad',
        'formatter' => function( $d, $row ) {
            switch ($d){
                case null: $result='';break;
                case 1: $result='Yes';break;
                case 0: $result='No';break;
                default: $result='';
            }
            return $result;
        }
    ),
    array(
        'db' => 'Initial_Sex',
        'dt' => 11 ,
        'field' => 'Initial_Sex'
    ),
    array(
        'db' => 'Initial_Race',
        'dt' => 12 ,
        'field' => 'Initial_Race',
    ),
    array(
        'db' => 'Initial_Ethnic',
        'dt'=> 13,
        'field' => 'Initial_Ethnic',
    ),
    array(
        'db' => 'Initial_VitalStatus',
        'dt' => 14 ,
        'field' => 'Initial_VitalStatus',
    ),
    array(
        'db' => 'Initial_OverallHistologyLegacy',
        'dt' => 15 ,
        'field' => 'Initial_OverallHistologyLegacy',
        'formatter' => function( $d, $row ) {
            if($d===null){
                $d='Unknown';
            }
            return $d;
        }
    ),
    array(
        'db' => 'Initial_COGStage',
        'dt'=> 16,
        'field' => 'Initial_COGStage',
    ),
    array(
        'db' => 'Initial_FIGOStage',
        'dt'=> 17,
        'field' => 'Initial_FIGOStage',
    ),
    array(
        'db' => 'Initial_AJCCStage',
        'dt'=> 18,
        'field' => 'Initial_AJCCStage',
    ),
    array(
        'db' => 'Initial_IGCCCGRiskGroup',
        'dt'=> 19,
        'field' => 'Initial_IGCCCGRiskGroup',
    ),
    array('db' => 'Note', 'dt'=> 20,'field' => 'Note',
    ),
    array('db' => 'CreateTime', 'dt'=> 21,'field' => 'CreateTime',
    )

);

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

$joinQuery="";
$joinQuery = "FROM `{$table}` LEFT JOIN `Histology` AS `his` ON (`{$table}`.`Patient_ID` = `his`.`His_Patient_ID`)" .
    " LEFT JOIN `CodeOverallHistologyLegacy` AS `ch` ON `his`.`Overall_Histology_Legacy`=`ch`.`ID`" .
    " LEFT JOIN `CodeSex` AS `cs` ON `{$table}`.`Sex`=`cs`.`ID`" .
    " LEFT JOIN `CodeDataContributorClinicalTrialGroup` AS `cdcctg` ON `{$table}`.`Data_Contributor_Clinical_Trial_Group`=`cdcctg`.`ID`" .
    " LEFT JOIN `CodeRace` AS `cr` ON `{$table}`.`Race`=`cr`.`ID`" .
    " LEFT JOIN `CodeVitalStatus` AS `cvs` ON `{$table}`.`Vital_Status`=`cvs`.`ID`".
    " LEFT JOIN `CodeEthnic` AS `ce` ON `{$table}`.`Ethnic`=`ce`.`ID`".
    " LEFT JOIN `CodeDataContributorCenter` AS `cdcc` ON `{$table}`.`Data_Contributor_Center`=`cdcc`.`ID`" .
    " LEFT JOIN `CodeCOGStage` AS `ccs` ON `{$table}`.`COG_Stage`=`ccs`.`ID`" .
    " LEFT JOIN `CodeFIGOStage` AS `cfs` ON `{$table}`.`FIGO_Stage`=`cfs`.`ID`".
    " LEFT JOIN `CodeAJCCStage` AS `cas` ON `{$table}`.`AJCC_Stage`=`cas`.`ID`".
    " LEFT JOIN `CodeIGCCCGRiskGroup` AS `cirg` ON `{$table}`.`IGCCCG_RiskGroup`=`cirg`.`ID`";

$extraCondition="`{$table}`.`isDelete` = 0";

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraCondition)
);


function getPatientIDFormat($operate,$d){
    $format='';
    if(strpos($operate,'edit') !== FALSE ){
        $format.="<a class='btn btn-xs btn-success editpid' href='patient.php?operate=edit&pid=".$d."'> Edit </a>";
    }

    if(strpos($operate,'delete') !== FALSE ){
        $format.="<button type='button' class='btn btn-xs btn-danger deletepid'
                 data-toggle='modal' data-target='#deletepat_modal'> Delete </button>";
    }

    if(strpos($operate,'select') !== FALSE ){
        $format.="<input type='radio' name='patient' value='".$d."'/>";
    }

    return $format."<span>".$d."</span>";
}