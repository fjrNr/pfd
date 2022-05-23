@extends('layouts.appTest')

@section('title')
Index AFL Box
@endsection

@section('styles')
	<link href="{{ asset('public/css/jquery-ui.custom.v1.12.1.min.css') }}" rel="stylesheet">
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
	                	View AFL Boxes
	                	<a href="{{url('/box/entry/afl') }}" style="font-weight: bold; float: right;" class="btn btn-success"> &#xff0b; AFL Box</a>
	                </div>

	                <div class="card-body">
	        	      	<table class="table" border="1" width="100%" id="myTable">
	                        <thead align="center">
	                            <th>Package Number</th>
                                <th>Start Periode</th>
                                <th>End Periode</th>
                                <th>Total AFLs</th>
                                <th>Location</th>
                                <th>Action</th>
	                        </thead>
	                        <tbody align="center">
	                        </tbody>
	                    </table>
	                </div>
	            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    	var oTable = $('#myTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[0, 'desc']],
            "ajax" : {
            	"url" : "{{ url('/box/readAll/afl') }}",
            },
            "columns":[
                { "data": "packNbr", "name": "packNbr"},
                { "data": "startDate", "name": "afl_copies.arrdate"},
                { "data": "endDate", "name": "afl_copies.arrdate"},
                { "data": "totalDoc", "name": "totalDoc", "searchable": false},
                { "data": "location", "name": "location"},
                { "data": "action", "name": "action", "orderable": false, "searchable": false},
            ],
            "initComplete": function(settings, json) {
                $('#myTable_filter input').unbind();
                $('#myTable_filter input').bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        oTable.search( this.value ).draw();
                    }
                }); 
            }
    	});
    </script>
@endsection
