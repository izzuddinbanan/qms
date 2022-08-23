@extends('layouts.template2')

@section('main')
<style type="text/css">
    .div_signature{
        border-radius: 2px;
        width:100%;
        height:150px;
        background-color: gray;
        display: flex;
        align-items: center;
        justify-content: center;
        color:white;
    }
</style>
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-cogs"></i> Handover Settings: {{$handover_menu->display_name ?? $handover_menu->name}}
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
					                <td class="" >{{$handover_menu->display_name ?? $handover_menu->name}} Settings</td>
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
					        					<input class="form-control" id="display_name" name="display_name" placeholder="e.g Waiver" value="{{$handover_menu->display_name ?? $handover_menu->name}}">
					        				</div>
					        			</div>
					        			<div class="row">
					        				<div class="col-md-4">
					        					Field Mandatory:
					        				</div>
					        				<div class="col-md-8">
					        					<div class="col-md-4 text-right">
                                                    <label class="switch">
                                                        <input type="checkbox" name="waiver" {{ $handover_menu->field_mandatory=="yes" ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
					        				</div>
					        			</div>	
                                        <br>
					        			<div class="row text-right">
					        				<a href="{{route('handover.index')}}" class="btn btn-danger">Cancel</a>
	                    					<button type="submit" name="waiver_submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
					        			</div>	
					        		</td>
					        	</tr>
					        </tbody>
	        			</table>
					</form>
				</div>

                <table class="table table-bordered table-hover table-striped table-framed">
                    <thead>
                        <tr>
                            <td class="" >{{$handover_menu->display_name ?? $handover_menu->original_name }} Preview</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                           <td class="">
                               {!! $waiver->description ?? '' !!}
                           </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <h3>Signed By:</h3>
                                    <div class="div_signature">
                                        Signature
                                    </div>
                                </div> 

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-2">
                                        Name:
                                    </div>
                                    <div class="col-md-10">
                                        <input class="form-control" disabled/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        Date:
                                    </div>
                                    <div class="col-md-10">
                                        <input class="form-control" disabled/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        Date:
                                    </div>
                                    <div class="col-md-10">
                                        24/9/2019
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <td>
                            <div class="row text-right">
                                <a href="{{route('waiver.edit', [$waiver->id ?? '0'])}}" class="btn btn-primary">Update Content<i class="icon-arrow-right14 position-right"></i></a>
                            </div>  
                        </td>
                  </tfoot>
                </table>
        	</div>
        </div>
    </div>
    
    <script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>

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