@extends('components.template-limitless.main')

@section('main')

<div class="panel panel-white">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8">
                <h4 class="panel-title textUpperCase"><i class="icon-graph"></i> Dashboard</h4>
                <small class="display-block">{{ get_day_type() }} {{ Auth::user()->name }}! Have a nice day.</small>
            </div>
        </div>
    </div>
</div>
@endsection
