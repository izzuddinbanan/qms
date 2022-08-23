@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >

            @include('components.body.panel-head', [
                'title'     => trans('main.digital-form'),
                'icon'      => 'icon-insert-template',
            ])


            <div class="panel-body">
                <div class="row">
                    
                    <form action="{{ route('form-status.update', [$formGroupStatus->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="form_id" value="{{ $formGroup->id }}">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.name') :</label>
                                <input type="text" class="form-control" name="name" value="{{ $formGroupStatus->name }}" autocomplete="off" autofocus="" required="" placeholder="e.g. Closed">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <label class="text-semibold">@lang('main.colorPicker') :</label>
                            <input type="text" class="form-control colorpicker-basic" value="{{ $formGroupStatus->color_code }}" id="color-picker">
                            <input type="hidden" name="colorcode" value="{{ $formGroupStatus->color_code }}" id="colorcode">
                        </div>

                        <div class="clearfix"></div>

                        @include('components.forms.basic-button', [
                            'route'     => route('form-status.index', [$formGroup->id]),
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
<script src="{{ url('assets/js/plugins/pickers/color/spectrum.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/pages/picker_color.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){

         changeToCode();
        
        $('#color-picker').change(function(){
           changeToCode();
        })

        function changeToCode(){
            var color = $("#color-picker").spectrum('get').toHexString();
            $("#colorcode").val(color)
        }
    });
</script>
@endsection