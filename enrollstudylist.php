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
$operate='';
if(isset($_GET['operate']) && $_GET['operate'] != ''){
    $operate=EscapeString::escape($_GET['operate']);
}
$item=$enstudyid='';
if(strpos($operate,'edit') !== FALSE){
    $item=(isset($_GET['item']) && $_GET['item'] != '')?EscapeString::escape($_GET['item']):'';
    $enstudyid=(isset($_GET['enstudyid']) && $_GET['enstudyid'] != '')?EscapeString::escape($_GET['enstudyid']):'';
}

if(strpos($operate,'view') !== FALSE){
    $enstudyid=(isset($_GET['enstudyid']) && $_GET['enstudyid'] != '')?EscapeString::escape($_GET['enstudyid']):'';
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
    <script src="js/modernizr-2.6.2.min.js"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
        #enrollstudytable td:nth-child(1) span{
            display: none;
        }
    </style>
</head>
<body>

<div class="fh5co-loader"></div>

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
                Enroll Study
            </li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">
            <div id="deleteenrollstudymsg" class="row"></div>
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
                        Enroll Study Information
                    </h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-12 text-center">
                    <?php
                    if(strpos($operate,'add') !== FALSE ){
                        echo "<a type='submit' class='btn btn-primary' id='newenrollstudyid' 
                                href='enrollstudy.php?operate=add'>Add New Enroll Study</a>";
                    }
                    ?>

                    <table id="enrollstudytable" class="display" style="width:100%">
<!--                        <table id="enrollstudytable" class="display responsive" style="width:100%">-->
                        <thead>
                            <tr>
                                <th class="text-center">Enroll Study Operation<span class="hidden">Enroll Study</span></th>
                                <th class="text-center">Study Name</th>
                                <th class="text-center">Study Arm</th>
                                <th class="text-center">System Patient ID</th>
                                <th class="text-center">Study / Source Patient ID</th>
                                <th class="text-center">System Sample UUID</th>
                                <th class="text-center">Study / Source Sample ID</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">Enroll Study Operation<span class="hidden">Enroll Study</span></th>
                                <th class="text-center">Study Name</th>
                                <th class="text-center">Study Arm</th>
                                <th class="text-center">System Patient ID</th>
                                <th class="text-center">Study / Source Patient ID</th>
                                <th class="text-center">System Sample UUID</th>
                                <th class="text-center">Study / Source Sample ID</th>
                            </tr>
                        </tfoot>
                    </table>
                    <br/>
                    <?php
                    if(strpos($operate,'edit') !== FALSE && $item !== ''){
                        echo "<button class='btn btn-primary'>Submit</button>";
                    }
                    ?>

                    <div id="deleteenrollstudy_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h2 class="modal-title">Delete a Enroll Study</h2>
                                </div>
                                <div class="modal-body" style="padding:24px;">
                                    Are you sure to delete enroll study id <span style="font-weight: 600;"></span> ?
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" data-dismiss="modal"
                                            type="button"> No</button>
                                    <button id="deleteenrollstudybtn" class="btn btn-primary" data-dismiss="modal"
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
            $(".menu-1 #navstudy a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();
        });

        $(document).ready(function(){

            /* Enroll Study Data Table */
            var enrollstudytable=$('#enrollstudytable');

            // Add a text input to each header cell
            $(enrollstudytable).find('thead th:not(:nth-child(1))').each( function () {
                var title = $(this).text();
                $(this).html( title+'<input type="text" placeholder=" Search " />' );
            } );

            if (!($.fn.DataTable.isDataTable(enrollstudytable))){
                var exportFormat={
                    format: {
                        body: function ( data, rowIdx, columnIdx, node ) {
                            return data.replace(/(<(?:.|\n)*?>)|(Edit)|(Delete)/gm, '');
                        }
                    }
                };
                var enrollstudydatatable=$(enrollstudytable).DataTable( {
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions:exportFormat
                        },
                        {
                            extend: 'csv',
                            exportOptions:exportFormat
                        },
                        {
                            extend: 'excel',
                            exportOptions:exportFormat
                        },
                        {
                            extend: 'pdf',
                            exportOptions:exportFormat
                        },
                        {
                            extend: 'print',
                            exportOptions:exportFormat
                        }
                    ],
                    "scrollX": true,
                    "pageLength": 5,
//                    "responsive": true,
                    "retrieve": true,
                    "processing": true,
                    /* With server-side processing enabled, all paging, searching, ordering actions that DataTables
                     performs are handed off to a server where an SQL engine (or similar) can perform these actions
                     on the large data set. However, with server-side processing enabled, regular expression of
                     datatable.column().search() API doesn't work. */
//                    "serverSide": true,
                    "ajax": {
                        "url": 'datatablescript/server_processing_enrollstudy.php',
                        "type": 'GET',
                        "data": function ( d ) {
                            d.operate="<?php echo $operate;?>";
                            d.item="<?php echo $item;?>";
                            d.enstudyid="<?php echo $enstudyid;?>";
                        }
                    },
                    "deferRender": true,
                    "searching": true,
                    "order": [[0, 'asc']]
                });
            }

            // Apply search for each column
            enrollstudydatatable.columns().every( function () {
                var that = this;
                $( 'input', this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that.search( this.value, true, false )
                            .draw();
                    }
                } );
            } );

            /* Delete Action */
            $(enrollstudytable).on('click','.deleteenrollstudyid',function(){

                var enrollstudyid=$(this).next().text();
                $('#deleteenrollstudy_modal').find('.modal-body span').text(enrollstudyid);
                $('#deleteenrollstudybtn').on('click',function(){
                    var deleteenrollstudymsg='#deleteenrollstudymsg';
                    $.ajax({
                        async: true,
                        cache: true,
                        type: "POST",
                        url: "senddeleteenrollstudy.php",
                        data: {"enrollstudyid":enrollstudyid},
                        success: function(result,status,xhr){
                            var str="<div class=\"col-md-12 text-center alert-dismissible alert "+result.class+" fade in\"> " +
                                "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                                "<strong>"+result.stat+"</strong><br>"+result.msg+"</div></div>";

                            $(enrollstudytable).DataTable().ajax.reload();
                            $(deleteenrollstudymsg).empty().append(str);
                        },
                        error: function(xhr,status,error){
                            var str="<div class=\"col-md-12 text-center alert-dismissible alert alert-danger fade in\"> " +
                                "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                                "<strong>Fail! </strong><br>Please contact developer.</div></div>";
                            $(deleteenrollstudymsg).empty().append(str);
                        },
                        dataType: "json"
                    });
                });

            });

        });
    </script>
</body>
</html>

