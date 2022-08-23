<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Quality Manage System') }}</title>
<link rel="shortcut icon" href="{{ url('images/qms_app_logo.png') }}">

<!-- Global stylesheets -->
<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
<link href="{{ url('template-limitless/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('template-limitless/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('template-limitless/css/core.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('template-limitless/css/components.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('template-limitless/css/colors.css') }}" rel="stylesheet" type="text/css">
<!-- /global stylesheets -->

<link href="{{ url('assets/css/icons/fontawesome/styles.min.css') }}" rel="stylesheet" type="text/css">

<link href="{{ asset('assets/plugins/dropify/dist/css/dropify.min.css') }}"    rel="stylesheet" type="text/css" />


<!-- Core JS files -->
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/styling/switchery.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/pickers/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/plugins/ui/nicescroll.min.js') }}"></script>

<script type="text/javascript" src="{{ url('template-limitless/js/core/app.js') }}"></script>
<script type="text/javascript" src="{{ url('template-limitless/js/pages/layout_fixed_custom.js') }}"></script>
<!-- /theme JS files -->


<!-- FORM STYLING -->
<script type="text/javascript" src="{{ url('assets/js/pages/form_inputs.js') }}"></script>
<!-- FORM STYLING -->

<!-- DRAG n Drop -->
<script type="text/javascript" src="{{ url('assets/js/plugins/ui/dragula.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

<!-- Alert styling -->
<script type="text/javascript" src="{{ url('assets/js/plugins/notifications/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<!-- Alert styling -->

<!-- Pnotify -->
<script type="text/javascript" src="{{ url('assets/js/plugins/notifications/pnotify.min.js') }}"></script>

<!-- WebUI-Popover --><!-- https://github.com/sandywalker/webui-popover -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/jquery.webui-popover/1.2.1/jquery.webui-popover.min.css">
<script src="https://cdn.jsdelivr.net/jquery.webui-popover/1.2.1/jquery.webui-popover.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>


<script type="text/javascript" src="{{ url('assets/plugins/confirmation/bootstrap-confirmation.min.js') }}"></script>


<link rel="stylesheet" type="text/css" href="{{ url('assets/plugins/imageSelect/css/imgareaselect-default.css') }}" />
<script type="text/javascript" src="{{ url('assets/plugins/imageSelect/scripts/jquery.imgareaselect.pack.js') }}"></script>

<!-- Zoom image -->
<script type="text/javascript" src="{{ url('assets/plugins/zoomImage/wheelzoom.js') }}"></script>

<!-- popup -->
<script type="text/javascript" src="{{ url('assets/js/pages/components_popups.js') }}"></script>

<script type="text/javascript" src="{{ url('assets/js/plugins/velocity/velocity.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/velocity/velocity.ui.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/pages/animations_velocity_ui.js') }}"></script>

<!-- Theme JS files -->
<script type="text/javascript" src="{{ url('assets/js/plugins/media/fancybox.min.js') }}"></script>

<script type="text/javascript" src="{{ url('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/pages/datatables_basic.js') }}"></script>

<script type="text/javascript" src="{{ url('assets/js/pages/ecommerce_product_list.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/pages/form_select2.js') }}"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<!-- Theme JS files -->
<script type="text/javascript" src="{{ url('assets/js/core/libraries/jquery_ui/core.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/extensions/cookie.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/trees/fancytree_all.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/plugins/trees/fancytree_childcounter.js') }}"></script>

<!-- NEW PHASE 2-->
<script type="text/javascript" src="{{ url('assets/js/plugins/forms/inputs/duallistbox.min.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/pages/form_dual_listboxes.js') }}"></script>

<script type="text/javascript" src="{{ url('assets/js/plugins/uploaders/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/plugins/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>


<style type="text/css">

    .textUpperCase {
        text-transform: uppercase;
    }

    .action-icon {
        font-size: 17px;
    }
    
    th.indexNo{
        width: 5%;
        text-align: center;
    }
    th{
        font-size: 14px;
    }
    th.action{
        width: 13%;
        text-align: center;
    }

    .largeIcon{
        font-size: 17px;
    }

    .right{
        text-align: right !important;
    }
    input[type=text]:focus,input[type=email]:focus,input[type=date]:focus,input[type=number]:focus, textarea:focus, txa-border:focus {
        box-shadow: 0 0 5px rgba(81, 203, 238, 1);
        border: 1px solid rgba(81, 203, 238, 1);
    }

    textarea:focus {
      box-shadow: 0 0 5px rgba(81, 203, 238, 1);
              border: 1px solid rgba(81, 203, 238, 1);
    }

    .dragMouse{
        cursor:all-scroll;
    }

    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('{{ asset("assets/images/loader-hammer.svg")}}') 50% 50% no-repeat rgba(0, 0, 0, 0.49);
    }

    /*.bs-wizard {margin-top: 40px;}*/

    /*Form Wizard*/
    .bs-wizard {border-bottom: solid 1px #e0e0e0; padding: 0 0 10px 0;}
    .bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
    .bs-wizard > .bs-wizard-step + .bs-wizard-step {}
    .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
    .bs-wizard > .bs-wizard-step .bs-wizard-info {color: #999; font-size: 14px;}
    
    .bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 30px; height: 30px; display: block; background: #fbe8aa; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;} 
    .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 14px; height: 14px; background: #e0e0e0; border-radius: 50px; position: absolute; top: 8px; left: 8px; } 
    
    .bs-wizard > .bs-wizard-step > .bs-wizard-dot-active {position: absolute; width: 30px; height: 30px; display: block; background: #d9fca7; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;} 
    .bs-wizard > .bs-wizard-step > .bs-wizard-dot-active:after {content: ' '; width: 14px; height: 14px; background: #0df906; border-radius: 50px; position: absolute; top: 8px; left: 8px; } 
    .bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 8px; box-shadow: none; margin: 20px 0;}

    .bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #d9fca7;}
    .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
    .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
    .bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
    .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}

    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}

    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot-active {background-color: #f5f5f5;}
    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot-active:after {opacity: 0;}

    .bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 50%;}
    .bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
    .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
    /*END Form Wizard*/


    .loading {
    position: absolute;    
    background-color: #ffffff;
    background-image: url({{ url("assets/images/load.gif")}});
    background-size: 180px 180px;
    background-position:center center;
    background-repeat: no-repeat;
    }

    /*.notification_hover:hover{
        box-shadow: 0 0 11px rgba(33,33,33,.2); 
        
    }

    .select2-selection--single:not([class*=bg-]):not([class*=border-]), .test {
        border-color: #37474f;
    }
    .select2-selection--single:not([class*=bg-]), .test {
        background-color: #37474f;
        color: #fcfcfc;
    }
    .select2-selection--single .select2-selection__placeholder , .test{
        color: #fcfcfc;
    }*/
    .bigdrop{
        width: 250px !important;

    }

    thead{
        background-color: #37474f;
        color: white;
    }

    .stepwizard-step p {
        margin-top: 10px;
    }

    .stepwizard-row {
        display: table-row;
    }

    .stepwizard {
        display: table;
        width: 100%;
        position: relative;
    }

    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }

    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-order: 0;

    }

    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }

    .sidebar-default .navigation li.active > a,
    .sidebar-default .navigation li.active > a:hover,
    .sidebar-default .navigation li.active > a:focus {
        background-color: #efefe9;
        color: #333333;
    }

    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }

    .bg-theme {
        background-color: #006D8D;
        border-color: #006D8D;
        color: #fff;
    }

    .bg-theme-2 {
        background-color: #37474F;
        border-color: #37474F;
        color: #fff;
    }

    .dashboard-heading {
        padding-left: 30px !important; 
        padding-bottom: 10px !important;
    }

    .dashboard-project-name {
        font-weight: 800; 
        color: #8D8D8D; 
        text-transform: uppercase;
    }

    .dashboard-title {
        font-size: 30px; 
    }

    .dashboard-pagination {
        padding-left: 30px !important; 
        padding-right: 30px !important; 
        padding-bottom: 10px !important; 
    }

    .dashboard-table-heading {
        background-color: #e4e2e2;
    }
    
    .dashboard-pagination .pagination > .active > a,
    .dashboard-pagination .pagination > .active > span,
    .dashboard-pagination .pagination > .active > a:hover,
    .dashboard-pagination .pagination > .active > span:hover,
    .dashboard-pagination .pagination > .active > a:focus,
    .dashboard-pagination .pagination > .active > span:focus {
        background-color: #006D8D !important;
        border-color: #006D8D !important;
    }

    .nav-tabs[class*=bg-] > .active > a,
    .nav-tabs[class*=bg-] > .active > a:hover,
    .nav-tabs[class*=bg-] > .active > a:focus {
        background-color: rgba(0, 0, 0, 0.4);
    }

    
    /* for project step4 and plan viewer setupmode */
    .fixed_header {
        table-layout: fixed;
        border-collapse: collapse;
    }

    .fixed_header tbody {
        display:block;
        width: 100%;
        overflow: auto;
        height: 300px;
    }

    .fixed_header thead tr {
        display: block;
    }

    .fixed_header th, .fixed_header td {
        padding: 5px;
        text-align: left;
        width: 200px;
    }
</style>
<script type="text/javascript" src="{{ url('assets/js/plugins/notifications/pnotify.min.js') }}"></script>


<script type="text/javascript">

    $(document).ready(function() { 
   
        $("#project_option").select2({dropdownCssClass : 'bigdrop'}); 
   
       
        // RANDOM PASSWORD
        $("#btn-random").click(function(){
            var randomstring = Math.random().toString(36).slice(-8);
            $("#password").val(randomstring);
        });

        // PREIVEW IMAGE UPLOAD
        $(".image_upload").change(function(){

            var id = "preview_" + this.id;

            previewImg(this, id);

            var fname = $('#' + id).val();
            var filepath = fname.split('.');
            previewImg(this, id);

        });

        $('.viewProfile').click(function(){

            event.preventDefault();
            var id = this.id;
            var link = "{{ url('profile') }}";
            var APP_URL = {!! json_encode(url('/')) !!}

            $.ajax({
                url: link + "/" + id,
                type:'GET',
                data:{'id' : id },
                success:function(response){

                    console.log(response);
                    if(response == 'error'){
                        new PNotify({
                            title: 'warning',
                            text: 'Record not found',
                            delay: 2000,
                            icon: 'icon-warning22'
                        });
                    }else{

                        var user_id = '{{ Auth::user()->id }}';
                        
                        if(response['id'] != user_id){
                            $('#editProfile').hide()
                        }else{
                            $('#editProfile').show()
                        }
                        var avatar = '<img src="' +APP_URL+ '/assets/images/placeholder.jpg" style="width: 130px; height: 130px;" alt="" class="img-circle">';
                        
                        if(response['avatar'] != null){
                            avatar = '<img src="' +APP_URL+ '/uploads/avatars/'+ response["avatar"] +'" style="width: 130px; height: 130px;" alt="" class="img-circle">';
                        }

                        $("#avatar").html(avatar);

                        $("#name_user").text(response["name"]);
                        $("#email").text(response["email"]);
                        $("#language_user").text(response["language"]["name"]);
                        if(response["contact"] == null || response["contact"] == ""){
                            $("#contact").text(' - ');
                        }else{
                            $("#contact").text(response["contact"]);
                        }
                        $('#viewProfileUser').modal('show');
                    }
                }
            });
        });
    
    });


    // STYLING CONFIRM ALERT
    function confirmAlert(id){
        var href = $("#del_"+id).attr('href')
        event.preventDefault();
        bootbox.confirm("Are you sure to remove this record ?", function(result) {
            if(result == true){
                window.location = href;
            }
        });
    }

    // SHOW PASSWORD
    function show_hidePass(id){
        if(id == 'show-pass'){
            $('#password').attr('type', 'text');
            $('#show-pass').attr('id', 'hide-pass');
        }else{
            $('#password').attr('type', 'password');
            $('#hide-pass').attr('id', 'show-pass');
        }
    }
    

    // for display message 
    function displayMessage(message, type, reload = true) {
        new PNotify({
            title: type[0].toUpperCase() + type.slice(1),
            text: message,
            delay: 2000,
            icon: 'icon-checkmark3',
            type: type
        });

        if (reload) {
            setTimeout(function() {
                location.reload(true);
            }, 1000);
        }
    }

    function getIssueMarkerIcon(status) {
        var issue_icon = "";

        if(status == 2)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_orange.png') }}";
        else if(status == 3)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_brown.png') }}";
        else if(status == 4)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_biege.png') }}";
        else if(status == 5)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_yellow.png') }}";
        else if(status == 6)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_lightblue.png') }}";
        else if(status == 7)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_grey.png') }}";
        else if(status == 8)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_purple.png') }}";
        else if(status == 9)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_pink.png') }}";
        else if(status == 10)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_green.png') }}";
        else if(status == 1)
            issue_icon = "{{ URL::asset('/assets/images/icon/pin_marker_blue.png') }}";
        
        
        return issue_icon;
    }

    function previewImg(input,previewBox) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#'+previewBox).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function IsValidJSONString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }

        if(str == null)
            return false;
        
        return true;
    }

</script>

<!-- PHASE 2 NEW SCRIPT -->
<script type="text/javascript">
    $(document).ready(function() {

        $('body').tooltip({
            selector: '.tooltip-show'
        });

        $('.dropify').dropify();

        $('body').on('click', '.ajaxDeleteButton', function(e){
            e.preventDefault();

            var msg = "This data will not be re-usable!";

            if ($(this).hasClass('ajaxDeleteCustom')){
                var msg = "There have a user was make a reservation for this event!";
            }

            var url = $(this).attr('href');
            swal({
                title: "Are You Sure?",
                text: msg,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "red",
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: url,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        dataType: 'json',
                        success: function (response) {
                            if(response.status=='ok'){
                                swal({
                                    title: "Success!",
                                    text: "Data has been deleted.",
                                    type: "success",
                                    timer: 500,
                                    showConfirmButton: false,
                                });
                                window.location = '{{ request()->url() }}';
                            }else{
                                swal("Failed!", "Data was unsuccessfully deleted.", "error");
                            }
                        }
                    });
                    return false;
                }
            });
        });
    });

</script>