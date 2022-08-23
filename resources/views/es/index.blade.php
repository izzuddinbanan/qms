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
					                <!-- <td class="" >No</td> -->
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
					        					<input class="form-control" id="display_name" name="display_name" value="{{$handover_menu->display_name}}" placeholder="e.g E/S">
					        				</div>
					        			</div>
					        			<div class="row">
					        				<div class="col-md-4">
					        					Field Mandatory:
					        				</div>
					        				<div class="col-md-8">
					        					<label class="switch">
						                            <input type="checkbox" id="es" name="es" onclick="es()" checked>
						                            <span class="slider round"></span>
						                        </label>
					        				</div>
					        			</div>	<br>
					        			<div class="row text-right">
					        				<a href="{{route('handover.index')}}" type="submit" name="survey" class="btn btn-danger">Cancel</a>
	                    					<button type="submit" name="es_submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
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

    <script>
    	$(document).ready(function() {
            if("{{$handover_menu->field_mandatory}}"=="yes")
            {
                document.getElementById("es").checked = true;
            }
            else{
                document.getElementById("es").checked = false; 
            }
        });
    </script>

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
    </style>
    
@endsection