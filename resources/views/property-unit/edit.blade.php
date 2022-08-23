@extends('layouts.template2')

@section('main')
<style type="text/css">
    .col1 {
        background-color: red;
    }
    .wrapper {
        position: relative;
        width: auto;
        height: 200px;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width:auto;
        height:200px;
        background-color: grey;
        border-color: black;
    }

    .dl-horizontal dt {
        margin-top: 0px
    }

    .dl-horizontal dt + dd {
        margin-top: 0px;
    }

    .form-control {
        height: 29px;
        margin-bottom: 5px; 
    }
</style>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Property Unit 
                </h5>
            </div>

            <form action="{{ route('property-unit.update', [$unit->id]) }}" method="POST" id="myForm">
                @csrf
                @method('put')

                <div class="panel-body">
                    <legend>Unit Info</legend>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ url('uploads/drawings/' . $unit->file) }}" class="img img-responsive">
                        </div>
                        <div class="col-md-8">
                            <dt>Project : {{ $unit->drawingSet->project->name }}</dt>
                            <dt>Phase : {{ $unit->phase ?? '-' }}</dt>
                            <dt>Block : {{ $unit->block ?? '-' }}</dt>
                            <dt>Level : {{ $unit->level ?? '-' }}</dt>
                            <dt>Unit : {{ $unit->unit ?? '-' }}</dt>
                            <dt>Handover Status : <span class="label label-{{ $unit->handover_status == 'not handed over' ? 'default' : 'success' }}">{{ $unit->handover_status }}</span></dt>

                        </div>
                    </div>
                    <div class="well">
                        <dl class="dl-horizontal">
                            
                            
                            <dt>Unit Id</dt>
                            <dd>
                                <input type="text" name="unit_id" class="form-control" value="{{ $unit->unit_id }}">
                            </dd>
                            <dt>Car Park</dt>
                            <dd>
                                <textarea class="form-control" name="car_park">{{ $unit->car_park }}</textarea>
                            </dd>
                            <dt>Access Card</dt>
                            <dd>
                                <textarea class="form-control" name="access_card">{{ $unit->access_card }}</textarea>
                            </dd>
                            <dt>Access Card</dt>
                            <dd>
                                <textarea class="form-control" name="key_fob">{{ $unit->key_fob }}</textarea>
                            </dd>

                            <dt>SPA Date</dt>
                            <dd>
                                <input type="date" name="spa_date" value="{{ $unit->spa_date }}" class="form-control">
                            </dd>
                            <dt>VP Date</dt>
                            <dd>
                                <input type="date" name="vp_date" value="{{ $unit->vp_date }}" class="form-control">
                            </dd>
                            <dt>DLP Expiry Date</dt>
                            <dd>
                                <input type="date" name="dlp_expiry_date" value="{{ $unit->dlp_expiry_date }}" class="form-control">
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="row col-md-12 text-right">
                        <a href="{{ route('property-unit.index') }}" type="button" class="btn btn-danger">Back</a>
                        <button type="submit" class="btn btn-primary">submit</button>
                    </div>
                </div>
            </form>
        </div>


    </div>

</div>


@endsection
