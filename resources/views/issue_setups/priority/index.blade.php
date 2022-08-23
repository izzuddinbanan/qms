@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-gear"></i> @lang('issueSetPriority.header')
                   
                        <a href="{{ route('setting_priority.create') }}">
                            <button class="btn btn-primary btn-sm pull-right" data-popup="tooltip" title="@lang('issueSetPriority.newPriority')" data-placement="left"><i class="icon-add"></i></button>
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
                                <th>@sortablelink('type' , 'Type')</th>
                                <th>@sortablelink('name' , 'Name')</th>
                                <th>@sortablelink('no_of_days' , 'No of Days')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $priority)
                            <tr class="">
                                <td>{{ $priority->type }}</td>
                                <td>{{ $priority->name }}</td>
                                <td>{{ $priority->no_of_days }}</td>
                                <td align="center">
                                    <a href="{{ route('setting_priority.show', [$priority->id]) }}" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon"></i>
                                    </a>
                                    <a href="{{ route('setting_priority.delete', [$priority->id]) }}" id="del_{{ $priority->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({!! $priority->id !!})">
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
                    @lang('general.showing') <b>{{($data->currentpage()-1)*$data->perpage()+1}}</b> @lang('general.to') <b>{{($data->currentpage()-1) * $data->perpage() + $data->count()}}</b> @lang('general.of') <b>{{$data->total()}}</b> @lang('general.entries')
                </div>
            </div>
            <div class="row" align="center">
                {!! $data->render("pagination::bootstrap-4") !!}

            </div>
            <br>
        </div>
    </div>

    {{-- <!-- modal_add_drawing -->
    <div id="modal_add_priority" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetPriority.newPriority')<hr></h5>
                </div>

                <form action="{{ route('setting_priority.store') }}" method="POST">
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
                                                <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                                <input type="text" placeholder="Commercial High" name="type" value="" autofocus="" required="" class="form-control" autocomplete="off" autofocus="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.name')</label>
                                                <input type="text" placeholder="High" name="name" value="" autofocus="" required="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.noDays')</label>
                                                <input type="number" placeholder="5" id="no_of_days" name="no_of_days" value="" required="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.noDaysNoti')</label>
                                                <input type="number" placeholder="5" name="no_of_days_notify" id="no_of_days_notify" value="" required="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    @else

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                                <input type="text" placeholder="Commercial High" name="type_lang[{{ $lang->id }}]" value="" autofocus="" class="form-control" autocomplete="off" autofocus="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.name')</label>
                                                <input type="text" placeholder="High" name="name_lang[{{ $lang->id }}]" value="" autofocus="" class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.noDays')</label>
                                                <input type="number" placeholder="5" name="no_of_days_lang" value="" class="form-control" autocomplete="off" readonly="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>@lang('issueSetPriority.noDaysNoti')</label>
                                                <input type="number" placeholder="5" name="no_of_days_notify_lang" value="" class="form-control" autocomplete="off" readonly="">
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
    <!-- /modal_add_drawing --> --}}


    {{-- <!-- modal_edit_priority -->
    <div id="modal_edit_priority" class="modal fade">
        <div class="modal-dialog  modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetPriority.updatePriority')<hr></h5>
                </div>

                <form action="{{ route('setting_priority.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="priority_id" id="priority_id" value="">

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
                                                    <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                                    <input type="text" placeholder="Commercial High" id="edit_type" name="type" value="" autofocus="" required="" class="form-control" autocomplete="off" autofocus="">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.name')</label>
                                                    <input type="text" placeholder="High" name="name" id="edit_name" value="" autofocus="" required="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.noDays')</label>
                                                    <input type="number" placeholder="5" id="edit_no_of_days" name="no_of_days" value="" required="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.noDaysNoti')</label>
                                                    <input type="number" placeholder="5" name="no_of_days_notify" id="edit_no_of_days_notify" value="" required="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    @else

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.type')</label>&nbsp;&nbsp;<small class="text-muted">*@lang('issueSetPriority.unique')</small>
                                                    <input type="text" placeholder="Commercial High" name="type_lang[{{ $lang->id }}]" id="edit_type_lang{{ $lang->id }}" value="" autofocus="" class="form-control" autocomplete="off" autofocus="">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.name')</label>
                                                    <input type="text" placeholder="High" name="name_lang[{{ $lang->id }}]" id="edit_name_lang{{ $lang->id }}" value="" autofocus="" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.noDays')</label>
                                                    <input type="number" placeholder="5" name="" id="edit_no_of_days_lang{{ $lang->id }}" value="" class="form-control" autocomplete="off" readonly="">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>@lang('issueSetPriority.noDaysNoti')</label>
                                                    <input type="number" placeholder="5" name="" id="edit_no_of_days_notify_lang{{ $lang->id }}" value="" class="form-control" autocomplete="off" readonly="">
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
    <!-- /modal_edit_priority --> --}}

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var lang = {!! json_encode($language) !!}

        function editForm(id){
            $('#modal_edit_priority').modal('show');

            event.preventDefault();
            $.ajax({
                url:"{{ route('setting_priority.edit') }}",
                type:'POST',
                data:{'id' : id },
                success:function(response){

                    var data_lang = JSON.parse(response["data_lang"]);

                    $('#edit_name').val(response["name"]);
                    $('#edit_no_of_days').val(response["no_of_days"]);
                    $('#edit_no_of_days_notify').val(response["no_of_days_notify"]);
                    $('#priority_id').val(response["id"]);
                    $('#edit_type').val(response["type"]);


                    lang.forEach(element => {

                        var name, type;

                        if(data_lang.hasOwnProperty(element["abbreviation_name"])){

                            name = data_lang[element["abbreviation_name"]].name;
                            type = data_lang[element["abbreviation_name"]].type;
                        }

                        $('#edit_no_of_days_lang' + element["id"]).val(response["no_of_days"]);
                        $('#edit_no_of_days_notify_lang' + element["id"]).val(response["no_of_days_notify"]);
                        $('#edit_name_lang' + element["id"]).val(name);
                        $('#edit_type_lang' + element["id"]).val(type);

                    });


                }
            });
        }   

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
