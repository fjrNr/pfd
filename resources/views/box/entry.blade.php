@extends('layouts.appTest')

@section('title')
    <?php if (Request::segment(2) == 'edit') { ?>
        Edit Box Assignment
    <?php }else{ ?>
        Insert Box Assignment
    <?php } ?>
@endsection

@section('styles')
<!-- 	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css"> -->

    <!-- local files -->
    <link href="{{ asset('public/css/jquery-ui.custom.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.v1.10.19.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/select.dataTables.v1.2.7.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!--     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script> -->

            <!-- local files -->
    <script src="{{ asset('public/js/datatable/dataTables.v1.10.19.min.js') }}"></script>
    <script src="{{ asset('public/js/datatable/dataTables.select.v1.2.7.min.js') }}"></script>
    <script src="{{ asset('public/js/datatable/moment.v2.18.1.min.js') }}"></script>
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	            <div class="card">
	                <div class="card-header">
                        <?php if (Request::segment(2) == 'entry') { ?>
                            Insert Submission Box                        
                        <?php } else {?>
                            Edit Submission Box
                        <?php } ?>
                    </div>

	                <div class="card-body">
                        <form id="box_form" <?php if (Request::segment(2) == 'edit') { ?> action="{{url('/box/update') }}/{{$box->id}}"<?php }else{ ?> action="{{url('/box/create') }}" <?php } ?>>
                            {{csrf_field()}}
                            <table id="static_field" width="100%">
                                <tr>
                                    <td><label>Class of Date</label></td>
                                    <td colspan="5">
                                        <div class="form-group">
                                            <input type="text" id="classOfDateField" class="form-control text" style="width: 100%; max-width: 120px" placeholder="dd-mm-yyyy" maxlength="10" required pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" 
                                            <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{date('d-m-Y', strtotime($box->classOfDate))}}" disabled
                                            <?php }else{ ?>
                                                value="{{date('d-m-Y')}}"
                                            <?php } ?>
                                            >
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Box Number</label></td>
                                    <td colspan="5">
                                        <div class="form-group">
                                            <input type="text" id="boxNoField" class="form-control text" style="width: 100%; max-width: 45px" required pattern="[0-9]" maxlength="1"
                                            <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{$box->boxNbr}}" disabled
                                            <?php } ?>
                                            >
                                        </div>
                                    </td>
                                    <td rowspan="2" class="text-center">
                                            <label style="font-size: 50px; font-weight: bold;" id="countAssign"
                                                <?php if (Request::segment(2) == 'entry') { ?>
                                                    hidden
                                                <?php } ?>
                                            > </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Package Number</label></td>
                                    <span class="text-danger"></span>
                                    <td><label>PFD</label></td>
                                    <td><label>/</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" id="yearField" style="width: 100%; max-width: 50px;" maxlength="2" required pattern="([0-9]+){2}" class="form-control text"
                                            <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{substr($box->packNbr, 4, 2)}}" disabled
                                            <?php }else{ ?>
                                                value="{{date('y')}}"
                                            <?php } ?>
                                            >
                                        </div>
                                    </td>
                                    <td><label>/</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" id="packageNoField" style="width: 100%; max-width: 70px;" maxlength="4" required pattern="([0-9]+){1,4}" class="form-control text"
                                            <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{substr($box->packNbr, 7)}}" disabled
                                            <?php } ?>
                                            >
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <label style="font-weight: bold">Submission List:</label>
                            <div class="tabs">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#indexTabPane" role="tab" data-toggle="tab" class="nav-link active">Index</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#onHandTabPane" role="tab" data-toggle="tab" class="nav-link">On Hand</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="indexTabPane">
                                    <br>
                                    <table class="table table-hover" id="indexTable" align="center" style="width:100%">
                                        <thead align="center">
                                            <th><input id="select_all" value="1" type="checkbox"></th>
                                            <th>Form Number</th>
                                            <th>Received Date</th>
                                            <th>Crew Number</th>
                                            <th>Crew Name</th>
                                            <th>Crew Rating</th>
                                            <th>Cover</th>
                                        </thead>
                                        <tbody align="center">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="onHandTabPane">
                                    <table class="table table-hover" id="onHandTable" align="center" style="width:100%">
                                        <br>
                                        <thead align="center">
                                            <th >Form Number</th>
                                            <th >Received Date</th>
                                            <th >Crew Number</th>
                                            <th >Crew Name</th>
                                            <th >Crew Rating</th>
                                            <th >Cover</th>
                                            <th >Action</th>
                                        </thead>
                                        <tbody align="center">
                                        <?php if (Request::segment(2) == 'edit') { ?>
                                            @foreach($submissions as $subm)
                                            <tr id="{{$subm->id}}">
                                                <td>{{$subm->formNbr}}</td>
                                                <td>{{$subm->receivedDate}}</td>
                                                <td>{{$subm->empNbr}}</td>
                                                <td>{{$subm->signed}}</td>
                                                <td>{{$subm->empRank}}</td>
                                                <td>{{$subm->quantity}}</td>
                                                <td align="center"><input type="button" value="X" class="btn btn-danger btn-remove"></td>
                                            </tr>
                                            @endforeach 
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <label id="countSelected" style="font-weight: bold"></label> -->
                            <input type="submit" id="create" value="Create" class="btn btn-success">
                        </form>
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
            var oTable = $('#indexTable').DataTable({
                "processing": true,
                "order": [[1, 'desc']],
                "serverSide": true,
                "ajax" : {
                    "url" : "{{ url('/submission/read/nonAssignedOnly') }}",
                    "data" : function (d){
                        d.inputBy = $('#registerField').val();
                        d.revDate = $('#receivedDateField').val();
                        d.formNbr = $('#formNbrField').val();
                    },
                },
                "columns":[
                    { "orderable": false, "render": function (d){
                        return '<input type="checkbox">';
                    }},
                    { "data": "formNbr"},
                    { "data": "receivedDate", "render": function(d) {
                        return moment(d).format("DD-MM-YYYY");
                    }},
                    { "data": "empNbr"},
                    { "data": "signed"},
                    { "data": "empRank"},
                    { "data": "quantity", "orderable": false},
                ],
                rowCallback: function(row, data, dataIndex){
                    // If row form number is in the list of selected row form numbers
                    if($.inArray(data.id, rows_selected) !== -1){
                       $(row).find('input[type="checkbox"]').prop('checked', true);
                       $(row).addClass('selected');
                    }
                },
            });

            var pTable = $('#onHandTable').DataTable();

            var rows_selected = [];
            @if (Request::segment(2) == 'edit')
                @foreach ($submissions as $subm) 
                    rows_selected.push({{$subm->id}});
                @endforeach
                console.log("rows_selected= " + rows_selected);
            @endif

            $('#indexTable tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');

                // Get row id
                var rowId = $row.attr('id');

                // Determine whether row id is in the list of selected row form numbers 
                var index = $.inArray(rowId, rows_selected);

                // If checkbox is checked and row id is not in list of selected row form numbers
                if(this.checked && index === -1){
                    rows_selected.push(rowId);
                }else if(!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                }

                if(this.checked){
                    $row.addClass('selected');
                    pTable.row.add([
                        $row.find('td:eq(1)').text(),
                        $row.find('td:eq(2)').text(),
                        $row.find('td:eq(3)').text(),
                        $row.find('td:eq(4)').text(),
                        $row.find('td:eq(5)').text(),
                        $row.find('td:eq(6)').text(),
                        '<td align="center"><input type="button" value="X" class="btn btn-danger btn-remove"></td>\n'
                    ]).node().id = rowId; 
                    pTable.draw(false);
                }else{
                    $row.removeClass('selected');
                    pTable.row($('#onHandTable tbody tr[id="'+ rowId +'"]')).remove().draw();
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(oTable);

                // Prevent click event from propagating to parent
                e.stopPropagation();
                $('#countSelected').empty();
                if(rows_selected.length > 1){
                    // $('#countSelected').append(rows_selected.length + ' items selected.');
                    console.log("rows selected: " + rows_selected);
                }else if(rows_selected.length == 1){
                    // $('#countSelected').append(rows_selected.length + ' item selected.');
                    console.log("rows selected: " + rows_selected);
                }
            });

            // Handle click on table cells with checkboxes
            $('#indexTable').on('click', 'tbody td, thead th:first-child', function(e){
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

            $('#classOfDateField').focus();

            $('.text').on('keydown', function(e){
                if(!((e.keyCode >= 48 && e.keyCode <= 57) || (e.which >= 48 && e.which <= 57)
                    || (e.keyCode == 8) || (e.which == 8)
                    || (e.keyCode == 13) || (e.which == 13)
                    || (e.keyCode == 9) || (e.which == 9))){
                    e.preventDefault();
                }
            })

            $('#onHandTable tbody').on('click', '.btn-remove', function(){
                var $row = $(this).parents('tr');
                var rowId = $row.attr('id');

                pTable.row($row).remove().draw();
                $('#indexTable tbody tr[id="'+ rowId +'"] td input').prop('checked', false);
                $('#indexTable tbody tr[id="'+ rowId +'"]').removeClass('selected');
                rows_selected.splice($.inArray(rowId, rows_selected), 1);
                updateDataTableSelectAllCtrl(oTable);

                console.log("rows selected: " + rows_selected);
            });

            $('#box_form').on('submit', function(e){
                e.preventDefault();
                disableInput(true);
                
                if(rows_selected.length > 0){
                    $.ajax({
                        type: 'GET',
                        url : $(this).attr('action'),
                        data : {
                            'classOfDate' : $('#classOfDateField').val(),
                            'boxNo' : $('#boxNoField').val(),
                            'packYear' : $('#yearField').val(),
                            'packNo' : $('#packageNoField').val(),
                            'submissionIdArray' : JSON.stringify(rows_selected),
                        },
                        success: function(response){
                            if(response.success){
                                alert(response.success);
                                disableInput(false);
                                $('input[type="text"]').val(null);
                                $('#classOfDateField').val('{{date('d-m-Y')}}');
                                $('#yearField').val('{{date('y')}}');
                                pTable.rows().remove().draw();
                                rows_selected = [];
                                oTable.ajax.reload(null, false);
                                updateDataTableSelectAllCtrl(oTable);
                            }else{
                                alert(response.error);
                                disableInput(false);
                            }
                        },
                        error: function(response){
                            alert(response.error);
                            disableInput(false);
                        }
                    });
                }else{
                    alert("Please select submission");
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $('#classOfDateField').datepicker({
            dateFormat: 'dd-mm-yy',
            onSelect:function(){
                $(this).datepicker("hide");
            }
        });

        function disableInput(bol){
            if(bol){
                $('input').prop('disabled', true);
                $('select').prop('disabled', true);
                $('.paginate_button').addClass('a-tag-disabled');
                $('#indexTable th').addClass('a-tag-disabled');
                $('#onHandTable th').addClass('a-tag-disabled');
            }else{
                $('input').prop('disabled', false);
                $('select').prop('disabled', false);
                $('.paginate_button').removeClass('a-tag-disabled');
                $('#indexTable th').removeClass('a-tag-disabled');
                $('#onHandTable th').removeClass('a-tag-disabled');
            }
        }
    </script>
@endsection	