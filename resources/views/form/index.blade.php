@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-users2"></i> Digital Forms
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#modal_add_form"><i class="icon-add"></i> Add Form</button>
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
                        <thead>
                            <tr class="">
                                <th class="indexNo">#</th>
                                <th>Form</th>
                                <th>Created at</th>
                                <th class="text-center">Version</th>
                                <th class="text-center">Status</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp

                            @forelse($data as $form)
                                <tr>
                                    <td align="center">{{ ++$i }}</td>
                                    <td class="col-md-4">{{ $form->name }}</td>
                                    <td class="col-md-2">{{ $form->created_at }}</td>
                                    <td class="col-md-1">
                                        <a class="bg-blue btn" href="{{ route('version.index', [$form->id]) }}" data-popup="tooltip" title="Edit" data-placement="top">
                                            View All Versions
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a class="bg-blue btn" href="{{ route('form-status.index', [$form->id]) }}" data-popup="tooltip" title="Edit" data-placement="top">
                                            Manage Status
                                        </a>
                                    </td>
                                    
                                    {{-- <td class="col-md-1"><span class="label {{ $form->status == 1 ? 'bg-success' : ($form->status == 2 ? 'bg-blue' : 'bg-danger') }}">{{ $form->status_name }}</span></td> --}}
                                    <td align="center"> 
                                        <a href="" data-popup="tooltip" title="Edit" data-placement="top">
                                            <i class="fa fa-edit largeIcon" id="open_{{ $form->id }}" onclick="return editForm({{ $form->id}})"></i>
                                        </a>

                                        <a href="{{ route('form.destroy', [$form->id]) }}" id="del_{{ $form->id }}" data-popup="tooltip" title="Delete" data-placement="top" onclick="confirmAlert({{ $form->id }})">
                                            <i class="fa fa-trash largeIcon"></i>
                                        </a>
                                    </td>
                                </tr>

                            @empty
                            <tr>
                                <td colspan="6" align="center"><i>No Results Found.</i></td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                    <br>
                     Showing <b>{{($data->currentpage()-1)*$data->perpage()+1}}</b> to <b>{{($data->currentpage()-1) * $data->perpage() + $data->count()}}</b> of  <b>{{$data->total()}}</b> entries
                </div>
            </div>
             <div class="row" align="center">
                {!! $data->render("pagination::bootstrap-4") !!}

            </div>
            <br>
        </div>
    </div>


	<!-- modal_add_form -->
	<div id="modal_add_form" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-image"></i> Add Form
                        <hr>
                    </h5>
                </div>

                <div class="panel-body">
                    {{-- <form method="POST" action="{{ route('form.store') }}" enctype="multipart/form-data" id="form_add"> --}}
                    <form enctype="multipart/form-data" id="form_add">
                        <div class="modal-body">
                            @csrf

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>Form Name</label> 
                                    <input type="text" autocomplete="off" placeholder="Verification form" id="form_group_name" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>

                            <br>
                            <div class="form-group row {{ $errors->has('image') ? 'has-error' : ''}}">
                                <div class="col-md-12 col-xs-12">
                                    <div class="dropzone dropzone-file-area" id="my-dropzone" name="image">
                                        <h4 class="sbold">
                                            Drop files here or <br>click to upload
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- modal_add_form -->

    <!-- modal_edit_form -->
    <div id="modal_edit_form" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gear"></i> Edit Form<hr></h5>
                </div>

                <form id="form_edit_info" action="" method="POST">
                    @csrf
                    @method('put')
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Form Name</label>
                                    <input type="text" placeholder="Form A(1)" name="name" value="" id="display_name" class="form-control" autocomplete="off" required="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- modal_edit_form -->

    <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/dropzone/basic.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/plugins/dropzone/dropzone.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/dropzone/form-dropzone.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        
        var form_arr = [];

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            Dropzone.autoDiscover = false;

            var myDropzone = new Dropzone("div#my-dropzone", {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                acceptedFiles: ".png,.jpg,.jpeg",
                url: "{{ route('ajax.form.upload') }}",
                type: 'post',
                maxFilesize: 10,
                autoProcessQueue: true,
                success: function (file, response) {
                    file.previewElement.classList.add("dz-success");
                    file.previewElement.id = response["name_unique"];

                    form_arr.push({
                        "name": response["name_unique"],
                        "height": response["height"],
                        "width": response["width"],
                    });
                },
                error: function (file, response) {
                    file.previewElement.classList.add("dz-error");
                    $('[class="dz-error-message"]').css("color", "pink");
                    $('[class="dz-error-message"]').css("top", "70px");
                    $('[class="dz-error-message"]').text('Max file exceeded.');
                }
            }).on("maxfilesreached", function(file) {
                $('div#my-dropzone').removeClass('dz-clickable');
                myDropzone.removeEventListeners();
        
            }).on('removedfile', function (file) {
                clearImageData(file.previewElement.id);
                $('div#my-dropzone').addClass('dz-clickable');
                myDropzone.setupEventListeners();
            });
        
            // Remove file if modal is closed4
            $('#modal_add_form').on('hidden.bs.modal', function () {
                myDropzone.getAcceptedFiles().forEach(element => {
                    clearImageData(element.previewElement.id);
                });
            });
        });

        $("#form_add").submit(function() {
            event.preventDefault();

            $.ajax({
                url: '{{ route("form.store") }}',
                type: 'post',
                data: { 
                    "name": $("#form_group_name").val(),
                    "file": form_arr
                },
                success: function (response) {
                    displayMessage("Form create successful", "success");
                }
            });
        });

        function clearImageData(image) {
            // var image = element.previewElement.id;
            if (image) {
                $.ajax({
                    url: '{{ route('ajax.form.delete') }}',
                    type: 'post',
                    data: { image: image },
                    success: function (response) {
                        if(response.status=='ok'){
                            form_arr = form_arr.filter(function( obj ) {
                                return obj.name != image;
                            });
                        } else{
                            new PNotify({ text: "fail to remove image", addclass: 'warning' });
                        }
                    }
                });
                return false;
            }
        } 

        function editForm(id){
            event.preventDefault();
            $.ajax({
                url: "{{ url('form') }}" + "/" + id,
                type:'get',
                success:function(response){
                    $('#display_name').val(response["name"]);
                    $('#form_edit_info').attr('action', ("{{ url('form') }}" + "/" + id));

                    $("#modal_edit_form").modal("toggle");
                }
            });
        }  

        function deleteForm(id) {
            bootbox.confirm("Are you sure to remove this form ?", function(result) {
                if(result){
                    $.ajax({
                        url: "{{ url('form') }}" + "/" + id,
                        type:'delete',
                        success:function(response){
                            new PNotify({ text: response['msg'], addclass: 'bg-' + response['type'] });
                        }
                    });
                }
            });
        }
    </script>


@endsection
