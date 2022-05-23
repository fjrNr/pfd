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
	                	<form method="POST" action="{{ url('/accounts/create') }}">
	                		@csrf
	                		<div class="form-group row">
	                            <label for="id" class="col-md-4 col-form-label text-md-right">ID</label>

	                            <div class="col-md-6">
	                                <input id="id" type="text" class="form-control{{ $errors->has('id') ? ' is-invalid' : '' }}" name="id" value="{{ old('id') }}" required autofocus>

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
	                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

	                                @if ($errors->has('name'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('name') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

	                            <div class="col-md-6">
	                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

	                                @if ($errors->has('password'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('password') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

	                            <div class="col-md-6">
	                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
	                            </div>
	                        </div>
							<div class="form-group row">
				                <label for="role" class="col-md-4 control-label text-md-right">Role</label>

				                <div class="col-md-6">
				                    <select name="role" class="form-control">
				                        <option value="ADMIN">Admin</option>
				                        <option value="SUBM OFC">Submission Officer</option>
				                        <option value="BOX OFC">Box Officer</option>
				                        <option value="ANALYST">Analyst</option>
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
	                                <input id="department" type="text" class="form-control{{ $errors->has('department') ? ' is-invalid' : '' }}" name="department" value="{{ old('department') }}" required autofocus>

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
	                                <input id="homebase" type="text" class="form-control{{ $errors->has('homebase') ? ' is-invalid' : '' }}" name="homebase" value="{{ old('homebase') }}" required autofocus>

	                                @if ($errors->has('homebase'))
	                                    <span class="invalid-feedback">
	                                        <strong>{{ $errors->first('homebase') }}</strong>
	                                    </span>
	                                @endif
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
