@extends('layouts.appTest')

@section('title')
Index Submission
@endsection

@section('styles')
<!-- 	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
	<link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"> -->

    <!-- local files -->
    <link href="{{ asset('public/css/jquery-ui.custom.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.v1.10.19.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!-- 	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script> -->

    <!-- local files -->
    <script src="{{ asset('public/js/datatable/dataTables.v1.10.19.min.js') }}"></script>
    <script src="{{ asset('public/js/datatable/moment.v2.18.1.min.js') }}"></script>

@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-12">
	            <div class="card">
	                <div class="card-header">
                        View Received Submissions<a href="{{url('/submission/entry') }}" style="font-weight: bold; float: right;" class="btn btn-primary"> &#xff0b; Submission</a>
                    </div>

                	@if (Session::has('message'))
                    <div class="col-md-12">
                        <div class="alert alert-info alert-dismissible" group="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ Session::get('message') }}
                        </div>
                    </div>
                	@endif

	                <div class="card-body">
                        <div class="form-table">
                        	Received Date: <input type="text" id="receivedDateField" placeholder="dd-mm-yyyy" class="searchTxt">
                        	Input by: <input type="text" id="registerField" class="searchTxt">
                            Form Number: <input type="text" id="formNbrField" class="searchTxt">
							<input type="button" value="Search">
							<input type="button" value="Reset">
                        	<br><br>
                        	<span class="text-danger" id="dateValid"></span>
                            <table border="1" class="table table-hover" id="myTable">
                                <thead>
                                    <th class="text-center">Form Number</th>
                                    <th class="text-center">Received Date</th>
                                    <th class="text-center">Created Date</th>
                                    <th class="text-center">Crew Number</th>
                                    <th class="text-center">Cover</th>
                                    <th class="text-center">Input By</th>
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
                "bFilter" : false,
                "processing": true,
                "order": [[0, 'asc']],
                "serverSide": true,
                "ajax" : {
                	"url" : "{{ url('/submission/read/all') }}",
                	"data" : function (d){
                		d.inputBy = $('#registerField').val();
                		d.revDate = $('#receivedDateField').val();
                        d.formNbr = $('#formNbrField').val();
                	},
                },
                "columns":[
                    { "data": "formNbr", "name": "formNbr"},
                    { "data": "receivedDate", "name": "receivedDate", "render": function(d) {
                        return moment(d).format("DD-MM-YYYY");
                    }},
                    { "data": "created_at", "name": "created_at", "render": function(d) {
                        return moment(d).format("DD-MM-YYYY");
                    }},
                    { "data": "empNbr" , "name": "empNbr"},
                    { "data": "quantity", "name": "quantity", "orderable": false},
                    { "data": "inputBy" , "name": "inputBy"},
                    { "data": "action" , "name": "action", "orderable": false, "searchable": false},
                ],
            });

            $('.searchTxt').on('keydown',function(e){
            	var revDate = $('#receivedDateField').val();
            	var regexDate = /^(\d{2})-(\d{2})-(\d{4})$/;
            	if( (e.which == 13 || e.keyCode == 13 ) && $('#receivedDateField').datepicker("widget").is(":visible") == false) {
            		if(revDate.match(regexDate)) {
            			$('#dateValid').text('');
            			oTable.draw();
            		} else if(revDate == ''){
            			$('#dateValid').text('');
            			oTable.draw();
            		} else{
            			$('#dateValid').text('Date format must be valid.');
            		}
            	}
            });

            $('#receivedDateField').datepicker({
            	dateFormat: 'dd-mm-yy',
		        onSelect:function(){
		            $(this).datepicker("hide");
		        }
            })

            $("input[value='Search']").on('click',function(){
            	var revDate = $('#receivedDateField').val();
            	var regexDate = /^(\d{2})-(\d{2})-(\d{4})$/;
            	if($('#receivedDateField').datepicker("widget").is(":visible") == false) {
            		if(revDate.match(regexDate)) {
            			$('#dateValid').text('');
            			oTable.draw();
            		} else if(revDate == ''){
            			$('#dateValid').text('');
            			oTable.draw();
            		} else{
            			$('#dateValid').text('Date format must be valid.');
            		}
            	}
            });

            $("input[value='Reset']").on('click', function(e){
            	$('.searchTxt').val('');
            });

            $(document).on('click','.btn-delete',function(){
                var formNo = $(this).closest('tr').find('td:eq(0)').text();
                var r = confirm("Are you sure to delete this submisssion: "+formNo+" ?");
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

            $('#startDateField').datepicker("show");

            
        });
	</script>
@endsection