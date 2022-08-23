@extends('components.template-limitless.main')

@section('main')
<style type="text/css">
    
    .dropzone {
        min-height: 229px;
    }

</style>

@include('project-settings.components.tab')


<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="fa fa-map-o"></i> New Drawing Plan</h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <div class="btn-group">
                    <a href="{{ route('set-general.show', [session('project_id')]) }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                    
                    <a href="{{ route('set-general.show', [session('project_id')]) }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>

                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <!-- <div class="row">
            <div class="col-md-2 col-xs-2">
                <a href="{{ route('set-drawing-plan.show', [session('drawing_set_id')]) }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class="icon-circle-left2"></i></b> Back</a>
            </div>
        </div> -->

        <div class="row" style="margin-top: 10px;">
            
            <form method="POST" action="{{ route('set-drawing-plan.store') }}" enctype="multipart/form-data" id="form-drawing">
                @csrf
                <div class="form-group">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                            <label class="text-semibold">Pick Mode :</label>
                            <br>
                            <button type="button" class="btn btn-primary btn-xs btn-mode" id="single-btn" onclick="changeField(this.id)"><i class="icon-circle-small"></i>Single Upload</button>
                            <button type="button" class="btn btn-default btn-xs btn-mode" id="batch-btn" onclick="changeField(this.id)"><i class="icon-circle-small"></i>Batch Upload</button>
                            <!-- <button type="button" class="btn btn-default btn-xs btn-mode" id="clone-btn" onclick="changeField(this.id)"><i class="icon-circle-small"></i>Clone</button> -->

                        <input type="hidden" name="mode" id="mode" value="single">
                    </div>
                </div>
                
                @include('project-settings.set-drawing-plans.components.single')

                <div id="batch-field" class="drawing-plan-field" style="display: none">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="text-semibold">File :</label>
                            <input type="file" name="batch_file" id="batch_file" class="form-control dropify" accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="text-semibold">Sample File :</label><br>
                            <a href="{{ url('sample-data/sample-batch-upload-drawing-plan.zip') }}" type="button" class="btn btn-default btn-labeled btn-labeled-right">Download Sample<b><i class="icon-download"></i></b></a>
                        </div>
                    </div>


                </div>


                <div class="form-group">
                    <div class="col-md-12 text-right" style="margin-top: 10px;">
                        <a href="{{ route('set-drawing-plan.show', [session('drawing_set_id')]) }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class="icon-circle-left2"></i></b> Back</a>

                        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                    </div>
                </div>
            </form>


        </div>
    </div>

</div>

@include('project-settings.set-drawing-plans.components.modal-response')

@endsection



@section('script')

<script>
   
    function changeField(id) {
        $('.btn-mode').removeClass('btn-primary');
        $("#" + id).removeClass('btn-default').addClass('btn-primary');

        $('#mode').val(id.replace('-btn', ''))
        switch(id.replace('-btn', '')) {
            case "single":
                $('.drawing-plan-field').hide();
                $("#single-field").show();
                break;
            case "batch":
                $('.drawing-plan-field').hide();
                $("#batch-field").show();
                break;
        }
    }

    $(document).ready(function(){

        $("#report_batch_upload").modal('toggle'); // AFTER BATCH UPLOAD EXECUTED
        $("#type_plan").change(function(){

            switch($(this).val()) {
                case "custom":
                    $(".custom-field").show();
                    $(".plan-field").hide();
                    break;
                case "common":
                    $(".custom-field").show();
                    $(".plan-field").show();
                    break;
                case "unit":
                    $(".custom-field").show();
                    $(".plan-field").show();
                    break;
            }
        });

        $("#plan_block, #plan_level, #plan_unit").on('keyup', function(){

            // var phase = $("#plan_phase").val();
            var block = $("#plan_block").val() == "" ? '' : $("#plan_block").val() + '-';
            var level = $("#plan_level").val() == "" ? '' : $("#plan_level").val() + '-';
            var unit = $("#plan_unit").val();

            var unit_name = block + level + unit;

            $("#plan_name").val(unit_name);

        });
    });


    // $(document).ready(function () {

    //     $("#report_batch_upload").modal('toggle');
    //     Dropzone.autoDiscover = false;
    //     var images = [];
    //     var myDropzone = new Dropzone("div#my-dropzone", {
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    //         },
    //         acceptedFiles: ".png,.jpg,.jpeg",
    //         // maxFiles: 2,
    //         url: "{{ route('ajax.upload') }}",
    //         type: 'post',
    //         maxFilesize: 10,
    //         maxFiles: 1,
    //         addRemoveLinks: true,
    //         acceptedFiles: 'image/*',
    //         autoProcessQueue: true,
    //         success: function (file, response) {

    //             $("#submitFormbtn").attr("disabled", false);   
    //             file.previewElement.classList.add("dz-success");
    //             file.previewElement.id = response["name_unique"];
    //             images.push(response);
    //         },
    //         error: function (file, response) {
    //             // $("#submit_new_form").attr('disabled','disabled');
    //             $("#submitFormbtn").attr("disabled", true);   
    //             file.previewElement.classList.add("dz-error");
    //             $('[class="dz-error-message"]').css("color", "red");
    //             $('[class="dz-error-message"]').css("top", "10px");
    //             $('[class="dz-error-message"]').text(response);
    //         }
    //     });

    //     myDropzone.on("maxfilesreached", function(file) {
    //         $('div#my-dropzone').removeClass('dz-clickable');
    //         myDropzone.removeEventListeners();

    //     });

    //     myDropzone.on('sending', function(file, xhr, formData){
    //         formData.append('type', $("#type").val());
    //     });

    //     myDropzone.on('removedfile', function (file) {

    //         var image = file.previewElement.id;
    //         var url = '{{ route('ajax.delete') }}';
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
    //             // return false;
    //         }
    //         $('div#my-dropzone').addClass('dz-clickable');
    //         myDropzone.setupEventListeners();
    //     });


    //     // Remove file if modal is closed
    //     $('#modal_add_drawing').on('hidden.bs.modal', function () {

    //         myDropzone.getAcceptedFiles().forEach(element => {
    //             var image = element.previewElement.id;

    //             var url = '{{ route('ajax.delete') }}';
    //             if (image) {
    //                 $.ajax({
    //                     url: url,
    //                     type: 'post',
    //                     data: { image: image,
    //                         _token: "{{ csrf_token() }}"
    //                     },
    //                     success: function (response) {
    //                         if(response.status=='ok'){
    //                             images.splice($.inArray(image, images),1);

    //                         }else{
    //                             // swal("Failed!", "Data failed to delete.", "error");
    //                         }
    //                         reset();
    //                     }
    //                 });
    //                 return false;
    //             }

    //         });

    //         myDropzone.removeAllFiles();
    //     })

    //     $('#form-drawing').submit(function(){

    //         for ( var i = 0, l = images.length; i < l; i++ ) {
    //             // sum += image[ i ];
    //             $(this).append($('<input />').attr('type', 'hidden').attr('name', 'image[]').attr('value', images[ i ]['image_name']));
    //             $(this).append($('<input />').attr('type', 'hidden').attr('name', 'name_unique[]').attr('value', images[ i ]['name_unique']));
    //             $(this).append($('<input />').attr('type', 'hidden').attr('name', 'width[]').attr('value', images[ i ]['width']));
    //             $(this).append($('<input />').attr('type', 'hidden').attr('name', 'height[]').attr('value', images[ i ]['height']));
    //         }

    //     });
    // });

</script>
    
@endsection
