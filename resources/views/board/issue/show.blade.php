@extends('components.template-limitless.main')

@section('main')
    <style>
        th, td{
            font-size: 11.5px;
        }
    </style>

    <div class="content">
        <div class="panel panel-flat">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <form id="form_download_report" action="{{ route('issue.download', [$issue->id]) }}" target="_blank" style="display: none;"></form>
                
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <a id="back_btn" href="#" class="label label-rounded label-icon bg-theme-2" style="color: white;"><i class="icon-arrow-left8"></i> Back </a>&nbsp;&nbsp; {{ $issue->location->name }} <small>({{ $issue->location->reference }})</small>
                            <button type="button" class="btn bg-theme visible-md visible-lg pull-right" onclick="getElementById('form_download_report').submit();"> Download </button>    
                            <button style="margin-top: 10px" type="button" class="btn bg-theme visible-xs" onclick="getElementById('form_download_report').submit();"> Download </button>    
                        </h5>
                    </div>

                    <div class="panel-body">
                        <table class="table table-xxs">
                            <tr>
                                <th colspan="2" style="text-align: center;">Issue Details</th>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ $issue->issue->name }}</td>
                            </tr>
                            <tr>
                                <th>Comment</th>
                                <td>{{ $issue->remarks }}</td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td>{{ $issue->priority ?? 'Not Set' }}</td>
                            </tr>
                            <tr>
                                <th>Reference ID</th>
                                <td>{{ $issue->reference }}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td> {{ $issue->location->drawingPlan->phase ? 'Phase ' . $issue->location->drawingPlan->phase : '' }} 
                                     {{ $issue->location->drawingPlan->block ? 'Block ' . $issue->location->drawingPlan->block : '' }} 
                                     {{ $issue->location->drawingPlan->level ? 'Level ' . $issue->location->drawingPlan->level : '' }} 
                                     {{ $issue->location->drawingPlan->unit ? 'Unit ' . $issue->location->drawingPlan->unit : '' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Phase</th>
                                <td>{{ $issue->location->drawingPlan->phase }}</td>
                            </tr>
                            <tr>
                                <th>Contractor</th>
                                <td>{{ isset($issue->contractor) ? $issue->contractor->display_name . ' (' .  $issue->contractor->abbreviation_name . ')' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Target Completion Date</th>
                                <td>{{ $issue->due_by }}</td>
                            </tr>
                            <tr>
                                <th>Created Date</th>
                                <td>{{ $issue->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-xs-12">
                    <br>
                    <div id="image_container" align="center">
                        <img width="70%" src="{{ $issue->location->drawingPlan->file }}" class="img-responsive" id="drawing_plan_image">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel-heading">
                        <legend class="panel-title">
                            Gallery
                        </legend>
                    </div>

                    <div class="panel-body" style="padding-top: 0px;">
                        <table class="table table-xxs">
                            @forelse($history as $info)
                                @foreach($info->images as $image)
                                    <a href="{{ url('uploads/issues/' . $image->image) }}" data-popup="lightbox" rel="gallery">
                                        <img src="{{ url('uploads/issues/' . $image->image) }}" class="img img-responsive" style="height: 50px;width: 50px;display: inline;">
                                    </a>
                                @endforeach
                            @empty
                            @endforelse
                        </table>
                    </div>
                </div>                
            </div>

            {{-- <div class="row">
                <div class="col-lg-12 col-md-12 col-xs-12">
                @forelse($history as $gallery)
                    @if($gallery->image != null)
                    <div class="thumbnail">
                        <div class="thumb">
                            <img src="{{ url('assets/images/placeholder.jpg') }}" alt="" class="img img-responsive" style="height: 50px;width: 50px;">
                            <div class="caption-overflow">
                                <span>
                                    <a href="{{ url('assets/images/placeholder.jpg') }}" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>
                                    <a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforelse
                </div>
            </div>
            <br> --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-body">
                        <ul class="media-list chat-stacked content-group">                            
                            @php
                                $current_status = "";
                            @endphp
                            @forelse($history as $info)
                                @if($info->status == $current_status)
                                    <hr width="70%" style="margin-bottom: 0px;margin-top: 10px;">
                                @endif
                                @if($info->status != $current_status)
                                    <li class="media date-step content-divider text-muted">
                                        <span><button class="btn btn-default" style="background-color: {{ $info->status->internal_color }};color: white;"> <strong>{{ strtoupper($info->status->internal) }}</strong> </button></span>
                                    </li>
                                    @php
                                        $current_status = $info->status;
                                    @endphp
                                @endif

                                <li class="media">
                                    <div class="media-left">    
                                        @if ($info->user)
                                        <img src="{{ ($info->user->avatar == null ? url('assets/images/placeholder.jpg') : url('uploads/avatars/' . $info->user->avatar)  )  }}" class="img-circle img-md" alt=""></div>    
                                        @endif
                                    <div class="media-body">
                                        <div class="media-heading">
                                            @if ($info->user)
                                            <a href="javascript:void()" class="text-semibold viewProfile" id="{{ $info->user->id }}">{{ $info->user->name }}</a>
                                            @endif
                                            <span class="media-annotation pull-right">{{$info->created_at->format('d M Y, h:i a')}} &nbsp;&nbsp;  {{$info->created_at->diffForHumans()}} <a href="#"><i class="icon-watch2 position-left text-muted"></i></a></span>
                                        </div>
                                        {{ $info->remarks }}
                                        <br>
                                        @foreach($info->images as $image)
                                            <a href="{{ url('uploads/issues/' . $image->image) }}" data-popup="lightbox" rel="gallery">
                                                <img src="{{ url('uploads/issues/' . $image->image) }}" class="img img-responsive" style="height: 50px;width: 50px;display: inline;">
                                            </a>
                                        @endforeach
                                           
                                    </div>
                                </li>
                                
                            @empty
                            
                            @endforelse

                        </ul>

                        <form action="{{ route('addInfo') }}" method="POST">
                            @csrf
                            <textarea name="info" class="form-control content-group" rows="3" cols="1" placeholder="Enter your message..."></textarea>
                            <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                            <div class="row">
                                <div class="col-xs-6">
                                </div>

                                <div class="col-xs-6 text-right">
                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-circle-right2"></i></b> Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script type="text/javascript">

    $(window).on('resize',function() {
        $("#marker").remove();
        rePositionMarker();
    });
    
    $('.sidebar-main-toggle').click(function() {
        setTimeout(() => {
            $(window).trigger('resize');
        }, 10);
    });

    $(document).ready(function() {

        var url = localStorage.getItem('prev_url') ? localStorage.getItem('prev_url') : "{!! url('/') !!}";

        $("#back_btn").attr("href", url);
    });

    function rePositionMarker(){
        var issue = {!! json_encode($issue) !!};
        var marker_icon = getIssueMarkerIcon(issue.status_id);
        var marker_position_x = issue.position_x,
            marker_position_y = issue.position_y,
            drawing_plan_height = issue.location.drawing_plan.height,
            drawing_plan_width = issue.location.drawing_plan.width,
            web_width = $("#drawing_plan_image").width(),
            web_height = $("#drawing_plan_image").height(),
            web_position = $("#drawing_plan_image").position();
        
        $("#image_container").append('<img id="marker" src="' + marker_icon + '" />');
        var pos_x = (marker_position_x * web_width / drawing_plan_width) + web_position.left - 15;
        var pos_y = (marker_position_y * web_height / drawing_plan_height) + web_position.top - 30;

        $("#marker")
            .css("position", "absolute")
            .css("width", '30px')
            .css("height", '30px')
            .css("top", pos_y)
            .css("left", pos_x);  
    }
</script>
@endsection
