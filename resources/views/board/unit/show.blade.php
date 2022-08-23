@extends('components.template-limitless.main')

@section('main')
    <style> 

    </style>

    <div class="content">
        <div class="panel panel-flat">
            <form id="form_download_report" action="{{ route('unit.individual.export', [$unit->id]) }}" target="_blank" style="display: none;"></form>

            <div class="panel-heading unit-header">
                <div class="row"> 
                    <div class="col-md-12">
                        <a id="back_btn" href="{{ $type == 'common' ? url('commonarea') : url('unit') }}" class="label label-rounded label-icon bg-theme-2" style="color: white;"><i class="icon-arrow-left8"></i> Back </a>
                    </div>    
                    <br>
                    <div class="col-md-6 col-xs-6"> 
                        <div class="media no-margin">
                            <div class="media-body">
                                <h4 class="no-margin"><b class="text-uppercase" style="font-size: 16px; color: gray"> Block {{ $unit->block }}, Level {{ $unit->level }}</b></h4>
                                <span class="text-uppercase">
                                    <b style="font-size: 26px;">{{ $unit->block . '-' . $unit->level . '-' . $unit->unit}}</b>
                                </span>
                            </div>
                        </div>
                        <br>
                        <table class="table table-xxs" style="margin-bottom: 20px;">
                            <tr style="font-size: 14px">
                                <td style="font-weight: bold">Project</td>
                                <td> {{ $unit->project }} </td>
                            </tr>
                        </table>

                        <p>Unit Details</p>
                        <form method="POST" action="{{ route('unit.update-details', [$unit->id]) }}">
                            @csrf
                            <table class="table table-xxs">
                                <tr style="font-size: 14px">
                                    <td style="font-weight: bold">Car Park Bay No. </td>
                                    <td>
                                        <textarea class="form-control" name="car_park">{{ $unit->car_park }}</textarea>
                                    </td>
                                </tr>
                                <tr style="font-size: 14px">
                                    <td style="font-weight: bold">Access Card(s) </td>
                                    <td>
                                        <textarea class="form-control" name="access_card">{{ $unit->access_card }}</textarea>
                                    </td>
                                </tr>
                                <tr style="font-size: 14px">
                                    <td style="font-weight: bold">Key Fob(s) </td>
                                    <td>
                                        <textarea class="form-control" name="key_fob">{{ $unit->key_fob }}</textarea>
                                    </td>
                                </tr>
                                <tr style="font-size: 14px">
                                    <td colspan="2" class="text-right">
                                        <button type="submit" class="btn btn-primary btn-xs">Update</button>
                                    </td>
                                </tr>
                            </table>
                        </form>


                    </div>

                    <div class="col-md-6 col-xs-6 unit-detail">
                        <div id="image_container" align="center"> 
                            <img style="width: 70%" id="drawing_plan_image" src="{{ $unit->drawing_plan_image }}" class="img-responsive">
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if ($unit->ready_to_handover == 0)
                    <a href="{{url('closeAndHandover', ['id'=>$unit->id])}}" class="btn btn-primary">Close & Handover</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 col-xs-9 col-xs-9">
                <div class="panel panel-flat">
                    <div class="panel-body" style="padding: 10px 0px 0px 0px">
                        <div id="issues_listing" style="padding-top: 6px;">
                    
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-3 col-lg-3">
                <div class="panel panel-flat">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">                                    
                                <button type="button" class="btn bg-theme-2 btn-sm" onclick="clearFilter()"> Clear </button>                            
                            </div>

                            <div class="col-md-12" style="margin-top: 20px! important"> 
                                <form id="form_filter_listing">
                                    <fieldset>
                                        <legend class="text-semibold">Filter Option</legend>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Status Filter</legend>
                                                    <select data-placeholder="Select Status" class="select" id="select_status">
                                                        <option value="0"> All </option> 
                                                        @foreach ($status as $key => $val)
                                                            <option value="{{ $key }}"> {{ $val }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>  
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Location Filter</legend>
                                                    <select data-placeholder="Select Location" class="select" id="select_location">
                                                        <option value="0"> All </option> 
                                                        @foreach ($location as $key => $val)
                                                            <option value="{{ $key }}"> {{ $val }} </option>    
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Contractor Filter</legend>
                                                    <select data-placeholder="Select Contractor" class="select" id="select_contractor">
                                                        <option value="0"> All </option> 
                                                        @foreach ($contractor as $key => $val)
                                                        <option value="{{ $key }}"> {{ $val }} </option>    
                                                        @endforeach
                                                    </select>
                                                </div>                        
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">

    var unit = {!! json_encode($unit) !!};
    var query_filter = {
        contractor : 0,
        location : 0,
        status : 0,
    };

    var current_issues = [];

    var current_page = 1;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
        }
    });

    $('.sidebar-main-toggle').click(function() {
        setTimeout(() => {
            $(window).trigger('resize');
        }, 10);
    });

    $(document).ready(function(){
        
        $('.select').select2(); 

        localStorage.setItem('prev_url', window.location.href);
        $('body').addClass('sidebar-xs');
        getIssuesListing();

        $("#select_status").change(function(event) {
            query_filter['status'] = event.target.value;
            current_page = 1;
            getIssuesListing();            
        });

        $("#select_location").change(function(event) {
            query_filter['location'] = event.target.value;
            current_page = 1;
            getIssuesListing();            
        });

        $("#select_contractor").change(function(event) {
            query_filter['contractor'] = event.target.value;
            current_page = 1;
            getIssuesListing();            
        });

        $(window).on('resize',function() {
            setupMarker();
        });
    });

    function exportListing(type) {
        $("#form_download_report").html("");
        
        $("#form_download_report").append("<input name='type' value= " + type + " />");
        for (var i in query_filter) {
            $("#form_download_report").append("<input name=" + i + " value= " + query_filter[i] + " />");
        }

        setTimeout(() => {
            $("#form_download_report").submit();
        }, 500);
    }

    function getIssuesListing() {
        $(".loader").show();
        $.ajax({
            url:"{{ url('units/issues/listing') }}" + "/" + {!! $unit->id !!} + '?page=' + current_page,
            type:"GET",
            data: query_filter,
            success:function(response) {
                $(".loader").hide();
                $("#issues_listing").html("").html(response['view']);

                $('#issues_listing table tbody tr').click(function () {
                    window.location.href = $(this).data('url');
                });

                
                current_issues = response['issues']['data'];
                setupMarker();
            }
        });
    }

    function gotoPage(page) {
        current_page = page;
        getIssuesListing();
    }

    function prevPage() {
        current_page--;
        getIssuesListing();
    }

    function nextPage() {
        current_page++;
        getIssuesListing();
    }

    function clearFilter() {
        query_filter = { 
            contractor : 0,
            location : 0,
            status : 0
        };

        $('#select_status, #select_location, #select_contractor').val('0').trigger('change');
        current_page = 1;
        getIssuesListing();
    }

    function setupMarker() {
        $("[id^=marker_]").remove();                
        current_issues.forEach(element => {
            rePositionMarker(element);
        });
    }

    function rePositionMarker(issue){        
        var marker_icon = getIssueMarkerIcon(issue.status_id);
        var marker_position_x = issue.position_x,
            marker_position_y = issue.position_y,
            drawing_plan_height = unit.drawing_plan_file_height,
            drawing_plan_width = unit.drawing_plan_file_width,
            web_width = $("#drawing_plan_image").width(),
            web_height = $("#drawing_plan_image").height(),
            web_position = $("#drawing_plan_image").position(),
            id = 'marker_' + issue.id;
        
        $("#image_container").append('<img title="Reference: ' + issue.reference + '" id="' + id + '" src="' + marker_icon + '" />');
        var pos_x = (marker_position_x * web_width / drawing_plan_width) + web_position.left - 15;
        var pos_y = (marker_position_y * web_height / drawing_plan_height) + web_position.top - 30;

        $("#" + id)
            .css('cursor', 'grab')
            .css("position", "absolute")
            .css("width", '30px')
            .css("height", '30px')
            .css("top", pos_y)
            .css("left", pos_x)
            .click(function() {
                window.location.href = "{!! url('issue') !!}/" + issue.id;
            });  
    }

</script>
@endsection
