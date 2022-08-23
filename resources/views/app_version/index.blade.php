@extends('components.template-limitless.main')

@section('main')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-mobile"></i> App Version</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('app-version.create') }}" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-file-plus"></i></b> @lang('main.add_new')</a>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="text-center">os</td>
                <td class="">Version</td>
                <td class="">Type</td>
                <td class="text-center">Status</td>
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
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            ajax: "{{ route('app-version.index-data') }}",
            order: [[ 4, "desc" ]], //order by ID to get latest order
            columnDefs: [
                { "width": "10%", "targets": [3] },
                { "width": "15%", "targets": [2,1] },
                {
                      "targets": [0, 1, 2, 3, 4, 5],
                      "className": "text-center",
                },
                // {
                // "targets": [ 0 ],
                //     "visible": false,
                //     "searchable": false
                // },
            ],
            columns: [
                { data: 'os', name: 'os' },
                { data: 'version', name: 'version'},
                { data: 'type', name: 'type'},
                { data: 'status-label', name: 'status'},
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
</script>
@endsection
