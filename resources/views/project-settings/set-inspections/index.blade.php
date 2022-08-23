@extends('components.template-limitless.main')

@section('main')
@include('project-settings.components.tab')

<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-file-pdf-o"></i> @lang('module.set-form')
                </h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group btn-top">
                    <a href="{{ route('set-link.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-location.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ol>
                    <li>Select form that use in this project.</li>
                    <li>Click <span class="label label-primary">submit</span> button at below to save your work.</li>
                </ol>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('set-inspection.store') }}">
        @csrf
        <div class="panel-body">

                <div class="row">
                    <div class="col-md-12">
                        
                        <div class="tabbable">
                            <ul class="nav nav-tabs bg-slate nav-tabs-component nav-justified">
                                <li class="active"><a href="#colored-rounded-justified-tab1" data-toggle="tab">Form</a></li>
                                <li><a href="#colored-rounded-justified-tab2" data-toggle="tab">Group Form</a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="colored-rounded-justified-tab1">
                                    <div class="col-md-12 col-xs-12">
                                        <select multiple="multiple" class="form-control listbox" name="form[]" id="form">
                                            @foreach($forms as $form)
                                                <option value="{{ $form->id }}" style="cursor: pointer;" title="{{ $form->name }}" {{ $form->selected ? 'selected' : '' }}>{{ $form->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="tab-pane" id="colored-rounded-justified-tab2">
                                    <div class="col-md-12 col-xs-12">
                                        <select multiple="multiple" class="form-control listbox" name="group[]" id="group">
                                            @foreach($groupForm as $group)
                                                <option value="{{ $group->id }}" style="cursor: pointer;" title="{{ $group->name }}" {{ $group->selected ? 'selected' : '' }}>{{ $group->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection


@section('script')
    <script type="text/javascript">

        $(document).ready(function(){
                
            $('.listbox').bootstrapDualListbox({
                preserveSelectionOnMove: 'moved',
                bootstrap2compatible : true,
                moveOnSelect: false
            });
        })

        function refresh(e){
            $('.listbox').trigger('bootstrapDualListbox.refresh', true); //to refresf -> fix selected value
        }

     
    </script>
@endsection