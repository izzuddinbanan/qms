@extends('layouts.template2')

@section('main')
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-flat">

            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-key"></i> Access Items :Batch Upload
                </h5>
            </div>

            <div class="container-fluid">
                <form action="{{ route('access-item.batch-upload-select-key') }}" method="POST">
                    <div class="row">
                        <div class="col-md-2">
                            <label style="color: white;">sad</label><br>
                            <button type="submit" class="btn btn-primary">Next</button>
                        </div>
                    </div>
                    <div class="row transcation_field" id="handover_submit_field">
                        
                        <div class="col-md-6">
                            <label>Pick A Key:</label>

                            <input type="text" name="search" id="search" value="" autocomplete="off" class="form-control" autocomplete="off" placeholder="Search here ...">

                            @csrf
                            <input type="hidden" name="key" id="key">
                        </div>
                        <div class="col-md-12">
                            <div id="tree">
                                <ul>
                                    @foreach($units as $unit)
                                    <li id="{{ $unit->id }}">{{ $unit->unit }}
                                        <ul>
                                            @if($unit->itemManagementSubmit)
                                                @foreach($unit->itemManagementSubmit as $item)
                                                    <li id="key_{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</li>
                                                @endforeach
                                            @else
                                                @foreach($unit->itemHandlerSubmit as $item)
                                                    <li id="key_{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </li>
                                    @endforeach
                                </ul>
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
    $(document).ready(function() {

    });


    $('#tree').jstree({
        "plugins" : ["checkbox", "search"],

    });


    var to = false;
    $('#search').keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
            var v = $('#search').val();
            $('#tree').jstree(true).search(v);
        }, 250);
    });

    $("#tree").bind("changed.jstree", function (e, data) {
        var checked =  data.selected;
        var data = [];

        $("#key").val(checked);

    });


</script>

@endsection
