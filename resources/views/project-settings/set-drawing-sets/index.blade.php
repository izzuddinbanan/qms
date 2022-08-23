@extends('components.template-limitless.main')

@section('main')

@include('project-settings.components.tab')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="icon-folder"></i> Drawing Set
                    <small style="cursor: pointer;"><i class="fa fa-question-circle-o" data-popup="tooltip" title=" @lang('project.headerDrawingTitle')" data-placement="top"></i></small>

                </h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group">
                    <a href="{{ route('set-general.show', [session('project_id')]) }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="" type="button" data-toggle="modal" data-target="#modal_add_drawing" class="btn btn-success" data-popup="tooltip" title="@lang('project.newDrawing')" data-placement="top"><i class="icon-add"></i></a>

                    <a href="{{ route('set-link.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>

                </div>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="text-center">Seq</td>
                <td class="">Name</td>
                <td class="">Total Drawing</td>
                <td class="text-center">Action</td>
            </tr>
        </thead>
    </table>
</div>

<!-- modal_add_drawing -->
<div id="modal_add_drawing" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h5 class="modal-title"><i class="fa fa-folder"></i> @lang('project.headerDrawing')<hr></h5>
            </div>

            <form action="{{ route('set-drawing-set.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.planName')</label>
                                <input type="text" placeholder="e.g South North" name="drawing_name" value="" class="form-control" autofocus="" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>{{ $key->display_name }} Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="key_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>{{ $es->display_name }} Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="es_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Close & Handover Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="close_and_handover_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($digital_forms as $digital_form)
                                    <option value="{{ $digital_form->id }}">{{ $digital_form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <a href="javascript:void(0)" type="button" class="btn bg-danger-400 btn-labeled" data-dismiss="modal"><b><i class="icon-close2"></i></b> @lang('general.close')</a>

                    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /modal_add_drawing -->

<!-- modal_update_drawing -->
<div id="modal_update_drawing" class="modal fade">
    <div class="modal-dialog modal-sm"">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-folder"></i> @lang('project.updateDrawingSet')<hr></h5>
            </div>

            <form action="{{ route('set-drawing-set.update', [0] ) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="drawing_id" id="drawing_id" value="">
                <div class="modal-body">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.planName')</label>
                                <input type="text" placeholder="South North" name="drawing_name" id="drawing_name" value="" class="form-control" autofocus="" required="" autocomplete="off">
                            </div>

                            <div class="col-md-12">
                                <label>Sequence</label>
                                <input type="number" placeholder="5" name="seq" id="seq" value="" class="form-control" required="" autocomplete="off" min="1">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>{{ $key->display_name }} Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="key_form"  id="key_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>{{ $es->display_name }} Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="es_form" id="es_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Close & Handover Form</label>
                                <select data-placeholder="Please Select" class="select-size-sm" name="close_and_handover_form" id="close_and_handover_form" autofocus="">
                                    <option value="">Please Select</option>
                                    @foreach($digital_forms as $digital_form)
                                    <option value="{{ $digital_form->id }}">{{ $digital_form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <a href="javascript:void(0)" type="button" class="btn bg-danger-400 btn-labeled" data-dismiss="modal"><b><i class=" icon-close2"></i></b> @lang('general.close')</a>

                    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /modal_update_drawing -->

@endsection



@section('script')

<script>
    $(document).ready(function() {

        $(".loader").show();
        $('#table-data').DataTable({
            rowReorder: true,
            serverSide: true,
            processing: true,
            responsive: true,
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            ajax: "{{ route('set-drawing-set.index-data') }}",
            columnDefs: [
                { "width": "10%", "targets": [0] },
                {
                      "targets": [0, 2, 3],
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'seq', name: 'seq'},
                { data: 'name', name: 'name'},
                { data: 'total-drawing', name: 'total-drawing', orderable: false, searchable: false},
                { data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            initComplete : function (respon){
                $(".loader").hide();
            },
            "language": {
              "emptyTable": {!! json_encode(trans('main.no-result')) !!}
            }
        });

    });


    var drawing_set = {!! json_encode($drawing_set) !!}

    function editForm(id){
        event.preventDefault();
        
        drawing_set.forEach(element => {
            if(element["id"] == id){
                $('#drawing_name').val(element["name"]);
                $('#seq').val(element["seq"]);
                $('#drawing_id').val(id);
                $('#key_form').val(element["handover_key_id"]).trigger('change');
                $('#es_form').val(element["handover_es_id"]).trigger('change');
                $('#close_and_handover_form').val(element["handover_form"]).trigger('change');
                $('#modal_update_drawing').modal('show');
            }
        });



    }

</script>
@endsection
