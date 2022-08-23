@extends('layouts.template2')

@section('main')
	<!-- <script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script> -->
	<script src="{{ asset('assets/plugins/ckeditor-full/ckeditor.js') }}"></script>
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-cogs"></i> Handover Settings: {{$handover_menu->display_name}}
                </h5>
            </div>

	        	<div class="panel-body">
	        		<div id="submit_field" class="transcation_field">
	        			<form action="{{ route('acceptance.updateTermsConditions') }}" method="POST">
	        				@csrf
		        			<table class="table table-bordered table-hover table-striped table-framed">
		        				<thead>
						            <tr>
						                <td class="" >{{$handover_menu->display_name}} Content</td>
						            </tr>
						        </thead>
						        <tbody>
						        	<tr>
						        		<td>
						        			<p style="font-weight:bold">Designation:</p>
						        			<input class="form-control" name="designation" value="{{$termsConditions->designation}}">
						        			<br>
						        			<p style="font-weight:bold">Terms & Conditions:</p>
						        			<textarea name="content" style="min-height:100px;" id="editor">
										        {{$termsConditions->termsConditions ?? ''}}
										    </textarea>	<br>
						        			<div class="row text-right">
						        				<a href="{{route('acceptance.index')}}" class="btn btn-danger">Cancel</a>
		                    					<button type="submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
						        			</div>	
						        		</td>
						        	</tr>
						        </tbody>
		        			</table>
						</form>
					</div>

				    <script type="text/javascript">
        
				        $(document).ready(function(){

				            CKEDITOR.replace( 'editor', {
				                toolbar: [
				                    { name: 'document', items: [ '-', 'Preview',] },
				                    { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
				                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', ] },
				                    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', ] },
				                    { name: 'styles', items: [ 'Styles', 'Format', 'FontSize' ] },
				                    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
				                    { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
				                    { name: 'insert', items: [ 'Link']},
				                ]
				            });
				        });

				    </script>
	        	</div>
        </div>
    </div>


    
@endsection