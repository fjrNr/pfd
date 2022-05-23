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
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	            <div class="card">
	                <div class="card-header">Entry AFLs Box</div>

	                <div class="card-body">
                        <form id="box_form" <?php if (Request::segment(2) == 'edit') { ?> action="{{url('/box/update/afl') }}/{{$box->id}}"<?php }else{ ?> action="{{url('/box/create/afl') }}" <?php } ?>>
                            {{csrf_field()}}
                            <table id="static_field" width="100%">
                                <tr>
                                    <td><label>Package Number</label></td>
                                    <td><label>AFL/</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" style="width: 100%; max-width: 50px;" maxlength="2" required pattern="([0-9]+){2}" class="form-control" name="packY" id="field1"
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
                                            <input type="text" style="width: 100%; max-width: 70px;" maxlength="4" required pattern="([0-9]+){1,4}" class="form-control" name="packNo" id="field2"
                                            <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{substr($box->packNbr, 7)}}" disabled
                                            <?php } ?>
                                            >
                                        </div>
                                    </td>
                                    <td>&ensp;</td>
                                </tr>
                            </table>

                            <label style="font-weight: bold">Expired AFL List:</label>
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
                            <br>
                            <div class="tab-content">
                                <div class="tab-pane active" id="indexTabPane">
                                    <table id="indexTable" class="table" align="center" style="width:100%">
                                        <thead>
                                            <tr>         
                                                <th class="text-center"><input id="select_all" value="1" type="checkbox"></th>
                                                <th class="text-center">A/C Register</th>
                                                <th class="text-center">Periode</th>
                                                <th class="text-center">Start Number</th>
                                                <th class="text-center">End Number</th>
                                                <th class="text-center">Total Copy</th>
                                            </tr>
                                        </thead>
                                        <tbody align="center">
                                            @foreach($aflCopies as $lg)
                                                <tr id="{{$lg->id}}">
                                                    <td></td>
                                                    <td>{{$lg->acReg}}</td>
                                                    <td>{{$lg->month}} {{$lg->year}}</td>
                                                    <td>{{$lg->startNum}}</td>
                                                    <td>{{$lg->endNum}}</td>
                                                    <td>{{$lg->totalCopy}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="onHandTabPane">
                                    <table class="table" id="onHandTable" align="center" style="width:100%">
                                        <thead>
                                            <th class="text-center">A/C Register</th>
                                            <th class="text-center">Periode</th>
                                            <th class="text-center">Start Number</th>
                                            <th class="text-center">End Number</th>
                                            <th class="text-center">Total Copy</th>
                                            <th class="text-center">Action</th>
                                        </thead>
                                        <tbody align="center">
                                        <?php if (Request::segment(2) == 'edit') { ?>
                                            @foreach($aflCopiesExst as $lg)
                                                <tr id="{{$lg->id}}">
                                                    <td>{{$lg->acReg}}</td>
                                                    <td>{{$lg->month}} {{$lg->year}}</td>
                                                    <td>{{$lg->startNum}}</td>
                                                    <td>{{$lg->endNum}}</td>
                                                    <td>{{$lg->totalCopy}}</td>
                                                    <td align="center"><input type="button" value="X" class="btn btn-danger btn-remove"></td>
                                                </tr>
                                            @endforeach
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <label id="count_selected" style="font-weight: bold"></label>
                            <input type="submit" id="create" value="Create" style="float: right" class="btn btn-success">
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
            var rows_selected = [];
            var reg_code_array = [];
            var count_selected = 0;

            @if (Request::segment(2) == 'edit')
                @foreach ($aflCopiesExst as $lg)
                    rows_selected.push("{{$lg->id}}");
                    reg_code_array.push("{{$lg->acReg}}".substring(4));
                    count_selected+={{$lg->totalCopy}};
                @endforeach
                console.log("reg code array= " + reg_code_array);
            @endif

            var iTable = $('#indexTable').DataTable({
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: 0,
                    render: function (data, type, full, meta){
                        return '<input type="checkbox">';
                        },
                    },
                ],
                order: [[2, 'desc']],
                orderCellsTop: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                rowCallback: function(row, data, dataIndex){
                    // If row form number is in the list of selected row form numbers
                    if($.inArray(data.id, rows_selected) !== -1){
                       $(row).find('input[type="checkbox"]').prop('checked', true);
                       $(row).addClass('selected');
                    }
                },
            });

            var oTable = $('#onHandTable').DataTable();


            $('#indexTable tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');

                // Get row id
                var rowId = $row.attr('id');

                // Set aircraft registration value
                var acReg = $row.find('td:eq(1)').text().substring(4);

                // Determine whether row id is in the list of selected row form numbers 
                var index = $.inArray(rowId, rows_selected);
                var index2 = $.inArray(acReg, reg_code_array);

                // If checkbox is checked and row id is not in list of selected row form numbers
                if(this.checked && index === -1){
                    rows_selected.push(rowId);
                    reg_code_array.push(acReg);
                }else if(!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                    reg_code_array.splice(index2, 1);
                }

                if(this.checked){
                    $row.addClass('selected');
                    oTable.row.add([
                        $row.find('td:eq(1)').text(),
                        $row.find('td:eq(2)').text(),
                        $row.find('td:eq(3)').text(),
                        $row.find('td:eq(4)').text(),
                        $row.find('td:eq(5)').text(),
                        '<td align="center"><input type="button" value="X" class="btn btn-danger btn-remove"></td>\n'
                    ]).node().id = rowId; 
                    oTable.draw(false);
                    count_selected+=parseInt($row.find('td:eq(5)').text());
                }else{
                    $row.removeClass('selected');
                    oTable.row($('#onHandTable tbody tr[id="'+ rowId +'"]')).remove().draw();
                    count_selected-=parseInt($row.find('td:eq(5)').text());
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(iTable);

                // Prevent click event from propagating to parent
                e.stopPropagation();

                console.log("rows selected: " + rows_selected);
                console.log("ac reg selected: " + reg_code_array);
                $('#count_selected').empty();
                if(count_selected > 1){
                    $('#count_selected').append(count_selected + " AFL copies selected.");
                }else if(count_selected == 1){
                    $('#count_selected').append(count_selected + " AFL copy selected.");
                }
            });

            // Handle click on table cells with checkboxes
            $('#indexTable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            // Handle click on "Select all" control
            $('thead #select_all', iTable.table().container()).on('click', function(e){
                if(this.checked){
                    $('tbody input[type="checkbox"]:not(:checked)', iTable.table().container()).trigger('click');
                }else{
                    $('tbody input[type="checkbox"]:checked', iTable.table().container()).trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle table draw event
            iTable.on('draw', function(){
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(iTable);
            });

            $('input[type="text"]').on('keydown', function(e){
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
                var acReg = $row.find('td:eq(0)').text().substring(4);

                count_selected-=parseInt($row.find('td:eq(4)').text());
                oTable.row($row).remove().draw();
                $('#indexTable tbody tr[id="'+ rowId +'"] td input').prop('checked', false);
                $('#indexTable tbody tr[id="'+ rowId +'"]').removeClass('selected');
                rows_selected.splice($.inArray(rowId, rows_selected), 1);
                reg_code_array.splice($.inArray(acReg, reg_code_array), 1);
                updateDataTableSelectAllCtrl(iTable);

                console.log("rows selected: " + rows_selected);
                console.log("reg_code_array: " + reg_code_array);
                $('#count_selected').empty();
                if(count_selected > 1){
                    $('#count_selected').append(count_selected + " AFL copies selected.");
                }else if(count_selected == 1){
                    $('#count_selected').append(count_selected + " AFL copy selected.");
                }
            });

            $('#box_form').on('submit', function(e){
                e.preventDefault();
                disableInput(true);

                var reg_code_array_copy = Array.from(reg_code_array);
                jQuery.unique(reg_code_array_copy);
                reg_code_array_copy.sort();

                if(rows_selected.length > 0){

                    $.ajax({
                        type: 'POST',
                        url : $(this).attr('action'),
                        data : {
                            'packYear' : $('#field1').val(),
                            'packNo' : $('#field2').val(),
                            'aflCopyIdArray' : JSON.stringify(rows_selected),
                            'notes' : reg_code_array_copy.toString().replace(/,(?=[^/s])/g, ", "),
                        },
                        success:function(response){
                            if(response.success){
                                alert(response.success);
                                console.log(response.success);
                                console.log(response.success2);

                                @if (Request::segment(2) == 'entry')
                                    $('#field1').val('{{date('y')}}');
                                    $('#field2').val('');
                                    $.each(rows_selected, function(key, value){
                                        iTable.row($('#indexTable tbody tr[id="'+ value +'"]')).remove().draw();
                                    })

                                    oTable.rows().remove().draw();
                                    count_selected = 0;
                                    rows_selected = [];
                                    updateDataTableSelectAllCtrl(iTable);
                                    $('#count_selected').empty();
                                @endif
                            
                            }else{
                                alert(response.error);
                            }
                            disableInput(false);
                        },
                        error: function(response){
                            alert(response.error);
                            disableInput(false);
                        }
                    });
                }else{
                    alert('Please select an AFL collection.');
                    disableInput(false);
                }
            })

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name^="csrf-token"]').attr('content')
                }
            });
        });

        function disableInput(bol){
            if(bol){
                $('input').prop('disabled', true);
            }else{
                $('input').prop('disabled', false);
            }
            $('#formNoField').focus();
        }
    </script>
@endsection	