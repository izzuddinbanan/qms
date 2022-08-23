@extends('layouts.template2') @section('main')
<style>
body {
    padding-right: 0px !important;
}

#form_option .form-group {
    margin-bottom: 10px !important;
}

#top_form_menu .form-group {
    margin-bottom: 10px !important;
}

#top_form_menu {
    background-color: #dcd9cd;   
    top:48px !important; 
    z-index: 999; 
    padding-top: 6px !important; 
}

.editing-mode, .display-mode, .preview-mode, .print-mode {
    z-index: 10;
    font-size: : 8;
    resize: none;
    position: absolute !important;
}

.editing-mode {
    background-color: #BDF0FF;
    cursor: grab;
}

.display-mode {
    background-color: #FFE9E3;
    cursor: grab;
}

.preview-mode {
    background-color: #F8EDDB;
}

#image_file_container div p {
    color: grey !important;
}

.btn-icon {
    background-color: #37474F;
    /* width: 40px !important; */
}

.btn-next i, .btn-prev i, .btn-icon i {
    color: white;
}

.btn-preview-prev, .btn-preview-next {
    position: absolute !important;
    top: 50% !important;
    width: 30px !important;
}

.detail-panel-heading {
    padding-top: 6px !important;
    padding-bottom: 10px !important;
    background-color: #006D8D !important;
}

.detail-panel-heading span, .detail-panel-heading h3 {
    color: white;
}

.detail-panel-body {
    padding-top: 22px!important;
}

.detail-panel-body .form-group label {
    font-weight: bold;
}

@media screen{
    #screenarea {
        display: block;
    } 

    #printarea {
        display: none;
    }
}

@media print{
    #screenarea {
        display: none;
    } 

    #printarea {
        display: block;
    }
}

</style>

<div class="content" id="screenarea">
	<div class="panel panel-flat">
		<form action="{{ route('closeAndHandover.submit', [$drawing_plan->id]) }}" method="post" id="myForm">
			@csrf
			<div class="panel-body">
				<div class="row">
                    <input type="hidden" name="drawing_plan_id" value="{{$drawing_plan->id}}"/>
                    <input type="hidden" name="form_id" value="{{$form_version->id}}"/>
	                <div class="col-md-12 col-xs-12" id="image_file_container"></div>
	            </div>
	            <div class="row text-right">
	            	<button type="submit" class="btn btn-primary">Close & Handover</button>
	            </div>
			</div>
		</form>
	</div>
</div>

<div id="printarea"></div>

<!-- modal_add_location-->
<div id="modal_add_location" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h5 class="modal-title">
					 Select Input Type					
				</h5>
            </div>
            <form id="form_add_input">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Option</label> 
                                <select required name="attribute_id" class="form-control" id="modal_add_option_attribute">
                                    <option value="">Please Select</option> 
                                    @foreach($option as $data)
                                    <option value="{{ $data->id }}">{{ $data->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group add-no-input">
                                <label>Number of Input</label> 
                                <input autocomplete="off" type="number" id="modal_add_no_input" value="1" min="1" class="form-control"> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
		</div>
	</div>
</div>
<!-- modal_add_location -->

<!-- modal signature box -->
<div id="modal_signature_box" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Signature</h4>
            </div>
            <div class="modal-body">
                <div class="row" align="center">
                    <input type="hidden" id="for_modal_id" value="">
                    <input type="hidden" id="for_location_id" value="">
                    <canvas id="signature_pad" width="400" height="400" style="border-style:solid; border-width:thin; border-color: black;"></canvas>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" style="float: left" class="btn btn-primary btn-sm" onclick="clearSignaturePad()">Clear</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="getSignatureImage()">OK</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- -->



<script type="text/javascript" src="{{ asset('assets/plugins/input-tag/tagsinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/plugins/input-tag/prism.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
<script type="text/javascript">

    // global variable -- start -- 
    var        
        mode, 
        switchery,
        signaturePad,
        editBool = false,
        forms = {!! json_encode($form_version->forms) !!},
        formVersionID = {!! json_encode($form_version->id) !!},
        options = {!! json_encode($option->toArray()) !!},
        roles = {!! json_encode($roles) !!},
        sections = {!! json_encode($sections) !!},
        formAttributesHolder = [],
        currentAttribute = {},
        preview_index,
        currentFormIndex = 0;
    // global variable -- end -- 

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
        }
    });

    // refresh to new -- start --
    function resetCurrentAttribute() {
        currentAttribute = {};
        editBool = false; 
        $(".btn-form").prop('disabled', false);

        $("[id^=point_]").css("pointer-events", "")
            .removeClass("editing-mode")
            .addClass("display-mode")
            .draggable().draggable("destroy")
            .resizable().resizable("destroy");   
        
        $(".option-dropdown-value, .option-no-row, .option-no-input").hide();
        $("#form_option").trigger("reset");
        $("#modal_option_dropdown_value").tagsinput("removeAll");
        $("#modal_option_section").val("").trigger("change");
        $("#modal_option_roles").val("").trigger("change");
        $("#modal_option_mandatory").prop("checked", false);
        $(".bootstrap-tagsinput input").prop("required", false);
        
        if (switchery != undefined) {
            switchery.destroy();
            $("#" + switchery.element.id).removeAttr("style");
            switchery.switcher.remove();
        }

        $("#modal_option_title, #modal_option_no_row, #modal_option_mandatory").off("input");
        // $("#modal_option_roles, #modal_option_section, #modal_option_dropdown_value").select2().off("change");

        $("#form_option").hide();
    }
    // refresh to new -- end --

  	$(document).ready(function () {
        $("body").disableSelection();
        $(".add-no-input").hide();
        signaturePad = new SignaturePad(document.getElementById("signature_pad"));

        // when document ready, loop and format object 
        forms.forEach(form => {
            let form_obj = {
                "id" : form["id"],
                "file": "{{ URL::asset('/uploads/forms/') }}" + '/' + form["file"],
                "height" : form["height"],
                "width" : form["width"],
            };

            form_obj.attributes = [];
            form["form_attributes"].forEach(attribute => {
                
                let attribute_obj = {
                    "id": attribute["id"],
                    "attribute_id": attribute["attribute_id"],
                    "key": attribute["key"],
                    "is_required": attribute["is_required"],
                    "form_section_id": attribute["form_section_id"],
                }
                
                attribute_obj.locations = [];
                attribute["locations"].forEach(location => {
                    attribute_obj.locations.push({
                        "id"             : location["id"],
                        "x"              : location["position_x"],
                        "y"              : location["position_y"],
                        "width"          : location["width"],
                        "height"         : location["height"],
                        "value"          : location["value"],
                        "number_of_row"  : location["number_of_row"],
                        "preview_input"  : ""
                    });
                });

                attribute_obj.roles = [];
                attribute["roles"].forEach(role => {
                    attribute_obj.roles.push(role["id"]);
                });

                form_obj.attributes.push(attribute_obj);
            });

            formAttributesHolder.push(form_obj);
        });

        mode = "preview";
        setupUpFormContainer();
    });  

    // form grouping and form input configuration setup -- start -- 
    function switchMode() {
        mode = mode == "preview" ? "setup" : "preview";
        setupUpFormContainer();
    }

    function nextForm() {
        currentFormIndex++;
        setupUpFormContainer();
    }

    function prevForm() {
        currentFormIndex--;
        setupUpFormContainer();
    }

    function setupUpFormContainer() {
        $(".loader").show(); // loader gif start

        $("#current_mode").val("").val(mode.toUpperCase() + " MODE");

        let index = formAttributesHolder[currentFormIndex] != undefined ? currentFormIndex : 0;
        $("#current_form_index").val("").val("Page " + (currentFormIndex + 1));
        
        $("#image_file_container")
            .html("")
            .append('<img id="image_file" class="img-responsive" src="' + formAttributesHolder[index]["file"] + '" />');

        setTimeout(function() {
            $("#top_form_menu")
                .css("min-width", $("#image_file_container").parent().outerWidth() + "px")
                .css("max-width", $("#image_file_container").parent().outerWidth() + "px");

            $("#image_file_detail")
                .css("min-width", $("#image_file_detail").parent().width() + "px")
                .css("max-width", $("#image_file_detail").parent().width() + "px");

            $('.btn-prev').prop('disabled', (currentFormIndex == 0 ? true: false));
            $('.btn-next').prop('disabled', (currentFormIndex == forms.length - 1 ? true: false));

            if (mode == "preview") {
                $("#image_file_detail, .btn-for-setup").hide();
                preview();

                $(".loader").hide();
            } else {
                $("#image_file_detail, .btn-for-setup").show();                
                formAttributesHolder[index]["attributes"].forEach((element, index) => {
                    draw(index ,element, "exist");
                });

                $(".loader").hide();
            }

        }, 1000);
    }

    function draw(current_index, element, mode) {   
        let attribute = options.find(o => o.id == element.attribute_id);
        
        element["locations"].forEach((location, index) => {
            let id = "point_" + current_index + "_" + index;
            width = location["width"] * $("#image_file").width() / formAttributesHolder[currentFormIndex]["width"];
            height = location["height"] * $("#image_file").height() / formAttributesHolder[currentFormIndex]["height"];
            position_left = location["x"] * $("#image_file").width() / formAttributesHolder[currentFormIndex]["width"] + parseInt($("#image_file_container").css("padding-left"));
            position_top = location["y"] * $("#image_file").height() / formAttributesHolder[currentFormIndex]["height"];

            switch (attribute["id"]) {
                case 1:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><p style="float: left"><i> A super long description </i></p></div>');                    
                    break;
                case 2:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><p style="float: left"><i> A short sentence </i></p> </div>');
                    break;
                case 3:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><p style="float: left"><i> Signature </i></p> </div>');
                    break;
                case 4:
                    break;
                case 5:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><p style="float: left"><i> 18/09/20XX </i></p> </div>');
                    break;
                case 6:
                case 8:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><div style="position: relative;"><div style="position: absolute; z-index: 3; top:0px; min-height: 100%; min-width: 100%;"></div><input type="checkbox" class="styled" checked="checked" disabled></div></div>');
                    break;
                case 7:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><div style="position: relative;"><div style="position: absolute; z-index: 3; top:0px; min-height: 100%; min-width: 100%;"></div><input type="radio" class="styled" checked="checked" disabled></div></div>');
                    break;
                case 9:
                    $("#image_file_container").append('<div id="' + id + '" name="' + id + '"><i style="float: right; margin-right:10px;" class="fa fa-sort-down"></i></div>');       
                    break;
            }

            $("#" + id).attr("title", element["key"] + " (" + attribute["name"] + ") ")                
                .addClass("display-mode")
                .css("top", position_top)
                .css("left", position_left)
                .css("width", width)
                .css("height", height)

            // if (attribute["id"] != 6 && attribute["id"] != 7 && attribute["id"] != 8) {                
                
            // }

            $("#" + id)                
                .dblclick(function(event) {
                    event.preventDefault();
                
                    var index = ($(this).attr("id")).split("_")[1];
                    if (!$(this).hasClass("editing-mode") ) {
                        resetCurrentAttribute();

                        currentAttribute = (formAttributesHolder[currentFormIndex]["attributes"][current_index] == undefined) ? JSON.parse(JSON.stringify(element)) : JSON.parse(JSON.stringify(formAttributesHolder[currentFormIndex]["attributes"][current_index]));

                        $("[id^=point_]").each(function(key, element) {
                            var key_arr = element.id.split("_");

                            if (key_arr[1] == index) {
                                $(".btn-form").prop("disabled", true);
                                $("#" + element.id).addClass("editing-mode").removeClass("display-mode")
                                    .draggable({
                                        drag: function() {
                                            editBool = true;
                                        },
                                        stop: function() { 
                                            var input_index = ($(this).attr('id')).split("_")[2];
                                            currentAttribute.locations[input_index].x = (($(this).position().left - parseInt($("#image_file_container").css("padding-left")))/ $("#image_file").width() * formAttributesHolder[currentFormIndex]["width"]);
                                            currentAttribute.locations[input_index].y = $(this).position().top / $("#image_file").height() * formAttributesHolder[currentFormIndex]["height"];
                                        }
                                    });

                                if (attribute["id"] != 6 && attribute["id"] != 7 && attribute["id"] != 8) {
                                    $("#" + element.id).resizable({  
                                        resize: function() {
                                            editBool = true;
                                        },
                                        stop: function() { 
                                            var input_index = ($(this).attr('id')).split("_")[2];
                                            currentAttribute.locations[input_index].width = $(this).width() / $("#image_file").width() * formAttributesHolder[currentFormIndex]["width"];
                                            currentAttribute.locations[input_index].height = $(this).height() / $("#image_file").height() * formAttributesHolder[currentFormIndex]["height"];
                                        }
                                    }).css("pointer-events", "");
                                }
                            } else {
                                if ($("#" + element.id).hasClass("ui-draggable") && $("#" + element.id).hasClass("ui-resizable")) {
                                    $("#" + element.id).draggable("destroy").resizable("destroy");
                                }                 

                                $("#" + element.id).removeClass("editing-mode").addClass("display-mode").css("pointer-events", "none");
                            }
                        });

                        $("#modal_option_mode").val(mode);
                        $("#modal_option_index").val(current_index);
                        $("#modal_option_attribute").val(currentAttribute["attribute_id"]).trigger('change');
                        $("#modal_option_roles").val(currentAttribute["roles"]).trigger('change');
                        $("#modal_option_section").val(currentAttribute["form_section_id"]).trigger('change');
                        $("#modal_option_title").val(currentAttribute["key"]);
                        $("#modal_option_mandatory").prop("checked", (currentAttribute["is_required"] == 1 ? true : false));

                        var elem = document.getElementById("modal_option_mandatory");
                        switchery = new Switchery(elem);

                        $("#modal_option_dropdown_value").tagsinput('removeAll');
                        if (currentAttribute["attribute_id"] == "9") {
                            if (currentAttribute["locations"]["0"]["value"]) {
                                var dropdownOption = currentAttribute["locations"][0]["value"].split("|");
                                dropdownOption.forEach(menu => {
                                    $("#modal_option_dropdown_value").tagsinput("add", menu, {preventPost: true});
                                });
                            } 

                            $(".option-dropdown-value").show();
                        }
                        
                        $("#modal_option_no_row").prop("required", (attribute["multiple_row"] == 1 ? true : false));
                        $("#modal_option_no_row").val(currentAttribute["locations"][0]["number_of_row"] == 0 ? 1 : currentAttribute["locations"][0]["number_of_row"]);
                        $(".option-no-row").css("display", (attribute["multiple_row"] == 1 ? "block" : "none"));

                        $("#modal_option_no_input").prop("required", (attribute["multiple_input"] == 1 ? true : false));
                        $("#modal_option_no_input").val(currentAttribute["locations"].length);
                        $(".option-no-input").css("display", (attribute["multiple_input"] == 1 ? "block" : "none"));

                        $("#form_option").show();
                    }
                });  

                if (mode == "new") {
                    $("#" + id).trigger("dblclick");
                }
        });

        $(".styled").uniform({
            radioClass: 'choice'
        });
        
    }

    function preview() {
        setTimeout(function(){       
            formAttributesHolder[currentFormIndex]["attributes"].forEach((element, index) => {
                element["locations"].forEach((location, l_index) => {
                    var current_id = "point_" + index + "_" + l_index;
                    var location_id = location["id"];
                    var width = location["width"] * $("#image_file").width() / formAttributesHolder[currentFormIndex]["width"];
                    var height = location["height"] * $("#image_file").height() / formAttributesHolder[currentFormIndex]["height"];
                    var position_left = location["x"] * $("#image_file").width() / formAttributesHolder[currentFormIndex]["width"] + parseInt($("#image_file_container").css("padding-left"));
                    var position_top = location["y"] * $("#image_file").height() / formAttributesHolder[currentFormIndex]["height"];

                    switch (element["attribute_id"]) {
                        case 1:
                            $("#image_file_container").append('<textarea maxlength="255" id="' + current_id + '" name ="' +location_id+ '"></textarea>');
                            break;
                        case 2:
                            $("#image_file_container").append('<input type="text" autocomplete="off" maxlength="100" id="' + current_id + '" name ="' +location_id+ '">');  
                            break;
                        case 3:
                            $("#image_file_container").append('<div onclick="showSignatureModal(' + '\'' + current_id + '\'' + ', ' + '\'' + location_id + '\'' + ')" id="' + current_id + '" name ="' +location_id+ '"></div>'); 
                            $("#image_file_container").append('<input type="hidden" id="location_id_' + location_id + '" name ="' +location_id+ '"></div>');  
                            break;
                        case 4:
                            break;
                        case 5:
                            $("#image_file_container").append('<input type="date" id="' + current_id + '" name ="' +location_id+ '">');   
                            break;
                        case 6:
                        case 8:
                            $("#image_file_container").append('<div id="' + current_id + '" name ="' +location_id+ '"><input type="checkbox" class="styled"></div></div>');
                            break;
                        case 7:
                            $("#image_file_container").append('<div id="' + current_id + '"><input type="radio" name="radio-' +  "point-" + index + '" class="styled" checked="checked"></div>');
                            break;
                        case 9:
                            var string_element = "<option></option>";
                            (location["value"].split("|")).forEach(function(data) {
                                string_element += "<option>" + data + "</option>";
                            });
                            
                            $("#image_file_container").append('<select placeholder="select one" id="' + current_id + '" name ="' +location_id+ '">' + string_element + '</select>');
                            break;
                    }

                    $("#" + current_id).addClass("preview-mode")
                        .css("top", position_top)
                        .css("left", position_left)
                        .css("height", height)
                        .css("width", width);
                });
            });

            $(".styled").uniform({
                radioClass: 'choice'
            });

            bindOnInput();
        }, 300); 
    }

    function bindOnInput() {
        $("[id^=point_]").change(function(e) {
            var current_id = e.target.id == "" || e.target.id == undefined ? e.target.parentElement.parentElement.parentElement.id : e.target.id;
            var point_arr = current_id.split("_");
            
            switch (formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["attribute_id"]) {
                case 1: 
                case 2:
                case 5:
                case 9:
                    formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][point_arr[2]]["preview_input"] = e.target.value;
                    break;
                case 7:
                    formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"].forEach((_, index) => {
                        formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][index]["preview_input"] = "0";
                    });
                    
                    formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][point_arr[2]]["preview_input"] = "1";
                    break;
                case 6:
                    let val = formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][point_arr[2]]["preview_input"];

                    formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][point_arr[2]]["preview_input"] = val == "" || val == "0" ? "1" : "0";
                    break;
            }

        });
    }

    // form grouping and form input configuration setup -- end -- 
    function saveAll() {    
        formAttributesHolder.forEach((form, index) => {
            formAttributesHolder[index]["attributes"] = form["attributes"].filter(v => v);
        });

        bootbox.confirm("Save current work ?", function(result) {
            if (result) {
                $.ajax({
                    url:"{{ route('form_attribute.saveAll') }}",
                    type:"POST",
                    data: { 
                        "version_id": formVersionID,
                        "forms": formAttributesHolder
                    },
                    success:function(response) {
                        if (response["success-message"]) {
                            displayMessage("Setup save successful", "success");
                        } else {
                            displayMessage("Fail to delete version", "warning", false);
                        }
                    }
                });
            }
        });
    }

    // create new input -- start -- 
    $("#modal_add_option_attribute").change(function() {
        $("#modal_add_no_input").val(1);

        switch ($(this).val()) {
            case "6":
            case "7":
            case "8":
            $(".add-no-input").show();
            break;
            default: 
            $(".add-no-input").hide();
            break;
        }
    });

    $("#form_add_input").submit(function(event) {
        event.preventDefault();

        currentIndex = formAttributesHolder[currentFormIndex]["attributes"].length;
        var locations = [];

        var width_height = parseInt($("#modal_add_option_attribute").val()) != 6 && parseInt($("#modal_add_option_attribute").val()) != 7 && parseInt($("#modal_add_option_attribute").val()) != 8 ? 80 :18;
        for (var i = 0; i < $("#modal_add_no_input").val(); i++) {
            locations.push({
                "id"             : null,
                "x"              : $("#image_file").width() / 2,
                "y"              : $("#image_file_container").height() / 2,
                "width"          : width_height,
                "height"         : width_height,
                "value"          : null,
                "number_of_row"  : 0,
                "preview_input"  : ""
            });
        }
    
        draw(currentIndex, {
            "id"                : null,            
            "attribute_id"      : parseInt($("#modal_add_option_attribute").val()),
            "is_required"       : 0,
            "form_section_id"   : null,
            "key"               : "",
            "locations"         : locations,
            "roles"             : [],
        }, "new");    

        editBool = true;
        $(".btn-form").prop("disabled", true);
        $("#modal_add_location").modal("toggle");
    });
    // create new input -- end -- 

    function removeCurrentAttribute() {
        let index = $("#modal_option_index").val();
        let mode = $("#modal_option_mode").val();
        
        let confirm = bootbox.confirm("Are you sure to remove current work ?", function(result) {
            if (result) {
                if (formAttributesHolder[currentFormIndex]["attributes"][index] != undefined) {
                    delete formAttributesHolder[currentFormIndex]["attributes"][index];
                }
                
                $("[id^=point_" + index + "_]").remove();
                resetCurrentAttribute();
            }
        });
    }

    function cancelCurrentAction() {
        let index = $("#modal_option_index").val();
        let mode = $("#modal_option_mode").val();

        if (editBool) {
            let confirm = bootbox.confirm("Are you sure to cancel current unsaved work ?", function(result) {
                if (result) {
                    $("[id^=point_" + index + "_]").remove();

                    if (mode != "new" && formAttributesHolder[currentFormIndex]["attributes"][index] != undefined) {
                        draw(index, formAttributesHolder[currentFormIndex]["attributes"][index], mode);
                    }

                    resetCurrentAttribute();
                }
            });
        } else {
            resetCurrentAttribute();
        }
    }

    $("#form_option").submit(function(event) {
        event.preventDefault();

        bootbox.confirm("Are you sure to save current work ?", function (result) {
            if (result) {
                let index = $("#modal_option_index").val();

                currentAttribute["key"] = $("#modal_option_title").val();
                currentAttribute["form_section_id"] = $("#modal_option_section").val();
                currentAttribute["is_required"] = $("#modal_option_mandatory").is(":checked") ? 1 : 0;
                currentAttribute["roles"] = ($("#modal_option_roles").val() ? $("#modal_option_roles").val() : []),

                currentAttribute["locations"].forEach((location, key) => {
                    currentAttribute["locations"][key]["value"] = ((currentAttribute["attribute_id"] == "9") ? ($("#modal_option_dropdown_value").tagsinput("items")).join("|") : ""); 
                    currentAttribute["locations"][key]["number_of_row"] = ((currentAttribute["attribute_id"] == "1" || currentAttribute["attribute_id"] == "2") ? $("#modal_option_no_row").val() : 0); 
                });

                if ($("#modal_option_mode").val() == "new") {
                    if (formAttributesHolder[currentFormIndex]["attributes"][index] == undefined) {
                        formAttributesHolder[currentFormIndex]["attributes"].push(JSON.parse(JSON.stringify(currentAttribute)));
                    } else {
                        formAttributesHolder[currentFormIndex]["attributes"][index] = JSON.parse(JSON.stringify(currentAttribute));
                    }
                } else {
                    formAttributesHolder[currentFormIndex]["attributes"][index] = JSON.parse(JSON.stringify(currentAttribute));
                } 

                resetCurrentAttribute();
            }
        });
    });

    // form section -- start -- 
    $("#form_section").submit(function(event) {
        event.preventDefault();
        
        if ($("#form_section_id").val() == 0) {

            $.ajax({
                url:"{{ route('section.store') }}",
                type:"POST",
                data: { 
                    "form_version_id": formVersionID,
                    "name": $("#form_section_name").val(),
                    "sequence": $("#form_section_sequence").val()
                },
                success:function(response) {
                    displayMessage("Section store sucessfully", "success");
                }
            });
        } else {
            let section_id = $("#form_section_id").val();

            $.ajax({
                url:"{{ url('section') }}" + "/" + section_id,
                type:"PUT",
                data: { 
                    "name": $("#form_section_name").val(),
                    "sequence": $("#form_section_sequence").val()
                },
                success:function(response) {
                    if (response["success-message"]) {
                        displayMessage("Section update sucessfully", "success");
                    } else {
                        displayMessage("Fail to update section", "warning", false);
                    }
                }
            });
        }
                        
        $("#modal_section").modal("toggle");
    });

    function showModalSection(id = null) {
        $("#form_section").trigger("reset");

        if (id) {
            let section = sections.find(o => o.id == id);
            $("#form_section_id").val(section["id"]);
            $("#form_section_name").val(section["name"]);
            $("#form_section_sequence").val(section["sequence"]);
        }

        $("#modal_show_sections").modal("hide");
        $("#modal_section").modal("show");
    }

    function cancelModalSection() {
        $("#modal_show_sections").modal("show");
        $("#modal_section").modal("hide");
    }

    function deleteSection(id) {
        $("#modal_show_sections").modal("hide");
        bootbox.confirm("Are you sure to delete selected section ?", function (result) {
            if (result) {
                $.ajax({
                    url:"{{ url('section') }}/" + id,
                    type:"DELETE",
                    success:function(response) {
                        if (response["success-message"]) {
                            displayMessage("Section remove successful", "success");
                        } else {
                            displayMessage("Section fail to remove", "warning", false);
                        }
                    }
                });
            } else {
                $("#modal_show_sections").modal("show");
            }
        }); 
    }
    // form section -- end -- 

    // signature -- start --
    function getSignatureImage() {
        var image = signaturePad.toDataURL();
        $("#modal_signature_box").modal('toggle');

        var id = $("#for_modal_id").val();
        var location_id = $("#for_location_id").val();
        var height = $("#" + id).height();
        
        $("#" + id).html("").append('<img src="' + image + '" style="height:' + height + 'px; ">');
        var point_arr = id.split("_");
        $("#location_id_" + location_id).val(image);
        formAttributesHolder[currentFormIndex]["attributes"][point_arr[1]]["locations"][point_arr[2]]["preview_input"] = image;
    }

    function clearSignaturePad() {
        signaturePad.clear();
    }

    function showSignatureModal(id, location_id) {
        $("#modal_signature_box").modal('toggle');
        
        $("#for_modal_id").val(id);
        $("#for_location_id").val(location_id);
        clearSignaturePad();
    }
    // signature -- end --

    // print document -- start -- 
    function printElement() {
        $(".loader").show();
        $.ajax({
            url:"{{ route('form_attribute.printPDF') }}",
            type:"POST",
            data: { 
                "input": formAttributesHolder,
            },
            success:function(response) {
                window.open(response); 

                $(".loader").hide();
            }
        });
    }
    // print document -- end --
    </script>
@endsection
