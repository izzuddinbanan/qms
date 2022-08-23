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
                    <i class="icon-key"></i> Access Items : Batch Sign-Off 
                </h5>
            </div>

            <form action="{{ route('access-item.batch-upload-store') }}" method="POST" id="myForm">
                @csrf

                <div class="panel-body">
                    

                    <div class="row">
                        <div class="col-md-9 col-md-offset-1">
                            <div class="col-md-12">
                                <label class="text-bold">Type: {{ ucwords(str_replace('_', ' ', session('type_selected'))) }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9 col-md-offset-1">

                            <div class="col-md-12">
                                    
                                <div class="panel-group content-group-xs" id="accordion1">
                                        
                                    @foreach($units  as $unit)
                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h6 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion1" href="#accordion-{{ $unit->id }}" aria-expanded="false" class="collapsed">{{ $unit->block . '-' . $unit->level . '-' . $unit->unit }}</a>
                                            </h6>
                                        </div>
                                        <div id="accordion-{{ $unit->id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                            <div class="panel-body">
                                                @if(session('type_selected') == 'handover_submit')
                                                    @foreach($unit->itemManagementSubmit as $item)
                                                        @if(in_array($item->id, session('key_selected')))
                                                        <li id="key_{{ $item->id }}">{{ $item->name }} {{ $item->code ?? '(' }}{{ $item->code }}{{ $item->code ?? ')' }}</li>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach($unit->itemHandlerSubmit as $item)
                                                        @if(in_array($item->id, session('key_selected')))
                                                        <li id="key_{{ $item->id }}">{{ $item->name }} {{ $item->code ?? '(' }}{{ $item->code }}{{ $item->code ?? ')' }}</li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <hr width="50%">


                    
                    @include('key-access.transactions.components.default_field')

                </div>

                <div class="panel-footer">
                    <div class="row col-md-12 text-right">
                        <button type="submit" class="btn btn-primary" onclick="submitForm(event)">Submit form <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </div>
            </form>
        </div>


    </div>

</div>

<script src="https://szimek.github.io/signature_pad/js/signature_pad.umd.js"></script>
<script>
    
    var type = 'submit';
    $(document).ready(function(){

        $("#type_transaction").change(function(){
            type = $(this).val();

            $(".transcation_field").hide();
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
        })

    });

    
    //SUBMIT ITEM SCRIPT //
    $("#add-item").click(function(){

        var count = 2;
        var itemNo = $('#myTable tbody tr').length + 1;
        var newRow = '<tr>' +
                        '<td><i class="fa fa-trash" style="color:red; cursor: pointer" onclick="removeButton(this)"></i> <span class="itemNo"></span></td>'+
                        '<td><input type="text" name="code[]" value="" class="form-control" placeholder="e.g 1001" autocomplete="off"></td>'+
                        '<td><input type="text" name="name[]" value="" class="form-control" placeholder="e.g Master Room" autocomplete="off"></td>'+
                        '<td><input type="number" name="quantity[]" value="" class="form-control" placeholder="e.g 4" min="1"></td>'+
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


        document.getElementById("myForm").submit();

    }

</script>

@endsection
