@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
          

            @include('components.body.panel-head', [
                'title'     => trans('main.group-form'),
                'desc'      => trans('main.add'),
                'icon'      => 'icon-stack',
            ])

            <div class="panel-body">

                <form action="{{ route('group-form.store') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-semibold">@lang('main.name') :</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" autofocus="" required="" placeholder="e.g. Form 1">
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-12">
                        <legend>List of Form</legend>

                        <select multiple="multiple" class="form-control listbox" name="form[]">
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    @include('components.forms.basic-button', [
                        'route'     => route('group-form.index'),
                        'div_size'  => 'col-md-12',
                        'position'  => 'right',
                        'function'  => 'refresh()', 
                    ])


                </form>
            
            </div>

        </div>
    </div>

@endsection


@section('script')
    <script type="text/javascript">

        $('.listbox').bootstrapDualListbox({
            preserveSelectionOnMove: 'moved',
            bootstrap2compatible : true,
            moveOnSelect: false
        });

        function refresh(e){
            $('.listbox').trigger('bootstrapDualListbox.refresh', true); //to refresf -> fix selected value
        }
    </script>
@endsection