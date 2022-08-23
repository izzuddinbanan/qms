@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >

            @include('components.body.panel-head', [
                'title'     => trans('main.digital-form'),
                'route'     => route('form-status.create', [$formGroup->id]),
                'icon'      => 'icon-insert-template',
                'back_route' => route('form.index'),
            ])

            @include('components.body.table-yajra', [
                'id'        => 'table-data',
                'class'     => '',
                'columns'    => [trans('main.name') => '',
                                'color' => 'text-center',
                                trans('main.action') => 'text-center',
                                ],
            ])
        </div>
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
            ajax: "{{ route('form-status.index-data', [$formGroup->id]) }}",
            columnDefs: [
                { "width": "10%", "targets": [2] },
                {
                      "targets": [1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'color', name: '',  orderable: false, searchable: false  },
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
