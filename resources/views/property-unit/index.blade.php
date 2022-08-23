@extends('layouts.template2')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-map5"></i> Property Unit
                </h5>
            </div>



            <div class="container-fluid">
                <div class="row">
                    <table id="table-data" class="table table-hover">
                        <thead>
                            <tr>
                                <td class="">Unit</td>
                                <td class="">Owner</td>
                                <td class="">Action</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    $(document).ready(function() {

        $(".loader").show();
        $('#table-data').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: "{{ route('property-unit.index-data') }}",
            columnDefs: [
                {
                      "targets": [1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'owner', name: 'unitOwner.name'},
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
</script>

@endsection
