@extends('layouts.appTest')

@section('title')
Index Box
@endsection

@section('styles')
	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">

    <link href="{{ asset('public/css/datatable/dataTables.v1.10.19.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{ asset('public/js/datatable/dataTables.v1.10.19.min.js') }}"></script>
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-12">
	            <div class="card">
	                <div class="card-header">
	                	View Accounts
	                	<a href="{{url('/accounts/create') }}" style="font-weight: bold; float: right;" class="btn btn-success"> &#xff0b; Create New</a>
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
	                	<div class="form-table">
                            <table border="1" class="table table-hover" id="myTable" style="width:100%;">
                                <thead>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">User Role</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Homebase</th>
                                    <th class="text-center">Action</th>
                                </thead>
                                <tbody align="center">
                                </tbody>
                            </table>
                        </div>
	                </div>
	            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
        	var oTable = $('#myTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax" : {
                	"url" : "{{ url('/accounts/getData') }}",
                },
                "columns":[
                    { "data": "id", "name": "id"},
                    { "data": "name" , "name": "name"},
                    { "data": "group", "name": "group"},
                    { "data": "department" , "name": "department"},
                    { "data": "homebase" , "name": "homebase"},
                    { "data": "action" , "name": "action", "orderable": false, "searchable": false},
                ],
        	});

            $(document).on('click','.btn-danger',function(){
                var id = $(this).closest('tr').find('td:eq(0)').text();
                var r = confirm("Are you sure to delete this user: "+id+" ?");
                if(r == true){
                    $.ajax({
                        type:'post',
                        url : $(this).attr('href'),
                        data : {
                            '_token' : "{{ csrf_token() }}",
                            'id' : $(this).closest('tr').attr('id'),
                        },
                        success: function(response){
                            alert(response.success);
                            oTable.ajax.reload(null, false);
                        }
                    });
                }
            });
        });
    </script>
@endsection
