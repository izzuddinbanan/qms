@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-gear"></i> @lang('issueSetIssue.header')
            
                        <a href="{{ route('setting_issue.create') }}">
                            <button class="btn btn-primary btn-sm pull-right" data-popup="tooltip" title="@lang('issueSetIssue.newIssue')" data-placement="left"><i class="icon-add"></i></button>
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
                    <table class="table table-bordered table-hover table-striped table-framed">
                        <thead class="dashboard-table-heading">
                            <tr class="">
                                <th width="10" class="text-center">U/O</th>
                                <th>@sortablelink('name' , 'Name')</th>
                                <th>@sortablelink('type_id' , 'Type')</th>
                                <th>@sortablelink('category_id' , 'Category')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($data as $issue)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="" {{ $issue->unit_owner ? 'checked' : '' }} disabled="">
                                </td>
                                <td>{{ $issue->name }}</td>
                                <td>{{ $issue->type->name }}</td>
                                <td>{{ $issue->category->name }}</td>
                                <td align="center">
                                    <a href="{{ route('setting_issue.show', [$issue->id]) }}" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon"></i>
                                    </a>
                                    <a href="{{ route('setting_issue.delete', [$issue->id]) }}" id="del_{{ $issue->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $issue->id }})">
                                        <i class="fa fa-trash largeIcon"></i>
                                    </a>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="4" align="center"><i>@lang('general.no_result')</i></td>
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

    <!-- modal_add_issue -->
    <div id="modal_add_issue" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetIssue.newIssue')<hr></h5>
                </div>

                <form action="{{ route('setting_issue.store') }}" method="POST">
                    @csrf

                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('issueSetIssue.category')</label>
                            <select data-placeholder="Please Select" class="select-size-sm" name="category" id="category_add" autofocus="" required="">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('issueSetIssue.type')</label>
                            <select data-placeholder="Please Select" class="select-size-sm" name="type" id="type_add" autofocus="" required="">
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('issueSetIssue.name')</label>
                                    <input type="text" placeholder="" name="issue_name" value="" class="form-control" autocomplete="off" required="">
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
    <!-- /modal_add_issue -->

    <!-- modal_edit_issue -->
    <div id="modal_edit_issue" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-gear"></i> @lang('issueSetIssue.udpateIssue')<hr></h5>
                </div>

                <form action="{{ route('setting_issue.update') }}" method="POST">
                    @csrf

                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('issueSetIssue.category')</label>
                            <select data-placeholder="Please Select" class="select-size-sm" name="category" id="category_edit" autofocus="" required="">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('issueSetIssue.type')</label>
                            <select data-placeholder="Please Select" class="select-size-sm" name="type" id="type_edit" autofocus="" required="">
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('issueSetIssue.name')</label>
                                    <input type="text" placeholder="" name="issue_name" id="issue_name" value="" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="issue_id" id="issue_id" value="">
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /modal_edit_issue -->


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function listCat(){
            event.preventDefault();
            $.ajax({
                url:"{{ route('setting_issue.listCat') }}",
                type:'POST',
                success:function(response){
                    $('#category_add').empty();
                    $('#category_add').append('<option value="">Please Select</option>');
                    response.forEach(element => {
                        
                        $('#category_add').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                    });
                }
            });
        }


        $("#category_add").on('select change', function(){

            var catID = $(this).val();
            $('#type_add').empty();

            $.ajax({
                url:"{{ route('setting_issue.listType') }}",
                type:'POST',
                data: {'catID' : catID },
                success:function(response){

                    $('#type_add').append('<option value="">Please Select</option>');
                    response.forEach(element => {
                        
                        $('#type_add').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                    });
                }
            });

        });

        $("#category_edit").on('select change', function(){

            var catID = $(this).val();
            $('#type_edit').empty();

            $.ajax({
                url:"{{ route('setting_issue.listType') }}",
                type:'POST',
                data: {'catID' : catID },
                success:function(response){

                    $('#type_edit').append('<option value="">Please Select</option>');
                    response.forEach(element => {
                        
                        $('#type_edit').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                    });
                }
            });

        });

        function editForm(id){

            event.preventDefault();
            $.ajax({
                url:"{{ route('setting_issue.edit') }}",
                type:'POST',
                data:{'id' : id },
                success:function(response){

                    $('#issue_name').val(response["issue"]["name"]);
                    $('#issue_id').val(response["issue"]["id"]);
                    $('#category_edit').empty();
                    $('#type_edit').empty();

                    response["category"].forEach(element => {
                        if(element["id"] == response["issue"]["category_id"]){
                            var select = 'selected';
                        }else{
                            var select = '';
                        }
                        $('#category_edit').append('<option value="'+ element["id"] +'" '+ select +'>'+ element["name"] +'</option>');
                    });


                    response["type"].forEach(element => {
                        if(element["id"] == response["issue"]["type_id"]){
                            var select = 'selected';
                        }else{
                            var select = '';
                        }
                        $('#type_edit').append('<option value="'+ element["id"] +'" '+ select +'>'+ element["name"] +'</option>');
                    });

                    $('#modal_edit_issue').modal('show');

                }
            });
        }  

        @if (Session::has('modal'))
            $('#modal_add_type').modal('show');
        @endif
    </script>

@endsection
