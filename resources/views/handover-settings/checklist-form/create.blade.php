@extends('components.template-limitless.main')

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
                <h4 class="panel-title textUpperCase"><i class="icon-file-check2"></i> New Checklist Form</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('checklist-form.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
            	<form action="{{ route('checklist-form.store') }}" method="POST">
                    @csrf
                    <div class="row" style="margin-bottom: 10px;">
	                    <div class="col-md-8">
                            <input type="textbox" name="name" value="{{ old('name') ? old('name') : 'Checklist with Appointment' }}" placeholder="Form Title e.g Key Form" class="form-control remove-border" style="font-size: 17px;font-weight: bold;" autocomplete="off" required="" autofocus="">
	                    </div>
	                    <div class="col-md-12" style="margin-top: 8px;">
                            <textarea class="form-control remove-border" name="description" placeholder="checklist description" rows="5"></textarea>
	                    </div>
	                </div>

	                <hr width="80%">
	                <div class="row" style="margin-bottom: 10px;">
	                    <div class="col-md-12">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="meter_reading" value="1"> Include Meter Reading
								</label>
							</div>
						</div>

	                </div>

	                <hr width="80%">


	                <div id="group-field">
		                	
		                <div class="panel panel-content section-field" style="padding: 10px;">
		                	<div class="row">
		                		<div class="col-md-12">
		                			<h6 class="no-margin text-bold">Section 1</h6>
		                		</div>
		                		
		                		<div class="col-md-12" style="margin-bottom: 10px;">
	                            	<input type="textbox" name="section[]" value="{{ old('section')[0] ? old('section')[0] : '' }}" placeholder="Input section here e.g Entrance" class="form-control remove-border" autocomplete="off" required="">
		                		</div>
			                    <div class="col-md-12">

			                    	<table class="table table-bordered table-hover table-striped table-framed" id="section_1">
								       
								        <tbody>
								            
							                <tr>
							                    <td>
							                        <div class="row" style="margin-bottom:5px;">
							                            <div class="col-md-3">
							                                <p style="font-weight: bold;">Name:</p>
							                            </div>
							                            <div class="col-md-9">
							                                <input type="text" name="item[0][]" value="" class="form-control" placeholder="e.g Item 1" autocomplete="off" required>
							                            </div>
							                        </div>
							                        <div class="row">
							                            <div class="col-md-3">
							                                <p style="font-weight: bold;">Quantity:</p>
							                            </div>
							                            <div class="col-md-9">
							                                <input type="number" name="quantity[0][]" value="1" min="1" class="form-control">
							                            </div>
							                        </div>
							                    </td>
							                </tr>
							            
								        </tbody>
								        <tfoot>
								            <tr>
								                <td width="100%">
								                	<a class="btn btn-primary btn-xs" onclick="addItem(0)" style="width:100%;">Add Item</a>
								                </td>
								            </tr>
								        </tfoot>
								    </table>

			                    </div>
			                </div>
		                </div>
	                </div>

	                <div class="row col-md-12">
	                	<a class="btn btn-primary btn-xs" onclick="addSection()">Add Section</a>
	                </div>

	                

	                <div class="row">
	                	<div class="col-md-12 text-right">
                			<button type="submit" class="btn bg-primary-400 btn-labeled btn-labeled-right"><b><i class=" icon-circle-right2"></i></b> @lang('main.submit')</a>

	                	</div>
	                </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')


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
		                		'<a class="btn btn-black" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>'+
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
        var count = 2;
    	$( ".section-name" ).each(function( index ) {
            $(this).text('Section ' + (count++));
        });
    }

    function addSection() {

        var count = $('.section-field').length;

	    var content = '<div class="panel panel-content section-field" style="padding: 10px;" id="section-field-'+ count +'">'+
	        	'<div class="row">'+
	        		'<div class="col-md-12">'+
	        			'<h6 class="no-margin text-bold section-name">Section '+ (count + 1) +'</h6>'+
	        		'</div>'+
	        		
	        		'<div class="col-md-12">'+
	                	'<input type="textbox" name="section[]" value="" placeholder="Input section here e.g Entrance" class="form-control remove-border" autocomplete="off" required>'+
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
					                	'<a class="btn btn-danger btn-xs" onclick="removeSection('+ count +')" style="width:50%;">Remove Section</a>'+
					                	'<a class="btn btn-primary btn-xs" onclick="addItem('+ count +')" style="width:50%;">Add Item</a>'+
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
