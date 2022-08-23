@extends('components.template-limitless.main')
@section('main')

@include('project-settings.components.tab')
        
<div class="panel panel-flat">

    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-file-pdf-o"></i> @lang('module.set-doc')</h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group">
                    <a href="{{ route('set-issue.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    <a href="{{ route('set-document.create') }}" class="btn btn-success" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ol>
                    <li>Click <span class="label label-primary"><i class="icon-add"></i></span> button to add new.</li>
                    <li>Select form that use in this project.</li>
                    <li>Click <span class="label label-primary">submit</span> button at below to save your work.</li>
                </ol>
            </div>
        </div>
    </div>

 

    <div class="panel-body">
        <form method="POST" action="{{ route('set-document.store') }}">
            @csrf

            <div class="col-md-12 col-xs-12">
                <select multiple="multiple" class="form-control listbox" name="doc[]" id="doc">
                    @foreach($docs as $doc)
                        <option value="{{ $doc->id }}" style="cursor: pointer;" title="{{ $doc->name }}" {{ $doc->selected ? 'selected' : '' }}>{{ $doc->name }} </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-primary" onclick="refresh()">
                    @lang('main.submit') <i class="icon-circle-right2"></i>
                </button>
            </div>

        </form>
    </div>
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

            function refresh(e){
                $('.listbox').trigger('bootstrapDualListbox.refresh', true); //to refresf -> fix selected value
            }

            $("#doc").change(function(){

                $('#button-field').fadeIn();
            });
        })
    </script>
@endsection