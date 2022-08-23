@extends('layouts.template2')

@section('main')
	<!-- <script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script> -->
	<script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>
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
						        					<input class="form-control" id="display_name" name="display_name" value="{{$handover_menu->display_name}}" placeholder="e.g Acceptance">
						        				</div>
						        			</div>
						        			<div class="row">
						        				<div class="col-md-4">
						        					Field Mandatory:
						        				</div>
						        				<div class="col-md-8">
						        					<label class="switch">
		                                                <input type="checkbox" id="acceptance" name="acceptance" onclick="acceptance()" checked>
		                                                <span class="slider round"></span>
		                                            </label>
						        				</div>
						        			</div>	<br>
						        			<div class="row text-right">
						        				<a href="{{route('handover.index')}}" class="btn btn-danger">Cancel</a>
		                    					<button type="submit" name="acceptance_submit" class="btn btn-primary">Save <i class="icon-arrow-right14 position-right"></i></button>
						        			</div>	
						        		</td>
						        	</tr>
						        </tbody>
		        			</table>
						</form>
					</div>
					<br>
					<div>
						<form action="{{ route('acceptance.updateTermsConditions') }}" method="POST">
	        			@csrf
						<table class="table table-bordered table-hover table-striped table-framed">
							<thead>
					            <tr>
					                <td class="" >{{$handover_menu->display_name}} Preview</td>
					            </tr>
					        </thead>
					        <tbody>
					        	<tr>
					        		<td>
					        			<div class="row">
					        				<h1>Acceptance Form</h1>
					        				<h3>Remarks</h3>
					        				<textarea class="form-control" placeholder="Remarks..." disabled></textarea>
					        			</div>
					        			<div class="row">
					        				<h3>Terms & Conditions:</h3>
					        			</div>
					        			<div class="row">
					        				<p>{!!$termsConditions->termsConditions ?? '' !!}</p>				
					        			</div>	
					        			<div class="row">
					        				<label class="checkbox_container">I acknowledged the above terms & conditions.
											  <input type="checkbox" checked="checked">
											  <span class="checkmark"></span>
											</label>
					        			</div>
					        			<div class="row">
					        				<h3>Received by:</h3>
					        				<div class="div_signature">
					        					Signature
					        				</div>
					        				<h6>Owner/ Owner's Representative Signature</h5>
					        			</div>
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
					        					IC/Passport No:
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
					        			<div class="row">
					        				<h3>Attended by:</h3>
					        				<div class="div_signature">
					        					Signature
					        				</div>
					        				<h6>Handover Representative Signature</h6>
					        			</div>
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
					        					Designation:
					        				</div>
					        				<div class="col-md-10">
					        					<input class="form-control" value="{{$termsConditions->designation}}" disabled/>
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
                    					<a href="{{route('acceptance.editTermsConditions')}}" class="btn btn-primary">Manage {{$handover_menu->display_name}} Content<i class="icon-arrow-right14 position-right"></i></a>
				        			</div>	
					        	</td>
					        </tfoot>
						</table>
						</form>
						<style>
							.row{
								margin-bottom:10px;
							}
							h1{
								font-weight: bold;
							}
							h3{
								font-weight: bold;
							}
							h6{
								font-weight: bold;
							}
							/* The container */
							.checkbox_container {
							  display: block;
							  position: relative;
							  padding-left: 35px;
							  margin-bottom: 12px;
							  cursor: pointer;
							  /*font-size: 22px;*/
							  -webkit-user-select: none;
							  -moz-user-select: none;
							  -ms-user-select: none;
							  user-select: none;
							  justify-content: center;
							}

							/* Hide the browser's default checkbox */
							.checkbox_container input {
							  position: absolute;
							  opacity: 0;
							  cursor: pointer;
							  height: 0;
							  width: 0;
							}

							/* Create a custom checkbox */
							.checkmark {
							  position: absolute;
							  top: 0;
							  left: 0;
							  height: 25px;
							  width: 25px;
							  background-color: gray;
							}

							/* On mouse-over, add a grey background color */
							.checkbox_container:hover input ~ .checkmark {
							  background-color: #D3D3D3;
							}

							/* When the checkbox is checked, add a blue background */
							.checkbox_container input:checked ~ .checkmark {
							  background-color: gray;
							}

							/* Create the checkmark/indicator (hidden when not checked) */
							.checkmark:after {
							  content: "";
							  position: absolute;
							  display: none;
							}

							/* Show the checkmark when checked */
							.checkbox_container input:checked ~ .checkmark:after {
							  display: block;
							}

							/* Style the checkmark/indicator */
							.checkbox_container .checkmark:after {
							  left: 9px;
							  top: 5px;
							  width: 5px;
							  height: 10px;
							  border: solid white;
							  border-width: 0 3px 3px 0;
							  -webkit-transform: rotate(45deg);
							  -ms-transform: rotate(45deg);
							  transform: rotate(45deg);
							}
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

					    <script>
					        $(document).ready(function() {
					            if("{{$handover_menu->field_mandatory}}"=="yes")
					            {
					                document.getElementById("acceptance").checked = true;
					            }
					            else{
					                document.getElementById("acceptance").checked = false; 
					            }
					        });

					        ClassicEditor
				            	.create( document.querySelector( '#editor' ) )
				            	.catch( error => {
				                console.error( error );
				            } );
					    </script>
					</div>
	        	</div>
        </div>
    </div>


    
@endsection