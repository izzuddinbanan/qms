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
						
						<table id="myTable" class="table table-bordered table-hover table-striped table-framed">
					        <thead>
					            <tr>
					                <td class="" >{{$handover_menu->display_name}} Preview</td>
					            </tr>
					        </thead>
					        <tbody>
					            @forelse ($survey as $s)
					                <tr>
					                    <td>
					                        <div class="row" style="margin-bottom:5px;">
					                            <div class="col-md-12">
					                                <p style="font-weight: bold;">{{$s->question}}</p>
					                            </div>
					                        </div>
					                        <div class="row">
					                        	@if($s->type=="rate")
					                        	<div class="rate {{$s->id}}">
												    <input type="radio" id="star5[{{$s->id}}]" name="rate[{{$s->id}}]" value="5" />
												    <label for="star5[{{$s->id}}]" title="text">5 stars</label>
												    <input type="radio" id="star4[{{$s->id}}]" name="rate[{{$s->id}}]" value="4" />
												    <label for="star4[{{$s->id}}]" title="text">4 stars</label>
												    <input type="radio" id="star3[{{$s->id}}]" name="rate[{{$s->id}}]" value="3" />
												    <label for="star3[{{$s->id}}]" title="text">3 stars</label>
												    <input type="radio" id="star2[{{$s->id}}]" name="rate[{{$s->id}}]" value="2" />
												    <label for="star2[{{$s->id}}]" title="text">2 stars</label>
												    <input type="radio" id="star1[{{$s->id}}]" name="rate[{{$s->id}}]" value="1" />
												    <label for="star1[{{$s->id}}]" title="text">1 star</label>
												 </div>
					                        	@elseif($s->type=="comment")
					                        		<Textarea class="form-control" placeholder="e.g In my opinion..." style="resize:none;min-height:100px;" autocomplete="off" disabled></Textarea>
					                        	@endif 
					                        </div>
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
					        	<td>
					        	<div class="row col-md-12 text-right">
					        		<a href="{{route('survey.index')}}" class="btn btn-danger">Back</a>
					        		@if($survey_version->status == "Draft")
				                    	<a href="{{route('survey.edit', [$survey_version->id])}}" type="submit" class="btn btn-primary">Edit Survey Form <i class="icon-arrow-right14 position-right"></i></a>
				                	@endif
				                </div>
				            	</td>
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
		.rate {
		    float: left;
		    height: 46px;
		    padding: 0 10px;
		}
		.rate:not(:checked) > input {
		    position:absolute;
		    top:-9999px;
		}
		.rate:not(:checked) > label {
		    float:right;
		    width:1em;
		    overflow:hidden;
		    white-space:nowrap;
		    cursor:pointer;
		    font-size:30px;
		    color:#ccc;
		}
		.rate:not(:checked) > label:before {
		    content: '★ ';
		}
		.rate > input:checked ~ label {
		    color: #ffc700;    
		}
		.rate:not(:checked) > label:hover,
		.rate:not(:checked) > label:hover ~ label {
		    color: #d8de23;  
		}
		.rate > input:checked + label:hover,
		.rate > input:checked + label:hover ~ label,
		.rate > input:checked ~ label:hover,
		.rate > input:checked ~ label:hover ~ label,
		.rate > label:hover ~ input:checked ~ label {
		    color: #c59b08;
		}
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