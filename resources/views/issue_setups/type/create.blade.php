@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-gear"></i> @lang('issueSetType.newType')
                </h5>
            </div>


            <div class="panel-body">
            
                <form action="{{ route('setting_type.store') }}" method="POST">
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
                                <div class="col-md-offset-1 col-md-6">
                                
                                    @if($lang->id == 1)

                                        <div class="form-group">
                                            <label>@lang('issueSetType.category')</label>
                                            <select data-placeholder="Please Select" class="select-size-sm" name="category" id="category" autofocus="" required="">
                                                <option value="">Please Select</option>
                                                @foreach($listCat as $val)
                                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetType.name')</label>
                                                    <input type="text" placeholder="" name="type_name" value="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Unit Owner(U/O)</label>
                                                    <input type="checkbox" class="styled" name="unit_owner" value="1" {{ old('unit_owner') == '1' ? 'checked' : '' }} ">
                                                </div>
                                            </div>
                                        </div>

                                    @else

                                        <div class="form-group">
                                            <label>@lang('issueSetType.category')</label>
                                            <input type="text" name="category_lang" id="category_lang_{{ $lang->id }}" value="" readonly="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetType.name')</label>
                                                    <input type="text" placeholder="" name="type_name_lang[{{ $lang->id }}]" value="" class="form-control" autocomplete="off">
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
                        <a href="{{ route('setting_type.index') }}">
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
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var lang = {!! json_encode($language) !!}
        var listCat = {!! json_encode($listCat) !!}

        console.log(listCat);
        $("#category").change(function(){

            var selectCat = $(this).val();


            listCat.forEach(element => {

                if(element["id"] == selectCat){


                    if(IsValidJSONString(element["data_lang"]) == true){
                        
                        var name;
                        var data_lang = JSON.parse(element["data_lang"]);

                        lang.forEach(language => {

                            if(data_lang.hasOwnProperty(language["abbreviation_name"])){
                                name = data_lang[language["abbreviation_name"]].name;
                                $('#category_lang_' + language["id"]).val(name);
                            }
                            
                        });
                    }

                }
                
            });



        });

    </script>
@endsection
