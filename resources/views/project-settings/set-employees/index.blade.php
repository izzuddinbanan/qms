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
                    <a href="{{ route('set-location.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-contractor.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <th class="indexNo">@lang('project.tabCurent')</th>
                <th >@lang('project.tabName')</th>
                <th >@lang('project.tabEmail')</th>
                <th >@lang('project.tabRole')</th>
                <th>@lang('project.action')</th>
            </tr>
        </thead>
    </table>
</div>
        
  
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
            ajax: "{{ route('set-employee.index-data') }}",
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            columnDefs: [
                { "width": "5%", "targets": [0] },
                { "width": "15%", "targets": [4] },
                {
                      "targets": [0, 3, 4],
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'is_current', name: 'is_current',orderable: false, searchable: false},
                { data: 'users.name', name: 'users.name'},
                { data: 'users.email', name: 'users.email'},
                { data: 'roles.display_name', name: 'roles.display_name'},
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

    function isCurrent(id, isCurrent){
        event.preventDefault();

        if ( isCurrent == true) {
            $.ajax({
                url:"{{ route('set-employee.remove-user') }}",
                type:'POST',
                data:{'role_id' : id },
                success:function(response){
                    console.log(response);
                    if(response['errors']){
                        displayMessage(response['errors'], 'error', reload = true);
                    }else{
                        new PNotify({
                            title: 'Success',
                            text: 'User successfully remove from this project.',
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
                url:"{{ route('set-employee.save-user') }}",
                type:'POST',
                data:{'role_id' : id },
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
</script>
@endsection