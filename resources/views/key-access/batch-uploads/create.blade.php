@extends('layouts.template2')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Access Items : Batch Sign-Off
                </h5>
            </div>

            <div class="container-fluid">
                <form action="{{ route('access-item.batch-upload-select') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-9 col-md-offset-1">
                            <label>Pick A Type:</label>

                            <div class="content-group">
                                <div class="row row-seamless btn-block-group">
                                    <div class="col-xs-6">
                                        <button type="button" class="btn btn-default btn-primary btn-block btn-float btn-float-lg btn-transaction" id="handover_submit-type">
                                            <h6 class="no-margin text-black">HANDOVER (SUBMIT)</h6>
                                            <span>Management to Project Department</span>
                                        </button>
                                    </div>

                                    <div class="col-xs-6">

                                        <button type="button" class="btn btn-default btn-block btn-float btn-float-lg btn-transaction" id="handover_return-type">
                                            <h6 class="no-margin text-black">HANDOVER (RETURN)</h6>
                                            <span>Project Department to Management</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="type_transaction" id="type_transaction" value="handover_submit">
                    </div>

                 
                    <div class="row transcation_field" id="handover_submit_field">
                        <div class="row">
                            <div class="col-md-9 col-md-offset-1">

                                @if($unit_management->count() > 0)
                                <div class="col-md-12">
                                    <label>List of Units:</label>
                                </div>

                                @foreach($unit_management as $unit)
                                    <div class="col-md-6">
                                        <table class="table table-xxs table-bordered table-hover table-striped table-framed">
                                            <thead>
                                                <tr>
                                                    <td class="" >{{ $unit->block . '-' . $unit->level . '-' . $unit->unit }}</td>
                                                    <td class="" width="10%"><input type="checkbox" class="checkbox-all-submit" id="checkbox-all-submit{{ $unit->id }}"></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($unit->itemManagementSubmit as $key)
                                                    <tr>
                                                        <td class="" >{{ $key->name }} {{ $key->code ? '(' : '' }}{{ $key->code }}{{ $key->code ? ')' : '' }}</td>
                                                        <td class="" width="10%">
                                                                <input type="checkbox" class="key-unit-submit{{ $unit->id }}" name="key-submit[{{ $unit->id }}][]" value="{{ $key->id }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                                <div class="row" style="padding-bottom: 15px;padding-top: 15px;">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>

                    <div class="row transcation_field" id="handover_return_field" style="display: none">

                        <div class="row">
                            <div class="col-md-9 col-md-offset-1">

                                @if($unit_handler->count() > 0)
                                <div class="col-md-12">
                                    <label>List of Units:</label>
                                </div>

                                    @foreach($unit_handler as $unit)
                                        <div class="col-md-6">
                                            <table class="table table-xxs table-bordered table-hover table-striped table-framed">
                                                <thead>
                                                    <tr>
                                                        <td class="" >{{ $unit->block . '-' . $unit->level . '-' . $unit->unit }}</td>
                                                        <td class="" width="10%"><input type="checkbox" class="checkbox-all-return" id="checkbox-all-return{{ $unit->id }}"></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($unit->itemHandlerSubmit as $key)
                                                        <tr>
                                                            <td class="" >{{ $key->name }} {{ $key->code ? '(' : '' }}{{ $key->code }}{{ $key->code ? ')' : '' }}</td>
                                                            <td class="" width="10%">
                                                                <input type="checkbox" name="key-return[{{ $unit->id }}][]" class="key-unit-return{{ $unit->id }}"  value="{{ $key->id }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                    <div class="row" style="padding-bottom: 15px;padding-top: 15px;">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>

                    
                </form>
                
            </div>

        </div>
    </div>

</div>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/themes/default/style.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/jstree.min.js"></script>
<script>
    $(document).ready(function(){

        $(".btn-transaction").click(function(){

            $(".btn-transaction").removeClass('btn-primary')
            $("#" + this.id).addClass('btn-primary')
            $(".transcation_field").hide();

            var type = (this.id).replace('-type','');
            $("#type_transaction").val(type);
            switch(type) {
                case "handover_submit":
                    $("#handover_submit_field").show();
                    break;

                case "handover_return":
                    $("#handover_return_field").show();
                    break;
            }

        });

    });

    // $(document).ready(function() {

    //     $("#type_transaction").change(function(){
    //         type = $(this).val();

    //         $(".transcation_field").hide();
    //         switch(type) {

    //             case "handover_submit":
    //                 $("#handover_submit_field").show();
    //                 break;

    //             case "handover_return":
    //                 $("#handover_return_field").show();
    //                 break;
    //         }
    //     })        
        

    // });

    $(".checkbox-all-submit").click(function(){

        var id = (this.id).replace('checkbox-all-submit', '')
        var check = $(this).prop('checked')
        if(check) {
            $('.key-unit-submit' + id).prop('checked', true)
        }else{
            $('.key-unit-submit' + id).prop('checked', false)
        }
    })


    $(".checkbox-all-return").click(function(){

        var id = (this.id).replace('checkbox-all-return', '')
        var check = $(this).prop('checked')
        if(check) {
            $('.key-unit-return' + id).prop('checked', true)
        }else{
            $('.key-unit-return' + id).prop('checked', false)
        }
    })

</script>

@endsection
