<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title')</title>
    @yield('header-script')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link href="{{ asset('customdownload/css/toastr.min.css') }}" rel="stylesheet">
    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    {{-- <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css"> --}}
    <!-- JQVMap -->
    {{-- <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css"> --}}
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    {{-- <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}"> --}}
    <!-- summernote -->
    {{-- <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css"> --}}
    <style>
        .error { color: red; }
        .content-wrapper { background-color: #f4f8ff !important; }
        .main-sidebar {
            background: linear-gradient(180deg, #0a3d91 0%, #0d6efd 60%, #0a58ca 100%) !important;
        }
        .brand-link {
            background: rgba(255, 255, 255, 0.1) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18) !important;
            padding: 14px 14px !important;
        }
        .brand-text {
            color: #ffffff !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px;
        }
        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.18) !important;
            padding: 8px 8px 12px !important;
        }
        .user-panel .info a {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 13px;
        }
        .sidebar .nav-header {
            color: rgba(255, 255, 255, 0.72) !important;
            font-weight: 700;
            padding-left: 12px !important;
            margin-top: 10px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.95) !important;
            border-radius: 10px;
            margin: 2px 8px;
            padding: 10px 12px !important;
            font-size: 13px;
            font-weight: 500;
        }
        .sidebar .nav-link .nav-icon {
            color: #ffffff !important;
            opacity: 0.95;
            font-size: 14px !important;
        }
        .sidebar .nav-link.active {
            background: #ffffff !important;
            color: #0d47a1 !important;
            box-shadow: 0 6px 16px rgba(5, 26, 64, 0.28);
        }
        .sidebar .nav-link.active .nav-icon {
            color: #0d47a1 !important;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div> --}}

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar elevation-4">
            <!-- Brand Logo -->
            <a href="" class="brand-link">
                <!--<img src="{{ asset('homepage/assets/img/logo.png') }}" alt="AdminLTE Logo"
                    class="brand-image"> -->
                <span class="brand-text font-weight-light">CHALLENGE MECHANISM</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                @include('app.layout.sidebar')
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand navbar-white ">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                            </li>
                        </ul>
                    </nav>
                    @yield('content-header')
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content-body')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
           <strong>© 2024 Apexrise Consultant and E-Service. </strong>
            All rights reserved.
           <!--<div class="float-right d-none d-sm-inline-block">
                <ul class="list-inline footer-links"> 
                        <li class="list-inline-item"> 
                            <a href="" class=""> 
                                Privacy Policy 
                            </a> 
                        </li> 
                        <li class="list-inline-item"> 
                            <a href="#" class=""> 
                                Terms of Service 
                            </a> 
                        </li> 
                        <li class="list-inline-item"> 
                            <a href="#" class=""> 
                                Sitemap 
                            </a> 
                        </li> 
                    </ul> 
            </div>-->
        </footer>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    {{-- <script src="plugins/jquery-ui/jquery-ui.min.js"></script> --}}
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- <!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> --}}
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script> --}}
<!-- daterangepicker -->

{{-- <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script> --}}

    <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('customdownload/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('customdownload/js/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('customdownload/js/toastr.js') }}"></script>


    <script>
        $(document).ready(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })
        })
        @if (session('status'))

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            toastr.success("{!! Session::get('status') !!}");
        @endif

        function createMessage(message, type = "success") {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "timeOut": "5000"
            }
            if (type == "success") {
                toastr.success(message);
            } else if (type == "error") {
                toastr.error(message);
            }

        }
    </script>
    @yield('footer-script')
</body>

</html>
