@extends('components.template-limitless.main')

@section('main')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-clipboard6"></i> @lang('audit.header')</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="">Date</td>
                <td class="">User</td>
                <td class="text-center">Email</td>
                <td class="text-center">Event</td>
                <td class="text-center">Model</td>
                <td class="text-center">Old Value</td>
                <td class="text-center">New Value</td>
            </tr>
        </thead>
    </table>


</div>


<!-- MODAL SECTION-->
<div id="modal-view-audit" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title"><i class="icon-clipboard6"></i> Audit Details</h5>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <!-- <div id="audit-data"></div> -->
                    <textarea  id="audit-data" class="form-control" readonly=""></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 10px;">
                <button type="button" class="btn btn-danger" data-dismiss="modal" style="width: 100%">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL SECTION-->

@endsection

@section('script')


<script>

    var dataAudit = [];
    $(document).ready(function() {

        $(".loader").show();
        var table = $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            order: [[ 0, 'desc' ]],
            dom: '<"datatable-header"fl><"datatable-scroll-lg"t><"datatable-footer"ip>',
            ajax: "{{ route('audit.index-data') }}",
            order: [[ 0, "desc" ]], //order by ID to get latest order
            columnDefs: [
                { "width": "10%", "targets": [3] },
                { "width": "15%", "targets": [2,1] },
                { "width": "5%", "targets": [5,6] },
                {
                      "targets": [0, 1, 2, 3, 4, 5],
                      "className": "text-center",
                }
            ],
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'name', name: 'users.name' },
                { data: 'email', name: 'users.email' },
                { data: 'event', name: 'event' },
                { data: 'auditable_type', name: 'auditable_type' },
                { data: 'old_values', name: 'old_values',  orderable: false},
                { data: 'new_values', name: 'new_values' },
            ],
            initComplete : function (respon){
                $(".loader").hide();
            },
            "language": {
              "emptyTable": {!! json_encode(trans('main.no-result')) !!}
            }
        });

        $('.dataTable').on('click', 'tbody tr', function() {
            
            dataAudit = table.row(this).data();

            var details = JSON.stringify(dataAudit, null, 2)

            var count = Object.keys(dataAudit).length;
            //adjust the text area based on legth
            $("#audit-data").attr('rows', count+2);

            
            $("#audit-data").text(details);
            $("#modal-view-audit").modal('toggle');

        });
});
</script>
    
@endsection
