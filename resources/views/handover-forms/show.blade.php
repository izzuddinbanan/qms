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
                <h4 class="panel-title textUpperCase"><i class="icon-insert-template"></i> Handover Form</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('handover-form.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-8">
                        <h1 class="no-margin text-bold">{{ $form->name }}</h1>
						<p>{{ $form->description }}</p>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 25px;">

                    <div class="col-md-12">
                    	<h6 class="no-margin text-semibold">Details:</h6>
                    	<table class="table table-bordered table-striped table-framed table-xxs">
                			<tr>
                				<td width="30%" style="background-color: #ccc;color: black;">Unit Number</td>
                				<td></td>
                			</tr>
                			<tr>
                				<td width="30%" style="background-color: #ccc;color: black;">Unit Owner</td>
                				<td></td>
                			</tr>
                			<tr>
                				<td width="30%" style="background-color: #ccc;color: black;">Car Park Bay No</td>
                				<td></td>
                			</tr>
                			<tr>
                				<td width="30%" style="background-color: #ccc;color: black;">Access Card(s)</td>
                				<td></td>
                			</tr>
                			<tr>
                				<td width="30%" style="background-color: #ccc;color: black;">Key Fob(s)</td>
                				<td></td>
                			</tr>
                    	</table>
					</div>

                </div>

                @if($form->meter_reading)
                <div class="row" style="margin-bottom: 20px;">

                    <div class="col-md-12">
                    	<h6 class="no-margin text-semibold">Meter Reading:</h6>
                    	<table class="table table-bordered table-striped table-framed table-xxs">
                    		<thead  style="background-color: #ccc;color: black;font-weight: bold;">
                    			<tr>
                    				<td class="text-center" width="5%">#</td>
                    				<td>Category</td>
                    				<td>Reading</td>
                    				<td>Date Time</td>
                    			</tr>
                    		</thead>
                            @php
                                $meter_read = ['electricity', 'water'];
                            @endphp
                    		@foreach($meter_read as $item)
                    		<tbody>
	                    		<tr>
	                    			<td class="text-center">{{ $loop->iteration }}</td>
	                    			<td>{{ ucwords($item) }}</td>
	                    			<td>0000</td>
	                    			<td>0000</td>
	                    		</tr>
                    		</tbody>
                    		@endforeach
                    	</table>
					</div>

                </div>
                @endif
                <div class="row" style="margin-bottom: 10px;">

                    <div class="col-md-12">
                    	<h6 class="no-margin text-semibold">Item List:</h6>
                    	<table class="table table-bordered table-striped table-framed table-xxs">
                    		@foreach($form->section as $section)
                    		<thead  style="background-color: #ccc;color: black;font-weight: bold;">
                    			<tr>
                    				<td colspan="2">{{ $section->name }}</td>
                    				<td class="text-center" width="5%">Quantity</td>
                    				<td width="5%"></td>
                    			</tr>
                    		</thead>
                    		<tbody>
                    			@foreach($section->item as $item)

	                    		<tr>
	                    			<td class="text-center" width="5%">{{ $loop->iteration }}</td>
	                    			<td>{{ $item->name }}</td>
	                    			<td class="text-center">{{ $item->quantity }}</td>
	                    			<td class="text-center">
	                    				<i class="fa fa-square-o"></i>
	                    			</td>
	                    		</tr>
                    			@endforeach
                    		</tbody>
                    		@endforeach
                    	</table>
					</div>

                </div>


                <div class="row">
                	<div class="col-md-12 text-right">
                		<a href="{{ route('handover-form.index') }}" type="submit" class="btn bg-warning-400 btn-labeled"><b><i class="icon-cancel-circle2"></i></b>Back</a>

                	</div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
