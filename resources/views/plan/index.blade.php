@extends('components.template-limitless.main')

@section('main')
    <style type="text/css">
        textarea {
            resize: none;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        .markerDrill-size{
            height: 40px;
            width: 40px;
            position: absolute;
            cursor: -webkit-grab; 
            cursor: grab;
        }
        .markerLocation-size{
            height: 40px;
            width: 40px;
            position: absolute;
            cursor: -webkit-grab; 
            cursor: grab;
        }
        .markerIssue-size{
            height: 40px;
            width: 40px;
            position: absolute;
            cursor: -webkit-grab; 
            cursor: grab;
        }
        .radio-inline, .checkbox-inline {
            padding-left: 15px; 
        }
        
        #div-image {
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            background-size: 100% auto;
            overflow-x:hidden;
            overflow-y:hidden;
            flex-grow: 0;
            flex-shrink: 0;
        }

        #container {
            position: absolute!important;
            overflow-x:hidden;
            overflow-y:hidden;
        }

        .btn-option {
            padding: 0px;
            height:34px !important; 
            width:34px !important;
        }
    </style>

    {{-- top option --}}
    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-body" id="bodyMode">
                <div class="row">
                    <div class="col-md-2 col-xs-2">
                        <select data-placeholder="Select Mode" class="select-size-xs" name="plan_mode" id="plan_mode">
                            <option value="">@lang('general.pleaseSelect')</option>
                            <option value="1">@lang('plan.setupMode')</option>
                            <option value="2" selected="">@lang('plan.issueMode')</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-3">
                        <select data-placeholder="Select Plan" class="select-size-xs" name="drawing_plan" id="drawing_plan">
                            @foreach($list as $plan)
                                <option value="{{ $plan->id }}" {{ ($plan->default == 1 ? 'selected' : '') }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <button class="btn btn-primary btn-sm" data-popup="tooltip" title="@lang('plan.filterHover')" data-placement="top" onclick="$('#filter_field').slideToggle()" id="filter_button"><i class="icon-gear"></i></button>
                        
                        <div id="setup_button" style="display: none;">
                            <button class="btn btn-success btn-sm" data-popup="tooltip" title="@lang('plan.addLinkHover')" data-placement="top" onclick="addArea('drill')">
                                <img src="{{ URL::asset('/assets/images/icon/drilldown__icon_transparent.png') }}" style="width:16px !important;height:16px !important;">
                            </button>
                            <button class="btn btn-success btn-sm" data-popup="tooltip" title="@lang('plan.addLocHover')" data-placement="top" onclick="addArea('zone')">
                                <img src="{{ URL::asset('/assets/images/icon/target_marker_blue.png') }}"  style="width:16px !important;height:16px !important;">
                            </button>
                        </div>

                        <button class="btn btn-default btn-sm" data-popup="tooltip" title="@lang('plan.allIssueHover')" data-placement="top" id="allIssue" onclick="return allIssue();" style="display: none;"><i class="fa fa-times" style="font-size: 17px;"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- checkbox --}}
    <div class="content">
        <div class="panel panel-flat" id="filter_field" style="display: none;">
            <div class="panel-body">
                <div class="col-md-12 col-xs-12">
                    <div class="form-group">
                        <label class="display-block text-semibold">@lang('plan.location')</label>
                        <label class="checkbox-inline-first">
                            <input type="checkbox" id="locationAll" checked="" value="">All
                        </label>
                        @foreach($location_status as $status)
                            <label class="checkbox-inline">
                                <input type="checkbox" onclick="locationFilter({{ $status->id }})" id="locationFilter_{{ $status->id }}" class=" location_filter" checked="" name="location[]" value="{{ $status->id }}"><img src="{{ $status->icon }}" style="width: 18px;height: 18px;">  {{ $status->name }}
                            </label>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label class="display-block text-semibold">Issues</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="issueAll" checked="" value="">All
                        </label>
                        @foreach($issue_status as $status)
                            <label class="checkbox-inline">
                                <input type="checkbox" onclick="issueFilter({{ $status->id }})" id="issueFilter_{{ $status->id }}" name="issue[]" value="{{ $status->id }}" class="issue_filter" checked=""><img src="{{ $status->icon }}" style="width: 18px;height: 18px;">  {{ $status->internal }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="panel panel-flat" style="background-color: transparent;border-color: transparent;">
            <div class="panel-body" style="background-color: transparent;">
                <div style="display: flex">
                    <div>
                        <div id="div-image" class="panel panel-flat">
                            <div id='container'></div>
                            <div class="btn-group" style="display: none;"> 
                                <button type="button" onclick="confirmMarker()" class="btn btn-primary" style="height:36px; width:36px"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" onclick="cancelMove()" class="btn btn-danger" style="height:36px; width:36px"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="setup_form" style="flex-grow: 1;">
                        <div id="tab_listing" class="panel panel-flat">
                            <ul class="nav nav-lg nav-tabs nav-justified no-margin no-border-radius bg-indigo-400">
                                <li class="active">
                                    <a href="#drill_table" id="drill_tab" class="tab-option text-size-small text-uppercase" data-toggle="tab"> @lang('plan.drill') </a>
                                </li>
    
                                <li class="">
                                    <a href="#location_table" id="location_tab" class="tab-option text-size-small text-uppercase" data-toggle="tab"> @lang('plan.location') </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in table-responsive" id="drill_table" style="padding: 0px; height: 400px;">                                
                                    <table class="table table-hover fixed_header">
                                        <thead class="dashboard-table-heading">
                                            <tr>
                                                <th style="width: 60%">@lang('plan.name')</th>
                                                <th>@lang('plan.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="drill_listing"></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade table-responsive fixed_header" id="location_table" style="padding: 0px; height: 400px;">
                                    <table class="table table-hover">
                                        <thead class="dashboard-table-heading">
                                            <tr>
                                                <th style="width: 60%">@lang('plan.areaName')</th>
                                                <th>@lang('plan.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="location_listing"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div id="setup_view" class="panel panel-flat" style="display: none;"> 
                            <form id="drill_setup_form" style="display: none;" class="panel-body">
                                <div class="form-group">
                                    <center> <div class="plan_image"></div> </center>
                                </div>
                                <div class="form-group">
                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Drawing Set</legend>
                                    <select data-placeholder="Select Drawing Set" class="select-search" id="drawing_set_option" autofocus="" required=""></select>
                                </div>
                                <div class="form-group">
                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Drawing Plan</legend>
                                    <select data-placeholder="Select Drawing Set" class="select-search" id="drawing_plan_option" autofocus="" required=""></select>
                                </div>

                                <div class="form-group">
                                    <button type="button" onclick="saveArea()"   class="btn btn-primary" style="height:36px; width:36px"><i class="fa fa-save" aria-hidden="true"></i></button> 
                                    <button type="button" onclick="deleteArea()" class="btn btn-danger for-edit"  style="height:36px; width:36px"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    <button type="button" onclick="cancelMove()" class="btn btn-danger"  style="height:36px; width:36px"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                            </form>

                            <form id="location_setup_form" style="display: none;" class="panel-body">
                                <div class="form-group">
                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Area Name</legend>
                                    <input class="form-control" type="text" id="name">
                                </div>
                                <div class="form-group for-edit">
                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Reference</legend>
                                    <input class="form-control" type="text" id="reference" disabled>
                                </div>
                                <div class="form-group">
                                    <legend class="text-size-mini text-muted no-border no-padding no-margin">Preset Color</legend>
                                    <input type="text" class="form-control colorpicker-palette" value="#27ADCA">
                                </div>
                                <div class="form-group">
                                    <button type="button" onclick="saveArea()"   class="btn btn-primary for-add for-edit" style="height:36px; width:36px"><i class="fa fa-save" aria-hidden="true"></i></button> 
                                    <button type="button" onclick="deleteArea()" class="btn btn-danger for-edit"           style="height:36px; width:36px"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    <button type="button" onclick="cancelMove()" class="btn btn-danger for-add for-edit"   style="height:36px; width:36px"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_drill_instruction" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Basic Instruction For Drill Setup</h5>
                </div>

                <div class="modal-body">
                    <h6 class="text-semibold">Note:</h6>
                    <p>1. Move the red drill marker to desired location.</p>
                    <p>2. After finished setup, select drawing set and drawing plan option on the right side.</p>
                    <p>3. Click the 'Save' icon to add or click the 'Cancel' icon to remove successfully.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_location_instruction" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Basic Instruction For Location Setup</h5>
                </div>

                <div class="modal-body">
                    <h6 class="text-semibold">Note:</h6>
                    <p>1. Click at the drawing plan to create the start point.</p>
                    <p>2. Move the pointer and click to create each segment.</p>
                    <p>3. After finished drawing, type in the area name and choose the preset color.</p>
                    <p>4. Click the 'Save' icon to add or click the 'Cancel' icon to remove successfully.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    
    @include('plan.modal')

    <script src="{{ url('js/konva.min.js') }}"></script>
    <script src="{{ url('assets/js/plugins/pickers/color/spectrum.js') }}"></script>
    <script src="{{ url('js/drawarea.js') }}" type="text/javascript" ></script>
    <script type="text/javascript">
        
    var plan_mode,            
        drawing_plan,
        drawing_plan_detail = {},
        active_marker_id;

    var ori_width,
        ori_height;
    var plan_id_array = [];
    var filter_issue_array = [];
    var filter_location_array = [];

    // this is for image upload 
    var images = [];
    var myDropzone;
    var issue_details;

    var flag_url = {!! json_encode(url('/')) !!};

    var drill_icon = "{{ URL::asset('/assets/images/icon/drilldown__icon_transparent.png') }}";
    var location_icon = "{{ URL::asset('/assets/images/icon/target_marker_blue.png') }}";
    var issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_white.png') }}";
    var join_issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_black.png') }}";

    var panel_width, panel_height;

    var setup_mode = "",
        setup_type = "",
        no_record = "<tr><td align='center' colspan='3'>@lang('general.no_result').</td></tr>";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function(){   

        setupKonvaElement('container');

        stage.on("dragstart", function(e) {
            e.target.moveTo(drawLayer);
            layer.draw();

            $('.btn-group').hide();
            return false;
        }).on("dragend", function(e){
            if (plan_mode == 1) {
                drawLayer.draw();
            } else {
                if (setup_type == "issue") {
                    if(setup_mode == "create" || setup_mode == "split" || setup_mode == "update" || setup_mode == "duplicate") {
                        var $marker = stage.find('#' + e.target.id())[0];

                        //calculation for resposition floation menu
                        var set_X, set_y;

                        if(($marker.getAttr('x') > center_point_X && $marker.getAttr('y') > center_point_y) || ($marker.getAttr('x') >center_point_X && $marker.getAttr('y') < center_point_y)){ //bottom right marker
                            set_X = -75;
                            set_y = 0 
                        }
                        else if(($marker.getAttr('x') < center_point_X && $marker.getAttr('y') > center_point_y) || ($marker.getAttr('x') < center_point_X && $marker.getAttr('y') < center_point_y)){ //bottom left marker
                            set_X = 45;
                            set_y = 0 

                        }

                        $('.btn-group').css('position', 'absolute')                        
                            .css('left', ($marker.getAttr('x') + set_X) + 'px')
                            .css('top', $marker.getAttr('y') + set_y + 'px') 
                            .show();
                    }
                }
            }

            return false;
        }).on("click", function(e) {            
            if (plan_mode == 1) {
                if (setup_type == "zone") {
                    if (e.evt.which === 1) {
                        var pos = this.getPointerPosition();
                        var shape = layer.getIntersection(pos);
                        var temp_shape = drawLayer.getIntersection(pos);
                        if (!shape && !temp_shape) points.splice(points.length, 0, Math.round(pos.x), Math.round(pos.y)); drawLine();
                    }
                }
            }

            if (plan_mode == 2 && setup_type == "" && setup_type == "") {
                if (e.evt.which === 1) {
                    var pos = this.getPointerPosition();
                    var shape = layer.getIntersection(pos);
                    var temp_shape = drawLayer.getIntersection(pos);
                    if (!shape && !temp_shape) {
                        locationIssueMenu(drawing_plan_detail.location[0].id);
                    }
                }
            }

            return false;
        });

        $('#plan_mode').trigger('change');

        $(".colorpicker-palette").spectrum({
            showPalette: true,
            palette: colorPalette
        });

        var status = {!! json_encode($issue_status) !!};
        status.map(s => { filter_issue_array.push(s.id) });

        var locations = {!! json_encode($location_status) !!};
        locations.map(s => { filter_location_array.push(s.id) });

        // get setting/option for issue,
        $.ajax({
            url: '{{ route("detailProject") }}',
            type:'POST',
            success:function(response){
                detailProject = response;
                detailProject["drawing"].forEach((element, index) => {
                    $('#drawing_set_option').append('<option value="'+ element["id"] +'" ' + (index == 0 ? 'selected' : '') + '>'+ element["name"] +'</option>');
                });
                
                $("#drawing_set_option").trigger("change");
                $('.loader').hide();
            },  
        });
        
        $("#setup_form").css('flex-basis', $(".panel-body").width() * 0.3).css("margin-left", "10px");
    });

    $('#plan_mode').change(function(){
        plan_mode = $(this).val();
        
        $('#setup_button, #allIssue, #filter_button, #setup_form').hide();

        if(plan_mode == 1){
            $("#setup_button").show();
            $("#setup_form").show();

            $('#bodyMode').css('background-image', 'url(\'{{ url("assets/images/grey.jpg") }}\')');
            if($('#filter_field').is(':visible')) 
                $("#filter_field").slideToggle();

            
            $("#div-image").parent().width($(".panel-body").width() * 0.7);
        } else if (plan_mode == 2){
            $("#filter_button").show();
            $('#bodyMode').css('background-image', 'url(\'{{ url("assets/images/greens.jpeg") }}\')');

            $("#div-image").parent().width($(".panel-body").width());
        } 

        $("#drawing_plan").trigger('change');
    });

    // drawing plan
    $('#drawing_plan').change(function() {
        drawing_plan = $(this).val();
        if (drawing_plan_detail != undefined && drawing_plan == drawing_plan_detail.id) {
            setupZoneElement();
            return false;
        }

        if (drawing_plan == null) return false;

        $.ajax({
            url: flag_url + "/plan/" + drawing_plan,
            type:'GET',
            beforeSend: function() {
                $('.loader').show();
            },
            success:function(response){
                drawing_plan_detail = response;
                ori_height = drawing_plan_detail["height"];
                ori_width = drawing_plan_detail["width"];
                
                setupZoneElement();
            },
        });
    });

    $("#drawing_set_option").on('change', function(){
        $(".plan_image").html('');
        $('#drawing_plan_option').empty().append('<option value="">Please Select</option>');
        
        detailProject["drawing"].forEach(element => {
            if(element["id"] == $("#drawing_set_option").val()){
                element["drawing_plan"].forEach(plan => {
                    $('#drawing_plan_option').append('<option value="'+ plan["id"] +'" >'+ plan["name"] +'</option>');
                });
            }
        });
    });

    $('#drawing_plan_option').on('change', function(){
        detailProject["drawing"].forEach(element => {
            if(element["id"] == $("#drawing_set_option").val()){
                element["drawing_plan"].forEach(element => {
                    if(element["id"] == $('#drawing_plan_option').val() ){
                        var img_link = flag_url +'/uploads/drawings/' + element["file"];
                        var img = '<a href="'+ img_link +'" data-popup="lightbox">'+
                                '<img src="'+ img_link +'" class="img-responsive" style="height: 200px;width: 200px;">'+
                                '</a>';

                        $(".plan_image").html('').html(img);
                    }
                });
            }
        });
    });

    function setupZoneElement() {
        $('.loader').show();

        resetAll();
        points = [];

        $calculation = $('.panel-body').width() / 1.4;
        if (ori_width >= $calculation) {
            panel_width = $(".panel-body").width() * (plan_mode == 1 ? 0.7 : 1);
        } else {
            panel_width = ($(".panel-body").width() / 2);
        }    
        panel_width -= 1;
        panel_height = ori_height / (ori_width / panel_width);

        center_point_X = panel_width / 2;
        center_point_y = panel_height / 2

        $("#div-image")
            .css("background-image", "url(" + flag_url +'/uploads/drawings/'+ drawing_plan_detail["file"] + ")")
            .css("width", panel_width + 'px')
            .css("height", panel_height + 'px');

        resetKonvaElement(panel_width, panel_height);

        setTimeout(function() {
            if(plan_mode == 1 || plan_mode == 2){
                if (drawing_plan_detail["drill"].length > 0) {
                    drawing_plan_detail["drill"].forEach((element, index) => {
                        var pos = calculatePoints('display', [element.position_x, element.position_y], ori_width, ori_height, panel_width, panel_height);

                        $("#drill_listing").append('<tr id="drow_' + index + '" onClick="selectRow(' + "'drill', " + index + ')" style="height: 61px !important;">' + 
                            '<td style="width: 60%">' + 'Drill ' + (index + 1) + '</td>' + 
                            '<td>' + 
                                '<div style="display: none;" id="daction_' + index + '">' +  
                                    '<button class="btn btn-primary btn-option" onClick="editArea(' + "'drill'" + ')"><i class="fa fa-edit" aria-hidden="true"></i></button>' +
                                    '<button class="btn btn-danger btn-option" style="margin-left: 2px;" onClick="deleteArea()"><i class="fa fa-trash" aria-hidden="true"></i></button>' +     
                                '</div>' +
                            '</td>' + 
                            '</tr>');
                        
                        var $drill = drawDrill(index, pos[0], pos[1]);
                        setupOnClickEvent($drill);
                    });
                } else {
                    $("#drill_listing").append(no_record);
                }

                if (drawing_plan_detail.location.length > 0) {
                    drawing_plan_detail["location"].forEach((element, index) => {
                        var pos = calculatePoints('display', element.points.split(','), ori_width, ori_height, panel_width, panel_height);
                        // if (index != 0) {    // REMOVE BECAUSE OTHER LOCATION ALREADY REMOVE 
                            $("#location_listing").append('<tr id="lrow_' + index + '" onClick="selectRow(' + "'location', " + index + ')" style="height: 61px !important;">' + 
                                '<td style="width: 60%">' + element.name + '</td>' + 
                                '<td>' + 
                                    '<div style="display: none;" id="laction_' + index + '">' +  
                                        '<button class="btn btn-primary btn-option" onClick="editArea(' + "'zone'" + ')"><i class="fa fa-edit" aria-hidden="true"></i></button>' +
                                        '<button class="btn btn-danger btn-option" style="margin-left: 2px;" onClick="deleteArea()"><i class="fa fa-trash" aria-hidden="true"></i></button>' +     
                                    '</div>' +
                                '</td>' + 
                                '</tr>');
                                
                            var border_color = element.status_id == 1 ? "black" : (element.status_id == 2 ? "#FBE35B" : "#91DE97");
                            var $zone = drawZone(index, pos, element.color, border_color, 'zone lstatus_' + element.status_id);
                            setupOnClickEvent($zone);
                        // }

                        if (plan_mode == 2) {
                            if (element.issues.length > 0) {
                                element.issues.forEach(issue => {
                                    if (issue.merge_issue_id == null) {
                                        var marker_icon, marker_name;

                                        if (issue.join_issue.length > 0) {
                                            marker_icon = "{{ URL::asset('/assets/images/icon/pin_marker_black.png') }}";
                                            marker_name = "issue merge " + "location_" + issue.location_id + " status_" + issue.status_id;
                                            
                                            issue.join_issue.forEach(join => { marker_name += " status_" + join.status_id; });
                                        } else {
                                            marker_icon = getIssueMarkerIcon(issue.status_id);
                                            marker_name = "issue  " + "location_" + issue.location_id + " status_" + issue.status_id;
                                        }
                                    
                                        var $position = calculatePoints('display', [issue.position_x, issue.position_y], ori_width, ori_height, panel_width, panel_height);
                                        var $issue = drawIssue(issue.id, $position[0], $position[1], marker_icon, marker_name, false);
                                        setupOnClickEvent($issue);
                                    }
                                });     
                            }
                        }
                    });
                } else {
                    $("#location_listing").append(no_record);
                }
            }

            setTimeout(function() {
                filterMarker();

                $('.loader').hide();
            }, 1000);
        }, 500); //end function settimeout
    }

    $(".colorpicker-palette").change(function(e) {
        color = $(".colorpicker-palette").spectrum('get').toHexString();

        drawLine();
    });

    function setupOnClickEvent(element) {
        element.on("click", function(e) {     
            if (setup_mode == "") { 
                if (e.target.name().includes("drill")) {
                    var index = e.target.attrs.id.split('_')[1];
                    if (plan_mode == 1) {
                        $(".tab-option").parent().removeClass("active");
                        $("#drill_tab").parent().addClass("active");
                        
                        $(".tab-content").children().removeClass("active in");
                        $("#drill_table").addClass("active in");
                        
                        selectRow("drill", index);
                        row = $("#drow_" + index);
                        if (row.length){
                            $("#drill_table tbody").scrollTop(0);
                            $("#drill_table tbody").scrollTop(row.offset().top - ($("#drill_table tbody").height() + 20 ));
                        }
                    } else {
                        plan_id_array.push($("#drawing_plan").val());

                        $.ajax({
                            url: '{{ route("planDetails") }}',
                            type:'POST',
                            data: {'id' : drawing_plan_detail.drill[index].id},
                            success:function(response){
                                $("#drawing_plan").val(response["to_drawing_plan_id"])
                                    .trigger("change");
                            },  
                        });
                    }
                }
                if (e.target.name().includes("zone")) {
                    var index = e.target.attrs.id.split('_')[1];
                    if (plan_mode == 1) {

                        $(".tab-option").parent().removeClass("active");
                        $("#location_tab").parent().addClass("active");
                        
                        $(".tab-content").children().removeClass("active in");
                        $("#location_table").addClass("active in");
                        
                        selectRow("location", index);
                        row = $("#lrow_" + index);
                        if (row.length){
                            $("#location_table tbody").scrollTop(0);
                            $("#location_table tbody").scrollTop(row.offset().top - ($("#location_table tbody").height() + 20));
                        }
                        
                    } else {
                        locationIssueMenu(drawing_plan_detail.location[index].id);
                    }                
                }
            }
            if (e.target.name().includes("issue")) {
                var index = e.target.attrs.id.split('_')[1];
                if (plan_mode == 2) {
                    if (setup_mode == "join" && setup_type == "issue") {
                        submitJoinIssue(index);
                    } else if (setup_mode == "merge" && setup_type == "issue") {
                        storeMergeIssue(index);
                    } else {
                        if (e.target.name().includes("merge")) {                    
                            viewListIssue(index);
                        } else {
                            if (setup_mode != "create" && setup_mode != "update") {
                                viewSingleMenuIssue(index);
                            }
                        }
                    }
                }
            }
            
            return false;
        });
    }

    function deselectCurrent() {
        if (Object.keys(current_zone).length) {
            var border_color = current_zone.status_id == 1 ? "black" : (current_zone.status_id == 2 ? "#FBE35B" : "#91DE97");
            var $zone = drawZone(current_zone.index, current_zone.points, current_zone.color, border_color, 'zone lstatus_' + current_zone.status_id);
            setupOnClickEvent($zone);
            current_zone = {};  
            points = [];
            color = "";
            drawLine();
        }

        if (Object.keys(current_drill).length) {
            var search = '#drill_' + current_drill.index;
            stage.find(search)[0].fill('');
            layer.draw();
        }
    }

    function selectRow(type, index){ 

        $('[id^=daction_], [id^=laction_]').hide();
        var name = '';

        deselectCurrent();
        
        if (type == 'drill') {
            current_drill = JSON.parse(JSON.stringify(drawing_plan_detail.drill[index]));
            current_drill.index = index;
            $('#daction_' + index).show();

            name = '#drill_' + index;
            stage.find(name)[0].fill('blue');
            layer.draw();
        } else {
            current_zone = JSON.parse(JSON.stringify(drawing_plan_detail.location[index]));
            current_zone.points = calculatePoints('display', current_zone.points.split(','), ori_width, ori_height, panel_width, panel_height);
            current_zone.index = index;
            points = current_zone.points;
            color = current_zone.color;
            $('#laction_' + index).show();

            name = '#zone_' + index;
            stage.find(name)[0].remove();
            layer.draw();
            drawLine();
        }
    }

    function splitIssue(id){
        displayMessage('Please drag the issue marker.', 'info', false);
        
        var index = searchById(id, issue_details);
        issue_details = issue_details[index];

        setup_type = "issue";
        setup_mode = "split";    

        $("#list_join_issue").modal('toggle');
        drawIssue('split', panel_width / 2, panel_height /2, issue_icon, 'issue split', true);
        setupPlanDisplay(parseInt(issue_details.location_id), 'issue_split');
    }

    function mergeIssue(){
        bootbox.confirm("Are you sure you want to merge these issues ? *Please note that this action cannot be undone.", function (result) {
            if (result) {

                setup_type = "issue";
                setup_mode = "merge";
                $("#allIssue").show();
                $('#menu_issue_details').modal('toggle');

                displayMessage("Select the issue marker to merge the issue.", "info", false);
                setupPlanDisplay(parseInt(issue_details.location_id), null, "issue_" + issue_details.id);
            }
        });
    }

    function moveMarker() {
        if (plan_mode == 2) {
            if(issue_details["status_id"] != 2){
                displayMessage('You are not allowed to move the issue', 'warning', false);
                return false;
            }
        }

        var $issue = stage.find('#issue_' + issue_details.id);
        if ($issue) {
            setup_type = "issue";
            setup_mode = "update";

            $("#menu_issue_details").modal('toggle');
            $issue[0].draggable(true);
            setupPlanDisplay(parseInt(issue_details.location_id), 'issue_' + issue_details.id);
        } else {
            displayMessage('You are not allowed to move the issue', 'warning', false);
        }
    }

    function duplicateIssue() {
        setup_type = "issue";
        setup_mode = "duplicate";

        $('#menu_issue_details').modal('toggle');
        displayMessage('Move the issue.', 'info', false);
        
        drawIssue("duplicate", panel_width / 2, panel_height / 2, "{{ URL::asset('/assets/images/icon/pin_marker_white.png') }}", "issue_duplicate", true);
        setupPlanDisplay(parseInt(issue_details.location_id), "issue_duplicate");
    }

    function addArea(type) {
        setup_mode = "create";
        setup_type = type;
        deselectCurrent();

        $("#tab_listing, #drill_setup_form, #location_setup_form, #setup_button").hide();
        $("#setup_view").show();

        if (type == "zone") {
            color = getRandomColor();

            $("#name").val("");
            $("#reference").val("");
            $(".for-edit").hide();
            $(".for-add").show();
            $(".colorpicker-palette").spectrum("set", color);
            $("#location_setup_form").show();

            $("#modal_location_instruction").modal("show");
        } else if (type == "drill") {
            $("#drill_setup_form").show();
            $("#plan_image").html("");
            $(".for-edit").hide();
            $("#drawing_set_option").val("").trigger("change");

            $("#modal_drill_instruction").modal("show");

            var $drill = drawDrill("new", panel_width / 2, panel_height/2, true);
            $drill.fill('red');
            layer.draw();
        }
    }

    function editArea(type){
        $("#tab_listing, #drill_setup_form, #location_setup_form").hide();
        $("#setup_view").show();
        
        setup_mode = "update";
        setup_type = type;
        
        if (type == 'drill'){
            current_zone = {};
            var $name = '#drill_' + current_drill.index;
            var shape = stage.find($name);  
            shape.moveTo(drawLayer);
            drawLayer.draw();
            layer.draw();
            shape.draggable(true);
            $("#drill_setup_form, .for-edit").show();

            $.ajax({    
                url:"{{ route('step3.getDetailsMarker') }}",
                type:'POST',
                data: {'id' : current_drill.id},
                beforeSend:function() { $('.loader').show(); },
                success:function(response){
                    $('.loader').hide();
                    $("#drawing_set_option").val(response.set.drawing_set_id).trigger('change');
                    $("#drawing_plan_option").val(response.drill.to_drawing_plan_id).trigger('change');
                }
            });
        } else {
            current_drill = {};
            $("#name").val(current_zone.name);
            $("#reference").val(current_zone.reference);
            $(".colorpicker-palette").spectrum("set", current_zone.color);
            $("#location_setup_form").show();
            $(".for-add").hide();
            $(".for-edit").show();
        }
    }

    function deleteArea(){
        var type = Object.keys(current_zone).length ? 'location' : 'link';
        bootbox.confirm("Are you sure to remove this " + type + " ?", function (result) {
            if (result) {
                if(type == 'link'){
                    $.ajax({
                        url: "{!! url('step3' ) !!}" + "/" + current_drill.id,
                        type:'delete',
                        data: {'id': current_drill.id, 'drawing_plan' : $("#drawing_plan").val() },
                        success:function(response){

                            displayMessage(response["msg"], response["type"], false);

                            if(response['type'] == 'success'){
                                drawing_plan_detail["drill"].splice(current_drill.index, 1);
                                setupZoneElement();
                            }
                        }
                    });
                }
                else if(type == 'location'){
                    $.ajax({
                        url: "{!! url('step4' ) !!}" + "/" + current_zone.id,
                        type:'delete',
                        data: {'id': current_zone.id, 'drawing_plan' : $("#drawing_plan").val() },
                        success:function(response){
                            displayMessage(response["msg"], response["type"], false);

                            if(response['type'] == 'success'){
                                drawing_plan_detail["location"].splice(current_zone.index, 1);
                                setupZoneElement();                            
                            }
                        }
                    });
                }
            }
        });
    }

    function cancelMove(){
        bootbox.confirm("Are you sure to cancel current unsaved work ?", function (result) {
            if (result) {
                setupZoneElement();
            }
        });
    }

    function saveArea(){
        if (setup_type == "zone") {
            if (points.length == 0) {
                displayMessage('Please select a place to setup zone.', 'warning', false);
                return false;
            }

            if ($("#name").val() == "") {
                displayMessage('Please insert name for zone.', 'warning', false);
                return false;
            }

            var $check = checkOverlay(points, layer); 
            if (!$check) {              
                displayMessage('Overlay is not allow', 'warning', false);
                return false;
            }
            
            // var allZones = layer.find('Line');
            // if (allZones.length > 0) {
            //     for (var i = 0; i < allZones.length; i++) {
            //         var $check = checkOverlay(allZones[i].points(), drawLayer); 
            //         if (!$check) {
            //             displayMessage('Overlay is not allow', 'warning', false);
            //             return false;
            //         }   
            //     }
            // }
        }

        if (setup_type == "drill") {
            if ($("#drawing_set_option").val() == "") {
                displayMessage('Drawing Set option cannot be empty.', 'warning', false);
                return false;
            }

            if ($("#drawing_plan_option").val() == "") {
                displayMessage('Drawing Plan option cannot be empty.', 'warning', false);
                return false;
            }
        }

        bootbox.confirm("Are you sure to save this work ?", function (result) {
            if (result) { 
                $('.loader').show();
                if (setup_type == "zone") {
                    var input = calculatePoints('store', points, ori_width, ori_height, panel_width, panel_height);
                    input = input.join();
                    color = $(".colorpicker-palette").spectrum('get').toHexString();
                    
                    if (setup_mode == "create") {
                        $.ajax({
                            url:"{{ route('step4.store') }}",
                            type:'POST',
                            data: {
                                drawing_plan_id: $("#drawing_plan").val(),
                                color: color,
                                points: input,
                                name: $("#name").val(),
                            },
                            success:function(response){
                                $('.loader').hide();
                                
                                displayMessage('Record successfully added.', 'success', false);
                                
                                drawing_plan_detail.location.push({
                                    'id': response.id,
                                    'name': response.name,
                                    'reference': response.reference,
                                    'points': response.points,
                                    'color' : response.color,
                                    'issues': [],
                                    'status_id': 1
                                });
                                setupZoneElement();
                            }
                        });
                    }

                    if (setup_mode == "update") {                            
                        $.ajax({
                            url:"{{ url('step4') }}" + "/" + current_zone.id,
                            type:'PUT',
                            data: {
                                name : $("#name").val(),
                                points: input,
                                color: color,
                                drawing_plan_id: $("#drawing_plan").val(),
                            },
                            success:function(response){
                                setTimeout(function(){
                                    $('.loader').hide();

                                    displayMessage('Record successfully updated.', 'success', false);
                                    drawing_plan_detail["location"][current_zone.index].points = response.points;
                                    drawing_plan_detail["location"][current_zone.index].color = color;
                                    drawing_plan_detail["location"][current_zone.index].name = response.name;
                                    drawing_plan_detail["location"][current_zone.index].reference = response.reference;

                                    setupZoneElement();
                                },300);
                            }
                        }); 
                    } 
                } else if (setup_type == "drill") {
                    var $name = '#drill_' + (Object.keys(current_drill).length ? current_drill.index : "new");
                    var $drill = stage.find($name);
                    var position = calculatePoints('store', [$drill[0].attrs.x + 20, $drill[0].attrs.y + 40], ori_width, ori_height, panel_width, panel_height);

                    if (setup_mode == "create") {
                        $.ajax({
                            url:"{{ route('step3.store') }}",
                            type:'POST',
                            data: {
                                set: $("#drawing_set_option").val(),
                                link_to_plan: $("#drawing_plan_option").val(),
                                drawing_plan_from: drawing_plan,
                                position_x: position[0],
                                position_y: position[1],
                            },
                            success:function(response){
                                $('.loader').hide();

                                displayMessage('Record successfully store.', 'success', false);
                                drawing_plan_detail["drill"].push({
                                    'id': response.drill_id,
                                    'position_x': response.pos_x,
                                    'position_y': response.pos_y,
                                    'to_drawing_plan_id': response.link_id
                                });
                                
                                setupZoneElement();
                            },
                        });
                    } 

                    if (setup_mode == "update") {
                        $.ajax({
                            url:"{{ route('step3.updates') }}",
                            type:'POST',
                            data: {
                                point_id: current_drill.id,
                                update_link_to_plan: $("#drawing_plan_option").val(),
                                x: position[0],
                                y: position[1],
                            },
                            success:function(response){
                                $('.loader').hide();

                                displayMessage('Record successfully updated.', 'success', false);
                                drawing_plan_detail["drill"][current_drill.index].position_x = response.pos_x;
                                drawing_plan_detail["drill"][current_drill.index].position_y = response.pos_y;
                                drawing_plan_detail["drill"][current_drill.index].to_drawing_plan_id = response.link_id;
                                
                                setupZoneElement();
                            },
                        });
                    } 
                }
            }
        });
    }

    // for modal use -- start --
    function showAdvance(){
        $(".advance_field").toggle();
        $(".showAdvance").hide();
        $(".hideAdvance").show();
    }

    function hideAdvance(){
        $(".advance_field").toggle();
        $(".showAdvance").show();
        $(".hideAdvance").hide();
    }
    // for modal use -- end --

    $("#locationAll").click(function(){
        $('.location_filter').prop('checked', this.checked);
        locationFilter();
    });

    $("#issueAll").click(function(){
        $('.issue_filter').prop('checked', this.checked);
        issueFilter();
    });        

    function locationFilter(id){
        
        filter_location_array = [];
        var countLocation = 0;
        $('.location_filter').each(function(i, obj) {
            var id = this.id;

            if($("#" + id).prop("checked") == true){
                countLocation++;
                filter_location_array.push($("#" + id).val());
            }
        });

        if(countLocation == $('.location_filter').length){
            $('#locationAll').prop('checked', true);
        }else{
            $('#locationAll').prop('checked', false);
        }

        filterMarker();
    }

    function issueFilter(id){

        filter_issue_array = [];
        var countIssue = 0;
        $('.issue_filter').each(function(i, obj) {                
            var id = this.id;

            var check = $("#" + id).prop("checked");

            if(check == true){
                countIssue++;
                filter_issue_array.push($("#" + id).val());
            }
        });

        if(countIssue == $('.issue_filter').length){
            $('#issueAll').prop('checked', true);
        }else{
            $('#issueAll').prop('checked', false);
        }

        filterMarker();
    }

    function filterMarker(){
        stage.find('.zone').hide();
        if(filter_location_array.length > 0){
            for (var i = 0; i < filter_location_array.length; i++) {
                stage.find('.lstatus_' + filter_location_array[i]).show();
            }
        }

        stage.find('.issue').hide();
        if(filter_issue_array.length > 0){
            for (var i = 0; i < filter_issue_array.length; i++) {
                stage.find('.status_' + filter_issue_array[i]).show();
            }
        }

        layer.batchDraw();

        // $('.location-marker').hide();
    }

    function submitJoinIssue(id) {
        
        $.ajax({
            url: '{{ route("plan.joinIssue") }}',
            type:'POST',
            data: {'issue_id' : issue_details.id, 'move_to_issue_id' : id},
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                var $location_id = parseInt(issue_details.location_id);
                id = parseInt(id);
                var $l_index = searchById($location_id, drawing_plan_detail.location);
                var $to_hide_index = searchById(parseInt(issue_details.id), drawing_plan_detail.location[$l_index].issues);            
                var $to_join_index = searchById(id, drawing_plan_detail.location[$l_index].issues);

                drawing_plan_detail.location[$l_index].issues[$to_hide_index].merge_issue_id = id;
                drawing_plan_detail.location[$l_index].issues[$to_join_index] = response;
                
                setTimeout(() => {
                    setupZoneElement();

                    $('.loader').hide();
                }, 500);
            }
        });

        // $("#drawing_plan").trigger("change");
        return false;
    }

    function storeMergeIssue(id){
        $.ajax({
            url: '{{ route("plan.storeMergeIssue") }}',
            type:'POST',
            data: {'active_marker_id' : issue_details.id , 'merge_issue_id' : id },
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                $('.loader').hide();
                displayMessage('Issue successfully merged.', 'success', false);
                
                var $location_id = parseInt(issue_details.location_id);
                var $l_index = searchById($location_id, drawing_plan_detail.location);
                var $index = searchById(issue_details.id, drawing_plan_detail.location[$l_index].issues);

                drawing_plan_detail.location[$l_index].issues.splice($index, 1);
                setupZoneElement();
            },  
        });
    }

    function viewListIssue(id){
        $.ajax({

            url:"{{ route('plan.listJoinIssue') }}",
            type:'POST',
            data: {'id' : id },
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                issue_details = response;
                var data, img_issue_url = "", issue_created_at;
                $("#body_list_joinIssue").html('');
                response.forEach(element => {
                    // if(issue_mode == 'merge' && element["id"] == active_marker_id ){
                    //     return ;
                    // }

                    if (element["start_image"].length > 0) {
                        img_issue_url = '<a href="'+ flag_url +'/uploads/issues/'+ element["start_image"][0]["image"] +'" data-popup="lightbox"><img src="'+ flag_url +'/uploads/issues/'+ element["start_image"][0]["image"] +'" style="width: 58px; height: 58px; border-radius: 2px;" alt=""></a>';
                    }

                        data = '<div class="row">'+
                        '<div class="col-md-6 col-xs-6">'+
                            '<strong><h5 style="font-size: 12px;">&nbsp;&nbsp;'+ element["reference"] +'&nbsp;&nbsp;<span class="label" style="background-color: '+ element["status"]["internal_color"] +';color: white;"> <strong>'+ element["status"]["internal"] +'</strong></span></strong></h5>'+
                        '</div>'+
                        '<div class="col-md-6 col-xs-6" align="right">';

                            // if(issue_mode == 'merge'){

                            //     data += '<div style="padding-top: 9px;">'+
                            //         '<span class="label label-success label-rounded" style="cursor: pointer;" onclick="storeMergeIssue('+ element["id"] +', true)">Merge</span>';
                            // }else{

                                data += '<div style="padding-top: 9px;">'+
                                    '<span class="label label-primary label-rounded" style="background-color:'+ element["status"]["internal_color"] +';border-color:'+ element["status"]["internal_color"] +'; cursor: pointer;" onclick="viewIssue('+ element["id"] +')">Open</span>'+
                                    '<span class="label label-primary label-rounded" style="cursor: pointer;" onclick="splitIssue('+ element["id"] +')">Split</span>';
                            // }

                            data += '</div>'+
                        '</div>'+
                    '</div>';
                    
                    data +='<div class="row">'+
                        '<div class="col-md-12">'+
                            '<div class="media no-margin-top">'+
                                '<div class="media-left">'+
                                    img_issue_url
                                +'</div>'+

                                '<div class="media-body">'+
                                    '<strong>'+ element['location']['name'] +'</strong>'+
                                    '<span class="help-block">Created on '+ element["new_created_at"] +'</span>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="table-responsive table-xxs">'+
                        '<table class="" style="width: 100%;">'+
                            '<tr>'+
                                '<th style="font-size: 12px;w" width="25%">Priority</th>'+
                                '<td style="font-size: 12px;"> : '+ element['priority']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Category</th>'+
                                '<td style="font-size: 12px;">: '+ element['category']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Type</th>'+
                                '<td style="font-size: 12px;"> : '+ element['type']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Issue</th>'+
                                '<td style="font-size: 12px;"> : '+ element['issue']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Due By</th>'+
                                '<td style="font-size: 12px;"> : '+ element['priority']['name'] +'</td>'+
                            '</tr>'+
                        '</table>'+
                    '</div><hr>';
                    $("#body_list_joinIssue").append(data);
                });
                
                setTimeout(function(){
                    $('.loader').hide();
                    $('#list_join_issue').modal('toggle');
                },500);
            },
        });
    }

    function viewIssue(id){
        $('#list_join_issue').modal('toggle');
        viewSingleMenuIssue(id);
    }

    function viewSingleMenuIssue(issueID){
        $(".advance_field").hide();
        $(".showAdvance").show();
        $(".hideAdvance").hide();
        $.ajax({

            url: '{{ route("plan.issueDetails") }}',
            type:'POST',
            data: {'id' : issueID},
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                $('.loader').hide();
                issue_details = response;
                active_marker_id = response.location_id;

                $(".start_image, .last_image").html('');                    
                
                if(response["status_id"] == 8 || response["status_id"] == 10){
                    $("#field_status_wip").hide();
                    $("#field_status_complete").show();
                }else{
                    $("#field_status_wip").show();
                    $("#field_status_complete").hide();
                }

                response["start_image"].forEach(element => {

                    if(element["seq"] == 1){
                        $(".start_image").append('<a href="'+ flag_url +'/uploads/issues/'+ element["image"] +'" data-popup="lightbox" rel="gallery"><img src="'+ flag_url +'/uploads/issues/'+ element["image"] +'" class="img-responsive img-thumbnail" style="height:150px"></a>');
                    }else{
                        $(".start_image").append('<a href="'+ flag_url +'/uploads/issues/'+ element["image"] +'" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded" style="display:none;">');
                    }
                });

                var count = 1;
                response["last_image"].forEach(element => {

                    if(count == 1){
                        $(".last_image").append('<a href="'+ flag_url +'/uploads/issues/'+ element["image"] +'" data-popup="lightbox" rel="gallery"><img src="'+ flag_url +'/uploads/issues/'+ element["image"] +'" class="img-responsive img-thumbnail" style="height:150px"></a>');
                    }else{
                        $(".last_image").append('<a href="'+ flag_url +'/uploads/issues/'+ element["image"] +'" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded" style="display:none;">')

                    }

                    count++;

                });

                var status_name = response["status"]["internal"];                    
                var status_color = response["status"]["internal_color"];   
                // var setOrNot = typeof response["priority"]['name'] !== typeof null ? response["priority"]['name'] : false;
                if(response['priority']!=undefined || response['priority']!=null){
                    var priority_name = response['priority']['name'];
                }
                else{
                    var priority_name = "Not Set";
                }

                if(response["issue_due"]!=undefined || response["issue_due"]!=null){
                    var due_by_details = response["issue_due"];
                }
                else{
                    var due_by_details = "Not Set";
                }

                $("#issue_id_details").html(response["reference"] + ' <span class="label label-success" style="background-color: '+ status_color +';border-color: '+ status_color +'">'+ status_name +'</span>');
                $("#priority_details").text(priority_name);
                
                var drawing_level = response["location"]["drawing_plan"]["block"] + "-" + response["location"]["drawing_plan"]["level"] + "-" + response["location"]["drawing_plan"]["unit"];

                $("#unit_details").text(drawing_level);
                $("#location_details").text(response["location"]["name"]);
                $("#category_details").text(response["category"]["name"]);
                $("#type_details").text(response["type"]["name"]);
                $("#issue_details").text(response["issue"]["name"]);
                $("#created_by_details").text(response["created_by"]["name"]);
                $("#comment_details").text(response["remarks"]);
                $("#created_date_details").text(response["new_created_at"]);
                $("#due_by_details").text(due_by_details);
                if(response["group_id"] == null){
                    $("#contractor_details").text("");
                    $("#contractor_abv_details").text("");
                }else{
                    $("#contractor_details").text(response["contractor"]["display_name"]);
                    $("#contractor_abv_details").text(response["contractor"]["abbreviation_name"]);
                }
                var btnIssue = "";
                if(response["status_id"] == 2){
                    $("#close_issue_div").hide();
                    $("#other_issue_div").show();
                    btnIssue += '<button type="button" class="btn btn-danger" style="width: 100%" onclick="voidIssue()">@lang("plan.void")</button>';
                }

                else if(response["status_id"] == 8 || response["status_id"] == 10){

                    $("#close_issue_div").show();
                    $("#other_issue_div").hide();
                    // btnIssue += '<button type="button" class="btn btn-success">CLOSE ISSUE</button>'
                                // +'<button type="button" class="btn btn-danger">REJECT</button>';

                } else if(response["status_id"] == 4){
                    $("#close_issue_div").show();
                    $("#other_issue_div").hide();
                }
                else{
                    $("#close_issue_div").hide();
                    $("#other_issue_div").show();
                    // btnIssue += '<button type="button" class="btn btn-danger" style="width: 100%" disabled="">CLOSED</button>';
                }
                
                $("#issue_detail_button").html(btnIssue);
                $("#menu_issue_details").modal('toggle');

            },  
        });
    }

    function resetAll(){  
        // issue_mode = 'view';
        $('.btn-group, #allIssue').hide();
        
        current_zone = {};
        current_drill = {};
        issue_details = "";
        active_marker_id = "";
        setup_mode = "";
        setup_type = "";
        plan_id_array = [];

        $("#tab_listing").show();
        $("#drill_setup_form, #location_setup_form, #setup_view").hide();
        $("#drill_listing, #location_listing").html("");   

        if (plan_mode == 1) {
            $("#setup_button").show();
        }
    }

    /**
     * FUNCTION MENU MARKER.
     */
    function confirmMarker() {
        var $cur_shape, $target;

        if (setup_type != "issue") {
            return false;
        }
        
        if (setup_mode == "create") {
            $cur_shape = stage.find('#issue_new')[0];
            $target  = '#zone_' + searchById(active_marker_id, drawing_plan_detail.location);
        } else if (setup_mode == "update") {
            $cur_shape = stage.find('#issue_' + issue_details.id)[0];
            $target = '#zone_' + searchById(parseInt(issue_details.location_id), drawing_plan_detail.location);
        } else if (setup_mode == "duplicate") {
            $cur_shape = stage.find('#issue_duplicate')[0];
            $target = '#zone_' + searchById(parseInt(issue_details.location_id), drawing_plan_detail.location);
        } else if (setup_mode == "split") {
            $cur_shape = stage.find('#issue_split')[0];
            $target = '#zone_' + searchById(parseInt(issue_details.location_id), drawing_plan_detail.location);                
        }
        
        var xx = $cur_shape.getAttr('x');
        var yy = $cur_shape.getAttr('y') + 40;    
            
        if ((layer.getIntersection({x: xx, y: yy}) === stage.find($target)[0]) || 
            ($target == '#zone_0' && 
            ( xx >= 0 && xx <= panel_width) && 
            ( yy >= 0 && yy <= panel_height)) // for location "others"
        ) {
            xx = xx / panel_width * ori_width;
            yy = yy / panel_height * ori_height;

            if (setup_mode == "create") {
                $("#add_issue_detail").modal('toggle');
                $("#location").val(active_marker_id);
                
                $("#pos_x").val(xx);
                $("#pos_y").val(yy);

                // fill option in category
                $('#category').empty().append('<option value="">Please Select</option>');
                detailProject["category"].forEach(element => {
                    $('#category').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                });

                // fill option in contractor
                $('#contractor').empty().append('<option value="">Please Select</option>');
                detailProject["contractor"].forEach(element => {
                    $('#contractor').append('<option value="'+ element["group_details"]["id"] +'">'+ element["group_details"]["display_name"] +'('+ element["group_details"]["abbreviation_name"] +')</option>');
                });

                // fill option in priority
                $('#priority').empty().append('<option value="">Please Select</option>');
                detailProject["priority"].forEach(element => {
                    $('#priority').append('<option value="'+ element["priority"]["id"] +'">'+ element["priority"]["name"] +' ('+ element["priority"]["no_of_days"] +' days)</option>');
                });

                $('#inspector').empty().append('<option value="">Please Select</option>');
                detailProject["inspector"].forEach(element => {
                    $('#inspector').append('<option value="'+ element["users"]["id"] +'">'+ element["users"]["name"] +'<small> ('+ element["users"]["email"] +')</small></option>');
                });

                document.querySelector("#due_by").valueAsDate = new Date();

                $("#add_issue_detail").modal('show');
            } else if (setup_mode == "update") {
                $.ajax({
                    url: '{{ route("plan.moveIssue") }}',
                    type:'POST',
                    data: {'id' : issue_details.id, 'x' : xx , 'y' : yy },
                    beforeSend:function(response){
                        $('.loader').show();
                    },
                    success:function(response){
                        $('.loader').hide();
                        displayMessage('record successfully updated.', 'success', false);
                        var $l_index = searchById(parseInt(issue_details.location_id), drawing_plan_detail.location);
                        var $i_index = searchById(parseInt(issue_details.id), drawing_plan_detail.location[$l_index].issues);
                        drawing_plan_detail.location[$l_index].issues[$i_index].position_x = xx;
                        drawing_plan_detail.location[$l_index].issues[$i_index].position_y = yy;
                        setupZoneElement();
                    },  
                });
            } else if (setup_mode == "duplicate") {
                bootbox.confirm("Are you sure to save this work ?", function (result) {
                    if (result) {
                        
                        $.ajax({
                            url:"{{ route('plan.editIssue') }}",
                            type:'POST',
                            data: {'id' : issue_details["id"]},
                            beforeSend:function(){
                                $('.loader').show();
                            },
                            success:function(response){
                                
                                $('.loader').hide();

                                $("#due_by").val(response["issue"]["due_by"]);
                                $("#comment").val(response["issue"]["remarks"]);
                                $("#location").val(issue_details["location_id"]);
                                
                                $("#pos_x").val(xx);
                                $("#pos_y").val(yy);

                                $('#category').empty().append('<option value="">Please Select</option>');
                                response["details"]["category"].forEach(element => {
                                    $('#category').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                                });

                                $('#category').val(response["issue"]["setting_category_id"]);

                                $('#type').empty().append('<option value="">Please Select</option>');
                                
                                for (let i = 0; i < detailProject["category"].length; i++) {
                                    if(detailProject["category"][i]["id"]  == response["issue"]["setting_category_id"]){

                                        detailProject["category"][i]["type"].forEach(element => {
                                            $('#type').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                                        });
                                    }else{
                                        continue;
                                    }                            
                                }

                                $('#type').val(response["issue"]["setting_type_id"]);

                                for (let i = 0; i < detailProject["category"].length; i++) {


                                    if(detailProject["category"][i]["id"]  == response["issue"]["setting_category_id"]){

                                        for (let y = 0; y < detailProject["category"][i]["type"].length; y++) {

                                            if(detailProject["category"][i]["type"][y]["id"]  == response["issue"]["setting_type_id"]){
                                                
                                                detailProject["category"][i]["type"][y]["issue"] .forEach(element => {
                                                    $('#issue').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                                                });

                                            }else{
                                                continue;
                                            }
                                        }

                                    }else{
                                        continue;
                                    }        
                                }

                                $('#issue').val(response["issue"]["setting_issue_id"]);

                                // fill option in contractor
                                $('#contractor').empty().append('<option value="">Please Select</option>');
                                response["details"]["contractor"].forEach(element => {
                                    $('#contractor').append('<option value="'+ element["group_details"]["id"] +'">'+ element["group_details"]["display_name"] +'('+ element["group_details"]["abbreviation_name"] +')</option>');
                                });

                                $('#contractor').val(response["issue"]["group_id"]);
                                // fill option in priority
                                $('#priority').empty().append('<option value="">Please Select</option>');
                                response["details"]["priority"].forEach(element => {
                                    $('#priority').append('<option value="'+ element["priority"]["id"] +'">'+ element["priority"]["name"] +'</option>');
                                });

                                $('#priority').val(response["issue"]["priority_id"]);

                                $('#inspector').empty().append('<option value="">Please Select</option>');
                                detailProject["inspector"].forEach(element => {
                                    $('#inspector').append('<option value="'+ element["users"]["id"] +'">'+ element["users"]["name"] +'</option>');
                                });

                                $("#inspector").val(response["issue"]["inspector_id"]);
                                $("#add_issue_detail").modal('toggle');
                            }
                        });
                    }
                }); 
            } else if (setup_mode == "split") {
                bootbox.confirm("Are you sure to save this work ?", function (result) {
                    if (result) {
                        $.ajax({
                            url: '{{ route("plan.splitIssue") }}',
                            type:'POST',
                            data: {'id' : issue_details.id, 'x' : xx , 'y' : yy },
                            beforeSend:function(response){
                                $('.loader').show();
                            },
                            success:function(response) {
                                $('.loader').hide();
                                
                                var $location_id = parseInt(issue_details.location_id);
                                var $l_index = searchById($location_id, drawing_plan_detail.location);
                                
                                if (issue_details.merge_issue_id == null) {
                                    var $index = searchById(issue_details.id, drawing_plan_detail.location[$l_index].issues);
                                    drawing_plan_detail.location[$l_index].issues[$index].join_issue = [];
                                    var $X = drawing_plan_detail.location[$l_index].issues[$index].position_x;
                                    var $Y = drawing_plan_detail.location[$l_index].issues[$index].position_y;
                                    drawing_plan_detail.location[$l_index].issues[$index].position_x = xx;
                                    drawing_plan_detail.location[$l_index].issues[$index].position_y = yy;

                                    drawing_plan_detail.location[$l_index].issues.forEach(issue => {
                                        if (issue.merge_issue_id == issue_details.id) {
                                            issue.merge_issue_id = null;
                                            issue.position_x = $X;
                                            issue.position_y = $Y;
                                        }
                                    });
                                } else {
                                    var $parent_index = searchById(parseInt(issue_details.merge_issue_id), drawing_plan_detail.location[$l_index].issues);
                                    drawing_plan_detail.location[$l_index].issues[$parent_index].join_issue = [];    

                                    var $index = searchById(issue_details.id, drawing_plan_detail.location[$l_index].issues);
                                    drawing_plan_detail.location[$l_index].issues[$index].merge_issue_id = null;
                                    drawing_plan_detail.location[$l_index].issues[$index].position_x = xx;
                                    drawing_plan_detail.location[$l_index].issues[$index].position_y = yy;
                                }
                                setupZoneElement();
                            },  
                        });
                    }
                }); 
            }
        } else {
            displayMessage('@lang("plan.notInLocation")', '@lang("general.warning")', false);
        }
    }

    $("#add_issue").click(function() {
        drawIssue("new", panel_width/2, panel_height/2, issue_icon, 'issue_new', true);
        
        $('#location_menu').modal('toggle');

        setup_type = "issue";
        setup_mode = "create";

        setupPlanDisplay(active_marker_id, "issue_new");
    });

    function setupPlanDisplay(location_id, marker_id = null, hide_id = null) {
        var index = searchById(location_id, drawing_plan_detail.location);

        layer.getChildren().hide();
        layer.find('.location_' + location_id).show();
        layer.find('#zone_' + index).show();

        if (marker_id) {
            layer.find("#" + marker_id).show();
        }

        if (hide_id) {
            layer.find('#' + hide_id).hide();
        }
        
        layer.batchDraw();

        setTimeout(() => {
            if (marker_id) {
                var $shape = layer.find('#' + marker_id)[0];
                $('.btn-group').css('position', 'absolute')                        
                    .css('left', ($shape.getAttr('x') + 45) + 'px')
                    .css('top', $shape.getAttr('y') + 'px') 
                    .show();
            }
        }, 200);
    }

    // submit form add issue details
    $("#form_add_issue").submit(function(event) {
        event.preventDefault();

        for ( var i = 0, l = images.length; i < l; i++ ) {
            $(this).append($('<input />').attr('type', 'hidden').attr('name', 'image[]').attr('class', 'imagesDefect').attr('value', images[ i ]));
        }

        $.ajax({
            url:"{{ route('plan.issueStore') }}",
            type:'POST',
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                $('.loader').hide();

                images = [];
                myDropzone.removeAllFiles(true); 

                $('#add_issue_detail').modal('toggle');
                $(".imagesDefect").remove();
                
                displayMessage('@lang("plan.successAddIssue")', '@lang("general.success")', false);

                var $location_id = response.location_id;
                var index = searchById($location_id, drawing_plan_detail.location);
                drawing_plan_detail.location[index].issues.push(response);
                setupZoneElement();
            },
            error: function (response) {

                displayMessage('An error has occurred. Please try again.', 'error', false);
                $('.loader').hide();
            }
        });
    });

    function locationIssueMenu(id) {
        $.ajax({
            url: '{{ route("locationDetails") }}',
            type:'POST',
            data: {'id' : id},
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                console.log(response)
                $('.loader').hide();

                active_marker_id = response["id"];

                if(response["status_id"] == 1){
                    $('.btn-close').attr('disabled', true).css('background-color', 'grey').css('border-color', 'grey');
                }

                $("#location_name").text(response["name"]);
                $("#location_reference").text(response["reference"]);
                $("#location_menu").css('margin-top',"8%").modal('toggle');
            },  
        });
    }

    $("#category").on('change', function(){ 
        categorySetting('add'); 
    });

    $("#edit_category").on('change', function(){
        categorySetting('edit'); 
    });


    $("#issue").on('change', function(){ 
        issueSetting('add')
    });

     $("#edit_issue").on('change', function(){ 
        issueSetting('edit')
    });


    function issueSetting(type){

        if(type == 'add'){
            var cat_id = $("#category").val();
            var type_id = $("#type").val();
            var issue_id = $("#issue").val();
        }else{

            var cat_id = $("#edit_category").val();
            var type_id = $("#edit_type").val();
            var issue_id = $("#edit_issue").val();
        }


        for (let i = 0; i < detailProject["category"].length; i++) {

            if(detailProject["category"][i]["id"]  == cat_id){

                for (let y = 0; y < detailProject["category"][i]["type"].length; y++) {

                    if(detailProject["category"][i]["type"][y]["id"]  == type_id){
                        
                        detailProject["category"][i]["type"][y]["issue"] .forEach(element => {

                            if(issue_id == element["id"]){
                                $("#contractor, #edit_contractor").val(element["group_id"]).trigger("change");

                            }
                        });

                    }else{
                        continue;
                    }
                }

            }else{
                continue;
            }        
        }

    }

    function categorySetting(type){

        if(type == 'add'){
            var cat_id = $("#category").val();
            var typeElement = $("#type");
        }else{
            var cat_id = $("#edit_category").val();
            var typeElement = $("#edit_type");

        }
        
        typeElement.empty().append('<option value="">Please Select</option>');
        
        for (let i = 0; i < detailProject["category"].length; i++) {
            

            if(detailProject["category"][i]["id"]  == cat_id){


                detailProject["category"][i]["type"].forEach(element => {
                    typeElement.append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                });

            }else{
                continue;
            }        
        }
    }

    function typeSetting(type){

        if(type == 'add'){
            var cat_id = $("#category").val();
            var type_id = $("#type").val();
            var issueElement = $('#issue');
        }else{

            var cat_id = $("#edit_category").val();
            var type_id = $("#edit_type").val();
            var issueElement = $('#edit_issue');

        }

        issueElement.empty().append('<option value="">Please Select</option>');
            

        for (let i = 0; i < detailProject["category"].length; i++) {


            if(detailProject["category"][i]["id"]  == cat_id){

                for (let y = 0; y < detailProject["category"][i]["type"].length; y++) {

                    if(detailProject["category"][i]["type"][y]["id"]  == type_id){
                        

                        detailProject["category"][i]["type"][y]["issue"] .forEach(element => {
                            issueElement.append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                        });

                    }else{
                        continue;
                    }
                }

            }else{
                continue;
            }        
        }
    }

    $("#type, #category").on('change', function(){
        typeSetting('add');
    });

    $("#edit_type, #edit_category").on('change', function(){
        typeSetting('edit');
    });

    // onchage priority - affect due_by field
    $("#priority").on('change', function(){

        var priority = $(this).val();
        
        for (let x = 0; x < detailProject["priority"].length; x++) {

            if(detailProject["priority"][x]["priority"]["id"] == priority){
                // date calculation
                var date = new Date();
                var newdate = new Date(date);

                newdate.setDate(newdate.getDate() + detailProject["priority"][x]["priority"]["no_of_days"]);
                
                var dd = newdate.getDate();
                var mm = newdate.getMonth() + 1;
                var y = newdate.getFullYear();

                dd = ("0" + dd).slice(-2);
                mm = ("0" + mm).slice(-2);

                var someFormattedDate = y + '-' + mm + '-' + dd;

                document.getElementById('due_by').value = someFormattedDate;


            }else{
                continue;
            }
        }
    });

    function viewLinkIssues(){

        $("#allIssue").show();
        $('#location_menu').modal('toggle');
        stage.find('.issue').hide();
        stage.find('.location_' + active_marker_id).show();
        layer.batchDraw();
}

    function allIssue(){
        if (setup_type == "issue" && (setup_mode == "join" || setup_mode == "merge")) {
            bootbox.confirm("Are you sure to cancel current unsaved work ?", function (result) {
                if (result) {                        
                    setupZoneElement();
                } else {
                    return false;
                }
            });
        } else {
            $("#allIssue").hide();
            stage.find('.issue').show();
            layer.batchDraw();
        }
    }

    function viewHistory(){
        $("#history_content").html('');

        $("#view_history").modal('toggle');
        $("#menu_issue_details").modal('toggle');

        var all_history = issue_details["history"].reverse();

        var history = '';
        var status_color = '';
        var remarks = '';
        var user = '';
        var userAvatar = '';
        var date = '';
        var status = '';
        var current_status = '';
            
        var bodyHistory = "";
        all_history.forEach(element => {
            
            status_color = element["status"]["internal_color"];
            status = element["status"]["internal"];
            remarks = element["remarks"];
            user = element["user"]["name"];
            date = element["issue_created"];
            diff = element["issue_diffHuman"];

            if(element["user"]["avatar"] == null){
                userAvatar = '{{ url("assets/images/placeholder.jpg") }}';
            }else{
                userAvatar = flag_url + '/uploads/avatars/'+ element["user"]["avatar"];
            }

            if(remarks == null){
                remarks = "";
            }

            if(element["status"]["internal"] == current_status){
                bodyHistory += '<hr width="70%" style="margin-bottom: 0px;margin-top: 10px;">';
            }

            if(status != current_status){
                bodyHistory += '<li class="media date-step content-divider text-muted">'
                                    +'<span><button class="btn btn-default" style="background-color: '+ status_color +';color: white;"> <strong>'+ status +'</strong> </button></span>'
                                +'</li>';
                current_status = status;
            }
                        
            bodyHistory +='<li class="media">'
                +'<div class="media-left"><img src="'+ userAvatar +'" class="img-circle img-md" alt=""></div>'
                +'<div class="media-body">'
                    +'<div class="media-heading">'
                        +'<a href="#" class="text-semibold">'+ user +'</a>'
                        +'<span class="media-annotation pull-right">'+ diff +'<i class="icon-watch2 position-right text-muted"></i></span>'
                    +'</div>'
                    +remarks + '<br>';

                    element["images"].forEach(element => {
                        var image_info = flag_url +'/uploads/issues/' + element["image"];
                            bodyHistory +='<a href="'+ image_info +'" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon">'
                                        +'<img src="'+ image_info +'" class="img img-responsive" style="height: 50px;width: 50px;">'
                                        +'</a>';
                    });
                                        
                bodyHistory +='</div>'
            +'</li>';

        });
        $("#history_content").append(bodyHistory);
    }

    $("#view_history, #add_info, #edit_issue_detail").on('hidden.bs.modal', function (e) {
        viewSingleMenuIssue(issue_details.id);
    });

    $("#add_issue_detail").on('hidden.bs.modal', function (e) {
        $(this)
        .find("input,textarea,select")
        .empty()
        .end();
        $("#type, #issue").empty();
        $("#comment").val('');
    });

    function addInfo(){
        $("#menu_issue_details").modal('toggle');

        $("#comment_info").val('');
        $(".filename").text('No file selected');
        $("#add_info").modal('toggle');
        $("#issue_id_info").val(issue_details.id);
    }

        // submit form add issue details
    $("#form_add_info").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url:"{{ route('plan.issueInfoStore') }}",
            type:'POST',
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                $('.loader').hide();
                displayMessage('New info successfully added.', 'success', false);
                $("#add_info").modal('toggle');

            }
        });
    });

    function editIssue(){
        if(issue_details["status_id"] != 2){
            displayMessage('You are not allowed to change the issue details.', 'success', false);
            return false;
        }

        $.ajax({
            url:"{{ route('plan.editIssue') }}",
            type:'POST',
            data: {'id' : issue_details["id"]},
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){

                
                $('.loader').hide();

                $("#edit_issue_id").val(response["issue"]["id"]);
                $("#edit_due_by").val(response["issue"]["due_by"]);
                $("#edit_comment").val(response["issue"]["remarks"]);

                $('#edit_category').empty().append('<option value="">Please Select</option>');
                response["details"]["category"].forEach(element => {
                    $('#edit_category').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                });

                $('#edit_category').val(response["issue"]["setting_category_id"]);

                $('#edit_type').empty().append('<option value="">Please Select</option>');
                
                for (let i = 0; i < detailProject["category"].length; i++) {                    
                    if(detailProject["category"][i]["id"]  == response["issue"]["setting_category_id"]){

                        detailProject["category"][i]["type"].forEach(element => {
                            $('#edit_type').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                        });

                    }else{
                        continue;
                    }        
                }

                $('#edit_type').val(response["issue"]["setting_type_id"]);

                $('#edit_issue').empty().append('<option value="">Please Select</option>');
                for (let i = 0; i < detailProject["category"].length; i++) {

                    if(detailProject["category"][i]["id"]  == response["issue"]["setting_category_id"]){

                        for (let y = 0; y < detailProject["category"][i]["type"].length; y++) {

                            if(detailProject["category"][i]["type"][y]["id"]  == response["issue"]["setting_type_id"]){
                                
                                detailProject["category"][i]["type"][y]["issue"] .forEach(element => {
                                    $('#edit_issue').append('<option value="'+ element["id"] +'">'+ element["name"] +'</option>');
                                });

                            }else{
                                continue;
                            }
                        }

                    }else{
                        continue;
                    }        
                }

                $('#edit_issue').val(response["issue"]["setting_issue_id"]);

                // fill option in contractor
                $('#edit_contractor').empty().append('<option value="">Please Select</option>');
                response["details"]["contractor"].forEach(element => {
                    $('#edit_contractor').append('<option value="'+ element["group_details"]["id"] +'">'+ element["group_details"]["display_name"] +'('+ element["group_details"]["abbreviation_name"] +')</option>');
                });

                $('#edit_contractor').val(response["issue"]["group_id"]);

                // fill option in priority
                $('#edit_priority').empty().append('<option value="">Please Select</option>');
                response["details"]["priority"].forEach(element => {
                    $('#edit_priority').append('<option value="'+ element["priority"]["id"] +'">'+ element["priority"]["name"] +'</option>');
                });

                $('#edit_priority').val(response["issue"]["priority_id"]);

                $('#edit_inspector').empty().append('<option value="">Please Select</option>');
                detailProject["inspector"].forEach(element => {
                    $('#edit_inspector').append('<option value="'+ element["users"]["id"] +'">'+ element["users"]["name"] +'</option>');
                });

                $("#edit_inspector").val(response["issue"]["inspector_id"]);
                
                $("#menu_issue_details").modal('toggle');
                $("#edit_issue_detail").modal('toggle');
            }
        });
    }

    // submit form add issue details
    $("#form_edit_issue").submit(function(event) {
        
        event.preventDefault();

        for ( var i = 0, l = images.length; i < l; i++ ) {
            // sum += image[ i ];
            $(this).append($('<input />').attr('type', 'hidden').attr('name', 'image[]').attr('class', 'imagesDefect').attr('value', images[ i ]));
        }

        $.ajax({
            url:"{{ route('plan.updateIssue') }}",
            type:'POST',
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                $('.loader').hide();

                images = [];
                myDropzone2.removeAllFiles(true); 
                $(".imagesDefect").remove();

                displayMessage('Record successfully updated.', 'success', false);
                $("#edit_issue_detail").modal('toggle');

            }
        });
    });

    function joinIssue(){
        if(issue_details["status_id"] != 2){
            displayMessage('You are not allowed to join the issue', 'warning', false);
            return false;
        }

        setup_type = "issue";
        setup_mode = "join";

        $("#allIssue").show();
        $('#menu_issue_details').modal('toggle');

        setupPlanDisplay(parseInt(issue_details.location_id), null, "issue_" + issue_details.id);

        displayMessage('Select the issue marker to join the issue.', 'info', false);
    }

    function mergeHistory(){
        $.ajax({

            url:"{{ route('plan.mergeHistory') }}",
            type:'POST',
            data: {'id' : issue_details.id },
            beforeSend:function(){
                $('.loader').show();
            },
            success:function(response){
                var data,img_issue_url, issue_created_at;

                if(response["errors"]){
                    $('.loader').hide();

                    displayMessage(response["errors"], 'info', false);
                    return false;
                }

                $("#body_list_joinIssue").html('');
                response.forEach(element => {

                    // if(issue_mode == 'merge' && element["id"] == active_marker_id ){
                    //     return ;
                    // }

                        img_issue_url = '<a href="'+ flag_url +'/uploads/issues/'+ element["start_image"][0]["image"] +'" data-popup="lightbox"><img src="'+ flag_url +'/uploads/issues/'+ element["start_image"][0]["image"] +'" style="width: 58px; height: 58px; border-radius: 2px;" alt=""></a>';
                    

                        data = '<div class="row">'+
                        '<div class="col-md-12 col-xs-12">'+
                            '<strong><h5 style="font-size: 12px;">&nbsp;&nbsp;'+ element["reference"] +'&nbsp;&nbsp;<span class="label" style="background-color: grey;color: white;"> <strong>MERGE</strong></span></strong></h5>'+
                        '</div>'+
                        '<div class="col-md-6 col-xs-6" align="right">';
                            data += '</div>'+
                        '</div>'+
                    '</div>';
                    
                    data +='<div class="row">'+
                        '<div class="col-md-12">'+
                            '<div class="media no-margin-top">'+
                                '<div class="media-left">'+
                                    img_issue_url
                                +'</div>'+

                                '<div class="media-body">'+
                                    '<strong>'+ element['location']['name'] +'</strong>'+
                                    '<span class="help-block">Created on '+ element["new_created_at"] +'</span>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="table-responsive table-xxs">'+
                        '<table class="" style="width: 100%;">'+
                            '<tr>'+
                                '<th style="font-size: 12px;w" width="25%">Priority</th>'+
                                '<td style="font-size: 12px;"> : '+ element['priority']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Category</th>'+
                                '<td style="font-size: 12px;">: '+ element['category']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Type</th>'+
                                '<td style="font-size: 12px;"> : '+ element['type']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Issue</th>'+
                                '<td style="font-size: 12px;"> : '+ element['issue']['name'] +'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<th style="font-size: 12px;">Due By</th>'+
                                '<td style="font-size: 12px;"> : '+ element['priority']['name'] +'</td>'+
                            '</tr>'+
                        '</table>'+
                    '</div><hr>';
                    $("#body_list_joinIssue").append(data);

                });
                
                setTimeout(function(){
                    $('.loader').hide();
                    $('#list_join_issue').modal('toggle');
                },500);
            },

        });
    }      

    function voidIssue(){
        $.ajax({
            url: '{{ route("plan.voidIssue") }}',
            type:'POST',
            data: {'id' : issue_details.id},
            success:function(response){
                $('#menu_issue_details').modal('toggle');
                
                var $location_id = parseInt(response.location_id);
                var l_index = searchById($location_id, drawing_plan_detail.location);
                var i_index = searchById(response.id, drawing_plan_detail.location[l_index].issues);
                drawing_plan_detail.location[l_index].issues[i_index] = response;
                setupZoneElement();
            },  
        });
    }

    function readyInpect(){
        updateLocation(2);
    }

    function closeHandOver(){
        updateLocation(3);
    }

    function updateLocation(status_id){

        $.ajax({
            url: '{{ route("plan.updateLocationStatus") }}',
            type:'POST',
            data: {'id' : active_marker_id, 'status_id' : status_id },
            success:function(response){
                
                $('#location_menu').modal('toggle');

                $l_index = searchById(parseInt(active_marker_id), drawing_plan_detail.location);
                drawing_plan_detail.location[$l_index].status_id = parseInt(status_id);

                displayMessage('Record successfully updated.', 'info', false);
                setupZoneElement();
            },  
        });
    }
    </script>

    <!-- UPLOAD IMAGE DEFECT  -->
    <script type="text/javascript">
    $(document).ready(function () {
        Dropzone.autoDiscover = false;
            myDropzone = new Dropzone("div#my-dropzone", {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                acceptedFiles: ".png,.jpg,.jpeg",
                // maxFiles: 2,
                url: "{{ route('ajax.uploadDefect') }}",
                type: 'post',
                maxFilesize: 10,
                addRemoveLinks: true,

                //maxFilesize //dz-max-files-reached

                autoProcessQueue: true,
                success: function (file, response) {
                    file.previewElement.classList.add("dz-success");
                    file.previewElement.id = response;
                    images.push(response);

                    $("#btnAddIssueSubmit").attr('disabled', false);
                },
                error: function (file, response) {
                    file.previewElement.classList.add("dz-error");
                    $('[class="dz-error-message"]').css("color", "pink");
                    $('[class="dz-error-message"]').css("top", "70px");
                    $('[class="dz-error-message"]').text('Max file exceeded.');
                }
            });

            myDropzone.on("maxfilesreached", function(file) {
                $('div#my-dropzone').removeClass('dz-clickable');
                myDropzone.removeEventListeners();

            });

            myDropzone.on('removedfile', function (file) {
                var image = file.previewElement.id;

                var url = '{{ route('ajax.destroyDefect') }}';
                if(images.length > 0){

                    if (image) {
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: { image: image,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if(response.status=='ok'){
                                    images.splice($.inArray(image, images),1);

                                }else{
                                    // swal("Failed!", "Data failed to delete.", "error");
                                }

                                if(images.length > 0) {
                                    $("#btnAddIssueSubmit").attr('disabled', false);

                                }else{
                                    $("#btnAddIssueSubmit").attr('disabled', true);
                                }
                            }
                        });
                        return false;
                    }
                }
                $('div#my-dropzone').addClass('dz-clickable');
                myDropzone.setupEventListeners();
            });

            myDropzone2 = new Dropzone("div#my-dropzone2", {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                acceptedFiles: ".png,.jpg,.jpeg",
                // maxFiles: 2,
                url: "{{ route('ajax.uploadDefect') }}",
                type: 'post',
                maxFilesize: 10,
                addRemoveLinks: true,

                //maxFilesize //dz-max-files-reached

                autoProcessQueue: true,
                success: function (file, response) {
                    file.previewElement.classList.add("dz-success");
                    file.previewElement.id = response;
                    images.push(response);

                    $("#btnAddIssueSubmit").attr('disabled', false);
                },
                error: function (file, response) {
                    file.previewElement.classList.add("dz-error");
                    $('[class="dz-error-message"]').css("color", "pink");
                    $('[class="dz-error-message"]').css("top", "70px");
                    $('[class="dz-error-message"]').text('Max file exceeded.');
                }
            });

            myDropzone2.on("maxfilesreached", function(file) {
                $('div#my-dropzone').removeClass('dz-clickable');
                myDropzone2.removeEventListeners();

            });

            myDropzone2.on('removedfile', function (file) {
                var image = file.previewElement.id;

                var url = '{{ route('ajax.destroyDefect') }}';
                if(images.length > 0){

                    if (image) {
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: { image: image,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if(response.status=='ok'){
                                    images.splice($.inArray(image, images),1);

                                }else{
                                    // swal("Failed!", "Data failed to delete.", "error");
                                }

                                if(images.length > 0) {
                                    $("#btnAddIssueSubmit").attr('disabled', false);

                                }else{
                                    $("#btnAddIssueSubmit").attr('disabled', true);
                                }
                            }
                        });
                        return false;
                    }
                }
                $('div#my-dropzone').addClass('dz-clickable');
                myDropzone2.setupEventListeners();
            });

            // Remove file if modal is closed
            // $('#add_issue_detail').on('hidden.bs.modal', function () {

            //     myDropzone.getAcceptedFiles().forEach(element => {
            //         var image = element.previewElement.id;

            //         var url = '{{ route('ajax.destroyDefect') }}';
            //         if (image) {
            //             $.ajax({
            //                 url: url,
            //                 type: 'post',
            //                 data: { image: image,
            //                     _token: "{{ csrf_token() }}"
            //                 },
            //                 success: function (response) {
            //                     if(response.status=='ok'){
            //                         images.splice($.inArray(image, images),1);

            //                     }else{
            //                         // swal("Failed!", "Data failed to delete.", "error");
            //                     }
            //                     reset();
            //                 }
            //             });
            //             return false;
            //         }

            //     });

            //     myDropzone.removeAllFiles();
            // })

            function reset(){
                $("#btnAddIssueSubmit").attr("disabled", true);  
            }

    });
    </script>
@endsection
