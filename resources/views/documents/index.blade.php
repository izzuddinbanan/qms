@extends('components.template-limitless.main')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            @include('components.body.panel-head', [
                'title'     => trans('main.document'),
                'route'     => route('document.create'),
                'icon'      => 'icon-files-empty2',
            ])


            @include('components.body.table-yajra', [
                'id'        => 'table-data',
                'class'     => '',
                'columns'    => [trans('main.name') => '',
                                trans('main.file') => 'text-center',
                                trans('main.version') => 'text-center',
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
            ajax: "{{ route('document.index-data') }}",
            columnDefs: [
                { "width": "10%", "targets": [3] },
                { "width": "15%", "targets": [2,1] },
                {
                      "targets": [1,2,3], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'url_file', name: 'file', orderable: false, searchable: false  },
                { data: 'view-version', name: '',  orderable: false, searchable: false  },
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
