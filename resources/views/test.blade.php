<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Post Flight Document</title>
    <link rel="shortcut icon" href="{{ asset('public/flight.png') }}">

    <!-- Styles -->
<!--     <link href="{{asset('public/css/menu/sb-admin-2.css')}}" rel="stylesheet">

    <script src="{{asset('public/css/menu/sb-admin-2.css')}}"></script> -->
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div>
                    <center><b>Post Flight Document</b></center>
                    <div class="panel-heading">Please Login</div>
                    <div class="panel-body">
                        @foreach($submissions as $subm)
                        {{$subm->receivedDate}} {{$subm->empNbr}}
                        <br>
                        @endforeach
                        {{$count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>