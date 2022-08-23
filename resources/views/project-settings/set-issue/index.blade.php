@extends('components.template-limitless.main')

@section('main')
@include('project-settings.components.tab')

    <div class="panel panel-flat">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h4 class="panel-title textUpperCase"><i class="fa fa-pencil-square-o"></i> @lang('project.issueHeader')
                    </h4>
                </div>
                <div class="col-md-6 col-xs-6 text-right">
                    <div class="btn-group btn-top">
                        <a href="{{ route('set-contractor.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.back')" data-placement="top"><i class="fa fa-backward"></i></a>
                        
                        <a href="{{ route('set-document.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="panel-body">

            <div class="tabbable ">
                <ul class="nav nav-sm nav-tabs nav-tabs-solid nav-tabs-component nav-justified">
                    <li class="active"><a href="#small-tab1" data-toggle="tab">@lang('project.tabCategory')</a></li>
                    <li><a href="#small-tab2" data-toggle="tab">@lang('project.tabPriority')</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="small-tab1">

                        @if(count($priority) > 0)
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" id="search" value="" autocomplete="off" class="form-control">
                            </div>
                            <div class="col-md-8">
                                
                                <form method="POST" action="{{ route('set-issue.storeIssue') }}" id="storeIssue">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" name="issue_id" id="issue_id">
                                            <div id="contractorField"></div>
                                            <button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">@lang('general.submit')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @else
                        <center>@lang('general.no_result')</center>

                        @endif
                        <br>
                        <div id="container"></div>
                    </div>

                    <div class="tab-pane" id="small-tab2">
                        <table id="table-data" class="table table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%">@lang('project.tabCurent')</th>
                                    <th>@lang('project.tabPriority')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priority as $priority)

                                    <tr class="">
                                        <td align="center"><input type="checkbox" {{ $priority->select }} name="priority[{{ $priority->id }}]" id="priority_{{ $priority->id }}" onclick="submitPriority({{ $priority->id }})" value="{{ $priority->id }}" class="styled"></td>
                                        <td> {{ $priority->type }} <small>({{ $priority->no_of_days }} days)</small></td>
                                    </tr>
                                @empty
                                    <tr class="">
                                        <td colspan="2" align="center"><i>@lang('general.no_result')</i></td> 
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>

        </div>

    </div>

@endsection
@section('script')
    

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/themes/default/style.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/jstree.min.js"></script>

    <script type="text/javascript">
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $(document).ready(function(){

            $.jstree.plugins.addHTML = function (options, parent) {
                this.redraw_node = function(obj, deep,
                                            callback, force_draw) {
                    obj = parent.redraw_node.call(
                        this, obj, deep, callback, force_draw
                    );
                    if (obj) {
                        var node = this.get_node(jQuery(obj).attr('id'));
                        if (node && 
                            node.data &&
                            ( "addHTML" in node.data ) ) {
                            jQuery(obj).append(
                                node.data.addHTML
                            );
                        }
                    }
                    return obj;
                };
            };

            $.jstree.defaults.addHTML = {};


            // data = [
            //     {text: "My Node", data: {addHTML: "<select><option>sad</option></select>"}, "icon" : "glyphicon glyphicon-file", state : {selected : true} },
            //     {text: "My Node", data: {addHTML: multiLineMarkup}},
            //     {text: "My Parent Node", data: {addHTML: "$10"}, children: [
            //             {text: "My child Node",
            //              data: {addHTML: multiLineMarkup},
            //              id: "aChild"},
            //             {text: "My child Node", data: {addHTML: "foobar"} , state : {selected : true}}
            //         ]
            //     },
            //     {text: "No addHTML in data", data: {}},
            //     {text: "No data"},
            //     {text: "Zero (false) value addHTML", data: { addHTML: 0}},
            //     {text: "My Node", data: {addHTML: "$10"}}
            // ];

            var data = {!! json_encode($issueArray) !!};

            $('#container').jstree({
                "plugins" : ["checkbox", "addHTML", "search"],
                core : {
                    'data' : data,
                    themes: {
                        responsive: false,
                    }
                }
            });


            var to = false;
            $('#search').keyup(function () {
                if(to) { clearTimeout(to); }
                to = setTimeout(function () {
                    var v = $('#search').val();
                    $('#container').jstree(true).search(v);
                }, 250);
            });

    
            $('#container').on("changed.jstree", function (e, data) {
                

                var checked =  data.selected;
                var data = [];
                var con = [];

                $("#contractorField").html("");
                checked.forEach(element => {

                    if(element.substring(0, 5) == "issue"){

                        data.push(element.substring(6));


                        if($("#conForIssue_" + element.substring(6)).val()){
                            
                            $("#contractorField").append($('<input />').attr('type', 'hidden').attr('name', 'contractor['+ element.substring(6) +']').attr('id', "selectCon_" + element.substring(6)).attr('value', $("#conForIssue_" + element.substring(6)).val()));
                        }
                    }
                });

                $("#issue_id").val(data);
            });
        });

        function submitPriority(id){

            var check = $("#priority_"+id).prop('checked');

            if ( check == true) {
                $.ajax({
                    url:"{{ route('set-issue.storePriority') }}",
                    type:'POST',
                    data:{'id' : id },
                    success:function(response){
                        displayMessage('Record saved successfully.', 'success', reload = false)
                    }
                });
            }else{
                $.ajax({
                    url:"{{ route('set-issue.removePriority') }}",
                    type:'POST',
                    data:{'id' : id },
                    success:function(response){

                    displayMessage('Priority successfully remove from this project.', 'success', reload = false)

                    }
                });
            }
        }


        function submitCategory(id){

            var check = $("#category_"+id).prop('checked');

            if ( check == true) {
                $.ajax({
                    url:"{{ route('set-issue.store') }}",
                    type:'POST',
                    data:{'category_id' : id },
                    success:function(response){

                        $('#groupFor_' + id).attr('disabled', false);

                        if(response['errors']){
                            displayMessage('Something Error.', 'error', reload = false)
                        }else{

                            displayMessage('Record saved successfully.', 'success', reload = false)
                        }
                    }
                });
            }else{

                $.ajax({
                    url:"{!! url('set-issue' ) !!}" + "/" + id,
                    type:'delete',
                    data:{'category_id' : id },
                    success:function(response){

                        $('#groupFor_' + id).attr('disabled', true).val("").trigger('change');

                        if(response['errors']){
                            displayMessage('Something Error.', 'error', reload = false)
                        }else{
                            displayMessage('Record saved successfully.', 'success', reload = false)
                        }
                    }
                });
            }
        }

        function selectConIssue(issueId){
      
            var contractor = $("#conForIssue_" + issueId).val();
            $("#selectCon_" + issueId).val(contractor);
        }

    </script>
@endsection
