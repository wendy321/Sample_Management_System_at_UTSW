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
$table = 'ChangeHistory';

// Table's primary key
$primaryKey = 'ID';

// Operation for patients decides style in column formatter
$operate = '';
if(isset($_GET['operate']) && $_GET['operate']!=''){
    $operate=EscapeString::escape($_GET['operate']);
}

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$result='';
$columns = array(
    array(
        'db' => 'ID',
        'dt' => 0,
        'field' => 'ID',
        'formatter' => function( $d, $row ) {
            return $d;
        }
    ),
    array(
        'db' => 'TableName',
        'dt' => 1,
        'field' => 'TableName',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => 'Primary_Key',
        'dt' => 2,
        'field' => 'Primary_Key',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => 'Field_Name',
        'dt' => 3,
        'field' => 'Field_Name',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => 'From_Value',
        'dt' => 4,
        'field' => 'From_Value',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => 'To_Value',
        'dt' => 5,
        'field' => 'To_Value',
        'formatter' => function( $d, $row ) {
            if ($d == '') {
                return 'Unknown';
            } else {
                return $d;
            }
        }
    ),
    array(
        'db' => 'Account',
        'dt' => 6,
        'field' => 'Account',
        'formatter' => function( $d, $row ) {
            switch ($d) {
                case "1":
                    $result = 'Admin';
                    break;
                case "2":
                    $result = 'User';
                    break;
                default:
                    $result = 'Unknown';
            }
            return $result;
        }
    ),
    array(
        'db' => 'ChangeTime',
        'dt' => 7,
        'field' => 'ChangeTime',
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

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns)
);
