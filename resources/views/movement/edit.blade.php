@extends('layouts.appTest')

@section('title')
    
@endsection

@section('styles')
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">

            <!-- local files -->
<!--     <link href="{{ asset('public/css/jquery-ui.custome.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/select.dataTables.v1.2.7.min.css') }}" rel="stylesheet"> -->
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    
                <!-- local files -->
<!--     <script src="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.js') }}"></script>
    <script src="{{ asset('public/css/datatable/dataTables.select.v1.2.7.min.js') }}"></script> -->
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Entry Document Movement</div>

                <div class="card-body">
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#inputForm" role="tab" data-toggle="tab" class="btn">Input Form</a>
                            </li>
                            <li>
                                <a href="#onHand" role="tab" data-toggle="tab" class="btn">On Hand</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        
                        <div class="tab-pane active in" id="inputForm">
                            <table class="table table-hover" border="1" width="100%" id="formTable">
                                Storage Date: <input type="text" name="stoDate" id="stoDateField"placeholder="dd-mm-yyyy" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" value="{{date('d-m-Y',strtotime(date('d-m-Y') . '1 day'))}}">
                                <thead align="center">
                                    <th>ID</th>
                                    <th>Class of Date</th>
                                    <th>Identity Box</th>
                                    <th>Action</th>
                                </thead>
                                <tbody align="center">
                                </tbody>
                            </table>
                            <input type="button" class="btn btn-success" value="Submit" id="submit">
                        </div>

                        <div class="tab-pane" id="onHand">
                            <input type="button" class="btn btn-success" value="+ Add" id="addRequest">
                            <table class="table table-hover" border="1" width="100%" id="onHandTable">
                                <thead align="center">
                                    <th><input id="select_all" value="1" type="checkbox"></th>
                                    <th>ID</th>
                                    <th>Class of Date</th>
                                    <th>Identity Box</th>
                                </thead>
                                <tbody align="center">
                                    @foreach($submBoxes as $subm)
                                        <tr>
                                            <td></td>
                                            <td>{{$subm->id}}</td>
                                            <td>{{date('d-m-Y', strtotime($subm->classOfDate))}}</td>
                                            <td>{{$subm->packNbr}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>	

<script type="text/javascript">
    function updateDataTableSelectAllCtrl(table){
        var $table             = table.table().node();
        var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
        var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
        var chkbox_select_all  = $('thead #select_all', $table).get(0);

        // If none of the checkboxes are checked
        if($chkbox_checked.length === 0){
            chkbox_select_all.checked = false;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = false;
            }
        // If all of the checkboxes are checked
        }else if($chkbox_checked.length === $chkbox_all.length){
            chkbox_select_all.checked = true;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = false;
            }
        // If some of the checkboxes are checked
        }else{
            chkbox_select_all.checked = true;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = true;
            }
        }
    }

    $(document).ready(function(){
        var rows_selected = [];
        var rows_idty = [];
        var rows_date = [];
        var idArraySend = [];

        // var rows_
        var oTable = $('#onHandTable').DataTable({
            columnDefs: [{
                orderable: false,
                targets: 0,
                render: function (data, type, full, meta){
                    return '<input type="checkbox">';
                    },
                },
                {orderable: false, visible: false, searchable: false, targets: 1},
            ],
            order: [[2, 'desc']],
            rowCallback: function(row, data, dataIndex){
                // Get row form Number
                var rowId = data[1];

                // If row form number is in the list of selected row form numbers
                if($.inArray(rowId, rows_selected) !== -1){
                   $(row).find('input[type="checkbox"]').prop('checked', true);
                   $(row).addClass('selected');
                }
            },
        });

        var fTable = $('#formTable').DataTable({
            columnDefs: [
                {orderable: false, visible: false, searchable: false, targets: 0},
                {orderable: false, searchable: false, targets: 3},
            ],
        });

        $('#onHandTable tbody').on('click', 'input[type="checkbox"]', function(e){
            var $row = $(this).closest('tr');

            // Get row data
            var data = oTable.row($row).data();

            // Get row id
            var rowId = data[1];
            var rowDate = data[2];
            var rowIdty = data[3];

            // Determine whether row id is in the list of selected row form numbers 
            var index = $.inArray(rowId, rows_selected);

            // If checkbox is checked and row id is not in list of selected row form numbers
            if(this.checked && index === -1){
                rows_selected.push(rowId);
                rows_date.push(rowDate);
                rows_idty.push(rowIdty);
                //console.log(rows_selected, rows_date, rows_idty);
            }else if(!this.checked && index !== -1){
                rows_selected.splice(index, 1);
                rows_date.splice(index, 1);
                rows_idty.splice(index, 1);
                //console.log(rows_selected, rows_date, rows_idty);
            }

            if(this.checked){
                $row.addClass('selected');
            }else{
                $row.removeClass('selected');
            }

            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(oTable);

            // Prevent click event from propagating to parent
            e.stopPropagation();
            $('#countSelected').empty();
            if(rows_selected.length > 1){
                $('#countSelected').append(rows_selected.length + ' items selected.');
            }else if(rows_selected.length == 1){
                $('#countSelected').append(rows_selected.length + ' item selected.');
            }
        });

        // Handle click on table cells with checkboxes
        $('#onHandTable').on('click', 'tbody td, thead th:first-child', function(e){
            $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead #select_all', oTable.table().container()).on('click', function(e){
            if(this.checked){
                $('tbody input[type="checkbox"]:not(:checked)', oTable.table().container()).trigger('click');
            }else{
                $('tbody input[type="checkbox"]:checked', oTable.table().container()).trigger('click');
            }

            // Prevent click event from propagating to parent
            e.stopPropagation();
        });

        // Handle table draw event
        oTable.on('draw', function(){
            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(oTable);
        });
        
        $('#addRequest').on('click', function(){
            for(var i = 0; i < rows_selected.length; i++){
                fTable.row.add([
                    rows_selected[i],
                    rows_date[i],
                    rows_idty[i],
                    '<input type="button" class="btn btn-danger btn-remove" value="X">',
                ]).draw(false);
                idArraySend.push(rows_selected[i]);
            };
            oTable.rows('.selected').remove().draw(false);
            rows_selected=[];
            rows_date=[];
            rows_idty=[];
            console.log(idArraySend);
        })

        $(document).on('click', '.btn-remove', function(){
            var selected_row = fTable.row($(this).closest('tr'));
            var index = $.inArray(selected_row.data()[0], idArraySend);
            oTable.row.add([
                '<input type="checkbox">',
                selected_row.data()[0],
                selected_row.data()[1],
                selected_row.data()[2],
            ]).draw(false);

            idArraySend.splice(index,1);
            console.log(idArraySend);
            selected_row.remove().draw(false);
        });

        $('#submit').on('click', function(e){
            var stoDate = $('#stoDateField').val();
            var regexDate = /^(\d{2})-(\d{2})-(\d{4})$/;
            
            e.preventDefault();
            if(fTable.data().count() > 0 && stoDate.match(regexDate)){
                var idArray = JSON.stringify(idArraySend);
                $.ajax({
                    type:'post',
                    url : '{{URL::to('movement/entry')}}',
                    data : {
                        'stoDate' : stoDate,
                        'idArray' : idArray,
                    },
                    success:function(response){
                        alert(response.success);
                        fTable.rows().remove().draw(false);
                        idArraySend=[];
                    }
                });
            }else{
                alert('Tidak');
            }
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection