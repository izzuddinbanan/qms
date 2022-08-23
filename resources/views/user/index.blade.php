@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-user-tie"></i> @lang('employee.header')
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#modal_add_user" data-popup="tooltip" title="@lang('employee.new_emp')" data-placement="left"><i class="icon-add"></i></button>
                        
                    </div>
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
                                <th>@lang('employee.name')</th>
                                <th>@lang('employee.email')</th>
                                <th>@lang('employee.contact')</th>
                                <th>@lang('employee.role')</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp
                            @forelse($data as $user_role)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user_role->users->name }}</td>
                                    <td>{{ $user_role->users->email }}</td>
                                    <td>{{ $user_role->users->contact }}</td>
                                    <td>{{ $user_role->roles->display_name }}</td>
                                    <td style="text-align: center;">
                                        <a href="" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon" id="open_{{ $user_role->id }}" onclick="return editForm({{ $user_role->id}})"></i>
                                        </a>
                                        <a href="{{ route('user.destroy', [$user_role->id]) }}" id="del_{{ $user_role->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $user_role->id }})">
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
            <div class="row" align="center">

            </div>
            <br>
        </div>
    </div>


    <!-- modal_add_user -->
    <div id="modal_add_user" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="icon-user-tie"></i> @lang('employee.new_emp')<hr></h5>
                </div>

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-12 col-xs-12">
                                    <label>@lang('employee.role')</label>
                                    <select data-placeholder="Please Select" class="select-size-sm" name="role" autofocus="" required="">
                                        <option value="">Please Select</option>
                                        @foreach($role as $roles)
                                        <option value="{{ $roles->id }}">{{ $roles->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <label>@lang('employee.name')</label>
                                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Stacy Webb" class="form-control" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-xs-12">
                                    <label>@lang('employee.email')</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="StacyWebb@gmail.com" class="form-control">
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <label>@lang('employee.password')</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info" type="button" data-popup="tooltip" title="Click for random Password" id="btn-random"><i class="icon-lock"></i></button>
                                        </span>
                                        <input type="password" class="form-control" placeholder="abc123" name="password" id="password" value="{{ old('password') }}" required="" autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="show-pass"  onclick="return show_hidePass(this.id)" ><i class="icon-eye"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group">
                                <div class="col-md-12 col-xs-12">
                                    <label>@lang('employee.contact')</label>
                                    <input type="text" name="contact" value="{{ old('contact') }}" placeholder="" class="form-control" autocomplete="off">
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
    <!-- /modal_add_user -->


    <!-- modal_edit_user -->
    <div id="modal_edit_user" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="icon-user-tie"></i> @lang('employee.update_emp')<hr></h5>
                </div>

                <form action="{{ route('user.updateUser') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group" id="role_field">
                            <label>@lang('employee.role')</label>
                            <select data-placeholder="Please Select" class="select-size-sm" name="role" id="edit_role" disabled="">
                                <option value="">Please Select Role</option>
                                @foreach($role as $roles)
                                <option value="{{ $roles->id }}">{{ $roles->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('employee.name')</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Stacy Webb" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('employee.email')</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="StacyWebb@gmail.com" class="form-control" disabled="" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('employee.contact')</label>
                                    <input type="text" name="contact" id="contact" value="{{ old('contact') }}" placeholder="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="user_id" value="" placeholder="" class="form-control">

                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /modal_edit_user -->

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function editForm(id){

            event.preventDefault();
            $.ajax({
                url:"{{ route('user.edit') }}",
                type:'POST',
                data:{'role_id' : id },
                success:function(response){

                    if(response["role_id"] == 2){
                        $("#role_field").hide();
                    }else{
                        $("#role_field").show();
                    }
                    // console.log(response["users"]["name"]);
                    
                    $('#name').val(response["users"]["name"]);
                    $('#email').val(response["users"]["email"]);
                    $('#contact').val(response["users"]["contact"]);
                    
                    $("#edit_role").val(response["role_id"]);
                    $("#user_id").val(response["users"]["id"]);
                    $('#edit_role').trigger('change');
                    $('#modal_edit_user').modal('show');

                }
            });
        }  
    </script>

    

@endsection
