<div class="row">
    <div class="col-md-9 col-md-offset-1">

        <div class="col-md-12">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks"></textarea>
        </div>
    </div>
</div>

<hr>
<div class="row">
    <div class="col-md-9 col-md-offset-1">
        <div class="col-md-12">
            <label>Submitter Signature</label>
            <div class="wrapper" style="border-color: black;border">
                <canvas id="signature-pad-submit" class="signature-pad" width=400 height=200></canvas>
            </div>
            <input type="hidden" name="signature_submitted" value="" id="signature_submitted">
            <button class="btn btn-danger btn xs" id="sign-submit-clear" type="button">Clear Signature</button>
        </div>

        <div class="col-md-12">
            <label>Name</label>
            <input type="text" name="submitted_name" value="{{ $DrawingPlan->unitOwner->name ?? '' }}" class="form-control" placeholder="e.g John Doe" autocomplete="off">
        </div>
    </div>
</div>

<hr width="50%">

<div class="row">
    <div class="col-md-9 col-md-offset-1">

        <div class="col-md-12">
            <label>received Signature</label>
            <div class="wrapper" style="border-color: black;">
                <canvas id="signature-pad-received" class="signature-pad" width=400 height=200></canvas>
            </div>
            <input type="hidden" name="signature_received" value="" id="signature_received">
            <button class="btn btn-danger btn xs" id="sign-receive-clear" type="button">Clear Signature</button>
        </div>
        <div class="col-md-12">
            <label>Name</label>
            <input type="text" name="received_name" value="" class="form-control" placeholder="e.g John Doe" autocomplete="off">
        </div>
    </div>
</div>

