@extends('layouts.appTest')

@section('title')
Index Box
@endsection

@section('styles')
	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">

	        <!-- local files -->
    <!-- <link href="{{ asset('public/css/jquery-ui.custome.v1.12.1.min.css') }}" rel="stylesheet"> -->
@endsection

@section('scripts')
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">
	                	Create Account
	                </div>

	                <div class="card-body">
	                	@if (Session::has('message'))
                        <div class="col-md-12">
                            <div class="alert alert-info alert-dismissible" group="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ Session::get('message') }}
                            </div>
                        </div>
                        @endif
	                	<form method="POST" <?php if (Request::segment(1) == 'user') { ?> action="{{ url('/user') }}" <?php }else{?> action="{{ url('/accounts/edit') }}/{{$user->id}}" <?php }?>>
	                		@csrf
	                		<div class="form-group row">
	                            <label for="id" class="col-md-4 col-form-label text-md-right">ID</label>

	                            <div class="col-md-6">
	                                <input id="id" type="text" class="form-control{{ $errors->has('id') ? ' is-invalid' : '' }}" name="id" value="{{$user->id}}" <?php if (Request::segment(1) == 'user') { ?> disabled <?php }?>required autofocus>

	                                @if ($errors->has('id'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('id') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
	                		<div class="form-group row">
	                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

	                            <div class="col-md-6">
	                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $user->name }}" required autofocus>

	                                @if ($errors->has('name'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('name') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
							<div class="form-group row">
				                <label for="role" class="col-md-4 control-label text-md-right">Role</label>

				                <div class="col-md-6">
				                    <select name="role" class="form-control" <?php if (Request::segment(1) == 'user') { ?> disabled <?php }?>>
				                        <option value="ADMIN" <?php if($user->group == 'ADMIN') {?>selected <?php } ?>>Admin</option>
				                        <option value="SUBM OFC" <?php if($user->group == 'SUBM OFC') {?>selected <?php } ?>>Submission Officer</option>
				                        <option value="BOX OFC" <?php if($user->group == 'BOX OFC') {?>selected <?php } ?>>Box Officer</option>
				                        <option value="ANALYST" <?php if($user->group == 'ANALYST') {?>selected <?php } ?>>Analyst</option>
				                    </select>
				                </div>
				                @if ($errors->has('role'))
				                    <span class="help-block">
				                        <strong>{{ $errors->first('role') }}</strong>
				                    </span>
				                @endif
				            </div>
	                        <div class="form-group row">
	                            <label for="department" class="col-md-4 col-form-label text-md-right">Department</label>

	                            <div class="col-md-6">
	                                <input id="department" type="text" class="form-control{{ $errors->has('department') ? ' is-invalid' : '' }}" name="department" value="{{ $user->department }}" required autofocus>

	                                @if ($errors->has('department'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('department') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="homebase" class="col-md-4 col-form-label text-md-right">Homebase</label>

	                            <div class="col-md-6">
	                                <input id="homebase" type="text" class="form-control{{ $errors->has('homebase') ? ' is-invalid' : '' }}" name="homebase" value="{{ $user->homebase }}" required autofocus>

	                                @if ($errors->has('homebase'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('homebase') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                        	<label class="col-md-4 col-form-label text-md-right">Password</label>
	                        	<div class="col-md-6">
	                        		<a <?php if (Request::segment(1) == 'user') { ?> href="user/password" <?php }else{?> href="{{$user->id}}/password" <?php }?> class="btn btn-warning">Change Password</a>
	                        	</div>
	                        </div>
	                        <div class="form-group row">
	                        	<label class="col-md-4 col-form-label text-md-right"></label>
	                        	<div class="col-md-6">
									<input type="reset" class="btn btn-primary" value="Reset">
	                				<input type="submit" class="btn btn-success" value="Submit">
	                        	</div>
	                        </div>
	                	</form>
	                </div>
	            </div>
            </div>
        </div>
    </div>
@endsection
