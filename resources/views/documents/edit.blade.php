@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
          

            @include('components.body.panel-head', [
                'title'     => trans('main.document'),
                'desc'      => trans('main.update'),
                'icon'      => 'icon-files-empty2',
            ])

            <div class="panel-body">
                <div class="row">
                    
                    <form action="{{ route('document.update', [$doc->id]) }}" method="POST" enctype="multipart/form-data" id="form-document">
                        @csrf
                        @method('put')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.name') :</label>
                                <input type="text" class="form-control" name="name" value="{{ $doc->name }}" autocomplete="off" autofocus="" required="" placeholder="e.g. Document 1">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.file') :</label>
                                @if($doc->url)
                                <a href="{{ $doc->url }}" target="_blank"><span class="label label-info pull-right"><i class="fa fa-download"></i> Download</span></a>
                                @endif
                                <input type="file" name="file" id="file" class="form-control dropify" @if($doc->file) data-default-file="{{ $doc->url }}" @endif>
                            </div>
                        </div>


                        @include('components.forms.basic-button', [
                            'route'     => route('document.index'),
                            'div_size'  => 'col-md-6',
                            'position'  => 'right',
                            'function'  => 'check(event)',
                        ])


                    </form>
                </div>

            </div>

        </div>
    </div>

@endsection


@section('script')
<script>
    
    function check(event){

        event.preventDefault();

        if($("#file").val() != ""){

            var confirms = confirm('Are you sure to upload a new version ?');

            if(confirms){
                $("#form-document").submit();
            }

        }else{

            $("#form-document").submit();

        }


    }
</script>

@endsection