
@extends('layouts.appTest')

@section('title')
    Insert Submission
@endsection

@section('styles')
	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">

    <style>
        .baris td:focus-within{
            background-color: #98CBE8;
        }
    </style>
@endsection

@section('scripts')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><?php if (Request::segment(2) == 'edit') { ?>Edit<?php }else{ ?>Entry<?php } ?> Submission</div>

                <div class="card-body">

                    <div class="alert alert-info" style="display:none">
                        <span id="spanSuccess"></span>
                        <button type="button" class="close" onclick="$('.alert-info').css('display', 'none'); $('#formNoField').focus()"><span>&times;</span></button>
                    </div>

                	<form id="submission_form" <?php if (Request::segment(2) == 'edit') { ?> action="{{url('/submission/update') }}/{{$submission->id}}"<?php }else{ ?> action="{{url('/submission/create') }}" <?php } ?>>
                        {{csrf_field()}}
                        <table id="static_field" width="100%">
                            <tr>
                                <td><label>Form Number</label></td>
                                <td>
                                    <div class="form-group" >
                                        <input type="text" name="formNbr" id="formNoField" class="form-control text" maxlength="5" style="width: 30%" tabindex="1" required pattern="([0-9]+){4,7}" <?php if (Request::segment(2) == 'edit') { ?>
                                                    value="{{$submission->formNbr}}"
                                            <?php } ?>
                                        autofocus>
                                        <span class="text-danger" id="formNbrErr"></span>
                                    </div>
                                </td>
                                <td>&ensp; &ensp;<label>Cover</label></td>
                                <td>
                                    <div class="form-group ">
                                        <input type="text" name="qtyDoc" id="qtyDocField" class="form-control text" maxlength="2" style="width: 20%" tabindex="4" required pattern="[0-9]+" 
                                        <?php if (Request::segment(2) == 'edit') { ?>
                                                value="{{$submission->quantity}}"
                                        <?php } ?>
                                        >
                                        <span class="text-danger" id="qtyDocErr"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Received Date</label></td>
                                <td><div class="form-group">
                                        <input type="text" name="receivedDate" id="receiveDateField" class="form-control text" style="width: 60%" tabindex="2"  placeholder="dd-mm-yyyy" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}"
                                        <?php if (Request::segment(2) == 'edit') { ?>
                                            value="{{date('d-m-Y',strtotime($submission->receivedDate))}}"
                                        <?php }else{ ?>
                                            value="{{date('d-m-Y')}}"
                                        <?php } ?>
                                        >
                                        <span class="text-danger" id="receivedDateErr"></span>
                                    </div>
                                </td>
                                <td>&ensp; &ensp;<label>Remark</label></td>
                                <td rowspan="4">
                                    <div class="form-group">
                                        <textarea type="text" name="remark" id="remarkField" class="form-control text" style="height: 200px; width: 100%; resize: none" tabindex="5"><?php if (Request::segment(2) == 'edit') { ?>{{$submission->remark}}<?php } ?></textarea>
                                        <span class="text-danger" id="remarkErr"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Crew Number</label></td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" name="empNbr" id="crewNoField" class="form-control text" placeholder="Search Crew Number" style="width: 60%" maxlength="6" tabindex="3" pattern="([0-9]+){6}" <?php if (Request::segment(2) == 'edit') { ?>value="{{$submission->empNbr}}"<?php } ?>>
                                        <span class="text-danger" id="empNbrErr"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Crew Name</label></td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" name="crewName" id="crewNameField" class="form-control" disabled <?php if (Request::segment(2) == 'edit') { ?>value="{{$submission->signed}}"<?php } ?>>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Rank</label></td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" name="crewName" id="rankField" class="form-control" style="width: 30%" disabled <?php if (Request::segment(2) == 'edit') { ?>value="{{$submission->empRank}}"<?php } ?>>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <br>
                        <label>AFL List:</label>
                        <table class="table table-bordered" id="dynamic_field" align="center" style="display: block; overflow-x: auto;">
                            <thead>
                            <th>AFL Number</th>
                            <th>Flight Plan</th>
                            <th>Dispatch Release</th>
                            <th>Weather Forecast</th>
                            <th>NOTAM</th>
                            <th>To/Ldg Data Card</th>
                            <th>Load Sheet</th>
                            <th>Fuel Receipt</th>
                            <th>Pax Manifest</th>
                            <th>NOTOC</th>
                            <th colspan="2">Action</th>
                            </thead>
                            <tbody>
                            <?php if (Request::segment(2) == 'edit') { ?>
                            @foreach($logs as $lg)
                            <tr id="row{{$loop->iteration}}" class="baris">
                                <td><input type="text" name="aflNbr[{{$loop->iteration}}]" id="aflNoField{{$loop->iteration}}" class="text form-control" style="width:95px" maxlength="9" required pattern="[0-9]{2}[A-Za-z]{2}[0-9]{4}[A-Z]?" value="{{$lg->aflNbr}}" ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbFlightPlan[{{$loop->iteration}}]" value="1"
                                    <?php if($lg->flightPlan == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbDispatch[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->dispatchRelease == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbWeather[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->weatherForecast == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbNotam[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->notam == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbLdgData[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->toLdgDataCard == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbLoadSheet[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->loadSheet == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbFuel[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->fuelReceipt == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbPax[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->paxManifest == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="checkbox" class="checkbox" name="cbNotoc[{{$loop->iteration}}]"  value="1"
                                    <?php if($lg->notoc == 1) {?>
                                        checked
                                    <?php } ?>
                                ></td>
                                <td align="center"><input type="button" name="remove" id="{{$loop->iteration}}" value="X" class="btn btn-danger btn-remove"></td>
                            </tr>
                            @endforeach 
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="alert alert-danger" id="aflNbrErr" style="display: none;">         
                        </div>
                        <br>
                        <td>
                            <input type="button" name="add" id="btnAdd" class="btn btn-primary" value="Add More">
                        </td>
                        <td>
                            <input type="submit" value="Submit" name="submit" id="submit" class="btn btn-success" style="float:right;">
                        </td>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>	

<script type="text/javascript">
    $(document).ready(function(){
        <?php if (Request::segment(2) == 'edit') { ?>
            var i = {{count($logs)}};
        <?php }else{ ?>
            var i = 0;
        <?php } ?>

        $(document).on('keydown', 'input, textarea', function(e) {
            if (e.keyCode == 13 || e.which == 13) {
                if ($(this).is('.text') || $(this).is('.checkbox')) {
                    e.preventDefault();
                    if ($(this).is('.checkbox')) {
                        this.checked = !this.checked;
                        var cp = $(this).closest('td').next().find(".checkbox").focus();
                        if (cp.length == 0) {
                            $('#btnAdd').focus();
                        }
                    }
                }
            } else if (e.keyCode == 37 || e.which == 37) {
                e.preventDefault();
                $('#receiveDateField').datepicker("hide");
                if ($(this).is('#qtyDocField')) {
                    $('#formNoField').select();
                } else if ($(this).is('#remarkField')) {
                    $('#receiveDateField').select();
                } else if ($(this).is("input[name^='cbFlightPlan']")){
                    $(this).closest('td').prev().find('input').select();                    
                } else if($(this).is('.checkbox') || $(this).is('.btn-remove')){
                    $(this).closest('td').prev().find('.checkbox').focus();
                } else if ($(this).is('#btnAdd')) {
                    if ($('.btn-remove').length > 0) {
                        $('.btn-remove').last().focus();
                    }
                } else if($(this).is('#submit')){
                    $('#btnAdd').focus();
                }
            } else if (e.keyCode == 38 || e.which == 38) {
                e.preventDefault();
                $('#receiveDateField').datepicker("hide");
                if ($(this).parent().parent().is(".baris:first")) {
                    window.scrollTo(0,0);
                    $('#remarkField').select();
                } else if ($(this).is("input[name^='aflNbr']") || $(this).is('.checkbox') || $(this).is('.btn-remove')){
                    window.scrollBy(0,-63);
                    if($(this).is("input[name^='aflNbr']")){
                        $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find("input[name^='aflNbr']").select();    
                    }else{
                        $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('.checkbox, .btn-remove').focus();
                    }
                } else if ($(this).is('#btnAdd') || $(this).is('#submit')) {
                    if ($('.btn-remove').length > 0) {
                        if($(this).is('#btnAdd')) $('.text:last').select();
                        else $('.btn-remove').last().focus(); 
                    }else{
                        window.scrollTo(0,0);
                        $('#remarkField').select();
                    }
                } else{
                    $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('.text').select();
                }
            } else if (e.keyCode == 39 || e.which == 39) {
                e.preventDefault();
                $('#receiveDateField').datepicker("hide");
                if ($(this).is('#formNoField')) {
                    $('#qtyDocField').select();
                } else if ($(this).is('#receiveDateField') || $(this).is('#crewNoField')) {
                    $('#remarkField').select();
                } else if ($(this).is("input[name^='aflNbr']")){
                    $(this).closest('td').next().find('.checkbox').focus();
                } else if ($(this).is("input[name^='cbNotoc']")){
                    $(this).closest('td').next().find('input').focus();
                } else if ($(this).is('.checkbox')){
                    $(this).closest('td').next().find('.checkbox').focus();
                } else if ($(this).is('.btn-remove')) {
                    if($('#btnAdd').prop('disabled')) $('#submit').focus();
                    else $('#btnAdd').focus();
                } else if($(this).is('#btnAdd')){
                    $('#submit').focus();
                }
            } else if (e.keyCode == 40|| e.which == 40) {
                e.preventDefault();
                $('#receiveDateField').datepicker("hide");
                if ($(this).is('#remarkField')) {
                    if ($('.btn-remove').length > 0) {
                        $('input[id^=aflNoField]:first').select();
                    }else{
                        $('#btnAdd').focus();
                    }
                } else if ($(this).parent().parent().is(".baris:last")) {
                    if ( ($(this).is("input[name^='aflNbr']") || $(this).is("input[name^='cbFlightPlan']") || $(this).is("input[name^='cbDispatch']") || $(this).is("input[name^='cbWeather']") || $(this).is("input[name^='cbNotam']")) 
                        &&
                        $('tr[id^=row]').length < 15){
                        $('#btnAdd').focus();
                    } else {
                        $('#submit').focus();
                    }
                } else if ($(this).is("input[name^='aflNbr']") || $(this).is('.checkbox') || $(this).is('.btn-remove')){
                    window.scrollBy(0,63);
                    if($(this).is("input[name^='aflNbr']")){
                        $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find("input[name^='aflNbr']").select();    
                    }else{
                        $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('.checkbox, .btn-remove').focus();
                    }
                } else{
                    $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('.text').select();
                }
            }else if(e.keyCode == 8 || e.which == 8){
                if($(this).is(".checkbox") || $(this).is(".btn-remove")){
                    e.preventDefault();
                    $(this).closest('tr').find('.text').select();
                }
            }else{
                if($(this).is('#formNoField') || $(this).is('#qtyDocField') || $(this).is('#crewNoField')){
                    if(!(e.keyCode >= 48 && e.keyCode <= 57) || !(e.which >= 48 && e.which <= 57)){
                        e.preventDefault();
                    }
                }else if($(this).is('input[id^=aflNoField]')){
                    $(this).on('input', function(){
                        $(this).val(function(_, val) {
                            return val.toUpperCase();
                        });
                    });
                }
            }
        });

        //for action access
        $("#btnAdd").click(function () {
            i++;
            $('#dynamic_field').append('<tr id="row'+i+'" class="baris">\n' +
                '<td><input type="text" name="aflNbr['+i+']" id="aflNoField'+i+'" class="text form-control" style="width:95px" maxlength="9" required pattern="[0-9]{2}[A-Za-z]{2}[0-9]{4}[A-Z]?"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbFlightPlan['+i+']" value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbDispatch['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbWeather['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbNotam['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbLdgData['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbLoadSheet['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbFuel['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbPax['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="checkbox" class="checkbox" name="cbNotoc['+i+']"  value="1"></td>\n' +
                '<td align="center"><input type="button" name="remove" id="'+i+'" value="X" class="btn btn-danger btn-remove"></td>\n' +
                '</tr>'
            );
            $('#aflNoField' +i+ '').focus();
            if($('tr[id^=row]').length == 15){
                $('#btnAdd').prop('disabled', true);
            }
        });

        $(document).on('click', '.btn-remove', function(){
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            $('#btnAdd').prop('disabled', false);
            $('#btnAdd').focus();
        });


        $('#submission_form').on('submit', function(e){
            e.preventDefault();
            clearMessageErrors();
            disableInput(true);

            var aflNbrArray = [];
            var cbFlightPlanArray = [];
            var cbDispatchArray = [];
            var cbWeatherArray = [];
            var cbNotamArray = [];
            var cbLdgDataArray = [];
            var cbLoadSheetArray = [];
            var cbFuelArray = [];
            var cbPaxArray = [];
            var cbNotocArray = [];

            $("input[name^='aflNbr']").each(function(){
                aflNbrArray.push($(this).val());
                cbFlightPlanArray.push($(this).closest('tr').find('td:eq(1)').find('.checkbox').is(':checked'));
                cbDispatchArray.push($(this).closest('tr').find('td:eq(2)').find('.checkbox').is(':checked'));
                cbWeatherArray.push($(this).closest('tr').find('td:eq(3)').find('.checkbox').is(':checked'));
                cbNotamArray.push($(this).closest('tr').find('td:eq(4)').find('.checkbox').is(':checked'));
                cbLdgDataArray.push($(this).closest('tr').find('td:eq(5)').find('.checkbox').is(':checked'));
                cbLoadSheetArray.push($(this).closest('tr').find('td:eq(6)').find('.checkbox').is(':checked'));
                cbFuelArray.push($(this).closest('tr').find('td:eq(7)').find('.checkbox').is(':checked'));
                cbPaxArray.push($(this).closest('tr').find('td:eq(8)').find('.checkbox').is(':checked'));
                cbNotocArray.push($(this).closest('tr').find('td:eq(9)').find('.checkbox').is(':checked'));
            });

            $.ajax({
                type: 'POST',
                url : $(this).attr('action'),
                data : {
                    'empNbr' : $('#crewNoField').val(), 
                    'receivedDate' : $('#receiveDateField').val(),
                    'formNbr' : $('#formNoField').val(),
                    'qtyDoc' : $('#qtyDocField').val(),
                    'remark' : $('#remarkField').val(),
                    'aflNbr' : aflNbrArray,
                    'cbFlightPlan' : cbFlightPlanArray,
                    'cbDispatch' : cbDispatchArray,
                    'cbWeather' : cbWeatherArray,
                    'cbNotam' : cbNotamArray,
                    'cbLdgData' : cbLdgDataArray,
                    'cbLoadSheet' : cbLoadSheetArray,
                    'cbFuel' : cbFuelArray,
                    'cbPax' : cbPaxArray,
                    'cbNotoc' : cbNotocArray,
                },
                success: function(response){
                    if(response.success){
                        <?php if (Request::segment(2) == 'entry') { ?>
                            $("input[type='text']").val('');
                            $('#receiveDateField').val('{{date('d-m-Y')}}');
                            $('textarea').val('');
                            $('#dynamic_field tbody').empty();
                        <?php } ?>
                            
                        $('.alert-info').css('display', 'block');
                        $('#spanSuccess').append(response.success);
                    }else{
                        printMessageErrors(response.errors, response.errors2);
                    }
                    disableInput(false);
                },
                error: function(response){
                    printMessageErrors(response.errors, response.errors2);
                    disableInput(false);
                }
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function clearMessageErrors() {
        $('.text-danger').empty();
        $('.alert-danger').empty();
        $('.alert-danger').css('display','none');
        $('.alert-info').css('display','none');
        $('#spanSuccess').empty(); 
    }

    function printMessageErrors(msg, msg2){
        if(msg2.length > 0) {
            $('.alert-danger').css('display', 'block');
            $.each(msg2, function(key, value) {
                $('.alert-danger').append('<li>'+ value +'</li>');
            });
        }
        $('#empNbrErr').append(msg.empNbr);
        $('#formNbrErr').append(msg.formNbr);
        $('#receiveDateErr').append(msg.receivedDate);
        $('#qtyDocErr').append(msg.qtyDoc);
    }

    function disableInput(bol){
        if(bol){
            $('input, textarea').prop('disabled', true);
        }else{
            $('input:not(#crewNameField, #rankField)').prop('disabled', false);
            $('textarea').prop('disabled', false);
        }
        $('#formNoField').focus();
    }


    $("#receiveDateField").datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect:function(){
            $(this).datepicker("hide");
            $('#crewNoField').select();
        }
    });

    $("#crewNoField").autocomplete({
        source: "{{URL::to('crew/read/autocomplete')}}",
        select:function(key, value){
            $('#crewNameField').val(value.item.name)
            $('#rankField').val(value.item.rank)
            $('#qtyDocField').select();
        }
    });
</script>
@endsection