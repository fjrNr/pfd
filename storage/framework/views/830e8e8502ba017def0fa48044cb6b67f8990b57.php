<?php $__env->startSection('title'); ?>
Index Box
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
	<link href="http://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-12">
	            <div class="card">
	                <div class="card-header">
	                	View Submissions Boxes
	                	<a href="<?php echo e(url('/box/entry')); ?>" style="font-weight: bold; float: right;" class="btn btn-success"> &#xff0b; Box</a>
	                </div>

	                <div class="card-body">
	                	Class of date: <input type="text" id="dateField" placeholder="dd-mm-yyyy">
	                	<input type="button" value="Reset">
	                	<br>
	                	<span class="text-danger" id="dateValid"></span>
	                	<br>
	                	<div id="list_form"></div>
	                </div>
	            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    	$(document).ready(function(){
        	window.onload = function() {
                $('#dateField').val('<?php echo e(date('d-m-Y')); ?>');
                loadBoxList();
            }

            function loadBoxList(){
            	$('#list_form').html('<div align="center">Loading assignment box list...</div>');
            	$.ajax({
                    type: 'get',
                    url : '<?php echo e(URL::to('box/getData')); ?>',
                    data : {
                        'packDate' : $('#dateField').val(),
                    },
                    success: function(data){
                        $('#list_form').html(data);
                    },
                });
            };

        	$('#dateField').datepicker({
            	dateFormat: 'dd-mm-yy',
		        onSelect:function(){
		            $(this).datepicker("hide");
		        }
            });

            $('#dateField').on('keydown',function(e){
            	var regexDate = /^(\d{2})-(\d{2})-(\d{4})$/;
            	if( (e.which == 13 || e.keyCode == 13 ) && $(this).datepicker("widget").is(":visible") == false) {
            		if($(this).val().match(regexDate)) {
            			$('#dateValid').text('');
            			loadBoxList();
            		} else{
            			$('#dateValid').text("Date format must be valid.");
            		}
            	}
            });

            $("input[value='Reset']").on('click', function(e){
            	$('#dateField').val('');
            });
    	});
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appTest', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>