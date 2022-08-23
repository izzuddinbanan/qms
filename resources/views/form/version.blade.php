@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-users2"></i> Digital Forms
                </h5>
            </div>
    
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn bg-primary" href="{{ route('version.duplicate', [$form->id]) }}" id="duplicate_btn" onclick="duplicateVersion()">
                            <i class="fa fa-plus"></i>  New Version
                        </a>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-framed">
                        <thead>
                            <tr class="">
                                <th class="col-md-3">Version </th>
                                <th class="col-md-2">Created at </th>
                                <th class="col-md-1">Status </th>
                                <th class="col-md-2 action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp

                            @forelse($data as $version)
                                <tr>
                                    <td >{{ $version->version }}</td>
                                    <td >{{ $version->created_at }}</td>
                                    <td >
                                        <span class="label {{ $version->status == 1 ? 'bg-success' : ($version->status == 2 ? 'bg-blue' : 'bg-danger') }}">{{ $version->status_name }}</span>
                                    </td>
                                    
                                    <td align="center"> 
                                        <a class="btn btn-primary" 
                                            @if ($version->status != 2) 
                                                disabled
                                            @else 
                                                href="{{ route('version.show', [$version->id]) }}" data-popup="tooltip" title="Configure" data-placement="top"
                                            @endif > <i class="fa fa-edit"></i>
                                        </a>

                                        <a class="btn btn-info" 
                                            @if ($version->status != 2) 
                                                disabled
                                            @else 
                                                href="{{ route('version.publish', [$version->id]) }}" id="btn_publish" data-popup="tooltip" title="Publish" data-placement="top" onclick="publishVersion()"
                                            @endif > <i class="fa fa-upload"></i> 
                                        </a>

                                        @if ($version->status == 2) 
                                            @if ($data->count() > 1)
                                            <a class="btn btn-danger" data-popup="tooltip" title="Delete" data-placement="top" onclick="deleteVersion({{ $version->id }})"> 
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                            @empty
                            <tr>
                                <td colspan="6" align="center"><i>No Results Found.</i></td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                    <br>
                     Showing <b>{{($data->currentpage()-1)*$data->perpage()+1}}</b> to <b>{{($data->currentpage()-1) * $data->perpage() + $data->count()}}</b> of  <b>{{$data->total()}}</b> entries
                </div>
            </div>
             <div class="row" align="center">
                {!! $data->render("pagination::bootstrap-4") !!}
            </div>
            <br>
        </div>
    </div>

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });    

        function duplicateVersion() {
            var href = $("#duplicate_btn").attr('href');
            event.preventDefault();
            bootbox.confirm("Are you sure to create new version ?", function(result) {
                if (result) {
                    $.ajax({
                        url: "{{ route('version.duplicate', [$form->id]) }}",
                        type:"POST",
                        success:function(response) {
                            if (response["success-message"]) {
                                displayMessage(response["success-message"], "success");
                            } else {
                                displayMessage(response["fail-message"], "warning");
                            }
                        }
                    });
                }
            });
        }

        function deleteVersion(id) {
            event.preventDefault();
        
            bootbox.confirm("Are you sure to delete this version ?", function(result) {
                
                if(result == true){
                    $.ajax({
                        url : "{{ url('form/version') }}" + "/" + id,
                        type: "DELETE",
                        success:function(response) {
                            if (response["success-message"]) {
                                displayMessage("Setup store successful", "success");
                            } else {
                                displayMessage("Fail to store setup", "warning", false);
                            }
                        }
                    });
                }
            });
        }

        function publishVersion() {
            var href = $("#btn_publish").attr('href');

            event.preventDefault();
            bootbox.confirm("Are you sure to publish this version ? Other version will be deactive once you confirmed.", function(result) {
                if(result == true){
                    window.location = href;
                }
            });
        }
    </script>


    

@endsection
