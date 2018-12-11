<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
}
$userid=!empty($_SESSION["userid"])?$_SESSION["userid"]:null;

require ('class/EscapeString.inc');
$operate=!empty($_GET['operate'])?EscapeString::escape($_GET['operate']):null;

$item=$uuid=$exist=$editenrollstudy=$editpatient='';
if(strpos($operate,'edit') !== FALSE){
    $item=(!empty($_GET['item']))?EscapeString::escape($_GET['item']):'';
    $uuid=(!empty($_GET['uuid']))?EscapeString::escape($_GET['uuid']):'';
    $exist=(!empty($_GET['exist']))?EscapeString::escape($_GET['exist']):'';
    $editenrollstudy=(!empty($_GET['edit_EnrollStudy']))?EscapeString::escape($_GET['edit_EnrollStudy']):'';
    $editpatient=(!empty($_GET['edit_Patient']))?EscapeString::escape($_GET['edit_Patient']):'';
}

if(strpos($operate,'view') !== FALSE){
    $uuid=(!empty($_GET['uuid']))?EscapeString::escape($_GET['uuid']):'';
    $editenrollstudy=(!empty($_GET['edit_EnrollStudy']))?EscapeString::escape($_GET['edit_EnrollStudy']):'';
    $editpatient=(!empty($_GET['edit_Patient']))?EscapeString::escape($_GET['edit_Patient']):'';
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sample Management System - UT Southwestern Medical Center | Department of Pediatrics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="manage medical samples including sample id generation, sample barcode generation,
		and linkage sample with patient information and clinical trial/study information" />
    <meta name="keywords" content="medical sample, sample barcodes" />
    <meta name="author" content="UTSW - QBRC" />

    <!-- Facebook and Twitter integration -->
    <meta property="og:title" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content=""/>
    <meta property="og:description" content=""/>

    <link rel="icon" href="images/utsw_logo_icon.jpg">
    <!-- Animate.css -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- FONTAWESOME STYLE CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"/>
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Theme style  -->
    <link rel="stylesheet" href="css/style.css">
    <!-- DataTable -->
    <link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="css/responsive.dataTables.min.css"/>
    <!-- Modernizr JS -->
<!--    <script src="js/modernizr-2.6.2.min.js"></script>-->
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="fh5co-loader-table"></div>

<div id="page">

    <header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
    </header>

    <?php include("nav.php"); ?>

    <div class="container-fluid" style="padding: 0;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item col-md-offset-3"><a href="manage.php">Manage</a></li>
            <li class="breadcrumb-item active">
                <?php
                switch($operate){
                    case 'edit': echo "Edit";break;
                    case 'add': echo "Add";break;
                    case 'view': echo "View";break;
                    default: echo "Edit"; break;
                }
                ?>
                Sample
            </li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">

            <?php

            # delete message
            if(strpos($operate,'delete') !== FALSE ){
                echo "<div id=\"deletesamplemsg\" class=\"row\"></div>";
            }
            ?>

            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>
                        <?php
                        switch($operate){
                            case 'edit': echo "Edit";break;
                            case 'add': echo "Add";break;
                            case 'view': echo "View";break;
                            default: echo "Edit"; break;
                        }
                        ?>
                        Sample Information
                    </h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-12 text-center">
                    <?php
                    if(strpos($operate,'add') !== FALSE ){
                        echo "<a type='submit' class='btn btn-primary newsid' href='createsample.php'>Add New Sample</a>";
                    }
                    ?>

                    <?php
                    if(strpos($operate,'edit') !== FALSE && $item==='parentsample'){
                        echo "<form action=''>";
                    }
                    ?>
                    <table id="sampletable" class="display" style="width:100%">
                        <!--                        <table id="sampletable" class="display responsive" style="width:100%">-->
                        <thead>
                        <tr>
                            <th class="text-center">System Sample UUID</th>
                            <th class="text-center">System Sample ID</th>
                            <th class="text-center">Local Sample ID</th>
                            <th class="text-center">Parent Sample UUID</th>
                            <th class="text-center">Date Derived From Parent</th>
                            <th class="text-center">System Patient ID</th>
                            <th class="text-center">Local Patient ID</th>
                            <th class="text-center">Source/Study Name</th>
                            <th class="text-center">Source/Study Sample ID</th>
                            <th class="text-center">Source/Study Patient ID</th>
                            <th class="text-center">Sample Contributor Consortium</th>
                            <th class="text-center">Sample Contributor Institute</th>
                            <th class="text-center">Procedure Type</th>
                            <th class="text-center">Procedure Date</th>
                            <th class="text-center">Pathological Status</th>
                            <th class="text-center">Sample Class</th>
                            <th class="text-center">Sample Type</th>
                            <th class="text-center">Specimen Type</th>
                            <th class="text-center">Nucleotide Size</th>
                            <th class="text-center">Anatomical Site</th>
                            <th class="text-center">Anatomical Laterality</th>
                            <th class="text-center">Storage Room</th>
                            <th class="text-center">Cabinet Type</th>
                            <th class="text-center">Cabinet Temperature</th>
                            <th class="text-center">Cabinet Number</th>
                            <th class="text-center">Shelf Number</th>
                            <th class="text-center">Rack Number</th>
                            <th class="text-center">Box Number</th>
                            <th class="text-center">Position Number</th>
                            <th class="text-center">Amount Value</th>
                            <th class="text-center">Amount Unit</th>
                            <th class="text-center">Concentration Value</th>
                            <th class="text-center">Concentration Unit</th>
                            <th class="text-center">Notes</th>
                            <th class="text-center">CreateTime</th>
                            <!--                            <th>Barcode 2D Data Matrix</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="text-center">System Sample UUID</th>
                            <th class="text-center">System Sample ID</th>
                            <th class="text-center">Local Sample ID</th>
                            <th class="text-center">Parent Sample UUID</th>
                            <th class="text-center">Date Derived From Parent</th>
                            <th class="text-center">System Patient ID</th>
                            <th class="text-center">Local Patient ID</th>
                            <th class="text-center">Source/Study Name</th>
                            <th class="text-center">Source/Study Sample ID</th>
                            <th class="text-center">Source/Study Patient ID</th>
                            <th class="text-center">Sample Contributor Consortium</th>
                            <th class="text-center">Sample Contributor Institute</th>
                            <th class="text-center">Procedure Type</th>
                            <th class="text-center">Procedure Date</th>
                            <th class="text-center">Pathological Status</th>
                            <th class="text-center">Sample Class</th>
                            <th class="text-center">Sample Type</th>
                            <th class="text-center">Specimen Type</th>
                            <th class="text-center">Nucleotide Size</th>
                            <th class="text-center">Anatomical Site</th>
                            <th class="text-center">Anatomical Laterality</th>
                            <th class="text-center">Storage Room</th>
                            <th class="text-center">Cabinet Type</th>
                            <th class="text-center">Cabinet Temperature</th>
                            <th class="text-center">Cabinet Number</th>
                            <th class="text-center">Shelf Number</th>
                            <th class="text-center">Rack Number</th>
                            <th class="text-center">Box Number</th>
                            <th class="text-center">Position Number</th>
                            <th class="text-center">Amount Value</th>
                            <th class="text-center">Amount Unit</th>
                            <th class="text-center">Concentration Value</th>
                            <th class="text-center">Concentration Unit</th>
                            <th class="text-center">Notes</th>
                            <th class="text-center">CreateTime</th>
                            <!--                            <th>Barcode 2D Data Matrix</th>-->
                        </tr>
                        </tfoot>
                    </table>
                    <br/>
                    <?php
                    if(strpos($operate,'edit') !== FALSE && $item === 'parentsample'){
                        echo "<div id=\"parentsamplemsg\" class=\"row hidden\">
                                  <div class=\"col-md-12 text-center\">
                                      <div class=\"alert alert-danger alert-dismissable\">
                                            <i class=\"icon-circle-cross\" data-dismiss=\"alert\" style=\"float:right;\"></i>
                                            Error: Parent Sample UUID can't be equal to Sample UUID.
                                      </div>
                                  </div>
                              </div>
                              <div style='color:#0C9CEE;'>Please submit only one page at a time.</div><br/>
                              <button id='addparentsamplebtn' class='btn btn-primary'>Submit</button>
                              </form>
                              <div id=\"parentresultmsg\" class=\"row\"></div>";
                    }
                    ?>

                    <div id="sample_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h2 class="modal-title">Select a Sample</h2>
                                </div>
                                <div class="modal-body" style="padding:24px;">
                                    <br/><br/>
                                    <table id="parentsampletable" class="display responsive" style="width: 100%;">
                                        <thead> <tr> <th>System Sample UUID</th> <th>System Sample ID</th>
                                            <th>Local Sample ID</th> <th>Parent Sample UUID</th>
                                            <th>Date Derived From Parent</th> <th>Patient ID</th>
                                            <th>Source Name</th>
                                            <th>Source Sample ID</th> <th>Source Patient ID</th>
                                            <th>Sample Contributor Consortium</th> <th>Sample Contributor Institute</th>
                                            <th>Storage Refrigerator</th> <th>Storage Layer</th>
                                            <th>Procedure Type</th> <th>Procedure Date</th>
                                            <th>Pathological Status</th> <th>Sample Class</th>
                                            <th>Sample Type</th> <th>Specimen Type</th>
                                            <th>Nucleotide Size</th> <th>Anatomical Site</th>
                                            <th>Anatomical Laterality</th> <th>Storage Room</th>
                                            <th>Cabinet Type</th> <th>Cabinet Temperature</th>
                                            <th>Cabinet Number</th> <th>Shelf Number</th>
                                            <th>Rack Number</th> <th>Box Number</th>
                                            <th>Position Number</th> <th>Amount Value</th>
                                            <th>Amount Unit</th> <th>Concentration Value</th>
                                            <th>Concentration Unit</th> <th>Notes</th> <th>CreateTime</th></tr> </thead>
                                        <tbody></tbody>
                                    </table>
                                </div><br/>
                                <div class="modal-footer">
                                    <button id="selectsamplebtn" class="btn btn-primary" data-dismiss="modal"
                                            type="button"> Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="deletesample_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h2 class="modal-title">Delete a Sample</h2>
                                </div>
                                <div class="modal-body" style="padding:24px;">
                                    Are you sure to delete <span style="font-weight: 600;"></span> ?
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" data-dismiss="modal"
                                            type="button"> No</button>
                                    <button id="deletesamplebtn" class="btn btn-primary" data-dismiss="modal"
                                            type="button"> Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <div class="gototop js-top">
        <a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- jQuery Easing -->
    <script src="js/jquery.easing.1.3.js"></script>
    <!-- Bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Waypoints -->
    <script src="js/jquery.waypoints.min.js"></script>
    <!-- DataTable -->
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/buttons.flash.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="js/pdfmake.min.js"></script>
    <script src="js/vfs_fonts.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <!-- Main -->
    <script src="js/main.js"></script>

    <script>
        $(function () {
            $(".menu-1 #navmgn").addClass("active");
            $(".menu-1 #navsample a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();
        });

        $(document).ready(function(){

            /* Sample Data Table */
            var sampletable=$('#sampletable');

            // Add a text input to each header cell
            $(sampletable).find('thead th').each( function () {
                var title = $(this).text();
                $(this).html( title+'<input type="text" placeholder=" Search " />' );
            } );

            if (!($.fn.DataTable.isDataTable(sampletable))){
                var exportFormat={
                    format: {
                        body: function ( data, rowIdx, columnIdx, node ) {
                            return data.replace(/(<(?:.|\n)*?>)|(Edit)|(Delete)/gm, '');
                        }
                    }
                };

                var sampleatatable=$(sampletable).DataTable({
                    "dom": 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions: exportFormat
                        },
                        {
                            extend: 'csv',
                            exportOptions: exportFormat
                        },
                        {
                            extend: 'excel',
                            exportOptions: exportFormat
                        },
                        {
                            extend: 'pdf',
                            exportOptions: exportFormat
                        },
                        {
                            extend: 'print',
                            exportOptions: exportFormat
                        }
                    ],
                    "scrollX": true,
                    "pageLength": 5,
                    //"responsive": true,
                    "retrieve": true,
                    "processing": true,
                    /* With server-side processing enabled, all paging, searching, ordering actions that DataTables
                     performs are handed off to a server where an SQL engine (or similar) can perform these actions
                     on the large data set. However, with server-side processing enabled, regular expression of
                     datatable.column().search() API doesn't work, so I turn off the server-side processing */
                    //"serverSide": true,
                    "ajax": {
                        "url": 'datatablescript/server_processing_sample.php',
                        "type": 'GET',
                        // Chrome deprecates the async: false because of bad user experience
                        "async": true,
                        "data": function (d) {
                            d.operate = "<?php echo $operate;?>";
                            d.item = "<?php echo $item;?>";
                            d.uuid = "<?php echo $uuid;?>";
                        }
                    },
                    "deferRender": true,
                    "searching": true
                    //Ajax event - fired when an Ajax request is completed.;
                }).on('xhr.dt', function ( e, settings, json, xhr ){
                    <?php

                    //fh5co-project container prepend
                    # single sample upload result message
                    if ($editenrollstudy!=='' || $editpatient!=='' || $exist!==''){
                        $prepend_str = "<div class=\"row\"><div class=\"col-md-12 text-center\"><div class=\"alert alert-info alert-dismissable\">".
                            "<i class=\"icon-circle-cross\" data-dismiss=\"alert\" style=\"float:right\"></i><strong>InfoMessage!</strong>";

                        if($editpatient!==''){
                            $prepend_str .= "<br><strong>A New Patient</strong> has been created based on your input. If you want to edit it, please click on".
                                "<a class=\"btn btn-xs btn-info\" href=\"patient.php?operate=edit&pid=".$editpatient."\" target=\"_blank\">Edit Patient</a>";
                        }

                        if($editenrollstudy!==''){
                            $prepend_str .= "<br><strong>A New Enroll Study</strong> has been created based on your input. If you want to edit it, please click on".
                                "<a class=\"btn btn-xs btn-info\" href=\"enrollstudy.php?operate=edit&enstudyid=".$editenrollstudy."\" target=\"_blank\">Edit Enroll Study</a>";
                        }

                        if($exist!==''){
                            $prepend_str .= "<br>This sample has already been in database.";
                        }

                        $prepend_str .= "</div></div></div>";
                        echo "$('#fh5co-project container').prepend('".$prepend_str."');";
                    }

                    # batch sample upload result message
                    if($uuid==="allnew"){

                        require_once ("class/dbencryt.inc");
                        require_once ("dbsample.inc");

# csv file upload
//                $db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_sample));
//                if($db->connect_error){
//                    die('Unable to connect to database: ' . $db->connect_error);
//                }
//
//                $sql="SELECT Exist_Sample_UUID,Error_Msg FROM BatchUploadSampleErrorLog WHERE Account=? AND Tag=1";
//                if($result = $db->prepare($sql))
//                {
//                    $result->bind_param('i',$userid);
//                    $result->execute();
//                    $result->bind_result($existsamuuid,$errmsg);
//                    $result->fetch();
//                    $result->close();
//
//                    if($existsamuuid!==null){
//                            echo "<div class=\"row\">
//                                        <div class=\"col-md-12 text-center alert alert-info\">
//                                            <div style='overflow-wrap: break-word;'>
//                                            <i class=\"icon-circle-cross\" style='float: right;'></i>
//                                            <strong>Info Message!</strong>
//                                            <br>";
//                            if(strpos($existsamuuid,",")>=0){
//                                echo " Samples have ";
//                            }else{
//                                echo " Sample has ";
//                            }
//                            echo "already existed in database. <br>System Sample UUID: ".$existsamuuid."</div></div></div>";
//                    }
//
//                    if(!empty($errmsg)){
//                        echo "<div class=\"row\">
//                                        <div class=\"col-md-12 text-center alert alert-danger\">
//                                            <div><i class=\"icon-circle-cross\" style=\"float:right;\"></i>
//                                            <strong>Error Message!</strong><br>".$errmsg."</div>
//                                        </div>
//                                      </div>";
//                    }
//                }

                        # xlsx file upload

                    }

                    ?>

                    if(json.data == null){
                        console.log(xhr.error);
                    }else {
                        // bug: how about there's really no sample data?
                        <?php
                        if($uuid==="allnew"){
                            require_once ("class/dbencryt.inc");
                            require_once ("dbsample.inc");

                            $db = new mysqli(Encryption::decrypt($hostname),Encryption::decrypt($username),Encryption::decrypt($password),Encryption::decrypt($dbname_remoter));
                            if($db->connect_error){
                                die('Unable to connect to database: ' . $db->connect_error);
                            }
                            $sql="SELECT J.Status, J.JobID FROM Jobs AS J INNER JOIN SMSParameters AS P ON J.JobID = P.JobID WHERE J.Software=\"samplemanagementsystem\" ".
                                "AND J.Analysis=\"samplebatchupload\" AND J.Status NOT IN(0,2) AND P.AccountID=? AND Tag=1 ORDER BY J.CreateTime DESC LIMIT 1;";
                            if($result = $db->prepare($sql)){
                                $result->bind_param('i', $userid);
                                $result->execute();
                                $result->bind_result($status,$jobid);
                                $result->fetch();
                                $result->close();
                                if(!empty($status)){
                                    $prepend_str = "";
                                    # warn msg
                                    $sql="SELECT WarnMsg,ExistedSampleUUIDs FROM SMSWarnResults AS W LEFT JOIN SMSParameters AS P ON W.JobID=P.JobID WHERE P.JobID=? AND P.AccountID=? AND P.Tag=1";
                                    if($result = $db->prepare($sql)) {
                                        $result->bind_param('si',$jobid,$userid);
                                        $result->execute();
                                        $result->bind_result($warnmsg, $existsamuuids);
                                        $result->fetch();
                                        $result->close();

                                        if ($existsamuuids !== null) {
                                            $prepend_str .= "<div class=\"row\"><div class=\"col-md-12 text-center\"><div class=\"alert alert-warning alert-dismissable\">".
                                                "<i class=\"icon-circle-cross\" data-dismiss=\"alert\" style=\"float:right\"></i><strong>Info Message!</strong><br>".$warnmsg."</div></div></div>";
                                        }

                                        # error msg
                                        $sql="SELECT ErrorMsg FROM SMSErrorResults AS E LEFT JOIN SMSParameters AS P ON E.JobID=P.JobID WHERE P.JobID=? AND P.AccountID=? AND P.Tag=1";
                                        if($result = $db->prepare($sql)){
                                            $result->bind_param('si',$jobid,$userid);
                                            $result->execute();
                                            $result->bind_result($errmsg);
                                            $result->fetch();
                                            $result->close();
                                            if(!empty($errmsg)){
                                                $prepend_str .=  "<div class=\"row\"><div class=\"col-md-12 text-center\"><div class=\"alert alert-danger alert-dismissable\">".
                                                    "<i class=\"icon-circle-cross\" data-dismiss=\"alert\" style=\"float:right\"></i><strong>Error Message!</strong><br>".$errmsg."</div></div></div>";
                                            }

                                            $prepend_str = preg_replace("/[\n\r|\n|\r]/","",$prepend_str);
                                            echo "$('#fh5co-project .container').prepend('".$prepend_str."');\n";
                                            echo "$('.fh5co-loader-table').fadeOut(2000);\n";
                                        }
                                    }
                                }else{
                                    echo "location.reload(true);";
                                }
                            }
                            $db->close();
                        }else{
                            echo "$('.fh5co-loader-table').fadeOut(2000);";
                        }
                        ?>
                    }
                });

            }

            // Apply search for each column
            $(sampletable).DataTable().columns().every( function () {
                var that = this;
                $( 'input', this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that.search( this.value, true, false )
                            .draw();
                    }
                } );
            } );

            /* Delete Action */
            $(sampletable).on('click','.deletesample',function(){
                var arr=$(this).parent().text().split(' ');
                var id=arr[arr.length-1];
                $('#deletesample_modal').find('.modal-body span').text(id);
                $('#deletesamplebtn').on('click',function(){
                    var deletesammsg='#deletesamplemsg';
                    $.ajax({
                        async: true,
                        cache: true,
                        type: "POST",
                        url: "senddeletesample.php",
                        data: {"uuid":id},
                        success: function(result,status,xhr){
                            var str="<div class=\"col-md-12 text-center alert-dismissible alert "+result.class+" fade in\"> " +
                                "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                                "<strong>"+result.stat+"</strong><br>"+result.msg+"</div></div>";

                            $(sampletable).DataTable().ajax.reload();
                            $(deletesammsg).empty().append(str);
                        },
                        error: function(xhr,status,error){
                            var str="<div class=\"col-md-12 text-center alert-dismissible alert alert-danger fade in\"> " +
                                "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                                "<strong>Fail! </strong><br>Please contact developer.</div></div>";
                            $(deletesammsg).empty().append(str);
                        },
                        dataType: "json"
                    });
                });
            });

            /* Parent Sample_UUID modal for single sample*/
            <?php
            if(strpos($operate,'edit') !== FALSE && $item==='parentsample'){
                echo "var parentddtable;
                          var parentsampletable=$('#parentsampletable');
                            if (!($.fn.DataTable.isDataTable(parentsampletable))){
                                parentddtable=$(parentsampletable).DataTable( {
                                    'responsive': true,
                                    'retrieve': true,
                                    'processing': true,
                                    'serverSide': true,
                                    'ajax': {
                                        'url': 'datatablescript/server_processing_sample.php',
                                        'type': 'GET',
                                        'data': function ( d ) {
                                            d.operate='select';
                                        }
                                    },
                                    'deferRender': true,
                                    'searching': true
                                });
                            }";

                echo "var parentinput;
                          var sid='';
                          $('body').on('click','#sampletable #ddl_parentuuid',function(){
                                parentinput=$(this);
                                $(parentinput).trigger('blur');
                                var arr=$(parentinput).closest('tr').find('td:first-child').text().split(' ');
                                sid=arr[arr.length-1];
                                setTimeout(function(){
                                    parentddtable.columns.adjust().responsive.recalc();
                                },190);
                                
                          });";

                echo "$('button[data-dismiss=\"modal\"]').on('click',function(){
                            var psid=$(parentsampletable).find('tbody tr td:first-child input:checked').val();
                            if(psid===sid){
                                $('body').find('#addparentsamplebtn').prop('disabled',true);
                                $(parentinput).val('');
                                $('#parentsamplemsg').removeClass('hidden');
                            }else{
                                $('body').find('#addparentsamplebtn').prop('disabled',false);
                                $(parentinput).val(psid);
                                 $('#parentsamplemsg').addClass('hidden');
                            }
                          });";


            }
            ?>


            $('#addparentsamplebtn').on('click',function(e){
                e.preventDefault();
                var parentuuidinputs=sampletable
                    .find('tbody td:nth-child(4) input')
                    .filter(function(){
                        return $.trim($(this).val()).length!==0;
                    });

                var dataarr=[];
                var suuidarr=[];
                $.each(parentuuidinputs,function(i,ele){
                    var parentuuidval=$.trim($(ele).val());
                    var arr=$(ele).closest('td').siblings('td:first-child').text().split(' ');
                    var suuid=$.trim(arr[arr.length-1]);
                    dataarr.push({"sampleuuid":suuid,"parentuuid":parentuuidval});
                    suuidarr.push(suuid);
                });

                $.ajax({
                    async: true,
                    cache: true,
                    type: "POST",
                    url: "sendaddparentsample.php",
                    data: {"data":dataarr},
                    success: function(result,status,xhr){
                        var parentsamplemsgdiv=$('#parentsamplemsg');
                        if(!$(parentsamplemsgdiv).hasClass('hidden')){
                            $(parentsamplemsgdiv).addClass('hidden');
                        }
                        var str="<div id=\"updatesamplemsg\" class=\"row\"><div class=\"col-md-12 text-center" +
                            " alert-dismissible alert "+result.class+" fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>"+result.stat+"</strong><br>"+result.msg+"</div></div></div>";
                        $('#parentresultmsg').empty().append(str);
                        if(result.goto!==""){
                            window.location.href=result.goto;
                        }
                    },
                    error: function(xhr,status,error){
                        var parentsamplemsgdiv=$('#parentsamplemsg');
                        if(!$(parentsamplemsgdiv).hasClass('hidden')){
                            $(parentsamplemsgdiv).addClass('hidden');
                        }
                        var str="<div class=\"col-md-12 text-center alert-dismissible alert alert-danger fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>Fail! </strong><br>"+error.toString()+"<br>Please contact developer.</div></div>";
                        $('#parentresultmsg').empty().append(str);
                    },
                    dataType: "json"
                });
            });
        });
    </script>
</body>
</html>

