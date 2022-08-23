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
                    <legend>Owner Info</legend>
                    <div class="well" style="margin-top: 10px;">
                        <dl class="dl-horizontal">
                            <dt>Name</dt>
                            <dd>{{ $unit->unitOwner->name }}</dd>
                            <dt>Email</dt>
                            <dd>{{ $unit->unitOwner->email }}</dd>
                            <dt>Phone No</dt>
                            <dd>{{ $unit->unitOwner->phone_no }}</dd>
                            <dt>House No</dt>
                            <dd>{{ $unit->unitOwner->house_no }}</dd>
                            <dt>Office No</dt>
                            <dd>{{ $unit->unitOwner->office_no }}</dd>
                        </dl>
                    </div>

                    @if($unit->jointOwner->count() > 0)
                    <legend>Joint Owner Info</legend>
                    @endif
                    @foreach($unit->jointOwner as $user)
                    <div class="well" style="margin-top: 10px;">
                        <dl class="dl-horizontal">
                            <dt>Name</dt>
                            <dd>{{ $user->user->name }}</dd>
                            <dt>Email</dt>
                            <dd>{{ $user->user->email }}</dd>
                            <dt>Phone No</dt>
                            <dd>{{ $user->user->phone_no }}</dd>
                            <dt>House No</dt>
                            <dd>{{ $user->user->house_no }}</dd>
                            <dt>Office No</dt>
                            <dd>{{ $user->user->office_no }}</dd>
                        </dl>
                    </div>
                    @endforeach
                </div>

                <div class="panel-footer">
                    <div class="row col-md-12 text-right">
                        <a href="{{ route('property-unit.index') }}" type="button" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </form>
        </div>


    </div>

</div>


@endsection
