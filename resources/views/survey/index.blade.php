@extends('layouts.template2')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-cogs"></i> Handover Settings: {{$handover_menu->display_name}}
                </h5>
            </div>

	        	<div class="panel-body">
	        		<div id="submit_field" class="transcation_field">
	        			<form action="{{ route('handover.editHandover') }}" method="POST">
	        				@csrf
		        			<table class="table table-bordered table-hover table-striped table-framed">
		        				<thead>
						            <tr>
						                <td class="" >{{$handover_menu->display_name}} Settings</td>
						            </tr>
						        </thead>
						        <tbody>
						        	<tr>
						        		<td>
						        			<div class="row">
						        				<div class="col-md-4">
						        					Display Name:
						        				</div>
						        				<div class="col-md-8">
						        					<input class="form-control" name="display_name" value="{{$handover_menu->display_name}}" placeholder="e.g Survey" required>
						        				</div>
						        			</div>
						        			<div class="row">
						        				<div class="col-md-4">
						        					Field Mandatory:
						        				</div>
						        				<div class="col-md-8">
						        					<label class="switch">
							                            <input type="checkbox" id="survey" name="survey" onclick="survey()" checked>
							                            <span class="slider round"></span>
							                        </label>
						        				</div>
						        			</div>	<br>
						        			<div class="row text-right">
						        				<a href="{{route('handover.index')}}" class="btn btn-danger">Cancel</a>
		                    					<button type="submit" name="survey_submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
						        			</div>	
						        		</td>
						        	</tr>
						        </tbody>
		        			</table>
	        			</form>
	        			<br>
						
						<table id="myTable" class="table table-bordered table-hover table-striped table-framed">
					        <thead>
					            <tr>
					                <td>Version</td>
					                <td>Status</td>
					                <td>Created At</td>
					                <td>Action</td>
					            </tr>
					        </thead>
					        <tbody>
					            @forelse ($survey_version as $sv)
					                <tr>
					                    <td>
					                    	{{$sv->version ?? "N/A"}}
					                    </td>
					                    <td>
					                    	@if($sv->status == "Publish")
					                    		<p style="color:green; font-weight: bold;">{{$sv->status}}</p>
					                    	@elseif($sv->status == "Expired")
					                    		<p style="color:red; font-weight: bold;">{{$sv->status}}</p>
					                    	@elseif($sv->status == "Draft")
					                    		<p style="color:blue; font-weight: bold;">{{$sv->status}}</p>
					                    	@endif
					                    </td>
					                    <td>
					                    	{{$sv->created_at}}
					                    </td>
					                    <td>
					                    	<a href="{{route('survey.show', [$sv->id])}}" class="btn btn-primary">View</a>
					                    	@if($sv->status == "Draft")
					                    		<a href="{{route('survey.edit', [$sv->id])}}" class="btn btn-primary">Edit</a>
					                    		<a href="{{route('survey.publish', [$sv->id])}}" class="btn btn-primary">Publish</a>
					                    	@endif
					                    </td>
					                </tr>
					            @empty
					                <tr>
					                    <td style="text-align:center">
					                        No survey question available.
					                    </td>
					                </tr>
					            @endforelse
					        </tbody>
					        <tfoot>
					        	<tr>
					        		<div class="row col-md-12 text-right">
					        			<a href="{{route('survey.create')}}" class="btn btn-primary">Create New Survey Form <i class="icon-arrow-right14 position-right"></i></a>
					        		</div>
					        	</tr>
					        </tfoot>
					    </table>	

					</div>
	        	</div>
        </div>
    </div>

    <style>
    	.row{
			margin-bottom:10px;
		}
    	.switch {
          position: relative;
          display: inline-block;
          width: 60px;
          height: 34px;
        }

        .switch input { 
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }

        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
    	*{
		    margin: 0;
		    padding: 0;
		}
    </style>
    
    <script>
    	$(document).ready(function() {
            if("{{$handover_menu->field_mandatory}}"=="yes")
            {
                document.getElementById("survey").checked = true;
            }
            else{
                document.getElementById("survey").checked = false; 
            }
        });
    </script>
    
@endsection