<?php

$role=null;
if(!empty($_SESSION["role"])){
    $role=$_SESSION["role"];
}

?>

<nav class="fh5co-nav" role="navigation">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-4">
                <div id="fh5co-logo"><a href="index.php">UTSouthwestern <span>Medical Center</span></a></div>
            </div>
            <div class="col-xs-6 col-sm-8 text-right menu-1">
                <ul>
                    <li id="navhome"><a href="index.php">Home</a></li>
<!--                    <li id="navsearch"><a href="searchsample.php">Search</a></li>-->
                    <li id="navcreate"><a href="createsample.php">Create Sample</a></li>
                    <li id="navmgn" class="has-dropdown">
                        <a href="manage.php">Manage Data <i class='icon-chevron-down'></i></a>
                        <ul class="dropdown">
                            <?php
                                if($role=="1"){

                                }
                            ?>
                            <li id="navsample"><a href="samplelist.php?operate=add_edit_delete">Search & Edit Sample</a></li>
                            <li id="navpat"><a href="patientlist.php?operate=add_edit_delete">Search & Edit Patient</a></li>
                            <li id="navstudy"><a href="enrollstudylist.php?operate=add_edit_delete">Search & Edit Enroll Study</a></li>
                            <li class="divider"></li>
                            <li id="navsampat"><a href="linksamplepatient.php">Link Sample to Patient</a></li>
                            <li id="navsamstudy"><a href="linksamplestudy.php">Link Sample to Study</a></li>
                            <li class="divider"></li>
                            <li class="" id="navchangehistory">
                                <a href="changehistorylist.php?operate=view">View Change History</a>
                            </li>
                        </ul>
                    </li>
                    <li id="navsum"><a href="summary.php">Summary</a></li>
                    <li id="navtutorial"><a href="">Tutorial</a></li>
                    <li id="navabout"><a href="about.php">About</a></li>
                    <?php
                    if($user===null){
                        echo "<li class=\"btn-cta\"><a href=\"login.php\"><span>Login</span></a></li>";
                    }else{
                        echo "<li class='has-dropdown'> 
                                    <a href=\"#\" style='color:#CCF8FF;'>Welcome, ".$user."! <i class='icon-chevron-down'></i></a>
                                    <ul class=\"dropdown\">
                                    <!--li><a href=\"#\"><i class=\"fa fa-fw fa-gear\"></i> Change Password</a></li-->
                                    <li><a href=\"logout.php\"><i class=\"fa fa-fw fa-sign-out\"></i> Log Out</a></li>
                                    </ul></li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</nav>