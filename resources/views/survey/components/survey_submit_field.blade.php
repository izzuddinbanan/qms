<div class="col-md-8">
    <table id="myTable" class="table table-bordered table-hover table-striped table-framed">
        <thead>
            <tr>
                <!-- <td class="" >No</td> -->
                <td class="" >Survey</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($survey as $s)
                <tr>
                    <td>
                        <div class="row" style="margin-bottom:5px;">
                            <div class="col-md-3">
                                <p style="font-weight: bold;">Title:</p>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="question[]" value="{{$s->question}}" class="form-control" placeholder="e.g Please rate our service." autocomplete="off" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <p style="font-weight: bold;">Type:</p>
                            </div>
                            <div class="col-md-9">
                                <select class="select-search" name="type_survey[]" data-placeholder="Select Type">
                                    @if($s->type=="rate")
                                    <option value="rate" selected="">Rate</option>
                                    <option value="Comment">Comment</option>
                                    @elseif($s->type=="comment")
                                    <option value="rate">Rate</option>
                                    <option value="comment" selected>Comment</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <a class="btn btn-black" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>
                        </div>
                        <div>
                            
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td>
                        <div class="row" style="margin-bottom:5px;">
                            <div class="col-md-3">
                                <p style="font-weight: bold;">Title:</p>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="question[]" value="" class="form-control" placeholder="e.g Please rate our service." autocomplete="off" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <p style="font-weight: bold;">Type:</p>
                            </div>
                            <div class="col-md-9">
                                <select class="select-search" name="type_survey[]" data-placeholder="Select Type">
                                    <option value="">Please Select</option>
                                    <option value="rate" selected="">Rate</option>
                                    <option value="Comment">Comment</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <a class="btn btn-black" onclick="removeButton(this)" style="background-color:#FF0000;color:white"><i class="fa fa-trash"></i> Remove</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td width="100%">
                	<a class="btn btn-primary" id="add-item" style="width:100%;">Add Item</a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>