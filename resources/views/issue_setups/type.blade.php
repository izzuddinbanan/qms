@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-gear"></i> @lang('issueSetType.header')
                        <button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#modal_add_type" data-popup="tooltip" title="@lang('issueSetType.newType')" data-placement="left"><i class="icon-add"></i></button>
                </h5>
            </div>


            <div class="panel-body">
                <div class="row">
                    <form action="{{ request()->fullUrl() }}" method="GET" role="search" id="searchFOrm">
                        <div class="form-group pull-right" >
                            <div class="col-md-12">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon"><i class="icon-search4"></i></span>
                                    <input type="text" name="search" value="{{ $search ? $search : '' }}" class="form-control" placeholder="@lang('general.searchPlaceHolder')..." autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-framed">
                        <thead class="dashboard-table-heading">
                            <tr class="">
                                <th>@sortablelink('name' , 'Name')</th>
                                <th>@sortablelink('category_id' , 'Category')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $type)
                            <tr class="">
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->inCategory->name }}</td>
                                <td align="center">
                                    <a href="" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon" id="open_{{ $type->id }}" onclick="return editForm({{ $type->id}})"></i>
                                    </a>
                                    <a href="{{ route('setting_type.delete', [$type->id]) }}" id="del_{{ $type->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $type->id }})">
                                        <i class="fa fa-trash largeIcon"></i>
                                    </a>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="3" align="center"><i>@lang('general.no_result').</i></td>
                            </tr>

                            @endforelse
                            
                            
                        </tbody>
                    </table>
                    @lang('general.showing') <b>{{($data->currentpage()-1)*$data->perpage()+1}}</b> @lang('general.to') <b>{{($data->currentpage()-1) * $data->perpage() + $data->count()}}</b> @lang('general.of')  <b>{{$data->total()}}</b> @lang('general.entries')
                </div>
            </div>
            <div class="row" align="center">
                {!! $data->render("pagination::bootstrap-4") !!}

            </div>
            <br>
        </div>
    </div>

    <!-- modal_add_drawing -->
    <div id="modal_add_type" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetType.newType')<hr></h5>
                </div>

                <form action="{{ route('setting_type.store') }}" method="POST">
                    @csrf

                    
                    <div class="modal-body">
                        
                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-component nav-justified">
                                @foreach($language as $lang)
                                    <li class="{{ $lang->id == 1 ? 'active' : '' }}"><a href="#solid-rounded-justified-tab{{ $lang->id }}" data-toggle="tab">{{ $lang->name }}</a></li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach($language as $lang)

                                <div class="tab-pane {{ $lang->id == 1 ? 'active' : '' }}" id="solid-rounded-justified-tab{{ $lang->id }}">
                                    

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
                                                    <input type="text" placeholder="" name="type_name" value="" class="form-control" autocomplete="off" required="">
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
                                                    <input type="text" placeholder="" name="type_name_lang[{{ $lang->id }}]" value="" class="form-control" autocomplete="off" required="">
                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                </div>
                                @endforeach

                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /modal_add_drawing -->

    <!-- modal_edit_drawing -->
    <div id="modal_edit_type" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetType.updateType')<hr></h5>
                </div>

                <form action="{{ route('setting_type.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type_id" id="type_id" value="" >

                    <div class="modal-body">
                        
                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-component nav-justified">
                                @foreach($language as $lang)
                                    <li class="{{ $lang->id == 1 ? 'active' : '' }}"><a href="#solid-rounded-justified-tab-edit{{ $lang->id }}" data-toggle="tab">{{ $lang->name }}</a></li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach($language as $lang)

                                <div class="tab-pane {{ $lang->id == 1 ? 'active' : '' }}" id="solid-rounded-justified-tab-edit{{ $lang->id }}">

                                    @if($lang->id == 1)

                                        <div class="form-group">
                                            <label>@lang('issueSetType.category')</label>
                                            <select data-placeholder="Please Select" class="select-size-sm" name="category" id="category_edit" autofocus="" required="">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetType.name')</label>
                                                    <input type="text" placeholder="" name="type_name" id="type_name" value="" class="form-control" autocomplete="off" required="">
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <div class="form-group">
                                            <label>@lang('issueSetType.category')</label>
                                            <input type="text" name="edit_category_lang" id="edit_category_lang_{{ $lang->id }}" value="" readonly="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetType.name')</label>
                                                    <input type="text" placeholder="" name="edit_type_name_lang[{{ $lang->id }}]" id="edit_type_name_lang{{ $lang->id }}" value="" class="form-control" autocomplete="off" required="">
                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                </div>
                                @endforeach

                            </div>
                        </div>


                        
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /modal_edit_drawing -->



    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var lang = {!! json_encode($language) !!}

        // function listCat(){
        //     event.preventDefault();
        //     $.ajax({
        //         url:"{{ route('setting_type.listCat') }}",
        //         type:'POST',
        //         success:function(response){
        //             $('#category').empty();
        //             $('#category').append('<option value="">Please Select</option>');
        //             response.forEach(element => {
                        
        //                 $('#category').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
        //             });
        //         }
        //     });
        // }

        function IsValidJSONString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        $("#category").change(function(){

            var id = $(this).val();
            var optionText = $("#category option:selected").text();

            $.ajax({
                url:"{{ route('setting_type.catLang') }}",
                type:'POST',
                data:{'id' : id },
                success:function(response){

                    var check = IsValidJSONString(response);

                    if(check == true){

                        var data_lang = JSON.parse(response);
                        lang.forEach(element => {

                            var name;

                            if(data_lang.hasOwnProperty(element["abbreviation_name"])){

                                name = data_lang[element["abbreviation_name"]].name;
                            }else{

                                name = optionText;
                            }

                            $('#category_lang_' + element["id"]).val(name);

                        });
                    }else{

                        $('[name=category_lang]').val(optionText);
                    }
                
                }
            });

        });

        function editForm(id){

            event.preventDefault();
            $.ajax({
                url:"{{ route('setting_type.edit') }}",
                type:'POST',
                data:{'id' : id },
                success:function(response){

                    
                    $('#category_edit').empty();
                    
                    $('#type_name').val(response["type"]["name"]);
                    $('#type_id').val(id);
                    
                    response["category"].forEach(element => {
                        $('#category_edit').append('<option value="'+ element["id"] +'" '+ element["selected"] +'>'+ element["name"] +'</option>');
                    });


                    if(IsValidJSONString(response["type"]["data_lang"]) && response["type"]["data_lang"] != null){

                        var data_lang = JSON.parse(response["type"]["data_lang"]);
                        console.log(data_lang);
                        lang.forEach(element => {

                            var name;

                            if(data_lang.hasOwnProperty(element["abbreviation_name"])){

                                name = data_lang[element["abbreviation_name"]].name;
                            }else{

                                name = "";
                            }

                            $('#edit_type_name_lang' + element["id"]).val(name);

                        });
                    }else{
                        $('[name=edit_type_name_lang]').val("");
                    }

                    if(IsValidJSONString(response["cat_lang"]) && response["cat_lang"]!= null){

                        var cat_lang = JSON.parse(response["cat_lang"]);

                        lang.forEach(element => {

                            var name;

                            if(cat_lang.hasOwnProperty(element["abbreviation_name"])){

                                name = cat_lang[element["abbreviation_name"]].name;
                            }else{

                                name = response["type"]["name"];
                            }

                            $('#edit_category_lang_' + element["id"]).val(name);

                        });
                    }else{
                        $('[name=edit_category_lang]').val(name);
                    }


                    $('#modal_edit_type').modal('show');

                }
            });
        }  

        @if (Session::has('modal'))
            $('#modal_add_type').modal('show');
        @endif
    </script>

@endsection
