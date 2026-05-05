<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>NPV CHALLENGE MECHANISM</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Favicons -->
    <!-- <link href="{{ asset('homepage/assets/img/favicon.png') }}" rel="icon"> -->
    <!-- <link href="{{ asset('homepage/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon"> -->

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">

    <!-- Vendor CSS Files -->

    <link href="{{ asset('homepage/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('homepage/assets/css/main.css') }}" rel="stylesheet">
    <style>
    
        .error {
            color: red;
        }

        #clock {
            font-size: 2em;
            font-weight: bold;
        }

        .alert-success {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
           
            
            border-color: #d6e9c6;
        }
    </style>
    @yield('header-script')

</head>

<body class="index-page">

    <header id="header" class="header fixed-top">

        <div class="topbar d-flex align-items-center">
            <div class="container d-flex justify-content-center justify-content-md-between">
                <div class="contact-info d-flex align-items-center">
                    <i class="bi bi-envelope d-flex align-items-center"><a
                            href="mailto:admin@indiaeauction.com">mail@apexriseconsultant.com</a></i>
                    <i class="bi bi-phone d-flex align-items-center ms-4"><span>+91 7990 8223 51</span></i>
                </div>
                {{-- <div class="social-links d-none d-md-flex align-items-center">
                    <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div> --}}
            </div>
        </div><!-- End Top Bar -->

        <div class="branding d-flex align-items-cente">

            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="" class="logo d-flex align-items-center">
                    <!-- Uncomment the line below if you also wish to use an image logo -->
                   <!-- <img src="{{ asset('homepage/assets/img/logo.png') }}" alt=""> -->
                    <!-- <h1 class="sitename">India E-auction</h1> -->
                    <!-- <span></span> -->
                    <b></i> <span id="clock"></span></b>

                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#hero">Home<br></a>
                        </li>
                        <li><a href="#about">About</a>
                        </li>
                        <li><a
                                href="#services">Services</a>
                        </li>

                        <li><a
                                href="#contact">Contact</a>
                        </li>
                        @if (auth()->user())
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    Logout
                                    ({{ auth()->user()->name }})
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('ra.login') }}">Login</a>
                        @endif

                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>

            </div>

        </div>

    </header>

    <main class="main">
        @yield('content')
    </main>

    <footer id="footer" class="footer">

        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-5 col-md-12 footer-about">
                    <a href="" class="logo d-flex align-items-center">
                        <span class="sitename">NPV CHALLENGE MECHANISM</span>
                    </a>
                    <p>The NPV Challenge Mechanism enables applicants to request a review or correction of the Net Present Value (NPV) calculated for their project or application.</p>
                    <!--  <div class="social-links d-flex mt-4">
                        <a href=""><i class="bi bi-twitter-x"></i></a>
                        <a href=""><i class="bi bi-facebook"></i></a>
                        <a href=""><i class="bi bi-instagram"></i></a>
                        <a href=""><i class="bi bi-linkedin"></i></a>
                    </div> -->
                </div>

                <div class="col-lg-2 col-6 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Services</a></li>

                    </ul>
                </div>

                <div class="col-lg-2 col-6 footer-links">
                    <h4>Our Links</h4>
                    <ul>
                      <!--   <li><a href="/policy">Privacy policy</a></li>
                        <li><a href="/terms-of-service">Terms of service</a></li>
                         <li><a href="#">Web Design</a></li>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Product Management</a></li>
                        <li><a href="#">Marketing</a></li>
                        <li><a href="#">Graphic Design</a></li> -->
                    </ul>
                </div>

                <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                    <h4>Contact Us</h4>
                    <!-- <p>A108 Adam Street</p>
                    <p>New York, NY 535022</p>
                    <p>United States</p> -->
                    <p class="mt-4"><strong>Phone:</strong> <span>+91 7990 8223 51</span></p>
                    <p><strong>Email:</strong> <span>admin@indiaeauction.com</span></p>
                    
                </div>

            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">NPV CHALLENGE MECHANISM</strong> <span>All Rights
                    Reserved</span> Apexrise Consultant and E-Service
            </p>
            <!--   <div class="credits">
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div> -->
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

    <!-- Vendor JS Files -->
    <script src="{{ asset('homepage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('homepage/assets/js/main.js') }}"></script>
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script src=" {{ asset('custom\numtoword.js') }}"></script>

    <script>
        async function updateClock() {
            const clockElement = document.getElementById('clock');

            try {
                // Fetch the current time from your Laravel route
                const response = await fetch('/api/current-time');
                const data = await response.json();
                const dateTimeString = data.utc_datetime;
                const now = new Date(dateTimeString);

                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');

                clockElement.textContent = `${hours}:${minutes}:${seconds}`;


            } catch (error) {
                console.error('Failed to fetch time:', error);
            }
            @if (isset($neworkCheck))
                checkInternetConnection();
            @endif
        }

        // Call updateClock initially and then every second
        updateClock();
        // setInterval(updateClock, 1000);


        clockInterval = setInterval(updateClock, 1000);
        // updateClock(); // initial call to display the clock immediately

        setTimeout(() => {
            $('.alert-success').hide()
        }, 2000);
    </script>
    @yield('footer-script')
</body>

</html>
