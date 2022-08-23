@extends('layouts.template2')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-users"></i> @lang('main.survey')
                </h5>
            </div>

            <form action="{{ route('survey.store') }}" method="POST" id="myForm">
	        	<div class="panel-body">
	        		<div id="submit_field" class="transcation_field">
				        <div class="col-md-12">
				            <label>List of Survey:</label>
				        </div>
							@csrf
				        	@include('survey.components.survey_submit_field')
					</div>
	        	</div>

	        	<div class="panel-footer">
	                <div class="row col-md-12 text-right">
	                	<a href="{{route('survey.index')}}" class="btn btn-danger">Cancel</a>
	                    <button type="submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
	                </div>
	            </div>
            </form>
        </div>
    </div>

    <style>
    	td:hover{
    		cursor:move;
    	}	
    </style>
    
    <script>
    	//draggable table
    	var fixHelperModified = function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index) {
				$(this).width($originals.eq(index).width())
			});
			return $helper;
		},
		updateIndex = function(e, ui) {
			$('td.index', ui.item.parent()).each(function (i) {
				$(this).html(i+1);
			});
		};

		$("#myTable tbody").sortable({
			helper: fixHelperModified
			//stop: updateIndex
		}).disableSelection();
	
		$("tbody").sortable({
			distance: 5,
			delay: 100,
			opacity: 0.6,
			cursor: 'move',
			update: function() {}
		});

		//SUBMIT ITEM SCRIPT //
	    $("#add-item").click(function(){

	        var count = 2;
	        var itemNo = $('#myTable tbody tr').length + 1;
	        var newRow = '<tr>'+
                			'<td>'+
                				'<div class="row" style="margin-bottom:5px;">'+
                					'<div class="col-md-3">'+
                						'<p style="font-weight: bold;">Title:</p>'+
		                			'</div>'+
			                		'<div class="col-md-9">'+
			                			'<input type="text" name="question[]" value="" class="form-control" placeholder="e.g Please rate our service." autocomplete="off" required>'+
			                		'</div>'+
			                	'</div>'+
			                	'<div class="row">'+
			                		'<div class="col-md-3">'+
			                			'<p style="font-weight: bold;">Type:</p>'+
			                		'</div>'+
			                		'<div class="col-md-9">'+
			                			'<select class="select-search select_append" name="type_survey[]" data-placeholder="Select Type">'+
			                                '<option value="">Please Select</option>'+
			                                '<option value="rate" selected="">Rate</option>'+
			                                '<option value="comment">Comment</option>'+
			                            '</select>'+
			                		'</div>'+
			                	'</div>'+
			                	'<div class="row">'+
			                		'<a class="btn btn-black" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>'+
			                	'</div>'+
			                '</td>'+
			            '</tr>';
	        $("#myTable tbody").append(newRow);

	        $( ".itemNo" ).each(function( index ) {
	            $(this).text(count++);
	        });

	        $(".select_append").select2();

	    });    

	    function removeButton(data){
	        $(data).closest("tr").remove();
	        var count = 2;

	        $( ".itemNo" ).each(function( index ) {
	            $(this).text(count++);
	        });
	    }	


    </script>
    
@endsection