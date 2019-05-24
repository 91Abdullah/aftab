<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('favison/img/favicon.pngs') }}" type="image/png">
    <title>Concept - Digital Random Dialer</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('favison/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('favison/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('favison/vendors/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('favison/vendors/owl-carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('favison/vendors/animate-css/animate.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('favison/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('favison/css/responsive.css') }}">
</head>
<body>

<!--================Header Menu Area =================-->
<header class="header_area">
    <div class="main_menu">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <a class="navbar-brand logo_h" href="{{ url('/') }}">
                    {{--<img src="{{ asset('favison/img/logo.png') }}" alt="">--}}
                    Concept
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="right-button">
                    <ul>
                        <li><a class="sign_up" href="{{ route('login') }}"><i class="fas fa-user"></i> Sign In</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
<!--================Header Menu Area =================-->

<!--================Home Banner Area =================-->
<section class="home_banner_area">
    <div class="banner_inner d-flex align-items-center">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-6 col-xl-5 offset-xl-7">
                    <div class="banner_content">
                        <h3>Random Digital Dialer</h3>
                        <h4>Key Features</h4>
                        <ul class="list-styled">
                            <li><i class="fas fa-check"></i> Based on Web real time communications</li>
                            <li><i class="fas fa-check"></i> User roles & authorization</li>
                            <li><i class="fas fa-check"></i> Bulk numbers uploading with multiple formats: csv, xls, xlxs</li>
                            <li><i class="fas fa-check"></i> Agent application window with random dialing, manual dialing and list based dialing</li>
                            <li><i class="fas fa-check"></i> Realtime dashboard</li>
                            <li><i class="fas fa-check"></i> Agent Reporting and Calls detail reporting</li>
                            <li><i class="fas fa-check"></i> Automated calls recording</li>
                        </ul>
                        <a class="banner_btn" href="{{ route('login') }}">Login<i class="ti-arrow-right"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!--================End Home Banner Area =================-->

<!-- ================ start footer Area ================= -->
<footer class="footer-area">
    <div class="container">
        <div class="footer-bottom row align-items-center text-center text-lg-left no-gutters">
            <p class="footer-text m-0 col-lg-8 col-md-12"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This application is made by <a href="http://www.telecard.com.pk" target="_blank">Telecard Limited</a>
        </div>
    </div>
</footer>
<!-- ================ End footer Area ================= -->






<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('favison/js/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('favison/js/popper.js') }}"></script>
<script src="{{ asset('favison/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('favison/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('favison/js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('favison/js/waypoints.min.js') }}"></script>
<script src="{{ asset('favison/js/mail-script.js') }}"></script>
<script src="{{ asset('favison/js/contact.js') }}"></script>
<script src="{{ asset('favison/js/jquery.form.js') }}"></script>
<script src="{{ asset('favison/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('favison/js/mail-script.js') }}"></script>
<script src="{{ asset('favison/js/theme.js') }}"></script>
</body>
</html>
