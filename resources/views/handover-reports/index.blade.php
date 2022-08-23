@extends('layouts.template2')

@section('main')
    <style type="text/css">
        .center-inTable{
            text-align: center;
        }
    </style>
    
    <div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-insert-template"></i> Handover Reports</h4>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="">Name</td>
                <td class="">Handover Status</td>
                <td class="">Handover Date</td>
                <td class="">SPA Date</td>
                <td class="">VP Date</td>
                <td class="">DLP Expiry Date</td>
                <td class="">Action</td>
            </tr>
        </thead>
    </table>
</div>

<script>
    $(document).ready(function() {

        $(".loader").show();
        $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            ajax: "{{ route('handoverreport.indexdata') }}",
            columnDefs: [
                {
                      "targets": [1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'status-hand-over', name: 'status-hand-over'},
                { data: 'handover-date', name: 'handover-date'},
                { data: 'spa-date', name: 'spa-date' },
                { data: 'vp-date', name: 'vp-date' },
                { data: 'dlp-expiry-date', name: 'dlp-expiry-date' },
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
</script>

@endsection
