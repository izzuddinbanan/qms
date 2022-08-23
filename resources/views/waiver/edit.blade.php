@extends('layouts.template2')

@section('main')
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
	        			<form action="{{ route('waiver.store') }}" method="POST">
	        				@csrf
	        				<input type="hidden" name="waiver_id" value="{{ $waiver->id ?? '' }}">
		        			<table class="table table-bordered table-hover table-striped table-framed">
		        				<thead>
						            <tr>
						                <td class="" >{{$handover_menu->display_name ?? $handover_menu->original_name }}</td>
						            </tr>
						        </thead>
						        <tbody>
						        	<tr>
						        		<td>
						        			<textarea name="content" style="min-height:200px;" id="editor">
										        {{$waiver->description ?? ''}}
										    </textarea>	<br>

						        			<div class="row text-right">
						        				<a href="{{route('waiver.index')}}" class="btn btn-danger">Cancel</a>
		                    					<button type="submit" class="btn btn-primary">Update <i class="icon-arrow-right14 position-right"></i></button>
						        			</div>	
						        		</td>
						        	</tr>
						        </tbody>
		        			</table>
						</form>
					</div>
	        	</div>
        </div>
    </div>


    <!-- Theme JS files -->
	<script type="text/javascript" src="{{ url('assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('assets/js/pages/editor_summernote.js') }}"></script>
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
    
@endsection