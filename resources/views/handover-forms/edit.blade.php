@extends('layouts.template2')

@section('main')

<style type="text/css">
	.remove-border {
		border: 0px;border-bottom: 1px solid #000;background-color: #ebebeb !important;
		/*font-weight: bold;*/
	}
	.remove-border:focus {
		border: 0px;border-bottom: 1px solid #000;
		/*font-weight: bold;*/
	}
</style>
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-insert-template"></i> {{ isset($type) ? 'Clone' : 'Edit' }} Handover Form</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('handover-form.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
            	<form action="{{ route('handover-form.update', [$form->id]) }}" method="POST">
                    @csrf
                    @method('put')

                    <!-- DATA FOR DIFF NORMAL EDIT OR CLONE -->
                    <input type="hidden" name="action_type" value="{{ $type ?? 'normal' }}">
                    <!-- DATA FOR DIFF NORMAL EDIT OR CLONE -->


                    <div class="row" style="margin-bottom: 10px;">
	                    <div class="col-md-8">
                            <input type="textbox" name="name" value="{{ $form->name }}" placeholder="Form Title e.g Key Form" class="form-control remove-border" style="font-size: 17px;font-weight: bold;" autocomplete="off" required="">
	                    </div>
	                    <div class="col-md-12">
                            <textarea class="form-control remove-border" name="description" placeholder="checklist description">{{ $form->description }}</textarea>
	                    </div>
	                </div>

	                <hr width="80%">
	                <div class="row" style="margin-bottom: 10px;">
	                    <div class="col-md-12">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="meter_reading" value="1" {{ $form->meter_reading ? 'checked' : '' }}> Include Meter Reading
								</label>
							</div>
						</div>

	                </div>

	                <hr width="80%">


	                <div id="group-field">
		                	

		                @foreach($form->section as $keySection => $valSection)
		                	@php
		                		$section_index = $keySection + 1;
		                	@endphp	
			                <div class="panel panel-content section-field" style="padding: 10px;" id="section-field-{{ $keySection }}">
			                	<div class="row">
			                		<div class="col-md-8 col-xs-8">
			                			<h6 class="no-margin text-bold section-name">Section {{ $section_index }}</h6>
			                		</div>
			                		<div class="col-md-4 col-xs-4 text-right">
			                			@if(!$loop->first)
	                						<a class="btn btn-danger btn-xs" onclick="removeSection({!! $keySection !!})">Remove Section</a>
					                	@endif
			                		</div>
			                		
			                		<div class="col-md-12">
		                            	<input type="textbox" name="section[]" value="{{ $valSection->name }}" placeholder="Input section here e.g Entrance" class="form-control remove-border" autocomplete="off">
			                		</div>
				                    <div class="col-md-12">

				                    	<table class="table table-bordered table-hover table-striped table-framed" id="section_{{ $section_index }}">
									       
									        <tbody>
									            
		                						@foreach($valSection->item as $keyItem => $valItem)
								                <tr>
								                    <td>
								                        <div class="row" style="margin-bottom:5px;">
								                            <div class="col-md-3">
								                                <p style="font-weight: bold;">Name:</p>
								                            </div>
								                            <div class="col-md-9">
								                                <input type="text" name="item[{{ $keySection }}][]" value="{{ $valItem->name }}" class="form-control" placeholder="e.g Item 1" autocomplete="off" required>
								                            </div>
								                        </div>
								                        <div class="row">
								                            <div class="col-md-3">
								                                <p style="font-weight: bold;">Quantity:</p>
								                            </div>
								                            <div class="col-md-9">
								                                <input type="number" name="quantity[{{ $keySection }}][]" value="{{ $valItem->quantity }}" min="1" class="form-control">
								                            </div>
								                        </div>
								                        @if(!$loop->first)
										                <div class="row">
									                		<a class="btn btn-black  btn-xs" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>
									                	</div>
									                	@endif
								                    </td>
								                </tr>


								                @endforeach
								            
									        </tbody>
									        <tfoot>
									            <tr>
									                <td width="100%">
									                	<a class="btn btn-primary btn-xs" onclick="addItem({!! $keySection !!})" style="width:100%;">Add Item</a>
									                </td>
									            </tr>
									        </tfoot>
									    </table>

				                    </div>
				                </div>
			                </div>
		                @endforeach
	                </div>

	                <div class="row col-md-12">
	                	<a class="btn btn-primary btn-xs" onclick="addSection()">Add Section</a>
	                </div>

	                

	                <div class="row">
	                	<div class="col-md-12 text-right">
	                		<a href="{{ route('handover-form.edit', [$form->id]) }}" type="submit" class="btn bg-warning-400 btn-labeled"><b><i class="icon-cancel-circle2"></i></b>Reset</a>

                			<button type="submit" class="btn bg-primary-400 btn-labeled btn-labeled-right"><b><i class=" icon-circle-right2"></i></b> @lang('main.submit')

	                	</div>
	                </div>

                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
	//SUBMIT ITEM SCRIPT //
    function addItem(index){

        var newRow = '<tr>'+
            			'<td>'+
            				'<div class="row" style="margin-bottom:5px;">'+
            					'<div class="col-md-3">'+
            						'<p style="font-weight: bold;">Name:</p>'+
	                			'</div>'+
		                		'<div class="col-md-9">'+
		                			'<input type="text" name="item['+ index +'][]" value="" class="form-control" placeholder="e.g Item 1" autocomplete="off" >'+
		                		'</div>'+
		                	'</div>'+
		                	'<div class="row">'+
		                		'<div class="col-md-3">'+
		                			'<p style="font-weight: bold;">Quantity:</p>'+
		                		'</div>'+
		                		'<div class="col-md-9">'+
		                			'<input type="number" name="quantity['+ index +'][]" value="1" min="1" class="form-control">'+
		                		'</div>'+
		                	'</div>'+
		                	'<div class="row">'+
		                		'<a class="btn btn-black btn-xs" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>'+
		                	'</div>'+
		                '</td>'+
		            '</tr>';
        			$(newRow).appendTo("#section_"+ (index + 1) +" tbody")
    };    

    function removeButton(data){
        $(data).closest("tr").remove();
        displaySectionName();
    }	

    function removeSection(index) {
        $("#section-field-" + index).remove();
        displaySectionName();

    }

    function displaySectionName () {
        var count = 1;
    	$( ".section-name" ).each(function( index ) {
            $(this).text('Section ' + (count++));
        });
    }

    function addSection() {

        var count = $('.section-field').length;

	    var content = '<div class="panel panel-content section-field" style="padding: 10px;" id="section-field-'+ count +'">'+
	        	'<div class="row">'+
	        		'<div class="col-md-12">'+
	        			'<h6 class="no-margin text-bold section-name">Section '+ (count + 1) +'<a class="btn btn-danger btn-xs pull-right" onclick="removeSection('+ count +')">Remove Section</a></h6>'+
	        		'</div>'+
	        		
	        		'<div class="col-md-12">'+
	                	'<input type="textbox" name="section[]" value="" placeholder="Input section here e.g Entrance" class="form-control remove-border" autocomplete="off">'+
	        		'</div>'+
	                '<div class="col-md-12">'+
	                	'<table id="section_'+ (count + 1) +'" class="table table-bordered table-hover table-striped table-framed table-item">'+
					       
					        '<tbody>'+
					            
				                '<tr>'+
				                    '<td>'+
				                        '<div class="row" style="margin-bottom:5px;">'+
				                            '<div class="col-md-3">'+
				                                '<p style="font-weight: bold;">Name:</p>'+
				                            '</div>'+
				                            '<div class="col-md-9">'+
				                                '<input type="text" name="item['+ count +'][]" value="" class="form-control" placeholder="e.g Item 1" autocomplete="off" required>'+
				                            '</div>'+
				                        '</div>'+
				                        '<div class="row">'+
				                            '<div class="col-md-3">'+
				                                '<p style="font-weight: bold;">Quantity:</p>'+
				                            '</div>'+
				                            '<div class="col-md-9">'+
				                                '<input type="number" name="quantity['+ count +'][]" value="1" min="1" class="form-control">'+
				                            '</div>'+
				                        '</div>'+
				                    '</td>'+
				                '</tr>'+
				            
					        '</tbody>'+
					        '<tfoot>'+
					            '<tr>'+
					                '<td width="100%">'+
					                	// '<a class="btn btn-danger btn-xs" onclick="removeSection('+ count +')" style="width:50%;">Remove Section</a>'+
					                	'<a class="btn btn-primary btn-xs" onclick="addItem('+ count +')" style="width:100%;">Add Item</a>'+
					                '</td>'+
					            '</tr>'+
					        '</tfoot>'+
					    '</table>'+

	                '</div>'+
	            '</div>'+
	        '</div>';

	    $("#group-field").append(content);
    }

</script>
@endsection
