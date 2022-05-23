@extends('layouts.appTest')

@section('title')
    <?php if (Request::segment(2) == 'edit') { ?>
        Edit Movement Request
    <?php }else{ ?>
        Entry Movement Request
    <?php } ?>
@endsection

@section('styles')
<!--     <link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css"> -->

            <!-- local files -->
    <link href="{{ asset('public/css/jquery-ui.custom.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.v1.10.19.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/select.dataTables.v1.2.7.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<!--     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script> -->

                <!-- local files -->
    <script src="{{ asset('public/js/datatable/dataTables.v1.10.19.min.js') }}"></script>
    <script src="{{ asset('public/js/datatable/dataTables.select.v1.2.7.min.js') }}"></script>
    <script src="{{ asset('public/js/datatable/moment.v2.18.1.min.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><?php if (Request::segment(2) == 'edit') { ?> Edit <?php } else { ?> Entry <?php } ?>  Movement Request</div>

                <div class="card-body">
                    <div class="alert alert-info" style="display:none">
                        <span id="spanSuccess"></span>
                        <button type="button" class="close" onclick="$('.alert-info').css('display', 'none');"><span>&times;</span></button>
                    </div>

                    <form id="movement_form" <?php if (Request::segment(2) == 'edit') { ?> action="{{url('/movement/update') }}/{{$movement->id}}"<?php }else{ ?> action="{{url('/movement/create') }}" <?php } ?>>
                        {{csrf_field()}}
                        <table id="static_field" width="100%">
                            <?php if (Request::segment(2) == 'edit') { ?>
                                <tr>
                                    <td><label>Status</label></td>
                                    <td>
                                        @switch($movement->status)
                                            @case(config('enums.status')[0])
                                                <label id="statusLabel" style="font-weight: bold; color: red;">{{$movement->status}}</label>
                                                @break
                                            @case(config('enums.status')[3])
                                                <label id="statusLabel" style="font-weight: bold; color: limegreen;">{{$movement->status}}</label>
                                                @break
                                            @default 
                                                <label id="statusLabel" style="font-weight: bold; color: orange;">{{$movement->status}}</label>
                                                @break
                                        @endswitch
                                    </td>
                                    <td><label>Shipping Note</label></td>
                                    <td rowspan="2" colspan="4">
                                        <div class="form-group">
                                            <textarea type="text" name="shipNote" id="shipNoteField" class="form-control text" maxlength="500" style="width: 100%" required
                                            <?php if ($movement->status != config('enums.status')[1]) { ?>
                                            disabled
                                            <?php } ?>
                                            >{{$movement->shippingNote}}</textarea>
                                            <span class="text-danger" id="shipNoteErr"></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            
                            <tr>
                                <td><label>Request Date</label></td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" name="reqDate" id="reqDateField" class="form-control text" style="width: 110px;" required pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" 
                                        <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{date('d-m-Y',strtotime($movement->requestDate))}}" 
                                        <?php } else { ?>
                                                value="{{date('d-m-Y')}}" 
                                        <?php } ?>
                                        disabled>
                                        <span class="text-danger" id="reqDateErr"></span>
                                    </div>
                                </td>
                                <?php if (Request::segment(2) == 'entry') { ?>
                                    <td><label>Shipping Date (Schedule)</label></td>
                                    <td colspan="4">
                                        <div class="form-group">
                                            <input type="text" name="shipDate" id="shipDateField" class="form-control text" required pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" style="width: 110px" value="">
                                            <span class="text-danger" id="shipDateErr"></span>
                                        </div>
                                    </td>
                                <?php } ?>
                            </tr>
                            
                            <?php if (Request::segment(2) == 'edit') { ?>
                                <tr>
                                    <td><label>Shipping Date</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="shipDate" id="shipDateField" class="form-control text" required style="width: 110px;" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" value="{{date('d-m-Y',strtotime($movement->shippingDate))}}"
                                            <?php if ($movement->status != config('enums.status')[1]) { ?> 
                                                disabled
                                            <?php } ?> 
                                            >
                                            <span class="text-danger" id="shipDateErr"></span>
                                        </div>
                                    </td>
                                    <td><label>News No</label></td>
                                    <td><label>JKTDSD/BAST/</label></td>
                                    <td>
                                        <div class="form-group" >
                                            <input type="text" name="newsNo" id="newsNoField" class="form-control text" pattern="([0-9]+){5}" style="width: 80px;" required maxlength="5" 
                                            <?php if (isset($movement->newsNo)) {?>
                                            value="{{explode('/',$movement->newsNo)[2]}}"
                                            <?php } ?>
                                            <?php if ($movement->status != config('enums.status')[1]) { ?> disabled <?php } ?>
                                            >
                                        </div>
                                    </td>
                                    <td><label>/</label></td>
                                    <td>
                                        <div class="form-group" >
                                            <input type="text" name="newsYear" id="newsYearField" class="form-control text" pattern="([0-9]+){4}" style="width: 60px;" required maxlength="4" 
                                            <?php if (isset($movement->newsNo)) {?>
                                            value="{{explode('/',$movement->newsNo)[3]}}"
                                            <?php } ?>
                                            <?php if ($movement->status != config('enums.status')[1]) { ?> disabled <?php } ?>
                                            >
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td colspan="4"><span class="text-danger" id="newsNoErr"></span></td>
                                </tr>
                                <tr>
                                    <td><label>Shipping No</label></td>
                                    <td>
                                        <div class="form-group ">
                                            <input type="text" name="shipNo" id="shipNoField" class="form-control text" maxlength="6" required style="width: 110px;" pattern="[0-9]+"
                                            <?php if (isset($movement->shippingNo)) {?>
                                            value="{{$movement->shippingNo}}"
                                            <?php } ?>
                                            <?php if ($movement->status != config('enums.status')[1]) { ?> disabled <?php } ?>
                                            >
                                            <span class="text-danger" id="shipNoErr"></span>
                                        </div>
                                    </td>
                                    <td><label>Storage Date</label></td>
                                    <td colspan="4">
                                        <div class="form-group" >
                                            @if($movement->status == config('enums.status')[2])
                                                <input type="text" name="stoDate" id="stoDateField" class="form-control text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" style="width: 110px;" required>
                                            @else
                                                <input type="text" name="stoDate" id="stoDateField" class="form-control text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}" style="width: 110px;" 
                                                <?php if (isset($movement->storageDate)) { ?>
                                                value="{{date('d-m-Y',strtotime($movement->storageDate))}}" 
                                                <?php } ?>
                                                disabled>
                                            @endif
                                            <span class="text-danger" id="stoDateErr"></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>

                        <div class="tabs">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#aflListPane" role="tab" data-toggle="tab" class="nav-link active">AFL List</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#nonAflListPane" role="tab" data-toggle="tab" class="nav-link">Non AFL List</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <br>
                            <div class="tab-pane active" id="aflListPane">
                                <table class="table table-hover" border="1" width="100%" id="aflTable">
                                    <thead align="center">
                                        <?php if(Request::segment(2) == 'entry') { ?>
                                            <th><input id="select_all" value="1" type="checkbox"></th>
                                        <?php } ?>
                                        <th>Package Number</th>
                                        <th>Start Periode</th>
                                        <th>End Periode</th>
                                        <th>Registration Code</th>
                                        <th>Total AFLs</th>
                                    </thead>
                                    <tbody align="center">
                                        @foreach($aflBoxes as $box)
                                            <tr id="{{$box->id}}">
                                                <?php if(Request::segment(2) == 'entry') { ?>
                                                    <td></td>
                                                <?php } ?>
                                                <td>{{$box->packNbr}}</td>
                                                <td>{{date('M Y',strtotime($box->startDate))}}</td>
                                                <td>{{date('M Y',strtotime($box->endDate))}}</td>
                                                <td>{{$box->notes}}</td>
                                                <td>{{$box->totalDoc}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="nonAflListPane">
                                <table class="table table-hover" border="1" width="100%" id="nonAflTable">
                                    <thead align="center">
                                        <?php if(Request::segment(2) == 'entry') { ?>
                                            <th><input id="select_all" value="1" type="checkbox"></th>
                                        <?php } ?>
                                        <th>Class Of</th>
                                        <th>Package Number</th>
                                        <th>Box Number</th>
                                        <th>Total Submissions</th>
                                        <th>Total Covers</th>
                                    </thead>
                                    <tbody align="center">
                                        @foreach($nonAflBoxes as $box)
                                            <tr id="{{$box->id}}">
                                                <?php if(Request::segment(2) == 'entry') { ?>
                                                    <td></td>
                                                <?php } ?>
                                                <td>{{$box->classOfDate}}</td>
                                                <td>{{$box->packNbr}}</td>
                                                <td>{{$box->boxNbr}}</td>
                                                <td>{{$box->totalDoc}}</td>
                                                <td>{{$box->totalCover}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <td>
                            @if (Request::segment(2) == 'edit' && $movement->status == config('enums.status')[0])
                                <input type="submit" class="btn btn-success" value="Submit" id="submitBtn" disabled style="float: right;">
                            @else
                                <input type="submit" class="btn btn-success" value="Submit" id="submitBtn" style="float: right;">
                            @endif

                            @if (Request::segment(2) == 'edit')
                                @if ($movement->status == config('enums.status')[3] || $movement->status == config('enums.status')[0])
                                    <input type="button" class="btn btn-danger" value="Cancel" id="cancelBtn" disabled style="float: right;">
                                @else
                                    <input type="button" class="btn btn-danger" value="Cancel" id="cancelBtn" style="float: right;">
                                @endif
                            @endif
                        </td>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>	

<script type="text/javascript">
    <?php if(Request::segment(2) == 'entry') { ?>
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
    <?php } ?>

    $(document).ready(function(){
        $('#shipDateField').focus();

        var aTable = $('#aflTable').DataTable({
            <?php if(Request::segment(2) == 'entry') { ?>
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        targets: 0,
                        render: function (data, type, full, meta){
                            return '<input type="checkbox">';
                        },
                    },
                ],
            <?php } ?>
            orderCellsTop: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            rowCallback: function(row, data, dataIndex){
                // If row form number is in the list of selected row form numbers
                if($.inArray(data.id, afl_rows_selected) !== -1){
                   $(row).find('input[type="checkbox"]').prop('checked', true);
                   $(row).addClass('selected');
                }
            },
        });

        var nTable = $('#nonAflTable').DataTable({
            <?php if(Request::segment(2) == 'entry') { ?>
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        targets: 0,
                        render: function (data, type, full, meta){
                            return '<input type="checkbox">';
                        },
                    },
                ],
            <?php } ?>
            orderCellsTop: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            rowCallback: function(row, data, dataIndex){
                // If row form number is in the list of selected row form numbers
                if($.inArray(data.id, non_afl_rows_selected) !== -1){
                   $(row).find('input[type="checkbox"]').prop('checked', true);
                   $(row).addClass('selected');
                }
            },
        });

        var afl_rows_selected = [];
        var non_afl_rows_selected = [];

        <?php if(Request::segment(2) == 'entry') { ?>
            $('#aflTable tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');

                // Get row id
                var rowId = $row.attr('id');

                // Determine whether row id is in the list of selected row form numbers 
                var index = $.inArray(rowId, afl_rows_selected);

                // If checkbox is checked and row id is not in list of selected row form numbers
                if(this.checked && index === -1){
                    afl_rows_selected.push(rowId);
                }else if(!this.checked && index !== -1){
                    afl_rows_selected.splice(index, 1);
                }

                if(this.checked){
                    $row.addClass('selected');
                }else{
                    $row.removeClass('selected');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(aTable);

                // Prevent click event from propagating to parent
                e.stopPropagation();
                if(afl_rows_selected.length > 1){
                    // $('#countSelected').append(rows_selected.length + ' items selected.');
                    console.log("AFL rows selected: " + afl_rows_selected);
                }else if(afl_rows_selected.length == 1){
                    // $('#countSelected').append(rows_selected.length + ' item selected.');
                    console.log("AFL rows selected: " + afl_rows_selected);
                }
            });
            $('#nonAflTable tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');

                // Get row id
                var rowId = $row.attr('id');

                // Determine whether row id is in the list of selected row form numbers 
                var index = $.inArray(rowId, non_afl_rows_selected);

                // If checkbox is checked and row id is not in list of selected row form numbers
                if(this.checked && index === -1){
                    non_afl_rows_selected.push(rowId);
                }else if(!this.checked && index !== -1){
                    non_afl_rows_selected.splice(index, 1);
                }

                if(this.checked){
                    $row.addClass('selected');
                }else{
                    $row.removeClass('selected');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(nTable);

                // Prevent click event from propagating to parent
                e.stopPropagation();
                if(non_afl_rows_selected.length > 1){
                    // $('#countSelected').append(rows_selected.length + ' items selected.');
                    console.log("Non AFL rows selected: " + non_afl_rows_selected);
                }else if(non_afl_rows_selected.length == 1){
                    // $('#countSelected').append(rows_selected.length + ' item selected.');
                    console.log("Non AFL rows selected: " + non_afl_rows_selected);
                }
            });


            // Handle click on table cells with checkboxes
            $('#aflTable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });
            // Handle click on table cells with checkboxes
            $('#nonAflTable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            // Handle click on "Select all" control
            $('thead #select_all', aTable.table().container()).on('click', function(e){
                if(this.checked){
                    $('tbody input[type="checkbox"]:not(:checked)', aTable.table().container()).trigger('click');
                }else{
                    $('tbody input[type="checkbox"]:checked', aTable.table().container()).trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });
            $('thead #select_all', nTable.table().container()).on('click', function(e){
                if(this.checked){
                    $('tbody input[type="checkbox"]:not(:checked)', nTable.table().container()).trigger('click');
                }else{
                    $('tbody input[type="checkbox"]:checked', nTable.table().container()).trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle table draw event
            aTable.on('draw', function(){
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(aTable);
            });
            // Handle table draw event
            nTable.on('draw', function(){
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(nTable);
            });
        <?php } ?>

        $('#movement_form').on('submit', function(e){
            e.preventDefault();

            <?php if(Request::segment(2) == 'entry') { ?>
            if(afl_rows_selected.length > 0 || non_afl_rows_selected.length > 0){
            <?php } ?>
                clearMessageErrors();
                $('input, textarea, select').prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url : $(this).attr('action'),
                    data : {
                        'requestDate' : $('#reqDateField').val(),
                        'idAflArray' : JSON.stringify(afl_rows_selected),
                        'nonIdAflArray' : JSON.stringify(non_afl_rows_selected),
                        'newsNo' : 'JKTDSD/BAST/'+$('#newsNoField').val()+'/'+$('#newsYearField').val(),
                        'shippingDate' : $('#shipDateField').val(),
                        'shippingNo' : $('#shipNoField').val(),
                        'shippingNote' : $('#shipNoteField').val(),
                        'storageDate' : $('#stoDateField').val(),
                    },
                    success:function(response){
                        if(response.success){
                            $('.alert-info').css('display', 'block');
                            $('#spanSuccess').append(response.success);
                            <?php if(Request::segment(2) == 'edit') { ?>
                                changeStatus(response.status, response.statusColor);
                            <?php } else { ?>
                                $.each(afl_rows_selected, function(key, value){
                                    aTable.row($('#aflTable tbody tr[id="'+ value +'"]')).remove().draw();
                                })
                                $.each(non_afl_rows_selected, function(key, value){
                                    nTable.row($('#nonAflTable tbody tr[id="'+ value +'"]')).remove().draw();
                                })
                                $('input:not(#reqDateField), select').prop('disabled', false);
                                afl_rows_selected = [];
                                non_afl_rows_selected = [];
                            <?php } ?>
                        }else{
                            printMessageErrors(response.errors);
                            changeStatus();
                        }
                    },
                    error:function(response){
                        printMessageErrors(response.errors);
                        changeStatus();
                    }
                });
            <?php if(Request::segment(2) == 'entry') { ?>
            }else{
                alert('Tidak');
            }
            <?php } ?>
        })

        <?php if(Request::segment(2) == 'edit') { ?>
        $('#cancelBtn').on('click', function(e){
            var r = confirm("Are you sure to cancel the movement request?");
            if(r){
                clearMessageErrors();
                $('input, textarea, select').prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url : "{{url('/movement/cancel') }}/{{$movement->id}}",
                    success:function(response){
                        if(response.success){
                            $('.alert-info').css('display', 'block');
                            $('#spanSuccess').append(response.success);
                            changeStatus(response.status, response.statusColor);
                        }else{
                            alert(response.error);
                            changeStatus();
                        }
                    },
                });
            }else{
                alert('Jangan dibatalkan');
            }
        })
        <?php } ?>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    <?php if(Request::segment(2) == 'edit') { ?>
    function changeStatus(status = '{{$movement->status}}', color = null) {
        $('#statusLabel').empty();
        $('#statusLabel').append(status);
        $('#statusLabel').css('color', color);

        $('input, textarea, select').prop('disabled', false);
        switch(status) {
            case '<?php echo config('enums.status')[1] ?>':
                $('#reqDateField').prop('disabled', true);
                $('#stoDateField').prop('disabled', true);
                break;
            case '<?php echo config('enums.status')[2] ?>': 
                $('input[type="text"]:not(#stoDateField), textarea').prop('disabled', true);
                $('#stoDateField').prop('required', true);
                break;
            default:
                $('input, textarea').prop('disabled', true); 
                break;
        }
    }
    <?php } ?>

    function clearMessageErrors() {
        $('.text-danger').empty();
        $('.alert-danger').empty();
        $('.alert-danger').css('display','none');
        $('.alert-info').css('display','none');
        $('#spanSuccess').empty();
    }

    function printMessageErrors(msg){

        $('#reqDateErr').append(msg.requestDate);
        $('#newsNoErr').append(msg.newsNo);
        $('#shipDateErr').append(msg.shippingDate);
        $('#shipNoErr').append(msg.shippingNo);
        $('#shipNoteErr').append(msg.shippingNote);
        $('#stoDateErr').append(msg.storageDate);
    }

    $("#stoDateField").datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect:function(){
            $(this).datepicker("hide");
            $('#crewNoField').select();
        }
    });

    $("#shipDateField").datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect:function(){
            $(this).datepicker("hide");
            $('#crewNoField').select();
        }
    });
</script>
@endsection