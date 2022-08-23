@extends('layouts.template2')

@section('main')

<div class="panel panel-white">
    <div class="panel-heading">
        <h6 class="panel-title"><i class="icon-file-text2"></i> Handover</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>

    <form class="steps-basic" action="#">
        <h6>{{$handover_menu_key->display_name}}</h6>
        <fieldset>
            @foreach($key as $k)
                <h4 style="font-weight:bold">{{$k->name}}</h4>
                <table class="table table-hover table-striped table-framed">
                    <thead>
                        <tr>
                            <td class="col-md-4">Item</td>
                            <td class ="col-md-4">Quantity</td>
                            <td class="col-md-4">Status</td> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($k->item as $k_items)
                        <tr>
                            <td>{{$k_items->name}}</td>
                            <td>{{$k_items->quantity}}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            @endforeach
        </fieldset>

        <h6>{{$handover_menu_es->display_name}}</h6>
        <fieldset>
            @foreach($es as $es_section)
                <h4 style="font-weight:bold">{{$es_section->name}}</h4>
                <table class="table table-hover table-striped table-framed">
                    <thead>
                        <tr>
                            <td class="col-md-4">Item</td>
                            <td class="col-md-4">Quantity</td>
                            <td class="col-md-4">Status</td>
                        </tr>                        
                    </thead>
                    <tbody>
                        @foreach($es_section->item as $es_items)
                        <tr>
                            <td>{{$es_items->name}}</td>
                            <td>{{$es_items->quantity}}</td>
                            <td></td>
                        </tr>
                        @endforeach    
                    </tbody>
                </table>
                <br>
            @endforeach
        </fieldset>

        <h6>{{$handover_menu_waiver->display_name}}</h6>
        <fieldset>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8" style="margin: 0 auto;">
                    <div class="row">
                        {!! $waiver->description ?? '' !!}  
                    </div>
                    <div class="row">
                        <h4>Signature</h4>
                    </div>
                    <div class="row">   
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </fieldset>

        <h6>{{$handover_menu_photo->display_name}}</h6>
        <fieldset>
            <div class="row">
            </div>
        </fieldset>

        <h6>{{$handover_menu_acceptance->display_name}}</h6>
        <fieldset>
            <div class="row">
            </div>
        </fieldset>

        <h6>{{$handover_menu_survey->display_name}}</h6>
        <fieldset>
            <div class="row">
            </div>
        </fieldset>


    </form>
</div>

<script src="https://szimek.github.io/signature_pad/js/signature_pad.umd.js"></script>
<script>
</script>

@endsection
