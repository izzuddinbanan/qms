@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
          

            @include('components.body.panel-head', [
                'title'     => trans('main.group-form'),
                'desc'      => trans('main.update'),
                'icon'      => 'icon-stack',
            ])

            <div class="panel-body">

                <form action="{{ route('group-form.update', [$group->id]) }}" method="POST">
                    @method('put')
                    @csrf
                    <!-- CLIENT/COMPANY COLUMN -->
                    <div class="col-md-6">
                        <!-- Client Name -->
                        <div class="form-group">
                            <label class="text-semibold">@lang('main.name') :</label>
                            <input type="text" class="form-control" name="name" value="{{ $group->name }}" autocomplete="off" autofocus="" required="" placeholder="e.g. Form 1">
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <!-- SUPER USER COLUMN -->
                    <div class="col-md-12">
                        <legend>List of Form</legend>

                        <select multiple="multiple" class="form-control listbox" name="form[]">
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}" {{ $form->selected ? 'selected' : '' }}>{{ $form->name }}</option>
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
        // Multiple selection
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