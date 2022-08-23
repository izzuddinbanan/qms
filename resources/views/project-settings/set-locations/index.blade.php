@extends('components.template-limitless.main')

@section('main')
@include('project-settings.components.tab')
    
<style type="text/css">
    #div-image {
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
        background-size: 100% auto;
        position: relative;
        overflow-x:hidden;
        overflow-y:hidden;
    }

    #container {
        position: absolute!important;
        overflow-x:hidden;
        overflow-y:hidden;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .btn-option {
        padding: 0px;
        height:34px !important; 
        width:34px !important;
    }

    .FixedHeightContainer
    {
        float:right;
        width:250px; 
        padding-top: 2px;
        background:#cecece;
    }
    .Content
    {
        height:224px;
        overflow:auto;
        background:#fff;
    }
</style>

<div class="loader" style=""></div>
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-compass"></i> @lang('project.location') <small style="cursor: pointer;"><i class="fa fa-question-circle-o" data-popup="tooltip" title="@lang('project.locationHelp')" data-placement="top"></i></small>
                </h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group btn-top">
                    <a href="{{ route('set-inspection.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-employee.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row" style="padding-bottom: 20px;">
            <div class="col-md-9 col-xs-9">
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        <select data-placeholder="Select Drawing Set" class="select-search" name="drawing_set" id="drawing_set" autofocus="" required="">
                            <option value="">Please Select</option>
                            @foreach($data as $drawingSet)
                                <option value="{{ $drawingSet->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $drawingSet->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-xs-4">
                        <select data-placeholder="Select Drawing Plan" class="select-search" name="drawing_plan" id="drawing_plan" autofocus="" required="">
                            <option value="">Please Select</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-xs-4">
                        <button type="button" class="btn btn-success btn-sm btn-add-location" style="display: none" data-popup="tooltip" title="@lang('project.locationAdd')" data-placement="top" onclick="editToggle('create')"><i class="icon-add"></i></button>
                        <button type="button" class="btn btn-primary btn-sm btn-add-location" style="display: none" data-popup="tooltip" title="Duplicate Location" data-placement="top" onclick="duplicate()">Duplicate</button>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex">
            <div>
                <div id="div-image" class="panel panel-flat">
                    <div id='container'></div>
                </div>
            </div>
            <div id="setup_form" class="panel panel-flat" style="flex-grow: 1;">
                <div class="table-responsive fixed_header" id="listing" style="display:block;">
                    <table class="table table-hover">
                        <thead class="dashboard-table-heading">
                            <tr>
                                <th style="width: 60%">@lang('project.areaName')</th>
                                <th>@lang('project.action')</th>
                            </tr>
                        </thead>
                        <tbody id="location_listing"></tbody>
                    </table>
                </div>
                <form style="display: none;" id="edit_form" class="panel-body">
                    <div class="form-group">
                        <legend class="text-size-mini text-muted no-border no-padding no-margin">@lang('project.areaName')</legend>
                        <input class="form-control" type="text" id="name" required autocomplete="off">
                    </div>
                    
                    
                    {{--<div class="form-group">
                        <legend class="text-size-mini text-muted no-border no-padding no-margin">Normal Form</legend>
                        <select multiple="" class="select select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="normal_form" id="normal_form">
                            <optgroup label="Single Form">
                                @foreach($forms as $form)

                                    @if($form->selected)
                                        <option value="s-{{ $form->id }}">{{ $form->name }}</option>

                                    @endif

                                @endforeach
                            </optgroup>
                            <optgroup label="Group Form">
                                @foreach($groupForm as $gform)

                                    @if($gform->selected)
                                        <option value="g-{{ $gform->id }}">{{ $gform->name }}</option>

                                    @endif

                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group">
                        <legend class="text-size-mini text-muted no-border no-padding no-margin">Hand Over Form</legend>
                        <select multiple="" class="select select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="hand_form" id="hand_form">
                            <optgroup label="Single Form">
                                @foreach($forms as $form)

                                    @if($form->selected)
                                        <option value="s-{{ $form->id }}">{{ $form->name }}</option>

                                    @endif

                                @endforeach
                            </optgroup>
                            <optgroup label="Group Form">
                                @foreach($groupForm as $gform)

                                    @if($gform->selected)
                                        <option value="g-{{ $gform->id }}">{{ $gform->name }}</option>

                                    @endif

                                @endforeach
                            </optgroup>
                        </select>
                    </div>--}}
                    <div class="form-group for-edit">
                        <legend class="text-size-mini text-muted no-border no-padding no-margin">@lang('project.reference')</legend>
                        <input class="form-control" type="text" id="reference" disabled>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" id="btn-manage-form" type="button">Manage Form <i class="fa fa-folder-open-o"></i></button>
                    </div>
                    <div class="form-group">
                        <legend class="text-size-mini text-muted no-border no-padding no-margin">@lang('project.presetColor')</legend>
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


    <div id="modal_instruction" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">@lang('project.basicInstruc')</h5>
                </div>

                <div class="modal-body">
                    <h6 class="text-semibold">@lang('project.note'):</h6>
                    <p>1. @lang('project.note_1')</p>
                    <p>2. @lang('project.note_2')</p>
                    <p>3. @lang('project.note_3')</p>
                    <p>4. @lang('project.note_4')</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">@lang('project.understand')</button>
                </div>
            </div>
        </div>
    </div>

    <div id="setup-form-location" class="modal fade"  data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Form Location Setup</h5>
                    </div>
                    <div class="modal-body">

                        <div class="tabbable">
                            <ul class="nav nav-tabs bg-slate nav-tabs-component nav-justified">
                                <li class="active"><a href="#colored-rounded-justified-tab1" data-toggle="tab">Form</a></li>
                                <li><a href="#colored-rounded-justified-tab2" data-toggle="tab">Hand Over Form</a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="colored-rounded-justified-tab1">
                                    <div class="col-md-12 col-xs-12">
                                        <select multiple="multiple" class="form-control listbox-normalForm" name="normalForm[]" id="normalForm">
                                        </select>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <hr>
                                            <label>List of Selected Form</label>
                                            <div style="max-height: 200px; overflow: auto;">
                                                <ol id="listNormalForm"></ol>
                                            </div>

                                        </div>
                                    </div>

                                      
                                </div>

                                <div class="tab-pane" id="colored-rounded-justified-tab2">
                                    <div class="col-md-12 col-xs-12">
                                        <select multiple="multiple" class="form-control listbox-handOverForm" name="handOverForm[]" id="handOverForm">
                                        </select>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <hr>
                                            <label>List of Selected Form</label>
                                            <div style="max-height: 200px; overflow: auto;">
                                                <ol id="listHandOverForm"></ol>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="refresh()">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal_duplicate" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('set-location.duplicate') }}" method="POST">
                    @csrf
                    
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Duplicate</h5>
                    </div>

                    <div class="modal-body">
                            
                        <div class="col-md-6 col-xs-6">
                        <input type="hidden" name="drawing_plan_id_location" id="drawing_plan_id_location" value="">
                            <label class="label">Drawing Set</label>
                            <select class="select-search" name="drawingSetLocation" id="drawingSetLocation" required="">
                                <option value="">Please Select</option>
                                @foreach($listDrawingSet as $drawSet)
                                    <option value="{{ $drawSet['id'] }}">{{ $drawSet['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 col-xs-12">
                            <hr>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <select multiple="multiple" class="form-control listbox-drawingPlanLocation" name="drawingPlanLocation[]" id="drawingPlanLocation" required="">
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="{{ url('js/konva.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/pickers/color/spectrum.js') }}"></script>
<script src="{{ url('js/drawarea.js') }}" type="text/javascript" ></script>
<script type="text/javascript">
    
    var select = '',
        allData,
        mode,
        panel_width,
        panel_height,
        ori_width,
        ori_height,
        edit_mode = false,
        flagsUrl = '{{ URL::asset('/uploads/drawings/') }}';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function duplicate () {

        $("#modal_duplicate").modal('toggle');
        $("#drawing_plan_id_location").val($("#drawing_plan").val());
    }

    $(document).ready(function(){
            // $("#setup-form-location").modal('toggle');

        $('body').addClass('sidebar-xs');

        setupKonvaElement('container');
        stage.on("dragstart", function(e) {
            e.target.moveTo(drawLayer);
            layer.draw();

            return false;
        }).on("dragend", function(e){
            drawLayer.draw();
            return false;
        }).on("click", function(e) {
            if (edit_mode) {
                e.evt.preventDefault();
            
                if (e.evt.which === 1) {
                    var pos = this.getPointerPosition();
                    var shape = layer.getIntersection(pos);
                    var temp_shape = drawLayer.getIntersection(pos);
                    if (!shape && !temp_shape) points.splice(points.length, 0, Math.round(pos.x), Math.round(pos.y)); 
                    
                    drawLine();
                }
            } else {
                var pos = this.getPointerPosition();
                var shape = layer.getIntersection(pos);
                
                if (shape) {
                    var $index = e.target.attrs.id.split('_')[1];
                    setSelected($index);

                    row = $("#lrow_" + $index);
                    if (row.length){
                        $("#listing tbody").scrollTop(0);
                        $("#listing tbody").scrollTop(row.offset().top - ($("#listing tbody").height() + 20));
                    }
                }
            }

            return false;
                
        });

        panel_width = $(".panel-body").width() * 0.7;
        $("#div-image").parent().css('width', panel_width);
        $("#setup_form").css('flex-basis', $(".panel-body").width() * 0.3).css("margin-left", "10px");
        
        $.ajax({
            url:"{{ route('set-link.get-all-set') }}",
            type:'POST',
            success:function(response){
                allData = response;

                $("#drawing_set").trigger('change');
            }
        });

        $(".colorpicker-palette").spectrum({
            showPalette: true,
            palette: colorPalette
        });

        
    });

    $("#drawing_set").on('change', function(){
        zones = [];
        $.ajax({
            url:"{{ route('set-link.list-plan') }}",
            type:'POST',
            data: {'drawing_set' : $("#drawing_set").val() },
            success:function(response){
                resetAll();

                $("#div-image")
                    .css('background-image', 'none')
                    .css('width', '0px')
                    .css('height', '0px');

                $('#drawing_plan').empty().append('<option value="">Please Select</option>');
                
                response.forEach(element => {
                    if(element["default"] == 1){
                        select = 'selected';
                    }else{
                        select = '';
                    }
                    $('#drawing_plan').append('<option value="'+ element["id"] +'" '+ select +'>'+ element["name"] +'</option>');
                });
                $('.loader').hide(); 
                if($('#drawing_plan').val() != ''){
                    $("#drawing_plan").trigger('change');
                }
            }
        });
    });

    $("#drawing_plan").on('change', function(){

        $("#drawing_plan_from").val($("#drawing_plan").val());
        $(".btn-add-location").show();
        $("#location_listing").html("");
        zones = [];
        $.ajax({
            url: {!! json_encode(url('/')) !!} + "/plan/" + $("#drawing_plan").val(),
            type:'GET',
            success:function(response){
                $('.loader').hide();    

                console.log(response);
                ori_height = response["height"];
                ori_width = response["width"];

                $calculation = $(".panel-body").width() / 1.4;
                if (ori_width >= $calculation) {
                    panel_width = $(".panel-body").width() * 0.7;
                } else {
                    panel_width = ($(".panel-body").width() / 2);
                }
                panel_width -= 2;
                panel_height = ori_height / (ori_width / panel_width);
                    
                $("#div-image")
                    .css("background-image", "url(" + flagsUrl +'/'+ response["file"] + ")")
                    .css("width", panel_width + 'px')
                    .css("height", panel_height + 'px');

                resetKonvaElement(panel_width, panel_height);

                setTimeout(function(){
                    $('.loader').hide();
                    response["location"].forEach((element, index) => {
                        formatToZones(element, ori_width, ori_height, panel_width, panel_height);
                    });

                    setupZoneAndEvent();

                }, 500); //end function settimeout
            }, //end function response
        });
    });

    function setSelected(index) {
        $("[id^='action_']").hide();

        $("#action_" + index).show();
        current_zone = JSON.parse(JSON.stringify(zones[index]));
        points = current_zone.points;
        color = current_zone.color;

        layer.removeChildren().draw();        
        drawLayer.removeChildren().draw();     
        drawZones();
        drawLine();
    }

    function prepareForEdit() {
        edit_mode = true;

        $("#listing").hide();
        $("#edit_form").show();

        editToggle("update");

        $("#name").val(current_zone.name);
        $("#reference").val(current_zone.reference);
        $(".colorpicker-palette").spectrum("set", current_zone.color);


    }

    function resetAll() {
        edit_mode = false;
        current_zone = {};
        points = [];

        $("#name").val("");
        $("#reference").val("");
        $(".colorpicker-palette").spectrum("set", getRandomColor());

        editToggle("view");
        setupZoneAndEvent();
        drawLine();

        normal_form_selected = [];
        hand_over_form_selected = [];
    }

    function saveArea() {
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
        
        var allZones = layer.find('Line');
        if (allZones.length > 0) {
            for (var i = 0; i < allZones.length; i++) {
                var $check = checkOverlay(allZones[i].points(), drawLayer); 
                if (!$check) {
                    displayMessage('Overlay is not allow', 'warning', false);
                    return false;
                }   
            }
        }

        bootbox.confirm("Are you sure to save this work ?", function (result) {
            if (result) {

                var input = calculatePoints('store', points, ori_width, ori_height, panel_width, panel_height);
                color = $(".colorpicker-palette").spectrum('get').toHexString();
                input = input.join();

                normal_form_selected = $("#normalForm").val() ? $("#normalForm").val() : [];
                hand_over_form_selected = $("#handOverForm").val() ? $("#handOverForm").val() : [] ;
                if (mode == 'create') {  
                    $('.loader').show();
                    $.ajax({
                        url:"{{ route('set-location.store') }}",
                        type:'POST',
                        data: {
                            drawing_plan_id: $("#drawing_plan").val(),
                            color: color,
                            points: input,
                            name: $("#name").val(),
                            normal_form: normal_form_selected,
                            hand_form: hand_over_form_selected,
                        },
                        success:function(response){

                            $('.loader').hide();
                            displayMessage('Record successfully created.', 'success', false);
                            
                            formatToZones(response, ori_width, ori_height, panel_width, panel_height);
                            resetAll();
                        }
                    });
                } else {
                    $.ajax({
                        url:"{{ route('update-location.set-location') }}",
                        type:'POST',
                        data: {
                            name : $("#name").val(),
                            id: current_zone.id,
                            points: input,
                            color: color,
                            drawing_plan_id: $("#drawing_plan").val(),
                            normal_form: normal_form_selected,
                            hand_form: hand_over_form_selected,
                        },
                        beforeSend:function() {
                            $('.loader').show();
                        },
                        success:function(response){

                            setTimeout(function(){
                                $('.loader').hide();

                                displayMessage('Record successfully updated.', 'success', false);

                                const index = searchById(current_zone.id, zones);
                                zones[index].name = response.name;
                                zones[index].reference = response.reference;
                                zones[index].points = points;
                                zones[index].color = color;

                                zones[index].normal_form = response.normal_form == null ? [] : response.normal_form.split(',');
                                zones[index].normal_group_form = response.normal_group_form == null ? [] : response.normal_group_form.split(',');
                                zones[index].main_form = response.main_form == null ? [] : response.main_form.split(',');
                                zones[index].main_group_form = response.main_group_form == null ? [] : response.main_group_form.split(',');

                                resetAll();
                            },300);
                        }
                    });
                }
            }
        });
    }

    function cancelMove(){
        bootbox.confirm("Are you sure to cancel current unsaved work ?", function (result) {
            if (result) {
                resetAll();
            }
        });
    }

    function deleteArea() {
        bootbox.confirm("Are you sure to remove ? All issue in this location will be removed. ", function (result) {
            if (result) {
                $.ajax({
                    url: "{!! route('set-location.remove-location' ) !!}",
                    type:'post',
                    data: {'id': current_zone.id, 'drawing_plan' : $("#drawing_plan").val() },
                    success:function(response){
                        displayMessage(response["msg"], response["type"], false);

                        if(response['type'] == 'success'){
                            var index = searchById(current_zone.id, zones);
                            delete zones[index];
                            resetAll();
                        }
                    }
                });
            }
        });
    }

    $(".colorpicker-palette").change(function(e) {
        color = $(".colorpicker-palette").spectrum('get').toHexString();

        drawLine();
    });

    function editToggle(mode) {
        this.mode = mode;

        if (this.mode == "create" || this.mode == "update") {
            edit_mode = true;
            color = $(".colorpicker-palette").spectrum('get').toHexString();
            $("#listing").hide();
            $("#edit_form").show();
            $(".btn-add-location").hide();

            if (this.mode == "create") {
                current_zone = {};
                points = [];
                $(".for-add").show();

                layer.removeChildren().draw();        
                drawLayer.removeChildren().draw();     
                drawLine();
                drawZones();
                $("#modal_instruction").modal('show');
            } else {
                $(".for-edit").show();
            }
        } else {
            edit_mode = false;
            $(".btn-add-location").show();
            $(".for-add, .for-edit").hide();
            $("#listing").show();
            $("#edit_form").hide();
        }
    }

    function setupZoneAndEvent() {
        layer.removeChildren().draw();        
        drawLayer.removeChildren().draw();        
        drawZones();
        $("#location_listing").html("");

        if (zones.length > 0) {
            zones.forEach((zone, index) => {

                $("#location_listing")
                    .append('<tr id="lrow_' + index + '" onClick="setSelected(' + index + ')" style="height: 61px !important;">' + 
                        '<td style="width: 60%">' + zone.name + '</td>' +  
                        '<td>' +
                            '<div style="display: none;" id="action_' + index + '">' +  
                                '<button class="btn btn-primary btn-option" onClick="prepareForEdit()"><i class="fa fa-edit" aria-hidden="true"></i></button>' +
                                '<button class="btn btn-danger btn-option" style="margin-left: 2px;" onClick="deleteArea()"><i class="fa fa-trash" aria-hidden="true"></i></button>' +     
                            '</div>' +
                        '</td></tr>');
            });
        } else {
            $("#location_listing").append("<tr><td align='center' colspan='3'>@lang('general.no_result')!</td></tr>");
        }
    }


</script>

@endsection

@section('script')
<script type="text/javascript">

    var normal_form = {!! json_encode($forms) !!};
    var group_form = {!! json_encode($groupForm) !!};

    var normal_form_selected = [];
    var hand_over_form_selected = [];


    function isEmpty(obj) {
        for(var key in obj) {
            if(obj.hasOwnProperty(key))
                return false;
        }
        return true;
    }

    $(document).ready(function(){

        var listDualNormalForm = $('.listbox-normalForm').bootstrapDualListbox({
            preserveSelectionOnMove: 'moved',
            bootstrap2compatible : true,
            moveOnSelect: false
        });

        var listDualHandOverForm = $('.listbox-handOverForm').bootstrapDualListbox({
            preserveSelectionOnMove: 'moved',
            bootstrap2compatible : true,
            moveOnSelect: false
        });

        $("#btn-manage-form").click(function(){
            

            var check_old_form = isEmpty(current_zone);
       
            listDualNormalForm.empty();
            listDualHandOverForm.empty();
            $("#listNormalForm").html('');
            $("#listHandOverForm").html('');

            normal_form.forEach(element => {
                var selectedNorm = '';
                var selectedHandOver = '';

                if(check_old_form){ //create location

                    if(normal_form_selected.indexOf('s-' + element.id) != '-1'){
                        selectedNorm = 'selected'; 
                    }


                    if(hand_over_form_selected.indexOf('s-' + element.id)  != '-1'){
                        selectedHandOver = 'selected'; 
                    }
                }else{

                    if(current_zone.normal_form.indexOf(''+element.id+'' || element.id) != '-1'){
                        selectedNorm = 'selected'; 
                    }

                    if(current_zone.main_form.indexOf(''+element.id+'' || element.id) != '-1'){
                        selectedHandOver = 'selected'; 
                    }


                }
           

                listDualNormalForm.append('<option value="s-' + element.id + '" '+ selectedNorm +'>' + element.name + '</option>');
                listDualHandOverForm.append('<option value="s-' + element.id + '" '+ selectedHandOver +'>' + element.name + '</option>');
            });

            group_form.forEach(element => {
                
                var selectedNorm = '';
                var selectedHandOver = '';

                if(check_old_form){ //create location

                    if(normal_form_selected.indexOf('g-' + element.id)  != '-1'){
                        selectedNorm = 'selected'; 
                    }


                    if(hand_over_form_selected.indexOf('g-' + element.id)  != '-1'){
                        selectedHandOver = 'selected'; 
                    }

                }else{

                 
                    if(current_zone.normal_group_form.indexOf(''+element.id+'' || element.id) != '-1'){
                        selectedNorm = 'selected'; 
                    }

                    if(current_zone.main_group_form.indexOf(''+element.id+'' || element.id) != '-1'){
                        selectedHandOver = 'selected'; 
                    }


                }

                listDualNormalForm.append('<option value="g-' + element.id + '" '+ selectedNorm +'>' + element.name + ' (group)</option>');
                listDualHandOverForm.append('<option value="g-' + element.id + '" '+ selectedHandOver +'>' + element.name + ' (group)</option>');
            });

            listDualNormalForm.bootstrapDualListbox('refresh', true);
            listDualHandOverForm.bootstrapDualListbox('refresh', true);


            $("#setup-form-location").modal('toggle');
             setTimeout(function() {

                $("#handOverForm").trigger('change');
                $("#normalForm").trigger('change');
            }, 1000);
        });

        $('.listbox').bootstrapDualListbox({
            preserveSelectionOnMove: 'moved',
            bootstrap2compatible : true,
            moveOnSelect: false
        });

        $("#normalForm").change(function(){

            var form_id = $(this).val();

            $.ajax({
                url: "{!! route('set-location.listFormSelect') !!}",
                type:'post',
                data: {'form_id': form_id },
                success:function(response){
                    
                    var form_data = response;
                    $("#listNormalForm").html('');
                    response.forEach(element => {

                        $("#listNormalForm").append("<li>" + element.name + "</li>");
                        
                        
                    });
                    
                }
            });

        });

        $("#handOverForm").change(function(){
            var form_id = $(this).val();
            $.ajax({
                url: "{!! route('set-location.listFormSelect') !!}",
                type:'post',
                data: {'form_id': form_id },
                success:function(response){
                    
                    var form_data = response;
                    $("#listHandOverForm").html('');
                    response.forEach(element => {
                        $("#listHandOverForm").append("<li>" + element.name + "</li>");
                    });
                    
                }
            });

        });

    })


    function refresh(e){

        $('.listbox').trigger('bootstrapDualListbox.refresh', true);

        $("#setup-form-location").modal('toggle');

        normal_form_selected = $("#normalForm").val() ? $("#normalForm").val() : [];
        hand_over_form_selected = $("#handOverForm").val() ? $("#handOverForm").val() : [] ;

        
    }

    
    $(document).ready(function(){


        var listDrawingPlanLocation = $('.listbox-drawingPlanLocation').bootstrapDualListbox({
            preserveSelectionOnMove: 'moved',
            bootstrap2compatible : true,
            moveOnSelect: false
        });


        var listDrawingSet = {!! json_encode($listDrawingSet) !!}
        $("#drawingSetLocation").change(function(){
                
            listDrawingPlanLocation.empty();
            var set_id = $(this).val();

            $.ajax({
                url: "{!! route('set-location.listDrawingPlan') !!}",
                type:'post',
                data: {'drawing_set_id': set_id },
                success:function(response){
                    
                    response.forEach(plan => {
                        listDrawingPlanLocation.append('<option value="' + plan.id + '">' + plan.name + '</option>');
                    });
                    listDrawingPlanLocation.bootstrapDualListbox('refresh', true);

                }
            });



            // listDrawingSet.forEach(element => {
            //     if(element["id"] == set_id){
            //         element["drawing_plan"].forEach(plan => {
            //             if(plan.location_no_general.length == 0){
            //                 listDrawingPlanLocation.append('<option value="' + plan.id + '">' + plan.name + '</option>');
            //             }

            //         });
            //     }
            // });

            // listDrawingPlanLocation.bootstrapDualListbox('refresh', true);
        });
    })
</script>
@endsection
