@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-gear"></i> @lang('issueSetCat.newCat')
                </h5>
            </div>


            <div class="panel-body">
            
                <form action="{{ route('setting_category.store') }}" method="POST">
                    @csrf
                    <div class="tabbable">
                        <ul class="nav nav-tabs bg-slate-400 nav-justified">
                            @foreach($language as $lang)
                                <li class="{{ $lang->id == 1 ? 'active' : '' }}"><a href="#solid-rounded-justified-tab{{ $lang->id }}" data-toggle="tab">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($language as $lang)

                            <div class="tab-pane {{ $lang->id == 1 ? 'active' : '' }}" id="solid-rounded-justified-tab{{ $lang->id }}">
                                <div class="col-md-offset-1"">
                                    

                                @if($lang->id == 1)

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>@lang('issueSetCat.name')</label>
                                                <input type="text" placeholder="" name="name" value="" autofocus="" required="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                @else

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>@lang('issueSetCat.name')</label>
                                                <input type="text" placeholder="" name="name_lang[{{ $lang->id }}]" value="" autofocus="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                </div>

                            </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="row pull-right col-md-8">
                        <a href="{{ route('setting_category.index') }}">
                            <button type="button" class="btn btn-danger">@lang('general.back')</button>
                        </a>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
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
