@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-gear"></i> @lang('issueSetCat.updateCat')
                </h5>
            </div>


            <div class="panel-body">
            
                <form action="{{ route('setting_category.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

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
                                            <div class="col-md-6">
                                                <label>@lang('issueSetCat.name')</label>
                                                <input type="text" placeholder="" name="name" value="{{ $category->name }}" autofocus="" required="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                @else

                                    <?php

                                        $name = "";
                                        if(isset($category->data_lang[$lang->abbreviation_name])){

                                            $name = $category->data_lang[$lang->abbreviation_name]->name;

                                        }
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>@lang('issueSetCat.name')</label>
                                                <input type="text" placeholder="" name="edit_name_lang[{{ $lang->id }}]" value="{{ $name }}" autofocus="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="row col-md-8">
                        <div class="pull-left">
                            <a href="{{ route('setting_category.create') }}">
                                <button type="button" class="btn btn-success"><i class="icon-add"></i> @lang('general.addMore')</button>
                            </a>
                        </div>
                        <div  class="pull-right">
                            <a href="{{ route('setting_category.index') }}">
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


    

@endsection
