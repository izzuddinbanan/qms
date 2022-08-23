@extends('components.template-limitless.main')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            @include('components.body.panel-head', [
                'title'     => trans('main.document'),
                'desc'      => $doc->name,
                'icon'      => 'icon-files-empty2',
                'back_route' => route('document.index'),
            ])


            @include('components.body.table-yajra', [
                'id'        => 'table-data',
                'class'     => '',
                'columns'    => [trans('main.uploaded-at') => 'text-center',
                                trans('main.publish') => 'text-center',
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
            ajax: "{{ route('document.show-data', [$doc->id]) }}",
            columnDefs: [
                {
                      "targets": [0,1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'uploaded_at', name: 'updated_at'},
                { data: 'publish-label', name: 'publish'},
                { data: 'action', name: '', orderable: false, searchable: false },

            ],
            // order: [[ 0, "desc" ]],
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
