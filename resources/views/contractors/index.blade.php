@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="fa fa-users"></i> @lang('company.header')
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#modal_add_contractor"  data-popup="tooltip" title="@lang('company.addNew')" data-placement="left"><i class="icon-add"></i></button>
                </h5>
            </div>

            <div class="panel-body">
                <div class="row">
                    <form action="{{ request()->fullUrl() }}" method="GET" role="search" id="searchFOrm">
                        <div class="form-group pull-right" >
                            <div class="col-md-12">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon"><i class="icon-search4"></i></span>
                                    <input type="text" name="search" value="" class="form-control" placeholder="@lang('general.searchPlaceHolder')..." autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-framed">
                        <thead class="dashboard-table-heading">
                            <tr class="">
                                <th class="indexNo">#</th>
                                <th>@lang('company.display')</th>
                                <th>@lang('company.abvName')</th>
                                <th>@lang('company.description')</th>
                                <th style="text-align: center;">@lang('company.users')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp

                            @forelse($data as $group)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $group->display_name }}</td>
                                    <td>{{ $group->abbreviation_name }}</td>
                                    <td>{{ $group->description }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('contractor.show', [$group->id]) }}">
                                            <button class="btn btn-info">@lang('company.view')</button>
                                        </a>
                                    </td>
                                    <td align="center">
                                        <a href="" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon" id="open_{{ $group->id }}" onclick="return editForm({{ $group->id}})"></i>
                                        </a>
                                        <a href="{{ route('group.destroy', [$group->id]) }}" id="del_{{ $group->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $group->id }})">
                                            <i class="fa fa-trash largeIcon"></i>
                                        </a>
                                    </td>
                                </tr>

                            @empty
                            <tr>
                                <td colspan="6" align="center"><i>@lang('general.no_result').</i></td>
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


    <!-- modal_add_contractor -->
    <div id="modal_add_contractor" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('company.add')<hr></h5>
                </div>

                <form action="{{ route('group.store') }}" method="POST">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('company.display')</label>
                                    <input type="text" placeholder="ABC Testing" name="display_name" value="{{ old('display_name') }}" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('company.abvName')</label>
                                    <input type="text" placeholder="ABC" name="abbreviation_name" value="{{ old('abbreviation_name') }}" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('company.description')</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
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
    <!-- /modal_add_contractor -->

    <!-- modal_edit_contractor -->
    <div id="modal_edit_contractor" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gear"></i> Add Contractor<hr></h5>
                </div>

                <form action="{{ route('step6.updateContractor') }}" method="POST">
                    @method('put')
                    @csrf
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Display Name</label>
                                    <input type="text" placeholder="ABC Testing" name="display_name" value="" id="display_name" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Abbreviation Name</label>
                                    <input type="text" placeholder="ABC" name="abbreviation_name" id="abbreviation_name" value="{{ old('abbreviation_name') }}" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" id="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="group_id" id="group_id" value="">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /modal_edit_contractor -->

    <script type="text/javascript">
        
        function editForm(id){

            event.preventDefault();
            $.ajax({
                url:"{{ route('group.edit', [0]) }}",
                type:'get',
                data:{'id' : id },
                success:function(response){

                    $('#abbreviation_name').val(response["abbreviation_name"]);
                    $('#display_name').val(response["display_name"]);
                    $('#description').val(response["description"]);
                    $('#group_id').val(response["id"]);
                    $('#modal_edit_contractor').modal('show');

                }
            });
        }  
    </script>


    

@endsection
