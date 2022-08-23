@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-gear"></i> @lang('issueSetPriority.updatePriority')
                    


                </h5>
            </div>


            <div class="panel-body">
            
                <form action="{{ route('setting_priority.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="priority_id" value="{{ $priority->id }}">

                    <div class="tabbable">
                        <ul class="nav nav-tabs bg-slate-400 nav-justified">
                            @foreach($language as $lang)
                                <li class="{{ $lang->id == 1 ? 'active' : '' }}"><a href="#solid-rounded-justified-tab{{ $lang->id }}" data-toggle="tab">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($language as $lang)

                            <div class="tab-pane {{ $lang->id == 1 ? 'active' : '' }}" id="solid-rounded-justified-tab{{ $lang->id }}">
                                

                                @if($lang->id == 1)
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                            <input type="text" placeholder="Commercial High" name="type" value="{{ $priority->type }}" autofocus="" required="" class="form-control" autocomplete="off" autofocus="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.name')</label>
                                            <input type="text" placeholder="High" name="name" value="{{ $priority->name }}" autofocus="" required="" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.noDays')</label>
                                            <input type="number" placeholder="5" id="no_of_days" name="no_of_days" value="{{ $priority->no_of_days }}" required="" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.noDaysNoti')</label>
                                            <input type="number" placeholder="5" name="no_of_days_notify" id="no_of_days_notify" value="{{ $priority->no_of_days_notify }}" required="" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                @else

                                <div class="form-group">

                                    <?php

                                        $type = "";
                                        $name = "";
                                        $no_of_days = "";
                                        $no_of_days_notify = "";

                                        if(isset($priority->data_lang[$lang->abbreviation_name])){

                                            $type = $priority->data_lang[$lang->abbreviation_name]->type;
                                            $name = $priority->data_lang[$lang->abbreviation_name]->name;

                                        }
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                            <input type="text" placeholder="Commercial High" name="type_lang[{{ $lang->id }}]" value="{{ $type }}" autofocus="" class="form-control" autocomplete="off" autofocus="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.name')</label>
                                            <input type="text" placeholder="High" name="name_lang[{{ $lang->id }}]" value="{{ $name }}" autofocus="" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.noDays')</label>
                                            <input type="number" placeholder="5" name="no_of_days_lang" value="{{ $priority->no_of_days }}" class="form-control" autocomplete="off" readonly="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>@lang('issueSetPriority.noDaysNoti')</label>
                                            <input type="number" placeholder="5" name="no_of_days_notify_lang" value="{{ $priority->no_of_days_notify }}" class="form-control" autocomplete="off" readonly="">
                                        </div>
                                    </div>
                                </div>


                                @endif


                            </div>
                            @endforeach

                        </div>
                    </div>
                        
                    <div class="row col-md-12">
                        <div class="pull-left">
                            <a href="{{ route('setting_priority.create') }}">
                                <button type="button" class="btn btn-success"><i class="icon-add"></i> @lang('general.addMore')</button>
                            </a>
                        </div>
                        <div class="pull-right">

                            <a href="{{ route('setting_priority.index') }}">
                                <button type="button" class="btn btn-danger">@lang('general.back')</button>
                            </a>
                            <button type="submit" class="btn btn-primary">@lang('general.update')</button>
                        </div>
                    </div>
                </form>
                   
            </div>
     
            <br>
        </div>
    </div>


    

    <script type="text/javascript">
        $(document).ready(function(){

            $("#no_of_days, #edit_no_of_days").change(function(){
                $("[name=no_of_days_lang]").val($(this).val());

            });

            $("#no_of_days_notify, #edit_no_of_days_notify").change(function(){
                $("[name=no_of_days_notify_lang]").val($(this).val());

            });
        });
    </script>
@endsection
