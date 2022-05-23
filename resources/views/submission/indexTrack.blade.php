@extends('layouts.appTest')

@section('title')
Search
@endsection

@section('styles')
    <link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

                <!-- local files -->
<!--     <link href="{{ asset('public/css/jquery-ui.custom.v1.12.1.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.css') }}" rel="stylesheet"> -->
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

                <!-- local files -->
    <!-- <script src="{{ asset('public/css/datatable/dataTables.bootstrap.v1.10.19.min.js') }}"></script> -->
@endsection

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">Track Submission</div>

	                <div class="card-body">
                        <div class="form-table">
                            <table>
                                <tr>
                                    <td><label>GA Flight &ensp;</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="text" style="width: 100%; max-width: 70px;" id="fltnbrField" maxlength="4">
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>&ensp; &ensp;<label>Flight Date &ensp;</label></td>
                                    <td>
                                        <div class="form-group ">
                                            <input type="text"class="text" maxlength="10" id="fltdateField" placeholder="dd-mm-yyyy">
                                        </div>
                                        <span class="text-danger" id="dateValid"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Depart / Arrive</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="text" style="width: 100%; max-width: 70px;" id="depField" maxlength="3">
                                        </div>
                                    </td>
                                    <td><label>/</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="text" style="width: 100%; max-width: 70px;" id="arrField" maxlength="3">
                                        </div>
                                    </td>
                                    <td>&ensp; &ensp;<label>PIC</label></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="text" id="picField" maxlength="6">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <input type="button" value="Search">
                            <input type="button" value="Reset">
                            <br>
                            <div id="search_form">
                            </div>
                        </div>
                    </div>
	            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.text:first').focus();
            $('.text').on('keydown', function(e){
                if((e.keyCode == 13 || e.which == 13) && $('#fltdateField').datepicker("widget").is(":visible") == false){
                    var fltDate = $('#fltdateField').val();
                    var regexDate = /^(\d{2})-(\d{2})-(\d{4})$/;

                    if(fltDate.match(regexDate) || fltDate == ''){
                        $('#search_form').html('<div align="center">Loading submission list...</div>');
                        $('#dateValid').text('');
                        $.ajax({
                            type: 'get',
                            url : '{{URL::to('submission/read/assignedOnly')}}',
                            data : {
                                'depstn' : $('#depField').val(),
                                'arrstn' : $('#arrField').val(),
                                'fltnbr' : $('#fltnbrField').val(),
                                'pic' : $('#picField').val(),
                                'fltdate' : fltDate,
                            },
                            success:function(data){
                                $('#search_form').html(data);
                            }
                        });
                    }else{
                        $('#dateValid').text('Date format must be valid.');
                    }
                }else if(e.keyCode == 37 || e.which == 37){
                    e.preventDefault();
                    $('#fltdateField').datepicker("hide");
                    if ($(this).is('#fltdateField')) {
                        $('#fltnbrField').select();
                    }else if($(this).is('#picField')){
                        $('#arrField').select();
                    }else if($(this).is('#arrField')){
                        $('#depField').select();
                    }
                }else if(e.keyCode == 38 || e.which == 38){
                    e.preventDefault();
                    $('#fltdateField').datepicker("hide");
                    if($(this).is('#arrField') || $(this).is('#depField')){
                        $('#fltnbrField').select();
                    }else if($(this).is('#picField')){
                        $('#fltdateField').select();
                    }
                }else if(e.keyCode == 39 || e.which == 39){
                    e.preventDefault();
                    $('#fltdateField').datepicker("hide");
                    if ($(this).is('#fltnbrField')) {
                        $('#fltdateField').select();
                    }else if($(this).is('#depField')){
                        $('#arrField').select();
                    }else if($(this).is('#arrField')){
                        $('#picField').select();
                    }
                }else if(e.keyCode == 40 || e.which == 40){
                    e.preventDefault();
                    $('#fltdateField').datepicker("hide");
                    if($(this).is('#fltnbrField')){
                        $('#depField').select();
                    }else if($(this).is('#fltdateField')){
                        $('#picField').select();
                    }
                }else{
                    if($(this).is('#depField') || $(this).is('#arrField')){
                        if((e.keyCode >= 65 && e.keyCode <= 90) || (e.which >= 65 && e.which <= 90) || e.keyCode == 8 || e.which == 8){
                            $(this).on('input', function(){
                                $(this).val(function(_, val) {
                                    return val.toUpperCase();
                                });
                            });
                        }else{
                            e.preventDefault();
                        }
                    }else if($(this).is('#fltnbrField') || $(this).is('#picField')){
                        if((e.keyCode >= 48 && e.keyCode <= 57) || (e.which >= 48 && e.which <= 57)|| e.keyCode == 8 || e.which == 8){
                            return val;
                        }else{
                            e.preventDefault();
                        }
                    }
                }
            });

            $('#fltdateField').datepicker({
                dateFormat: 'dd-mm-yy',
                onSelect:function(){
                    $(this).datepicker("hide");
                }
            })
        })      
    </script>
@endsection
