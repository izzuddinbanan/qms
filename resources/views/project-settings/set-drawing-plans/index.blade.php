@extends('components.template-limitless.main')

@section('main')
<style type="text/css">
    
    .dropzone {
        min-height: 229px;
    }

</style>

@include('project-settings.components.tab')


<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-map-o"></i> Drawing Plan</h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group">
                    <a href="{{ route('set-general.show', [session('project_id')]) }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-drawing-plan.create') }}" class="btn btn-success" data-popup="tooltip" title="New Drawing Plan" data-placement="top"><i class="icon-add"></i></a>

                    <a href="{{ route('set-link.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>

                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-2 col-xs-2">
                <a href="{{ route('set-drawing-set.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class="icon-circle-left2"></i></b> Back</a>
            </div>
            <div class="col-md-6 col-xs-6">
                <select data-placeholder="Select Drawing Set" class="select-search" name="drawing_set" id="drawing_set" autofocus="" required="">
                    @foreach($listDrawSets as $drawingSet)
                        <option value="{{ $drawingSet->id }}"  {{ ($drawingSet->id == $drawing->id ? 'selected' : '') }}>{{ $drawingSet->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('set-drawing-set.index') }}" type="button" class="btn bg-teal-400 btn-labeled"><b><i class=" icon-folder"></i></b> Batch Upload</a>
            </div> -->

        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="text-center">Seq</td>
                <td class="">Image</td>
                <td class="">Name</td>
                <td class="">Type</td>
                <td class="">Phase</td>
                <td class="">Block</td>
                <td class="">Level</td>
                <td class="">Unit</td>
                <td class="text-center">Action</td>
            </tr>
        </thead>
    </table>
</div>



<!-- modal_update_drawing -->
<div id="modal_update_drawing" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-map-o"></i> Update Drawing Plan<hr></h5>
            </div>

            <form action="{{ route('set-drawing-plan.update', [0] ) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="plan_id" id="plan_id" value="">
                <div class="modal-body">
                    
                    <div class="form-group">
                        <div class="row">
                            <div id="img-plan" align="center"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.planType')</label>
                                <select class="select-search" name="type_plan" id="type_plan">
                                    <option value="unit">Unit</option>
                                    <option value="common">Common</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label>@lang('project.planName')</label>
                                <input type="text" placeholder="South North" name="plan_name" id="plan_name" value="" class="form-control" autofocus="" required="" autocomplete="off">
                            </div>

                            <div class="col-md-4">
                                <label>Sequence</label>
                                <input type="number" placeholder="5" name="plan_seq" id="plan_seq" value="" class="form-control" required="" autocomplete="off" min="1">
                            </div>
                        </div>


                        <div class="row" id="plan-field">
                            <div class="col-md-3">
                                <label>Phase</label>
                                <input type="text" name="phase" id="plan_phase" class="form-control" value="" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label>Block</label>
                                <input type="text" name="block" id="plan_block" class="form-control" value="" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label>Level</label>
                                <input type="text" name="level" id="plan_level" class="form-control" value="" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label>Unit</label>
                                <input type="text" name="unit" id="plan_unit" class="form-control" value="" autocomplete="off">
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

<!-- modal_clone_drawing -->
<div id="modal_clone_drawing" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-map-o"></i> Clone Drawing Plan<hr></h5>
            </div>

            <form action="{{ route('set-drawing-plan.clone') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id_clone" id="plan_id_clone" value="">
                <div class="modal-body">
                    
                    <div class="form-group">
                        <div class="row">
                            <div id="img-plan-clone" align="center"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>No of Copies</label>
                                <input type="number" name="plan_clone" value="" min="1" class="form-control" required="">
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
   
    $(document).ready(function () {

        // DATATABLE
        $(".loader").show();
        $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: "{{ route('set-drawing-plan.index-data') }}",
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            columnDefs: [
                { "width": "10%", "targets": [0] },
                {
                      "targets": [0],
                      "className": "text-center",
                },
            ],
            "order": [[ 0, "asc" ]],
            columns: [
                { data: 'seq', name: 'seq'},
                { data: 'image', name: 'image', orderable: false, searchable: false},
                { data: 'name', name: 'name'},
                { data: 'types', name: 'types'},
                { data: 'phase', name: 'phase'},
                { data: 'block', name: 'block'},
                { data: 'level', name: 'level'},
                { data: 'unit', name: 'unit'},
                { data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            initComplete : function (respon){
                $(".loader").hide();
            },
            "language": {
              "emptyTable": {!! json_encode(trans('main.no-result')) !!}
            }
        });
        // DATATABLE


        $("#drawing_set").change(function(){
            var drawingSet = $(this).val();
            var url = {!! json_encode(route('set-drawing-plan.index')) !!}
            
            window.location = url + '/' + drawingSet;
        });

        $("#type_plan").change(function(){

            switch($(this).val()) {
                case "custom":
                    $("#plan-field").hide();
                    break;
                case "common":
                    $("#plan-field").show();
                    break;
                case "unit":
                    $("#plan-field").show();
                    break;
            }
        });

        $("#plan_block, #plan_level, #plan_unit").on('keyup', function(){

            // var phase = $("#plan_phase").val();
            var block = $("#plan_block").val() == "" ? '' : $("#plan_block").val() + '-';
            var level = $("#plan_level").val() == "" ? '' : $("#plan_level").val() + '-';
            var unit = $("#plan_unit").val();

            var unit_name = block + level + unit;

            $("#plan_name").val(unit_name);

        });

    });

    var allPlan = {!! json_encode($allPlan) !!}

    function editForm(id){
        event.preventDefault();
        
        allPlan.forEach(element => {
            if(element["id"] == id){
                $('#modal_update_drawing').modal('show');

                if(element["types"] == 'custom'){
                    $("#plan-field").hide();
                }else{
                    $("#plan-field").show();
                }
                $("#type_plan").val(element["types"]).trigger('change');
                $("#plan_id").val(element["id"])
                $('#plan_name').val(element["name"])
                $('#plan_seq').val(element["seq"])
                $('#plan_phase').val(element["phase"])
                $('#plan_block').val(element["block"])
                $('#plan_level').val(element["level"])
                $('#plan_unit').val(element["unit"])
                $('#img-plan').html('<img src="' + element["file_url"] + '" class="img-thumbnail" width="50%">')
            }
        });
    }

    function clonePlan(id){
        event.preventDefault();

        allPlan.forEach(element => {
            if(element["id"] == id){
                
                $("#modal_clone_drawing").modal('toggle');
                $("#plan_id_clone").val(element["id"])
                $('#img-plan-clone').html('<img src="' + element["file_url"] + '" class="img-thumbnail" width="50%">')
            }
        });
    }

</script>
    
@endsection
