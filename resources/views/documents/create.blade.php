@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
          

            @include('components.body.panel-head', [
                'title'     => trans('main.document'),
                'desc'      => trans('main.add'),
                'icon'      => 'icon-files-empty2',
            ])

            <div class="panel-body">
                <div class="row">
                    
                <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-semibold">@lang('main.name') :</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" autofocus="" required="" placeholder="e.g. Document 1">
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-semibold">@lang('main.file') :</label>
                            <input type="file" name="file"class="form-control dropify" required="">
                        </div>
                    </div>


                    @include('components.forms.basic-button', [
                        'route'     => get_route_session() ? get_route_session() : route('document.index'),
                        'div_size'  => 'col-md-6',
                        'position'  => 'right',
                    ])


                </form>
                </div>
            
            </div>

        </div>
    </div>

@endsection


@section('script')

@endsection