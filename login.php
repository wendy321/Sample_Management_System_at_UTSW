<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <title>Sampling System - UT Southwestern Medical Center | Department of Pediatrics</title>
    <link rel="icon" href="images/utsw_logo_icon.jpg">
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="css/bootstrap.css" rel="stylesheet"/>
    <!-- FONTAWESOME STYLE CSS -->
    <link href="css/font-awesome.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="css/style1.css" rel="stylesheet"/>
    <style>
        .wrapper {
            margin-top: 80px;
            margin-bottom: 80px;
        }

        .fh5co-nav {
            position: absolute;
            top: 0;
            margin: 0;
            width: 100%;
            padding: 40px 0;
            z-index: 1001;
        }
        @media screen and (max-width: 768px) {
            .fh5co-nav {
                padding: 20px 0;
            }
        }
        .fh5co-nav #fh5co-logo {
            font-size: 24px;
            padding: 0;
            font-weight: bold;
        }

        #fh5co-logo {
            color: #fff;
        }

        #fh5co-logo span {
            padding: 5px 5px;
            font-size: 18px;
            text-transform: capitalize;
            font-weight: 400;
        }


        .fh5co-cover {
            height: 600px;
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
            position: relative;
            float: left;
            width: 100%;
        }
        .fh5co-cover .overlay {
            z-index: 0;
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.3);
        }

        .form-signin {
            max-width: 380px;
            padding: 15px 35px 45px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,0.1);

        .form-signin-heading,

        .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
        @include box-sizing(border-box);

        &:focus {
             z-index: 2;
         }
        }

        input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        input[type="password"] {
            margin-bottom: 20px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        }

        #fh5co-footer {
            padding: 3em 0;
            clear: both;
        }
        @media screen and (max-width: 768px) {
            #fh5co-footer {
                padding: 3em 0;
            }
        }
        #fh5co-footer {
            background: #000000;
        }
        #fh5co-footer .fh5co-footer-links {
            padding: 0;
            margin: 0;
        }
        #fh5co-footer .fh5co-footer-links li {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        #fh5co-footer .fh5co-footer-links li a {
            color: #000;
            text-decoration: none;
        }
        #fh5co-footer .fh5co-footer-links li a:hover {
            text-decoration: underline;
        }
        #fh5co-footer .fh5co-widget {
            margin-bottom: 30px;
        }
        @media screen and (max-width: 768px) {
            #fh5co-footer .fh5co-widget {
                text-align: left;
            }
        }
        #fh5co-footer .fh5co-widget h3 {
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 15px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        #fh5co-footer .copyright .block {
            color: #828282;
            display: block;
        }
    </style>
</head>
<body>

<div class="fh5co-loader"></div>

<div id="page">

    <nav class="fh5co-nav" role="navigation">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-sm-4">
                    <div id="fh5co-logo">UTSouthwestern <span>Medical Center</span></div>
                </div>
            </div>
        </div>
    </nav>

    <header class="fh5co-cover" role="banner" style="height:150px; background-image:url(images/img_bg_2.jpg);">
        <div class="overlay"></div>
    </header>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6" style="min-height:500px;">
                    <div class="wrapper">
                        <h3><img src="images/login-image.png" class="center-block"/></h3>
                        <form class="form-signin" action="checkuser.php" method="post">
                            <h2 class="form-signin-heading">Please login</h2>
                            <br>
                            <label for="username"> Account:</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="max 10 characters" required="" autofocus="" maxlength="10"/>
                            <label for="password"> Password:</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="max 15 characters" required="" maxlength="15"/>
                            <br>
                            <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </section>

    <?php include("footer.php") ?>
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="js/jquery.min.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="js/bootstrap.min.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="js/custom.js"></script>

</div>

</body>
</html>
