@extends('layouts.appTest')

@section('title')
    Movement Confirmation    
@endsection

@section('styles')
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
                    View Requested Movements
                    <a href="{{url('/movement/entry') }}" style="font-weight: bold; float: right;" class="btn btn-success"> &#xff0b; Request</a>
                </div>

                <div class="card-body">
                    <table class="table" border="1" width="100%" id="myTable">
                        <thead align="center">
                            <th>Request Date</th>
                            <th>Shipping Date</th>
                            <th>Shipping No</th>
                            <th>Total Box</th>
                            <th>Status</th>
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
    $(document).ready(function(){
        var oTable = $('#myTable').DataTable({
            "bFilter" : true,
            "processing": true,
            "order": [[0, 'desc']],
            "serverSide": true,
            "ajax" : {
                "url" : "{{ url('/movement/readAll')}}",
            },
            "columns":[
                { "data": "requestDate", "name": "requestDate"},
                { "data": "shippingDate", "name": "shippingDate"},
                { "data": "shippingNo", "name": "shippingNo"},
                { "data": "totalBox", "name": "totalBox", "searchable": false},
                { "data": "status" , "name": "status"},
                { "data": "action" , "name": "action", "orderable": false, "searchable": false},
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

        $(document).on('click','.btn-delete',function(){
            var r = confirm("Are you sure to cancel this movement request?");
            if(r == true){
                $.ajax({
                    type:'POST',
                    url : $(this).attr('href'),
                    data : {
                        '_token' : "{{ csrf_token() }}",
                        'id' : $(this).closest('tr').attr('id'),
                    },
                    success: function(response){
                        if(response.success){
                            alert(response.success);
                            oTable.ajax.reload(null, false);
                        }else{
                            alert(response.error);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection