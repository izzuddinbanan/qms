@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-users"></i> @lang('main.customer') 
                        <a class="btn_cust" href="#">
                            <button class="btn btn-primary pull-right btn_cust" data-toggle="modal" data-target="#modalAddCutomer">@lang('main.add_customer')</button>
                        </a>
                        <div class="btn-group btn_cust pull-right">
                            <button type="button" class="btn btn-primary pull-right dropdown-toggle" data-toggle="dropdown">Export<span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ url('customer/exportsample') }}">Export Sample</a></li>
                                <li><a href="{{ url('customer/export') }}">Export Records</a></li>
                            </ul>
                        </div>
                        <!-- <a class="btn_cust" href="{{ url('customer/export') }}">
                            <button class="btn btn-primary pull-right btn_cust">@lang('main.export')</button>
                        </a> -->
                        <a class="btn_cust" href="#">
                            <button class="btn btn-primary pull-right btn_cust" data-toggle="modal" data-target="#modalImport">@lang('main.import')</button>
                        </a>
                </h5>
            </div>

            @include('components.body.table-yajra', [
                'id'        => 'table-data',
                'class'     => '',
                'columns'    => [trans('main.name') => '',
                                trans('main.email') => '',
                                trans('main.contact') => '',
                                trans('main.status') => '',
                                trans('main.action') => '',
                                ],
            ])

        </div>
    </div>
    
    {!! Form::open(['action' => ['CustomerController@import'],'method'=>'POST','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
    <!-- Modal Import -->
    <div class="modal fade" id="modalImport" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Import customer</h4>
                </div>
                <div class="modal-body">
                    <p>Please choose and import customer information. Only Excel file is accepted.</p>
                    <input type="file" name="customer_import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default">Import</button>
                </div>
            </div>
        </div>
    </div> 
    {!! Form::close() !!}  


    <!-- Modal Add Customer -->
    <div class="modal fade" id="modalAddCutomer" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add customer</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['action' => ['CustomerController@store'],'method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h5 class="panel-title">Please insert customer's information</h5>
                            </div>

                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="name" class="form-control" placeholder="Name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Email:</label>
                                    <div class="col-lg-9">
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Contact:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="contact" class="form-control" placeholder="Contact Number">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Unit:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="unit" class="form-control" placeholder="Unit">
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Submit<i class="icon-arrow-right14 position-right"></i></button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!} 
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div> 

    @if(session('upload_status') && session('upload_status')=="failed")
    <!-- Modal Show Errors -->
    <div id="modal_import_error" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title failed"><i class="fa fa-exclamation-circle"></i> Import Failed<hr></h5>
                </div>

                <div class="panel-body">
                    <p>Total fail records: {{session('error_counts')}}</p>
                    <p>Records not added. List below show the reason of adding failed:</p>
                    <ul class="failed">
                    @foreach(session('error_msg') as $key => $failed)
                        <li>{{$failed}}</li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif 

    <script>
        $(document).ready(function() {

            $(".loader").show();
            $('#table-data').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: "{{ route('customer.index-data') }}",
                columnDefs: [
                    { "width": "5%", "targets": [1] },
                    { "width": "5%", "targets": [1,1] },
                    {
                          "targets": [1,2,3], // your case first column
                          // "className": "text-center",
                    },
                ],
                columns: [
                    { data: 'users.name', name: 'users.name' },
                    { data: 'email', name: 'email', orderable: false, searchable: false  },
                    { data: 'contact', name: 'contact',  orderable: false, searchable: false  },
                    { data: 'status', name: 'status',  orderable: false, searchable: false  },
                    { data: 'action', name: 'action',  orderable: false, searchable: false  },
                ],
                initComplete : function (respon){
                    $(".loader").hide();
                },
                "language": {
                  "emptyTable": {!! json_encode(trans('main.no-result')) !!}
                }
            });

        });

        @if(session('upload_status') && session('upload_status')=="failed")
        $("#modal_import_error").modal('toggle');
        @endif

    </script>

    <style>
        .btn_cust{
            margin:3px;
        }
        .failed{
            color:#bb1e3c;
        }
    </style>

    
@endsection