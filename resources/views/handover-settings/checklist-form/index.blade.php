@extends('components.template-limitless.main')

@section('main')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-file-check2"></i> Checklist form</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('checklist-form.create') }}" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-file-plus"></i></b> @lang('main.add_new')</a>
            </div>
        </div>
    </div>

    <table id="table-data" class="table table-hover table-responsive">
        <thead>
            <tr>
                <td class="">Name</td>
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
            ajax: "{{ route('checklist-form.index-data') }}",
            columnDefs: [
                {
                      "targets": [1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at'},
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
