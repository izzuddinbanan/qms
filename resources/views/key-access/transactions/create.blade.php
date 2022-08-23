@extends('layouts.template2')

@section('main')
<style type="text/css">
    .col1 {
        background-color: red;
    }
    .wrapper {
        position: relative;
        width: auto;
        height: 200px;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width:auto;
        height:200px;
        background-color: grey;
        border-color: black;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Access Items : New Transaction ({{ $DrawingPlan->unit }})
                    <a href="{{ route('key-access.show', [$DrawingPlan->id]) }}" class="pull-right">
                        <button class="btn btn-primary">Back</button>
                    </a>
                </h5>
            </div>

            <form action="{{ route('access-item.transaction-store') }}" method="POST" id="myForm">
                @csrf

                <input type="hidden" name="drawing_plan_id" value="{{ $DrawingPlan->id }}">

                <div class="panel-body">
                    

                    <div class="row">
                        <div class="col-md-9 col-md-offset-1">
                            <label>Pick A Type:</label>

                            <div class="content-group">
                                <div class="row row-seamless btn-block-group">
                                    <div class="col-xs-6">
                                        <button type="button" class="btn btn-default btn-primary btn-block btn-float btn-float-lg btn-transaction" id="submit-type">
                                            <h6 class="no-margin text-black">SUBMIT</h6>
                                            <span>Unit Owner to Management</span>
                                        </button>

                                        <button type="button" class="btn btn-default btn-block btn-float btn-float-lg btn-transaction" id="handover_submit-type">
                                            <h6 class="no-margin text-black">HANDOVER (SUBMIT)</h6>
                                            <span>Management to Project Department</span>
                                        </button>
                                    </div>

                                    <div class="col-xs-6">
                                        <button type="button" class="btn btn-default btn-block btn-float btn-float-lg btn-transaction" id="return-type">
                                            <h6 class="no-margin text-black">RETURN</h6>
                                            <span>Management to Unit Owner</span>
                                        </button>

                                        <button type="button" class="btn btn-default btn-block btn-float btn-float-lg btn-transaction" id="handover_return-type">
                                            <h6 class="no-margin text-black">HANDOVER (RETURN)</h6>
                                            <span>Project Department to Management</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="text" name="type_transaction" id="type_transaction" value="submit" hidden="">
                    </div>


                    <hr width="50%">

                    @include('key-access.transactions.components.submit_field')
                    
                    @include('key-access.transactions.components.handover_submit_field')

                    @include('key-access.transactions.components.handover_return_field')


                    
                    <hr width="50%">


                    
                    @include('key-access.transactions.components.default_field')

                </div>

                <div class="panel-footer">
                    <div class="row col-md-12 text-right">
                        <button type="button" class="btn btn-primary" onclick="submitForm(event)" id="checking-button">Submit form <i class="icon-arrow-right14 position-right"></i></button>
                        <button type="submit" id="submit-button" class="btn btn-primary" style="display: none;">submit</button>
                    </div>
                </div>
            </form>
        </div>


    </div>

</div>

<script src="https://szimek.github.io/signature_pad/js/signature_pad.umd.js"></script>
<script>
    
    $(document).ready(function(){

        $(".btn-transaction").click(function(){

            $(".btn-transaction").removeClass('btn-primary')
            $("#" + this.id).addClass('btn-primary')
            $(".transcation_field").hide();

            var type = (this.id).replace('-type','');
            $("#type_transaction").val(type);
            switch(type) {
                case "submit":
                    $("#submit_field").show();
                    break;

                case "handover_submit":
                    $("#handover_submit_field").show();
                    break;

                case "handover_return":
                    $("#handover_return_field").show();
                    break;

                case "return":
                    $("#handover_submit_field").show();
                    break;
            }

        });

    });

    
    //SUBMIT ITEM SCRIPT //
    $("#add-item").click(function(){

        var count = 2;
        var itemNo = $('#myTable tbody tr').length + 1;
        var newRow = '<tr>' +
                        '<td><i class="fa fa-trash" style="color:red; cursor: pointer" onclick="removeButton(this)"></i> <span class="itemNo"></span></td>'+
                        '<td><input type="text" name="code[]" value="" class="form-control" placeholder="e.g 1001" autocomplete="off"></td>'+
                        '<td><input type="text" name="name[]" value="" class="form-control" placeholder="e.g Master Room" autocomplete="off" required></td>'+
                        '<td><input type="number" name="quantity[]" value="1" class="form-control" placeholder="e.g 4" min="1" required></td>'+
                    '</tr>';

        $("#myTable tbody").append(newRow);

        $( ".itemNo" ).each(function( index ) {
            $(this).text(count++);
        });

    });

    function removeButton(data){
        $(data).closest("tr").remove();
        var count = 2;

        $( ".itemNo" ).each(function( index ) {
            $(this).text(count++);
        });
    }
    //SUBMIT ITEM SCRIPT //


    // SIGNATURE PAD SUBMIT
    var signature_submit = document.getElementById('signature-pad-submit');

    function resizeCanvas() {
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        signature_submit.width = signature_submit.offsetWidth * ratio;
        signature_submit.height = signature_submit.offsetHeight * ratio;
        signature_submit.getContext("2d").scale(ratio, ratio);
    }

    window.onresize = resizeCanvas;
    resizeCanvas();

    var signaturePadSubmit = new SignaturePad(signature_submit);


    $("#sign-submit-clear").click(function() {
        signaturePadSubmit.clear();
    });

    // SIGNATURE PAD RECEIVE
    var signature_received = document.getElementById('signature-pad-received');

    function resizeCanvasSubmit() {
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        signature_received.width = signature_received.offsetWidth * ratio;
        signature_received.height = signature_received.offsetHeight * ratio;
        signature_received.getContext("2d").scale(ratio, ratio);
    }

    window.onresize = resizeCanvasSubmit;
    resizeCanvasSubmit();

    var signaturePadReceive = new SignaturePad(signature_received);




    $("#sign-submit-clear").click(function() {
        signaturePadSubmit.clear();
    });

    $("#sign-receive-clear").click(function() {
        signaturePadReceive.clear();
    });


    function submitForm(event) {
        event.preventDefault();

        if (signaturePadSubmit.isEmpty()) {
            return alert("Please provide a signature first.");
        }

        if (signaturePadReceive.isEmpty()) {
            return alert("Please provide a signature first.");
        }

        $("#signature_submitted").val(signaturePadSubmit.toDataURL('image/png'));
        $("#signature_received").val(signaturePadReceive.toDataURL('image/png'));



        var submitButton = document.getElementById('submit-button');
        var sendButton = document.getElementById('checking-button');

        sendButton.addEventListener('click', send);

        function send(e) {
            submitButton.click();
        }

    }

</script>

@endsection
