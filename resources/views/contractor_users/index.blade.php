@extends('components.template-limitless.main')

@section('main')
    <div class="content"">
        <div class="panel panel-flat" >
            <div class="panel-heading">



                <h5 class="panel-title">
                    
                   
                </h5>

                 <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <h5 class="panel-title">
                        <i class="icon-users4"></i> @lang('companyUser.header') <small>({{ $GroupContractor->display_name }})</small>
                    </div>

                    <div class="col-md-6 col-xs-6 text-right">
                        <a href="{{ route('group.index') }}">
                            <button type="button" class="btn btn-primary btn-sm" data-popup="tooltip" title="@lang('general.back')" data-placement="left"><i class="icon-arrow-left52"></i></button>
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_add_user" data-popup="tooltip" title="@lang('companyUser.newUser')" data-placement="left"><i class="icon-add"></i></button>
                    </div>
                </div>



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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact No</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp
                            @forelse($data as $contractor)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $contractor->users->name }}</td>
                                <td>{{ $contractor->users->email }}</td>
                                <td>{{ $contractor->users->contact }}</td>
                                <td style="text-align: center;">
                                    <a href="" data-popup="tooltip" title="@lang('general.edit')" data-placement="top">
                                        <i class="fa fa-edit largeIcon" id="open_{{ $contractor->id }}" onclick="return editForm({{ $contractor->users->id }})"></i>
                                    </a>
                                    <a href="{{ route('contractor.destroy', [$contractor->users->id]) }}" id="del_{{ $contractor->users->id }}" data-popup="tooltip" title="@lang('general.delete')" data-placement="top" onclick="confirmAlert({{ $contractor->users->id }})">
                                        <i class="fa fa-trash largeIcon"></i>
                                    </a>

                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="5" align="center"><i>@lang('general.no_result').</i></td>
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


    <!-- modal_add_user -->
    <div id="modal_add_user" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="icon-users4"></i> @lang('companyUser.newUser')<hr></h5>
                </div>

                <form action="{{ route('contractor.store') }}" method="POST">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.email')</label>
                                    <input type="text" placeholder="example@gmail.com" name="email" id="email" value="{{ old('abbreviation_name') }}" class="form-control" autocomplete="off" required="">
                                    <small id="email-error"></small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.password')</label>
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
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.name')</label>
                                    <input type="text" placeholder="ABC Testing" name="name" id="name" value="{{ old('name') }}" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.contact')</label>
                                    <input type="text" placeholder="0115118978" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="group_id" value="{{ $GroupContractor->id }}">
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
                    <h5 class="modal-title"><i class="icon-users4"></i> @lang('companyUser.editUser')<hr></h5>
                </div>

                <form action="{{ route('contractor.update', [0]) }}" method="POST">

                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.email')</label>
                                    <input type="text" placeholder="example@gmail.com" name="email" id="edit_email" value="" class="form-control" autocomplete="off" disabled="" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.name')</label>
                                    <input type="text" placeholder="ABC Testing" name="name" id="edit_name" value="" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>@lang('companyUser.contact')</label>
                                    <input type="text" placeholder="0115118978" name="contact_no" id="edit_contact_no" value="" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" id="edit_user_id" value="">
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

        $(document).ready(function(){
            $("#email").on('blur', function(){

                var email = $(this).val();

                if (email != ""){

                    $.ajax({
                        url:"{{ route('contractor.verifyUser') }}",
                        type:'POST',
                        data:{'email' : email },
                        beforeSend:function(){
                        },
                        success:function(response){

                            //if email/user is same and role is same
                            if( response['errors'] ){

                                $("#email").select();
                                event.preventDefault();
                                new PNotify({
                                    text: response['errors'],
                                    addclass: 'bg-danger'
                                });

                                // $("#email-error").text("Email already used.")
                            }

                            if( response["name"] != undefined ){
                                $("#name").val(response["name"]);
                                $("#contact_no").val(response["contact"]);
                                $("#name").prop('readonly', true);
                                $("#contact_no").prop('readonly', true);   

                            }else{

                                $("#name").prop('readonly', false);
                                $("#contact_no").prop('readonly', false);
                            }

                        }
                    });
                }
            });
        })
        

        function editForm(id){
            
            event.preventDefault();
            $.ajax({
                url:"{{ route('contractor.edit', [0]) }}",
                type:'get',
                data:{'id' : id },
                success:function(response){
                    
                    $('#edit_email').val(response["email"]);
                    $('#edit_name').val(response["name"]);
                    $('#edit_contact_no').val(response["contact"]);
                    $('#edit_user_id').val(response["id"]);
                    $('#modal_edit_user').modal('show');

                }
            });
        }  
    </script>

    

@endsection
