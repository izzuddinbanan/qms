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

</script>