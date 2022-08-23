@extends('layouts.template2')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Access Items
                </h5>
            </div>

            <div class="container-fluid">
                <h6>Unit Owner Details</h6>

                <div class="row">
                    
                    @if($DrawingPlan->unitOwner)
                    <table class="table table-hover table-responsive table-xs">
                        <tr>
                            <td>Owner Name:  {{ $DrawingPlan->unitOwner->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Unit : {{ $DrawingPlan->block . '-' . $DrawingPlan->level . '-' . $DrawingPlan->unit }}</td>
                        </tr>
                    </table>
                    @else
                    <table class="table table-hover table-responsive">
                        <tr>
                            <td><label class="label label-success">VACANT</label></td>
                        </tr>
                    </table>
                    @endif
                </div>
            </div>

        </div>


        <div class="panel panel-flat">
            <div class="container-fluid">
                <h6>Submitted Items</h6>
                <div class="row">
                    <table id="table-data" class="table table-hover table-responsive table-xs">
                        <thead>
                            <tr>
                                <td class="">Code</td>
                                <td class="">Name</td>
                                <td class="">Possessor</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($DrawingPlan->itemSubmitted as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ ucwords($item->possessor) }}</td>
                            </tr>

                            @empty
                            <tr>
                                <td class="text-center" colspan="3">No Items.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel panel-flat">
            <div class="container-fluid">
                <h6>
                    Transactions
                    <a href="{{ url('key-access/transaction/'. $DrawingPlan->id .'/create') }}" class="btn btn-primary pull-right">Add New</a>
                </h6>
                <div class="row">
                    <table id="table-transaction" class="table table-hover">
                        <thead>
                            <tr>
                                <td class="">Transaction Date</td>
                                <td class="">code</td>
                                <td class="">Type</td>
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
        var url = {!! json_encode(route('key-access.index-data')) !!}

        console.log(url)

        $('#table-transaction').DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: url + '/transaction/' +  {!! json_encode($DrawingPlan->id) !!},
            order: [[ 0, "desc" ]], //order by ID to get latest order
            columnDefs: [
                {
                      "targets": [1,2], // your case first column
                      "className": "text-center",
                },
            ],
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'code', name: 'code' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false   },

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
