@extends('layouts.template2')

@section('main')
<style type="text/css">
    .col1 {
        background-color: red;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Access Items : Item Transaction
                    <a href="{{ route('key-access.show', [$transaction->drawing_plan_id]) }}" class="pull-right">
                        <button class="btn btn-primary">Back</button>
                    </a>
                </h5>
            </div>

            <div class="container-fluid">

                <div class="col-md-12">
                    <h6><legend>Unit Owner Details</legend></h6>
                    <p>Unit Number : {{ $DrawingPlan->unit }}</p>
                    <p>Unit Owner : {{ $DrawingPlan->unitOwner->name }}</p>
                </div>

                <div class="col-md-12"  style="padding-bottom: 0px;">
                    <h6><legend>Item Liset</legend></h6>
                </div>
                <div class="row">
                    
                    <table  class="table table-hover table-responsive table-xs table-border">
                        <thead>
                            <tr>
                                <td>Code</td>
                                <td>Name</td>
                                <td>From</td>
                                <td>To</td>
                                <td>Quantity</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaction->items as $eachKey)
                            <tr>
                                <td>{{ $eachKey['code'] }}</td>
                                <td>{{ $eachKey['name'] }}</td>
                                <td>{{ $transaction->keyFrom() }}</td>
                                <td>{{ $transaction->keyTo() }}</td>
                                <td>{{ $eachKey['quantity'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">No Key.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="col-md-12"  style="padding-bottom: 0px;">
                    <h6><legend>Remarks</legend></h6>
                    <div class="form-group">
                        <textarea class="form-control" disabled="" rows="5">{{ $transaction->internal_remarks }}</textarea>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-8 col-xs-6 col-lg-8">
                        <p>Submitted By : </p>
                        <div class="row col-md-6 text-center" style="padding-bottom: 0px !important;">
                            <img src="{{ $transaction->sign_submit_url }}" class="img" width="150" height="150">
                            <hr style="padding-bottom: 0px !important;margin-bottom: 0px !important;">
                        </div>
                        <div class="row col-md-12">
                            <p>({{ $transaction->keyFrom() }})</p>
                            <p>Name : {{ $transaction->name_submit }}</p>
                            <p>Date : {{ $transaction->signature_submit_datetime }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6 col-lg-4">
                        <p>Received By : </p>
                        <div class="row col-md-6 text-center" style="padding-bottom: 0px !important;">
                            <img src="{{ $transaction->sign_receive_url }}" class="img" width="150" height="150">
                            <hr style="padding-bottom: 0px !important;margin-bottom: 0px !important;">
                        </div>
                        <div class="row col-md-12">
                            <p>({{ $transaction->keyTo() }})</p>
                            <p>Name : {{ $transaction->name_receive }}</p>
                            <p>Date : {{ $transaction->signature_receive_datetime }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>

</div>

<script>
 
</script>

@endsection
