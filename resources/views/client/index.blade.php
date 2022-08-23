@extends('components.template-limitless.main')

@section('main')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-user-tie"></i> Clients</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('client.create') }}" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-file-plus"></i></b> @lang('main.add_new')</a>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="text-center">Logo</td>
                <td class="">Name</td>
                <td class="">Abv. Name</td>
                <td class="text-center">Created At</td>
                <td class="text-center">Action</td>
            </tr>
        </thead>
    </table>


</div>

@endsection


@section('script')

<script>
    $(document).ready(function() {

        $(".loader").show();
        $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: "{{ route('client.index-data') }}",
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            columnDefs: [
                { "width": "12%", "targets": [0] },
                { "width": "15%", "targets": [4] },
                {
                      "targets": [0, 3, 4],
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'image_logo', name: 'image_logo', orderable: false, searchable: false},
                { data: 'name', name: 'name'},
                { data: 'abbreviation_name', name: 'abbreviation_name'},
                { data: 'created_at', name: 'created_at'},
                { data: 'action', name: 'action',  orderable: false, searchable: false},

            ],
            initComplete : function (respon){
                $(".loader").hide();
            },
            "language": {
              "emptyTable": {!! json_encode(trans('main.no-result')) !!}
            }
        });

    });
    
    function switchALert(id,e){
        
        e.preventDefault();
        var url = $("#switch_"+id).attr('href')
        swal({
            title: "switch to this user?",
            text: "",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "green",
            confirmButtonText: "Yes, switch now!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true,
            showLoaderOnConfirm: true
        },
        function(isConfirm) {
            if (isConfirm) {
                window.location = url;
            }
        });
    }
</script>
@endsection