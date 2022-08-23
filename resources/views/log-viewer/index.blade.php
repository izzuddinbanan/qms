@extends('components.template-limitless.main')

@section('main')
<div class="panel panel-flat">
    <iframe src="{{ route('log-viewer::logs.list') }}" width="100%" height="750"></iframe>
</div>

@endsection
