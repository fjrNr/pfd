
<label style="font-weight: bold">Submission List:</label>
<table class="table" id="submission_list" align="center" style="width:100%">
    <thead>
        <th class="text-center"><input id="select_all" value="1" type="checkbox"></th>
        <th class="text-center">ID</th>
        <th class="text-center">Form Number</th>
        <th class="text-center">Received Date</th>
        <th class="text-center">Document</th>
        <th class="text-center">Crew Number</th>
        <th class="text-center">Crew Name</th>
        <th class="text-center">Crew Rating</th>
    </thead>
    <tbody align="center">
        @foreach($submissions as $subm)
            <tr>
                <td></td>
                <td>{{$subm->id}}</td>
                <td>{{$subm->formNbr}}</td>
                <td>
                    @if(isset ($subm->receivedDate))
                        {{date('d-m-Y', strtotime($subm->receivedDate))}}                        
                    @else
                        {{"Unidentified"}}
                    @endif
                </td>
                <td>{{$subm->quantity}}</td>
                <td>
                    @if(isset ($subm->empNbr))
                        {{$subm->empNbr}}
                    @else
                        {{"Unidentified"}}
                    @endif
                </td>
                <td>
                    @if(isset ($subm->signed))
                        {{$subm->signed}}
                    @else
                        {{"-"}}
                    @endif
                </td>
                <td>
                    {{$subm->empRank}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<label id="countSelected" style="font-weight: bold"></label>
<input type="button" class="btn btn-success" value="&#x2713; Submit" style="float: right" id="submit">


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
        var table = $('#submission_list').DataTable({
            columnDefs: [{
                orderable: false,
                targets: 0,
                render: function (data, type, full, meta){
                    return '<input type="checkbox">';
                    },
                },
                {orderable: false, visible: false, searchable: false, targets: 1},
            ],
            order: [[3, 'desc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
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

        $('#submission_list tbody').on('click', 'input[type="checkbox"]', function(e){
            var $row = $(this).closest('tr');

            // Get row data
            var data = table.row($row).data();

            // Get row id
            var rowId = data[1];

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
            }else{
                $row.removeClass('selected');
            }

            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(table);

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
        $('#submission_list').on('click', 'tbody td, thead th:first-child', function(e){
            $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead #select_all', table.table().container()).on('click', function(e){
            if(this.checked){
                $('tbody input[type="checkbox"]:not(:checked)', table.table().container()).trigger('click');
            }else{
                $('tbody input[type="checkbox"]:checked', table.table().container()).trigger('click');
            }

            // Prevent click event from propagating to parent
            e.stopPropagation();
        });

        // Handle table draw event
        table.on('draw', function(){
            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(table);
        });

        $('#submit').on('click', function(e){
            e.preventDefault();
            if(rows_selected.length > 0){
                var idArray = JSON.stringify(rows_selected);

                $('#boxNoField').prop('disabled', false);
                $('#yearField').prop('disabled', false);
                $('#packageNoField').prop('disabled', false);
                
                var boxNo = $('#boxNoField').val();
                var year = $('#yearField').val();
                var packageNo = $('#packageNoField').val();

                $('#boxNoField').prop('disabled', true);
                $('#yearField').prop('disabled', true);
                $('#packageNoField').prop('disabled', true);

                $.ajax({
                    type:'post',
                    url : '{{URL::to('box/assign/')}}',
                    data : {
                        'boxNo' : boxNo,
                        'packageNo' : packageNo,
                        'idArray' : idArray,
                        'year' : year,
                    },
                    success: function(response){
                        if(response.error){
                            alert(response.error);
                        }else{
                            alert(response.success);
                            $.ajax({
                                type: 'get',
                                url : '{{route('box/countAssigned/')}}',
                                data: {
                                    'id' : response.boxId
                                },
                                success: function(data){
                                    $('#countAssign').empty();
                                    $('#countAssign').append(data);
                                },
                            });
                            table.rows('.selected').remove().draw(false);
                            $('#countSelected').empty();
                            rows_selected = [];
                        }
                    }
                });
            }else{
                alert('Please select minimal one of all submissions in submission list.');
            };
        });
    })
</script>