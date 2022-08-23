@extends('components.template-limitless.main')

@section('main')
<style type="text/css">
    .markerDrill-size{
        height: 40px;
        width: 40px;
        position: absolute;
        cursor: -webkit-grab; 
        cursor: grab;
    }
</style>
@include('project-settings.components.tab')
<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-link"></i> Links
                    <small style="cursor: pointer;"><i class="fa fa-question-circle-o" data-popup="tooltip" title="Link will be act as a drill down from one plan to another plan" data-placement="top"></i></small>

                </h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group btn-top">
                    <a href="{{ route('set-drawing-set.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-inspection.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row" style="padding-bottom: 20px;">
            <div class="col-md-8 col-xs-8">
                <div class="col-md-6 col-xs-6">
                    <select data-placeholder="Select Drawing Set" class="select-search" name="drawing_set" id="drawing_set" autofocus="" required="">
                        <option value="">Please Select</option>
                        @foreach($drawingSets as $drawingSet)
                            <option value="{{ $drawingSet->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $drawingSet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xs-6">
                    <select data-placeholder="Select Drawing Plan" class="select-search" name="drawing_plan" id="drawing_plan" autofocus="" required="">
                        <option value="">Please Select</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-xs-4">
                <button type="button" class="btn btn-success btn-sm" id="add_drill_down" style="display: none" data-popup="tooltip" title="Add drill down marker" data-placement="top"><i class="icon-add"></i></button>
            </div>
        </div> 
        <div class="row" style="position: relative;overflow-x:scroll;overflow-y:hidden;">
            <div class="col-md-12 col-xs-12" id="div-image" align="center" style="margin: 0;padding: 0;">
            </div>
        </div>

    </div>
</div>


    <!-- modal_update_link-->
    <div id="modal_update_link" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-link"></i> Update Link To<hr></h5>
                </div>

                <form method="POST" action="" enctype="multipart/form-data" id="form_update_link">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <center>
                                    <div class="plan_image"></div>
                                </center>
                            </div>
                            <div class="col-md-12">
                                <label>Drawing Set</label>
                                <select data-placeholder="Select Drawing Set" class="select-search" name="update_set" id="update_set" autofocus="" required="">
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Drawing Plan</label>
                                <select data-placeholder="Select Drawing Set" class="select-search" name="update_link_to_plan" id="update_link_to_plan" autofocus="" required="">
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="point_id" id="point_id" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /modal_update_link -->


    <!-- modal_choose_link-->
    <div id="modal_choose_link" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-link"></i> New Link<hr></h5>
                </div>

                <form method="POST" action="" enctype="multipart/form-data" id="form_choose_link">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <center>
                                    <div class="plan_image"></div>
                                </center>
                            </div>
                            <div class="col-md-12">
                                <label>Drawing Set</label>
                                <select data-placeholder="Select Drawing Set" class="select-search" name="set" id="set" autofocus="" required="">
                                    <option value="">Please Select</option>
                                    @foreach($drawingSets as $drawingSet)
                                        <option value="{{ $drawingSet->id }}">{{ $drawingSet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Drawing Plan</label>
                                <select data-placeholder="Select Drawing Plan" class="select-search" name="link_to_plan" id="link_to_plan" autofocus="" required="">
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="drawing_plan_from" id="drawing_plan_from" value="">
                        <input type="hidden" name="position_x" id="position_x" value="">
                        <input type="hidden" name="position_y" id="position_y" value="">
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /modal_choose_link -->
@endsection

@section('script')
    <script type="text/javascript">
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var drawing_set,
            drawing_plan,
            top_left_image,
            ori_width,
            ori_height,
            web_height,
            web_width,
            select = '',
            allData,
            current_marker,
            center_x, //var to fix floating menu
            center_y, //var to fix floating menu
            mode = 'view';


        var flagsUrl = '{{ URL::asset('/uploads/drawings/') }}';
        var drill_url = "{{ URL::asset('/assets/images/icon/drilldown__icon_transparent.png') }}";

        $(document).ready(function(){

            var panel_body = $(".panel-body").width();
            $("#div-image").css('width', panel_body);


            $("#drawing_set").trigger('change');

            $.ajax({
                url:"{{ route('set-link.get-all-set') }}",
                type:'POST',
                success:function(response){
                    $("#drawing_set").trigger('change');
                    allData = response;
                }
            });
 

            $("#drawing_set").on('change', function(){
               
                drawing_set =  $("#drawing_set").val();

                $.ajax({
                    url:"{{ route('set-link.list-plan') }}",
                    type:'POST',
                    data: {'drawing_set' : drawing_set },
                    beforeSend:function(response){
                        $('.loader').show();
                    },
                    success:function(response){
                        $('.loader').hide();
                        
                        $("#div-image").html('');
                        $('#drawing_plan').empty().append('<option value="">Please Select</option>');
                        
                        var i = 1;
                        response.forEach(element => {
                            $('#drawing_plan').append('<option value="'+ element["id"] +'" '+ (i == 1 ? 'selected' : '') +'>'+ element["name"] +'</option>');
                            i++;
                        });

                        $("#drawing_plan").trigger('change');

                    }
                });
                
            });


            $("#drawing_plan").on('change', function(){
        
                drawing_plan = $("#drawing_plan").val();

                if(drawing_plan != ''){

                    $("#drawing_plan_from").val(drawing_plan);
                    $("#add_drill_down").show();

                    $.ajax({

                        url:"{{ route('set-link.view-plan') }}",
                        type:'POST',
                        data: {'drawing_plan' : drawing_plan },
                        // beforeSend:function(response){
                        //     $('.loader').show();
                        // },
                        success:function(response){

                            // $('.loader').hide();

                            // Get Original size of image from DB
                            ori_height = response["drawing_plan"]["height"];
                            ori_width = response["drawing_plan"]["width"];
                                
                            $("#div-image").html('').append('<img src="'+ flagsUrl +'/'+ response["drawing_plan"]["file"] +'" class="img-responsive" id="drop-image-drill">');

                            setTimeout(function(){
                                $('.loader').hide();

                                // to get centre position of drawing plan

                                var drill_icon = '<img src="'+drill_url+'" id="marker_test" class="markerDrill-size">'

                                $('#div-image').append(drill_icon);
                                $('#marker_test').css('left', '30%').css('top', '30%');
                                center_x = $('#marker_test').position().left;
                                center_y = $('#marker_test').position().top;
                                 $('#marker_test').remove();

                                $("#drop-image-drill").droppable({
                                    drop: function( event, ui ) {}
                                });

                                // get position of image
                                top_left_image = $("#drop-image-drill").position();
                                web_height = $("#drop-image-drill").height();
                                web_width = $("#drop-image-drill").width();
                                
                                response["drill"].forEach(element => {
                                    
                                    //Append maker

                                    var drill_icon = '<img src="'+drill_url+'"  id="marker_'+ element["id"] +'"  class="markerDrill-size" onclick="viewMenu('+ element["id"] +')">';

                                    $('#div-image').append(drill_icon);

                                    //GET DATA FROM DB
                                    var point = {"drill_id" : element["id"], "pos_x" : element["position_x"],"pos_y" : element["position_y"], "link_id" : element["to_drawing_plan_id"], "link_name" : element["link"]["name"] };
                                    

                                    $('#marker_'+element["id"]).tooltip({
                                        'title' : element["link"]["name"],
                                        'animation': true,
                                        'placement': 'bottom',
                                    }); 

                                    repositionMarker(point);
                                });

                            }, 500); //end function settimeout
                        }, //end function response
                    });
                }
            });

        });


        function repositionMarker(response){
            //calculation get position marker
            // icon size = 13.7*16
            var x = (response["pos_x"] * web_width / ori_width) + top_left_image.left - 20;
            var y = (response["pos_y"] * web_height / ori_height) + top_left_image.top - 40;

            $('#marker_'+response["drill_id"] ).css('left', x).show().css('top', y);
        }


        function displayFloatingMenu(element) { 
    
            var left_post = 35; //x-axis
            var top_post = 28; //y-axis

            // SETUP MODE
            if(mode == 'view'){

                if(element.position().left > center_x){
                    left_post = -105;
                }

                if(element.position().top < center_y){
                    top_post = -30;
                }

                $('#div-image').append('<div class="btn-group" style="position: absolute; top: ' + (element.position().top - (top_post)) + 'px; left: ' + (element.position().left +  left_post)+'px">' + 
                    '<button type="button" onclick="editMarker()" class="btn btn-primary" style="height:36px; width:36px"><i class="fa fa-edit" aria-hidden="true"></i></button>' + 
                    '<button type="button" onclick="moveMarker()" class="btn btn-primary" style="height:36px; width:36px"><i class="fa fa-arrows" aria-hidden="true"></i></button>' +
                    '<button type="button" onclick="deleteMarker()" class="btn btn-danger" style="height:36px; width:36px"><i class="fa fa-trash" aria-hidden="true"></i></button>' + 
                    '</div>' 
                );
            }

            else if(mode == 'edit' || mode == 'create'){
                
                left_post = 35; //x-axis
                top_post = 28;

                if(element.position().left > center_x){
                    left_post = -70;
                }

                if(element.position().top < center_y){
                    top_post = -30;
                }
            
                $('#div-image').append('<div class="btn-group" style="position: absolute; top: ' + (element.position().top - (top_post)) + 'px; left: ' + (element.position().left +  left_post)+'px">' + 
                    '<button type="button" onclick="saveMove()" class="btn btn-primary" style="height:36px; width:36px"><i class="fa fa-save" aria-hidden="true"></i></button>' + 
                    '<button type="button" onclick="cancelMove()" class="btn btn-danger" style="height:36px; width:36px"><i class="fa fa-times" aria-hidden="true"></i></button>' + 
                    '</div>' 
                );
            }

        }


        function resetAll() {
            mode = "view";
            $('.btn-group').not('.btn-top').remove();
        }

        function viewMenu(id){

            if(mode == 'edit'){

                if(id != current_marker){
                    displayMessage('Please save your current unsaved work.', 'error', false);
                }
                return false;
            }

            if(mode == 'create'){

                if(id != 'marker'){
                    displayMessage('Please save your current unsaved work.', 'error', false);
                }
                return false;
            }


            resetAll();
            if(id == current_marker){
                current_marker = '';
                return false;
            }

            current_marker = id;
            displayFloatingMenu($('#marker_' + id));
        }

        function moveMarker(){

            mode = 'edit';
            $('.btn-group').not('.btn-top').remove();

            $('#marker_' + current_marker).draggable({
                start: function(event, ui) {             
                    $('.btn-group').not('.btn-top').remove();
                }, 
                revert: function(is_valid_drop){

                    if(!is_valid_drop){
                       return true;
                    }
                },
                stop: function (event, ui) {
                    displayFloatingMenu($(this));
                },
                drag: function( event, ui ) {
                },
            });
            $('#marker_' + current_marker).draggable('enable');

            displayFloatingMenu( $('#marker_' + current_marker));

        }

        function cancelMove(){

            bootbox.confirm("Are you sure to cancel current unsaved work ?", function (result) {
                if (result) {
                    
                    if(mode == 'create'){
                        $('[data-popup="tooltip"]').tooltip('destroy'); 
                        $('#marker').remove();
                    }else{

                        $.ajax({
                            url:"{{ route('set-link.get-pos') }}",
                            type:'POST',
                            data: { 'point_id' :  current_marker },
                            success:function(ori_pos){

                                var x = ((ori_pos["position_x"]* web_width / ori_width) + top_left_image.left - 20);
                                var y = ((ori_pos["position_y"] * web_height / ori_height) + top_left_image.top - 40) ;

                                $('#marker_'+ori_pos["id"] ).animate({
                                    top: y,
                                    left: x,
                                }, 500 );
                                $('#marker_' + ori_pos["id"]).draggable('disable');
                                current_marker = '';

                            },

                        });
                    }
                    

                    resetAll();
                }
            });
             
        }

        function saveMove(){

            bootbox.confirm("Are you sure to save this work ?", function (result) {
                if (result) {
                    
                    var pos = getOriPosition();

                    if(mode == 'create'){
                        var posOri = getOriPosition();
                        $("#position_x").val(posOri[0]);
                        $("#position_y").val(posOri[1]);

                        $('[data-popup="tooltip"]').tooltip('destroy'); 
                        $('.plan_image').html('');
                        $('#modal_choose_link').modal('show');

                    }else{

                        $.ajax({
                            url:"{{ route('set-link.update-pos') }}",
                            type:'POST',
                            data: { 'id' :  current_marker, 'x' : pos[0] , 'y' : pos[1]},
                            success:function(response){
                                displayMessage('record successfully updated.', 'success', false);
                                
                                $('#marker_' + response["id"]).draggable('disable');
                                current_marker = '';
                                resetAll();

                            },

                        });

                    }

                }
            });


        }

        function getOriPosition(){

            if(mode == 'create'){
                var point = $('#marker').position();
            }else if(mode == 'edit'){

                var point = $('#marker_' + current_marker).position();
            }

            //calculation get point start from image ->save this point in DB
            var position_x = point.left - top_left_image.left + 20;
            var position_y = point.top - top_left_image.top + 40;

            var pos_x_ori = position_x * ori_width / web_width;
            var pos_y_ori = position_y * ori_height / web_height;

            return [pos_x_ori, pos_y_ori];
        }

        function deleteMarker(){

            bootbox.confirm("Are you sure to remove this marker ?", function (result) {
                if (result) {
                    
                    $.ajax({
                        url: "{!! route('set-link.remove-drill' ) !!}",
                        type:'post',
                        data: {'id': current_marker, 'drawing_plan' : $("#drawing_plan").val() },
                        success:function(response){

                            displayMessage(response["msg"], response["type"], false);

                            if(response['type'] == 'success'){
                                $("#marker_" + current_marker).remove();
                                resetAll();
                            }

                        }
                    });


                }
            });
        }

        function editMarker(){


            $.ajax({    
                url:"{{ route('set-link.get-detail-marker') }}",
                type:'POST',
                data: {'id' : current_marker},
                beforeSend:function(){
                    $('.loader').show();
                },
                success:function(response){
                    
                    $('.loader').hide();
                    
                    $('#update_set').empty().append('<option value="">Please Select</option>');
                    $('#update_link_to_plan').empty().append('<option value="">Please Select</option>');
                    allData.forEach(element => {

                        if(element["id"] == response["set"]["drawing_set_id"]){

                            element["drawing_plan"].forEach(element => {

                                if(element["id"] == response["drill"]["to_drawing_plan_id"]){

                                    loadImage(element);
                                    
                                    var plan_select = 'selected';
                                }else{
                                    var plan_select = '';
                                }
                                
                                if(element["id"] != drawing_plan){

                                    $('#update_link_to_plan').append('<option value="'+ element["id"] +'" '+ plan_select +'>'+ element["name"] +'</option>');
                                }
                            });

                            var select = 'selected';
                        }else{
                            var select = '';
                        }
                        $('#update_set').append('<option value="'+ element["id"] +'" '+ select +'>'+ element["name"] +'</option>');
                    });
                    $("#point_id").val(current_marker);
                    $("#modal_update_link").modal('toggle');

                }
            });

        }


        function loadImage(element){
            var img_link = flagsUrl +'/' + element["file"];
            var img = '<a href="'+ img_link +'" data-popup="lightbox">'+
                    '<img src="'+ img_link +'" class="img-responsive" style="height: 200px;width: 200px;">'+
                    '</a>';

            $(".plan_image").html('').html(img);
        }

        $(document).ready(function(){

             $("#update_set").on('change', function(){
            
                $(".plan_image").html('');

                $('#update_link_to_plan').empty().append('<option value="">Please Select</option>');
                allData.forEach(element => {

                    if(element["id"] == $("#update_set").val()){

                        element["drawing_plan"].forEach(element => {

                            if(element["id"] != drawing_plan){

                                $('#update_link_to_plan').append('<option value="'+ element["id"] +'" >'+ element["name"] +'</option>');
                            }
                        });

                    }

                });
            });

            $("#set").on('change', function(){
            
                $(".plan_image").html('');

                $('#link_to_plan').empty().append('<option value="">Please Select</option>');
                allData.forEach(element => {

                    if(element["id"] == $("#set").val()){

                        element["drawing_plan"].forEach(element => {
                            console.log(element)

                            if(element["id"] != drawing_plan){

                                $('#link_to_plan').append('<option value="'+ element["id"] +'" >'+ element["name"] +'</option>');
                            }
                        });

                    }

                });
            });

            $('#update_link_to_plan').on('change', function(){

                allData.forEach(element => {

                    if(element["id"] == $("#update_set").val()){

                        element["drawing_plan"].forEach(element => {

                            if(element["id"] == $('#update_link_to_plan').val() ){

                                loadImage(element);

                            }
                        });

                    }

                });

            });

            $('#link_to_plan').on('change', function(){

                allData.forEach(element => {

                    if(element["id"] == $("#set").val()){

                        element["drawing_plan"].forEach(element => {

                            if(element["id"] == $('#link_to_plan').val() ){

                                loadImage(element);

                            }
                        });

                    }

                });

            });


            $("#form_update_link").submit(function(event) {
                event.preventDefault();
                
                $.ajax({
                    url:"{{ route('set-link.update-other') }}",
                    type:'POST',
                    data: $("#form_update_link").serialize(),
                    beforeSend:function(){
                        $('.loader').show();
                    },
                    success:function(response){

                        event.preventDefault();

                        $('#marker_' + response["drill_id"]).tooltip('destroy');


                        setTimeout(function(){
                            $('.loader').hide();

                            $('#marker_' + response["drill_id"]).tooltip({
                                            'title' : response["link_name"],
                                            'animation': true,
                                            'placement': 'bottom',
                                        }); 

                            $('#modal_update_link').modal('toggle');
                            displayMessage('Record successfully updated.', 'success', false);
                            current_marker = '';
                            resetAll();
                        },300);
                        
                    },

                });
                
            });

            $("#add_drill_down").click(function(){

                if($(".drag-marker").length){

                    displayMessage('Only one marker at a time.', 'warning', false);
                    return false;
                }
                $('.btn-group').not('.btn-top').remove();
                mode = 'create';

                var drill_icon = '<img src="'+drill_url+'"  id="marker" class="point-marker markerDrill-size drag-marker" data-popup="tooltip">';


                $('#div-image').append(drill_icon);

                $('#marker').tooltip({
                    'title' : 'Drag this marker',
                    'animation': true,
                    'placement': 'bottom',
                    'trigger': 'click',
                });

                $('html').animate({scrollTop: $('#marker').offset().top}, 500);

                $('#marker').css('top', "50%").css('left', "50%");
                $("#marker").trigger('click');

                $( '#marker' ).draggable({
                    start: function(event, ui) {             
                        $('[data-popup="tooltip"]').tooltip('destroy'); 
                        $('.btn-group').not('.btn-top').remove();
                    }, 
                    revert: function(is_valid_drop){

                        if(!is_valid_drop){
                           return true;
                        }
                    },
                    stop: function (event, ui) {
                        // console.log(mode);
                        displayFloatingMenu( $('#marker'));

                        // var posOri = getOriPosition();
                        
                        // $("#position_x").val(posOri[0]);
                        // $("#position_y").val(posOri[1]);

                    },
                    drag: function( event, ui ) {
                    },
                });


                displayFloatingMenu( $('#marker'));
            });


            $("#form_choose_link").submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url:"{{ route('set-link.store') }}",
                    type:'POST',
                    data: $("#form_choose_link").serialize(),
                    success:function(response){


                        $('#marker').remove();

                        var drill_icon = '<img src="'+drill_url+'"  id="marker_'+ response["drill_id"] +'" class="point-marker markerDrill-size" data-popup="tooltip" onclick="viewMenu('+ response["drill_id"] +')">';


                        $('#div-image').append(drill_icon);

                        repositionMarker(response);
                        


                       setTimeout(function(){
                            $('.loader').hide();

                            $('#marker_'+response["drill_id"]).tooltip({
                                'title' : response["link_name"],
                                'animation': true,
                                'placement': 'bottom',
                            }); 

                            $('#modal_choose_link').modal('toggle');
                            displayMessage('New link Added', 'success', false);

                            current_marker = '';
                            resetAll();
                        },300);


                    }
                });
            });


        });



    </script>

@endsection
