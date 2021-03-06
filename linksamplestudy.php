<?php

session_start();

$user=null;
if(!empty($_SESSION["user"])){
    $user=$_SESSION["user"];
}else{
    header("location:login.php");
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

    <!-- Facebook integration -->
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
            <li class="breadcrumb-item active">Link Sample and Study</li>
        </ol>
    </div>

    <div id="fh5co-project">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>Link Sample and Study</h2>
                </div>
            </div>
            <hr class="hr-set"/>
            <br/>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div id="sampletablemsg" class="row"></div>
                    <table id="sampletable" class="display" style="width:100%">
<!--                        <table id="sampletable" class="display responsive" style="width:100%">    -->
                        <thead>
                        <tr>
                            <th class="text-center">System Sample UUID</th>
                            <th class="text-center">System Sample ID</th>
                            <th class="text-center">Local Sample ID
                                <i class="fa fa-question-circle" title="Sample ID in your local system"></i>
                            </th>
                            <th class="text-center">Parent Sample UUID</th>
                            <th class="text-center">Date Derived From Parent</th>
                            <th class="text-center">System Patient ID</th>
                            <th class="text-center">Local Patient ID</th>
                            <th class="text-center">Source Name</th>
                            <th class="text-center">Source Sample ID</th>
                            <th class="text-center">Source Patient ID</th>
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
                        </tbody>
                    </table>
                    <div style='color:#0C9CEE;'>Please submit only one page at a time.</div><br/>
                    <button id="submitbtn" class="btn btn-primary" type="submit">Submit</button>

                    <div id="updatesamplemsg" class="row"></div>

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
            $(".menu-1 #navsamstudy a").trigger('mouseover').css("color","black");
            $(".menu-1 #navmgn .dropdown").delay(2000).fadeOut();
        });

        $(document).ready(function(){

            /* sample Data Table */
            var sampletable=$('#sampletable');

            if (!($.fn.DataTable.isDataTable(sampletable))){
                var sampledatatable=$(sampletable).DataTable( {
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
                        "url": 'datatablescript/server_processing_sample.php',
                        "type": 'GET',
                        "data": function ( d ) {
                            d.operate="edit";
                            d.item="study";
                        }
                    },
                    "deferRender": true,
                    "searching": true
                });

                setTimeout(function(){
                    var cntrows=sampledatatable.data().length;
                    if(cntrows===0){
                        var str="<div class=\"col-md-12 text-center" +
                            " alert-dismissible alert alert-info fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>Info Message! </strong><br>Currently, all samples are enrolled into studies. If you want " +
                            "to change studies of samples, please go to <a href='samplelist.php?operate=add_edit_delete'> " +
                            "Manage > Edit Sample </a>.</div></div>";
                        $('#sampletablemsg').empty().append(str);
                        $('#submitbtn').prop('disabled',true)
                    }
                },500);
            }


            /* update "link sample and patient info" in database */
            $('#submitbtn').on('click',function(){
                var studyinputs=sampletable
                    .find('tbody td:nth-child(8) select option:selected')
                    .filter(function(){
                        var value=$.trim($(this).val());
                        return (value!=="99" && value!=="");
                    });
                var dataarr=[];
                var uuidarr=[];
                $.each(studyinputs,function(i,ele){
                    var studyidval=$.trim($(ele).val());
                    var arr=$(ele).closest('td').siblings('td:first-child').text().split(' ');
                    var suuid=$.trim(arr[arr.length-1]);
                    dataarr.push({"uuid":suuid,"studyid":studyidval});
                    uuidarr.push(suuid);
                });

                $.ajax({
                    async: true,
                    cache: true,
                    type: "POST",
                    url: "sendlinksamplestudy.php",
                    data: {"data":dataarr},
                    success: function(result,status,xhr){
                        var str="<div class=\"col-md-12 text-center" +
                            " alert-dismissible alert "+result.class+" fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>"+result.stat+"</strong><br>"+result.msg+"</div></div>";
                        $('#updatesamplemsg').empty().append(str);
                        $(sampletable).DataTable().draw();
                        if(result.goto!==""){
                            window.location.href=result.goto;
                        }
                    },
                    error: function(xhr,status,error){
                        var str="<div class=\"col-md-12 text-center alert-dismissible alert alert-danger fade in\"> " +
                            "<div><i class=\"close icon-circle-cross\" data-dismiss=\"alert\" aria-label=\"close\"></i> " +
                            "<strong>Fail! </strong><br>"+error.toString()+"<br>Please contact developer.</div></div>";
                        $('#updatesamplemsg').empty().append(str);
                    },
                    dataType: "json"
                });
            });

        });
    </script>
</body>
</html>

