@extends('layouts.appTest')

@section('title')
Change Password
@endsection

@section('styles')
    <link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    <!-- local files -->
<!--     <link href="{{ asset('public/css/jquery-ui.custome.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.css') }}" rel="stylesheet"> -->
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

            <!-- local files -->
    <!-- <script src="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.js') }}"></script> -->
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">Change Password</div>

	                <div class="card-body">
                        @if (Session::has('message'))
                        <div class="col-md-12">
                            <div class="alert alert-info alert-dismissible" group="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ Session::get('message') }}
                            </div>
                        </div>
                        @endif
                        <form method="POST" <?php if (Request::segment(2) == 'edit') { ?> <?php }else{?> action="{{url('/user/password') }}" <?php } ?>>
                            @csrf

                            <?php if (Request::segment(2) != 'edit') { ?>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>

                                <div class="col-md-6">
                                    <input id="currentPassword" type="password" class="form-control{{ $errors->has('currentPassword') ? ' is-invalid' : '' }}" name="currentPassword" required>

                                    @if ($errors->has('currentPassword'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('currentPassword') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <?php }?>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>

                                <div class="col-md-6">
                                    <input id="newPassword" type="password" class="form-control{{ $errors->has('newPassword') ? ' is-invalid' : '' }}" name="newPassword" required>

                                    @if ($errors->has('newPassword'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('newPassword') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm New Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="newPasswordConfirmation" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">
                                        Change
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
	            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        
    </script>
@endsection