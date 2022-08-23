@extends('components.template-limitless.main')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            @include('components.body.panel-head', [
                'title'     => trans('main.group-form'),
                'route'     => route('group-form.create'),
                'icon'      => 'icon-stack',
            ])


            @include('components.body.table-yajra', [
                'id'        => 'table-data',
                'class'     => '',
                'columns'    => [trans('main.name') => '',
                                trans('main.total') => 'text-center',
                                trans('main.action') => 'text-center',
                                ],
            ])

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
            ajax: "{{ route('group-form.index-data') }}",
            columnDefs: [
                { "width": "10%", "targets": 2 },
                { "width": "15%", "targets": 1 },
                {
                      "targets": [1], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'total-label', name: 'total' },
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
