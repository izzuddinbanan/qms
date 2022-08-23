@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-gear"></i> @lang('issueSetCat.header')
                    
                    <a href="{{ route('setting_category.create') }}">
                        <button class="btn btn-primary btn-sm pull-right" data-popup="tooltip" title="@lang('issueSetCat.newCat')" data-placement="left"><i class="icon-add"></i></button>
                    </a>
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
                    <table class="table table-bordered table-hover table-striped table-framed table-xxs">
                        <thead class="dashboard-table-heading">
                            <tr class="">
                                <th>@sortablelink('name' , 'Name')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $category)
                            <tr class="">
                                <td>{{ $category->name }}</td>
                                <td align="center">
                                    <a href="{{ route('setting_category.show', [$category->id]) }}" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon" id="open_{{ $category->id }}"></i>
                                    </a>
                                    <a href="{{ route('setting_category.destroy', [$category->id]) }}" id="del_{{ $category->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $category->id }})">
                                        <i class="fa fa-trash largeIcon"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" align="center"><i>@lang('general.no_result').</i></td>
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

    <!-- modal_add_category -->
    <div id="modal_add_category" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetCat.newCat')<hr></h5>
                </div>

                <form action="{{ route('setting_category.store') }}" method="POST">
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
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetCat.name')</label>
                                                    <input type="text" placeholder="" name="name" value="" autofocus="" required="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    @else

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetCat.name')</label>
                                                    <input type="text" placeholder="" name="name_lang[{{ $lang->id }}]" value="" autofocus="" class="form-control" autocomplete="off">
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
    <!-- /modal_add_category -->

    <!-- modal_edit_category -->
    <div id="modal_edit_category" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetCat.updateCat')<hr></h5>
                </div>

                <form action="{{ route('setting_category.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="category_id" id="category_id" value="">

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
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetCat.name')</label>
                                                    <input type="text" placeholder="" name="name" id="name" value="" autofocus="" required="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    @else

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetCat.name')</label>
                                                    <input type="text" placeholder="" name="edit_name_lang[{{ $lang->id }}]" id="edit_name_lang{{ $lang->id }}" value="" autofocus="" class="form-control" autocomplete="off">
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
    <!-- /modal_edit_category -->

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var lang = {!! json_encode($language) !!}

        function editForm(id){
            $('#modal_edit_category').modal('show');

            event.preventDefault();
            $.ajax({
                url:"{{ route('setting_category.edit') }}",
                type:'POST',
                data:{'id' : id },
                success:function(response){

                    var data_lang = JSON.parse(response["data_lang"]);

                    $('#name').val([response["name"]]);
                    $('#category_id').val(response["id"]);


                    lang.forEach(element => {

                        var name;

                        if(data_lang.hasOwnProperty(element["abbreviation_name"])){

                            name = data_lang[element["abbreviation_name"]].name;
                        }

                        $('#edit_name_lang' + element["id"]).val(name);

                    });

                }
            });
        }   
    </script>

@endsection
