@extends('components.template-limitless.main')

@section('main')
<style type="text/css">
.fileitem {
    display: inline-block;
    width: 250px;
    /*cursor: pointer;*/
    margin-left: 5px;
    margin-top: 5px;
}

.filethumbnail {
    display: inline-block;
    width: 250px;
    height: 150px;
    background-position: center;
    background-size: cover;
    border-style: solid;
    border-color: #e9e9ea;
}
.filetitle {
    text-align: center;
    color: white;
    background: #37474f;
    overflow: hidden;
    white-space: nowrap;
    padding: 5px;
    vertical-align: top;
    font-size: 12px;
    border-style: 0.5px solid;
    border-color: #92a8d1;
}

.test {
    width: 130px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-image: url('searchicon.png');
    background-position: 10px 10px; 
    background-repeat: no-repeat;
    padding: 12px 20px 12px 40px;
    -webkit-transition: width 0.4s ease-in-out;
    transition: width 0.4s ease-in-out;
}

.test:focus {
    width: 100%;
}
</style>

<div class="page-header">

    <div class="row">
       

        <div class="col-md-8 col-xs-8">
            <h4 class="panel-title textUpperCase"><i class="icon-city"></i> @lang('project.welcome')</h4>
        </div>
        <div class="col-md-4 col-xs-4 text-right">
            <a href="{{ route('set-general.create') }}" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-file-plus"></i></b> @lang('main.add_new')</a>
        </div>
    </div>



    <div class="row">

        @forelse($data as $project)

            <div class="fileitem ">
                <div class="filethumbnail thumb" style="background-image: url('{{ $project->logo_url }}');">
                    <div class="caption-overflow">
                        <span>

                            <a href="{{ route('set-general.show', [$project->id]) }}" class="btn border-white text-white btn-flat btn-icon btn-rounded">
                                <i class="icon-eye"></i>
                            </a>
                            @if(role_user()->role_id == 3 || role_user()->role_id == 2)
                            <a href="{{ route('project.destroy', [$project->id]) }}" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5 ajaxDeleteButton"><i class="icon-bin"></i></a>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="filetitle">{{ $project->name }}</div>
            </div>
        @empty
            <br>

            <div class="text-center">
                <img src="{{ url('images/loader-hammer.svg') }}" >
                <br>
                No Project here. <a href="{{ route('set-general.create') }}">Add Project </a> Now.
            </div>
        @endforelse

    </div>

    <div class="row" align="center">
        {!! $data->render("pagination::bootstrap-4") !!}
    </div>
</div>


@endsection
