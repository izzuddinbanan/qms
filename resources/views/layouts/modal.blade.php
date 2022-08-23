<!-- Small modal for view profile -->
<div id="viewProfileUser" class="modal fade">
    <div class="modal-dialog modal-sm" style="top:18% !important;">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                
                <div class="row">
                    <div class="col-md-4 col-xs-4" style="text-align: left">
                        <div class="media no-margin-top">
                            <div id="avatar"></div>
                        </div>
                    </div>
                    <div class="col-md-8 col-xs-8">
                        <!-- NAME FIELD -->
                        <div class="form-group" style="margin-left: 7px;">
                            <h5><i class="icon-user"></i> <span id="name_user"></span></h5>
                        </div>
                        <div class="form-group" style="margin-left: 7px;word-wrap: break-word;">
                            <h5><i class="icon-mail-read"></i> <span id="email"></span></h5>
                        </div>
                        <div class="form-group" style="margin-left: 7px;">
                            <h5><i class="icon-mobile2"></i> <span id="contact"></span></h5>
                        </div>
                        <div class="form-group" style="margin-left: 7px;">
                            <h5><i class="fa fa-language"></i> <span id="language_user"></span></h5>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-danger" data-dismiss="modal" style=""><i class="icon-cross3"></i></button>
                <a href="{{ route('profile.edit', [Auth::user()->id]) }}" id="editProfile">
                    <button type="button" class="btn btn-primary"><i class="icon-pencil"></i></button>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- /small modal for view profile-->