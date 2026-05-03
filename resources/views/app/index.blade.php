@extends('app.layout.app')
@section('page_title')
    Home Page
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
    <style>
        #users_list_processing {
            display: none
        }
    </style>
@endsection
@section('content-body')
    <div class="row">
  
    </div>

    <div class="col-md-12">

    </div>
    <!-- /.row -->
    <!-- Main row -->
@endsection
@section('footer-script')
 
@endsection
