@extends('components.template-limitless.main')

@section('main')
@include('project-settings.components.tab')

<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-user"></i> @lang('project.tabEmployee')</h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group btn-top">
                    <a href="{{ route('set-employee.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-issue.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <th class="indexNo">@lang('project.tabCurent')</th>
                <th>@lang('project.tabContractor')</th>
                <th style="text-align: center;"><i class="fa fa-gears"></i></th>
            </tr>
        </thead>
    </table>
</div>
        
<!-- modal_edit_contractor -->
<div id="modal_edit_contractor" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-gear"></i> Edit Contractor<hr></h5>
            </div>

            <form action="{{ route('set-contractor.update-contractor') }}" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.tabDisplay')</label>
                                <input type="text" placeholder="ABC Testing" name="display_name" value="" id="display_name" class="form-control" autocomplete="off" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.abvName')</label>
                                <input type="text" placeholder="ABC" name="abbreviation_name" id="abbreviation_name" value="{{ old('abbreviation_name') }}" class="form-control" autocomplete="off" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>@lang('project.description')</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="group_id" id="group_id" value="">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /modal_edit_contractor -->
  
@endsection

@section('script')

<script>

    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {

        $(".loader").show();
        $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: "{{ route('set-contractor.index-data') }}",
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            columnDefs: [
                {
                      "targets": [0,2],
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'is_current', name: 'is_current',orderable: false, searchable: false},
                { data: 'display_name', name: 'display_name'},
                { data: 'action', name: 'action',orderable: false, searchable: false},

            ],
            initComplete : function (respon){
                $(".loader").hide();
            },
            "language": {
              "emptyTable": {!! json_encode(trans('main.no-result')) !!}
            }
        });


    });

    function isCurrent(id, isCurrent){
        event.preventDefault();

        if ( isCurrent == true) {
            $.ajax({
                url:"{!! url('step6' ) !!}" + "/" + id,
                type:'delete',
                data:{'group_id' : id },
                success:function(response){
                    
                    if(response['errors']){
                        new PNotify({
                            title: 'Error',
                            text: 'Something Error.',
                            delay: 2000,
                            icon: 'icon-warning22',
                            type: 'error'
                        });
                    }else{
                        new PNotify({
                            title: 'Success',
                            text: 'The contractor successfully remove from this project.',
                            delay: 1500,
                            icon: 'icon-checkmark3',
                            type: 'success'
                        });
                        $(".isCurrent-" + id).removeClass('fa-check-square-o').addClass('fa-square-o');

                    }

                }
            });
            
        }else{

            $.ajax({
                url:"{{ route('set-contractor.save-contractor') }}",
                type:'POST',
                data:{'group_id' : id },
                success:function(response){

                    if(response['errors']){
                        new PNotify({
                            title: 'Error',
                            text: 'Something Error.',
                            delay: 2000,
                            icon: 'icon-warning22',
                            type: 'error'
                        });
                    }else{
                        new PNotify({
                            title: 'Success',
                            text: 'Record saved successfully.',
                            delay: 1500,
                            icon: 'icon-checkmark3',
                            type: 'success'
                        });

                        $(".isCurrent-" + id).removeClass('fa-square-o').addClass('fa-check-square-o');

                    }

                }
            });

            
        }
    }

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