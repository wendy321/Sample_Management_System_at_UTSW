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
$table = 'EnrollStudy';

// Table's primary key
$primaryKey = 'ID';

// Operation for patients decides style in column formatter
$operate=!empty($_GET['operate'])?EscapeString::escape($_GET['operate']):null;
$item=!empty($_GET['item'])?EscapeString::escape($_GET['item']):null;
$enstudyid=!empty($_GET['enstudyid'])?EscapeString::escape($_GET['enstudyid']):null;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$result='';
$columns = array(
    array(
        'db' => '`EnrollStudy`.`ID`',
        'dt' => 0,
        'field' => 'ID',
        'formatter' => function( $d, $row ) {
            global $operate;
            return getIDFormat($operate,$d)." <span>".$d."</span>";
        }
    ),
    array(
        'db' => 'Initial_CodeStudy',
        'dt' => 1,
        'field' => 'Initial_CodeStudy',
        'formatter' => function($d, $row){
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => '`EnrollStudy`.`Study_Arm`',
        'dt' => 2,
        'field' => 'Study_Arm',
        'formatter' => function( $d, $row ) {
            switch ($d){
                case 1: $result='Experimental Arm';break;
                case 2: $result='Control Arm';break;
                default: $result='Unknown';
            }
            return $result;
        }
    ),
    array(
        'db' => '`EnrollStudy`.`Patient_ID`',
        'dt' => 3 ,
        'field' => 'Patient_ID',
        'formatter' => function( $d, $row ) {
            if($row[8]=="1"){
                return 'Unknown';
            }else{
                if ($d == '') {
                    return 'Unknown';
                } else {
                    return '<a href="patientlist.php?operate=view&pid='.$d.'" target="_blank"><u>'.$d.'</u></a>';
                }
            }
        }
    ),
    array(
        'db' => '`EnrollStudy`.`Within_Study_Patient_ID`',
        'dt' => 4 ,
        'field' => 'Within_Study_Patient_ID',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => '`EnrollStudy`.`Sample_UUID`',
        'dt' => 5 ,
        'field' => 'Sample_UUID',
        'formatter' => function( $d, $row ) {
            if($row[9]=="1"){
                return 'Unknown';
            }else {
                if ($d == '') {
                    return 'Unknown';
                } else {
                    return '<a href="samplelist.php?operate=view&uuid=' . $d . '" target="_blank"><u>' . $d . '</u></a>';
                }
            }
        }
    ),
    array(
        'db' => '`EnrollStudy`.`Within_Study_Sample_ID`',
        'dt' => 6 ,
        'field' => 'Within_Study_Sample_ID',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array('db' => '`EnrollStudy`.`CreateTime`', 'dt'=> 7,'field' => 'CreateTime',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array('db' => '`pat`.`isDelete`', 'dt'=> 8,'field' => 'isDelete',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array('db' => '`sam`.`isDelete`', 'dt'=> 9,'field' => 'isDelete',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
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

$joinQuery = "FROM `{$table}` LEFT JOIN `CodeStudy` AS `cs` ON (`{$table}`.`Study_ID` = `cs`.`ID`) ".
             " LEFT JOIN `Patient` AS `pat` ON `{$table}`.`Patient_ID`=`pat`.`Patient_ID`".
             " LEFT JOIN `Sample` AS `sam` ON `{$table}`.`Sample_UUID`=`sam`.`UUID`";

if(strpos($enstudyid,',') !== FALSE){
    $idarr=explode(",",$enstudyid);
    $idstr="";
    $isfirst=1;
    foreach ($idarr as $k=>$v){
        if($isfirst===1){
            $idstr.="\"".$v."\"";
            $isfirst=0;
        }else{
            $idstr.=",\"".$v."\"";
        }
    }
    $extraCondition = "`{$table}`.`{$primaryKey}` IN (".$idstr.") AND `{$table}`.`isDelete` = 0 ";
}elseif($enstudyid!==null){
    $extraCondition = "`{$table}`.`{$primaryKey}` =\"".$enstudyid."\" AND `{$table}`.`isDelete` = 0 ";
}else{
    $extraCondition="`{$table}`.`isDelete` = 0";
}

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraCondition)
);


function getIDFormat($operate,$d){
    $format='';
    if(strpos($operate,'edit') !== FALSE ){
        $format.="<a class='btn btn-xs btn-success editenrollstudyid' href='enrollstudy.php?operate=edit&enstudyid=".$d."'> Edit </a>";
    }

    if(strpos($operate,'delete') !== FALSE ){
        $format.="<button type='button' class='btn btn-xs btn-danger deleteenrollstudyid'
                 data-toggle='modal' data-target='#deleteenrollstudy_modal'> Delete </button>";
    }

    if(strpos($operate,'select') !== FALSE ){
        $format.="<input type='radio' name='enrollstudy' value='".$d."'/>";
    }

    return $format;
}