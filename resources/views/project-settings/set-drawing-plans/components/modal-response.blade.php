
@if(session('success_upload'))
<!-- Modal with h6 -->
<div id="report_batch_upload" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title">@lang('project.batchUpload')</h6>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <i class="icon-checkmark3 text-success"></i>
                        <b>Success</b><hr style="margin-top: 0px;">
                        <p style="padding-left: 12px;"><span class="status-mark border-success"></span> {{ session('total_success') }} from {{ session('total_files') }} of drawing plan <b>succesfully</b> uploaded.</p>

                        @if(isset(session('data')["success"]))
                        @foreach(session('data')["success"] as $key => $success)
                            <p style="padding-left: 19px;"><span class="status-mark border-info"></span> <b>{{ ucwords($key) }} Type</b></p>
                            
                            @foreach($success as $type)

                                <p style="padding-left: 30px;">- {{ $type["basename"] }}</p>
                            @endforeach
                            
                        @endforeach
                        @endif

                    </div> 
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <i class="icon-cross2 text-danger"></i>
                        <b>Fails</b><hr style="margin-top: 0px;">
                        <p style="padding-left: 12px;"><span class="status-mark border-danger"></span> {{ (session('total_files') - session('total_success')) }} from {{ (session('total_files')) }} of drawing plan <b>unsuccesfully</b> uploaded.</p>
                        
                        @if(isset(session('data')["fail"]))
                        @foreach(session('data')["fail"] as $key => $fail)
                            <p style="padding-left: 19px;"><span class="status-mark border-info"></span> <b>{{ ucwords($key) }} Type</b></p>
                                
                                @php
                                    $message = "";
                                @endphp
                            @foreach($fail as $type)

                                @if($message != $type["message"])
                                <p style="padding-left: 30px;"> <small style="color: red;">{{ $type["message"] }}</small></p>
                                @endif
                                @php
                                    $message = $type["message"];
                                @endphp
                                <p style="padding-left: 35px;">- {{ $type["basename"] }}</p>
                            @endforeach
                            
                        @endforeach
                        @endif

                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /modal with h6 -->
@endif