<!-- SETUP MODE MODAL -->
    
    <!-- modal_choose_link-->
    {{-- <div id="modal_choose_link" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="fa fa-link"></i> New Link</h5>
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
                                    @foreach($data as $drawingSet)
                                        <option value="{{ $drawingSet->id }}">{{ $drawingSet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Drawing Plan</label>
                                <select data-placeholder="Select Drawing Set" class="select-search" name="link_to_plan" id="link_to_plan" autofocus="" required="">
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
    </div> --}}
    <!-- /modal_choose_link -->

    <!-- modal_update_link-->
    {{-- <div id="modal_update_link" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="fa fa-link"></i> Update Link To</h5>
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
    </div> --}}
    <!-- /modal_update_link -->

    {{-- <!-- modal_add_location-->
    <div id="modal_add_location" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h5 class="modal-title"><i class="icon-location4"></i> Add Location</h5>
                </div>

                <form method="POST" action="" enctype="multipart/form-data" id="form_add_location">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" autofocus="" autocomplete="off" required="" placeholder="room 21" id="name_location">
                            </div>
                        </div>

                        <input type="hidden" name="drawing_plan_id" id="drawing_plan_id" value="">
                        <input type="hidden" name="position_x" id="position_x_location" value="">
                        <input type="hidden" name="position_y" id="position_y_location" value="">
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
    <!-- /modal_add_location -->

    <!-- modal_update_location-->
    <div id="modal_update_location" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="icon-location4"></i> Update Location</h5>
                </div>

                <form method="POST" action="" enctype="multipart/form-data" id="form_update_location">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>Name</label>
                                <input type="text" name="name" id="name" value="" class="form-control" autofocus="" autocomplete="off" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>Location Reference</label>
                                <input type="text" name="reference" id="reference" value="" class="form-control" autocomplete="false" readonly="" disabled="">
                            </div>
                        </div>
                        <input type="hidden" name="update_drawing_plan_id" id="update_drawing_plan_id" value="">
                        <input type="hidden" name="update_location_id" id="update_location_id" value="">
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
    <!-- /modal_update_location --> --}}

<!-- SETUP MODE MODAL -->


<!-- ISSUE MODE MODAL -->

    <!-- Mini modal For location menu -->
    <div id="location_menu" class="modal fade">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title"><span id="location_name"></span>&nbsp;<small>(<span id="location_reference"></span>)</small></h5>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <button class="btn btn-primary" style="width: 100%" id="add_issue">@lang('plan.addIssue')</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" style="width: 100%" onclick="viewLinkIssues()">@lang('plan.viewLinkIssue')</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" style="width: 100%" onclick="viewIssueDocuments()">@lang('plan.viewDOc')</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" style="width: 100%" onclick="readyInpect()">@lang('plan.readyInspect')</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success btn-close" style="width: 100%" onclick="closeHandOver()">@lang('plan.closeHandOver')</button>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- /Mini modal For location menu -->


<!-- Mini modal For add issue_detail -->
<div id="add_issue_detail" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><i class="icon-pencil5"></i> @lang('plan.addIssue')</h5>
            </div>
            <form id="form_add_issue">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.category')</label>
                            <select data-placeholder="Select Category" class="select-search" name="category" id="category" autofocus="" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.type')</label>
                            <select data-placeholder="Select Type" class="select-search" name="type" id="type" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>

                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.issue')</label>
                            <select data-placeholder="Select Issue" class="select-search" name="issue" id="issue" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        @if($role->role_id != 4)
                            <!-- if admin add issue -->
                            <div class="col-md-{{ ($role->role_id != 4 ? 6 : 12) }} col-xs-{{ ($role->role_id != 4 ? 6 : 12) }}">
                                <label>@lang('plan.inspector')</label>
                                <select data-placeholder="Select Inspector" class="select-search" name="inspector" id="inspector" autofocus="" required="">
                                    <option value="">@lang('general.pleaseSelect')</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-md-{{ ($role->role_id != 4 ? 6 : 12) }} col-xs-{{ ($role->role_id != 4 ? 6 : 12) }}">
                            <label>@lang('plan.contractor')</label>
                            <select data-placeholder="Select Contractor" class="select-search" name="contractor" id="contractor" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label>@lang('plan.priority')</label>
                            <select data-placeholder="Select Priority" class="select-search" name="priority" id="priority" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label>@lang('plan.due')</label>
                            <input type="date" name="due_by" id="due_by" value="" class="form-control" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <label>@lang('plan.comment')</label>
                            <textarea class="form-control" name="comment" id="comment" rows="2"></textarea>
                        </div>
                        <!-- <div class="col-md-6 col-xs-6">
                            <label>Image upload</label>
                            <input type="file" class="file-styled-primary" name="image" id="image" value="" accept="image/*">
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
                            <div class="col-md-12 col-xs-12">
                            <label>@lang('plan.image')</label>
                              <div class="dropzone dropzone-file-area" id="my-dropzone" name="image">
                              </div>
                            </div>
                        </div>
                    </div>


                    <!-- hidden field -->
                    <input type="hidden" name="location" id="location" value="">
                    <input type="hidden" name="pos_x" id="pos_x" value="">
                    <input type="hidden" name="pos_y" id="pos_y" value="">


                </div>
                <div class="clearfix" style="padding-bottom: 10px;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                    <button type="submit" class="btn btn-primary" disabled="" id="btnAddIssueSubmit">@lang('general.submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /Mini modal For location menu -->

<!-- Mini modal For view issue menu- issue details -->
<div id="menu_issue_details" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><i class="icon-flag3"></i> @lang('plan.issueDetails')</h5>
            </div>
            <div class="modal-body">
                
                <!-- image field -->
                <div class="row" align="center" id="field_status_wip" >
                    <center><div class="start_image"></div></center>
                </div>

                <div class="row" align="center" id="field_status_complete">
                    <div class="col-md-6 col-xs-6">
                        <center><div class="start_image"></div></center>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <center><div class="last_image"></div></center>
                    </div>
                </div>
                <br>

                <div class="row">
                    <strong><h5>&nbsp;&nbsp;<span id="issue_id_details"></span></h5></strong>
                </div>
                <div class="table-responsive">
                    <table class="" style="width: 100%;">
                        <tr>
                            <th>@lang('plan.priority')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="priority_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.unit')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="unit_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.location')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td>: <span id="location_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.category')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="category_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.type')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="type_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.issue')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="issue_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.createDate')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="created_date_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.createBy')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="created_by_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.comment')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="comment_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.due')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="due_by_details"></span></td>
                        </tr>
                        <tr>
                            <th>@lang('plan.assignTo')</th>
                            <!-- <td>&nbsp; : &nbsp;</td> -->
                            <td> : <span id="contractor_details"></span> (<span id="contractor_abv_details"></span>)</td>
                        </tr>
                    </table>
                </div>
                <hr>

                <div id="close_issue_div">
                    <div class="row">
                        <div class="col-md-4 col-xs-4" align="center">
                        </div>
                        
                        <!-- <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeIssue()"><i class="icon-split text-primary"></i><span>Merge Issue</span></a>
                        </div> -->
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="viewHistory()">
                                <i class="icon-history text-primary"></i>
                                <span>@lang('plan.history')</span>
                            </a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                        </div>
                    </div>
                    <div class="row advance_field" style="padding-top: 10px;display: none;">
                        <div class="col-md-2 col-xs-2" align="center">
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeIssue()"><i class="icon-merge text-primary"></i><span>@lang('plan.mergeIssue')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeHistory()"><i class="icon-stack-text text-primary"></i><span>@lang('plan.mergeHistory')</span></a>
                        </div>
                        <div class="col-md-2 col-xs-2" align="center">
                           
                        </div>
                    </div>
                    <div class="row" style="padding-top: 10px;">
                        <div class="col-md-12 col-xs-12" align="center">
                            <a onclick="showAdvance()" class="showAdvance" style="color: black;"><fieldset>@lang('plan.moreAdv') <i class="icon-circle-down2"></i></a>
                            <a onclick="hideAdvance()" class="hideAdvance" style="display: none;color: black;"><fieldset>@lang('plan.hideAdv') <i class="icon-circle-up2"></i></a>
                        </div>
                        <!-- <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeIssue()"><i class="icon-split text-primary"></i><span>Merge Issue</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                           
                        </div> -->
                    </div>
                </div>
                <div id="other_issue_div">
                    <div class="row">
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="editIssue()" id="edit-issue-btn"><i class="icon-pencil text-primary"></i><span>@lang('general.edit')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="addInfo()"><i class="icon-file-plus text-primary"></i><span>@lang('plan.addInfo')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="viewHistory()">
                                <i class="icon-history text-primary"></i>
                                <span>@lang('plan.history')</span>
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="joinIssue()"><i class="icon-link2 text-primary"></i><span>@lang('plan.joinIssue')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="moveMarker()"><i class="icon-move text-primary"></i><span>@lang('plan.moveIssue')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="duplicateIssue()"><i class="icon-copy4 text-primary"></i><span>@lang('plan.duplicateIssue')</span></a>
                        </div>
                    </div>


                    <div class="row advance_field" style="padding-top: 10px;display: none;">
                        <div class="col-md-2 col-xs-2" align="center">
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeIssue()"><i class="icon-merge text-primary"></i><span>@lang('plan.mergeIssue')</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeHistory()"><i class="icon-stack-text text-primary"></i><span>@lang('plan.mergeHistory')</span></a>
                        </div>
                        <div class="col-md-2 col-xs-2" align="center">
                           
                        </div>
                    </div>
                    <div class="row" style="padding-top: 10px;">
                        <div class="col-md-12 col-xs-12" align="center">
                            <a onclick="showAdvance()" class="showAdvance" style="color: black;"><fieldset>@lang('plan.moreAdv') <i class="icon-circle-down2"></i></a>
                            <a onclick="hideAdvance()" class="hideAdvance" style="display: none;color: black;"><fieldset>@lang('plan.hideAdv') <i class="icon-circle-up2"></i></a>
                        </div>
                        <!-- <div class="col-md-4 col-xs-4" align="center">
                            <a href="#" class="btn btn-link btn-float has-text" onclick="mergeIssue()"><i class="icon-split text-primary"></i><span>Merge Issue</span></a>
                        </div>
                        <div class="col-md-4 col-xs-4" align="center">
                           
                        </div> -->
                    </div>
                </div>
                
            </div>

            <div class="clearfix" style="padding-bottom: 10px;"></div>
            <div class="modal-footer" id="issue_detail_button">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                <!-- <button type="button" class="btn btn-danger" style="width: 100%">Close</button> -->
            </div>

        </div>
    </div>
</div>
<!-- /Mini modal For location menu -->

<!-- Mini modal For view history-->
<div id="view_history" class="modal fade" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><i class="icon-history"></i> History</h5>
            </div>
            <div class="modal-body">
                <div >
                    <ul class="media-list content-group" id="history_content">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Mini modal For location menu -->

<!-- Mini modal For add issue_info_detail -->
<div id="add_info" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><i class="icon-file-plus"></i> @lang('plan.addInfo')</h5>
            </div>
            <form action="" id="form_add_info">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>@lang('plan.comment')</label>
                            <textarea class="form-control" name="comment_info" id="comment_info" rows="4" placeholder="Message..." autofocus="" required=""></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>@lang('plan.imageUpload')</label>
                            <input type="file" class="file-styled-primary" name="image_info" id="image_info" value="" accept="image/*">
                        </div>
                    </div>

                    <input type="hidden" name="issue_id_info" id="issue_id_info" value="">

                </div>
                <div class="clearfix" style="padding-bottom: 10px;"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="width: 100%">@lang('general.submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /Mini modal For location menu -->

<!-- Mini modal For edit issue_detail -->
<div id="edit_issue_detail" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">@lang('plan.updateIssueDetails')</h5>
            </div>

            <form id="form_edit_issue">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.category')</label>
                            <select data-placeholder="Select Category" class="select-search" name="category" id="edit_category" autofocus="" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.type')</label>
                            <select data-placeholder="Select Type" class="select-search" name="type" id="edit_type" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>

                        <div class="col-md-4 col-xs-4">
                            <label>@lang('plan.issue')</label>
                            <select data-placeholder="Select Issue" class="select-search" name="issue" id="edit_issue" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        @if($role->role_id != 4)
                            <!-- if admin add issue -->
                            <div class="col-md-{{ ($role->role_id != 4 ? 6 : 12) }} col-xs-{{ ($role->role_id != 4 ? 6 : 12) }}">
                                <label>@lang('plan.inspector')</label>
                                <select data-placeholder="Select Inspector" class="select-search" name="inspector" id="edit_inspector" autofocus="" required="">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-md-{{ ($role->role_id != 4 ? 6 : 12) }} col-xs-{{ ($role->role_id != 4 ? 6 : 12) }}">
                            <label>@lang('plan.contractor')</label>
                            <select data-placeholder="Select Contractor" class="select-search" name="contractor" id="edit_contractor" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <label>@lang('plan.priority')</label>
                            <select data-placeholder="Select Priority" class="select-search" name="priority" id="edit_priority" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <label>@lang('plan.due')</label>
                            <input type="date" name="due_by" id="edit_due_by" value="" class="form-control" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <label>@lang('plan.comment')</label>
                            <textarea class="form-control" name="comment" id="edit_comment" rows="2" required=""></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
                            <div class="col-md-12 col-xs-12">
                            <label>@lang('plan.image')</label>
                              <div class="dropzone dropzone-file-area" id="my-dropzone2" name="image">
                              </div>
                            </div>
                        </div>
                    </div>


                    <!-- hidden field -->
                    <input type="hidden" name="issue_id" id="edit_issue_id" value="">
                    
                </div>
                <div class="clearfix" style="padding-bottom: 10px;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /Mini modal For edit isue details -->

<!-- modal For show documents for issues -->
<div id="modal_show_issue_document" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"> @lang('plan.docList')</h5>
            </div>

            <div class="modal-body" id="issue_documents_panel">
                
            </div>
        </div>
    </div>
</div>
<!-- /modal For show documents for issue -->


<!-- Mini modal For edit edit_link_detail -->
<div id="edit_link_detail" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">@lang('plan.updateLink')</h5>
            </div>
            
            <form action="" id="form_edit_link_detail">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>This marker will link to :-</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <center>
                                <div class="plan_image"></div>
                            </center>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>@lang('plan.drawingSet')</label>
                            <select data-placeholder="Select Drawing Set" class="select-search" name="drawing_set" id="edit_drawing_set" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>@lang('plan.drawingPlan')</label>
                            <select data-placeholder="Select Drawing Plan" class="select-search" name="drawing_plan" id="edit_drawing_plan" required="">
                                <option value="">@lang('general.pleaseSelect')</option>
                            </select>
                        </div>
                    </div>
                    <!-- hidden field -->
                    <input type="hidden" name="link_id" id="link_id" value="">
                </div>

                <div class="clearfix" style="padding-bottom: 10px;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('general.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- /Mini modal For edit edit_link_detail -->


<!-- Mini modal For edit edit_link_detail -->
<div id="list_join_issue" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">@lang('plan.listIssue')</h5>
            </div>
            
            <div class="modal-body" id="body_list_joinIssue">

            </div>
        </div>
    </div>
</div>
<!-- /Mini modal For edit edit_link_detail -->


<!-- Mini modal For merge history -->
<div id="modal_merge_history" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">@lang('plan.mergeHistory')</h5>
            </div>
            
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<!-- Mini modal For merge history -->