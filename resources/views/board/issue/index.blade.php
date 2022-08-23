@extends('components.template-limitless.main')

@section('main')
    <style type="text/css">
        .center-inTable{
            text-align: center;
        }
    </style>
        
    <div class="content row">
        <div class="col-xs-12 col-md-9">
            <div class="panel panel-flat">
                <div class="panel-heading no-padding">
                    <div id="issue_tab">                             
                        <ul class="nav nav-tabs bg-theme no-margin">
                            @foreach ($status as $key => $val)
                            <li id="status_{{ $key }}"><a onclick="applyFilter({ status : {{ $key }} })"> {{ $val }} 
                                <span class="badge bg-slate position-right"> </span>
                            </a></li>
                            @endforeach 
                        </ul> 
                        <ul class="nav nav-tabs bg-theme no-margin" style="margin-top: 2px!important">
                            @foreach ($priority as $key => $val)
                            <li id="priority_{{ $key }}"><a onclick="applyFilter({ priority : {{ $key }} })"> {{ $val }} 
                                <span class="badge bg-slate position-right"> </span>
                            </a></li>
                            @endforeach 
                        </ul> 
                    </div>
                </div>
                <br>
                <div class="panel-body table-body no-padding">
                    
                </div>
            </div>
        </div>
        <div class="col-md-3 hidden-xs">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="form_export_listing" action="{{ route('issue.export') }}" target="_blank" style="display: none;"></form>
                            <button type="button" class="btn bg-theme-2 btn-sm" onclick="clearFilter()"> Clear Filter </button>                            
                        </div>
                        @php
                            $block_option = $unit->groupBy('block');
                            $level_option = $unit->groupBy('level');
                            $unit_option = $unit->groupBy('unit');
                        @endphp
                        <div class="col-md-12" style="margin-top: 20px! important"> 
                            <form id="form_filter_listing">
                                <fieldset>
                                    <legend class="text-semibold">Filter Option</legend>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Unit Type Filter</legend>
                                                <select data-placeholder="Select Unit Type" class="select" id="select_unit_type">
                                                    <option value="0"> All </option> 
                                                    <option value="unit"> Unit </option> 
                                                    <option value="common"> Common Area </option> 
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Category Filter</legend>
                                                <select data-placeholder="Select Category" class="select" id="select_category">
                                                    <option value="0"> All </option> 
                                                    @foreach ($defect_type as $type)
                                                        <option value="{{ $type->id }}"> {{ $type->name }} </option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Contractor Filter</legend>
                                                <select data-placeholder="Select Contrator" class="select" id="select_contractor">
                                                    <option value="0"> All </option> 
                                                    @foreach ($contractors as $key => $contractor)
                                                        <option value="{{ $key }}"> {{ $contractor }} </option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Location Filter</legend>
                                                <select data-placeholder="Select Location" class="select" id="select_location">
                                                    <option value="0"> All </option> 
                                                    @foreach ($location as $val)
                                                        <option value="{{ $val->id }}"> {{ $val->name }} </option> 
                                                    @endforeach                                                
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Block Filter</legend>
                                                <select data-placeholder="Select Block" class="select" id="select_block">
                                                    <option value="0"> All </option> 
                                                    @foreach ($block_option as $key => $val)
                                                        <option value="{{ $key }}"> {{ $key }} </option>    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Level Filter</legend>
                                                <select data-placeholder="Select Level" class="select" id="select_level">
                                                    <option value="0"> All </option> 
                                                    @foreach ($level_option as $key => $val)
                                                        <option value="{{ $key }}"> {{ $key }} </option>    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Unit Filter</legend>
                                                <select data-placeholder="Select Unit" class="select" id="select_unit">
                                                    <option value="0"> All </option> 
                                                    @foreach ($unit_option as $key => $val)
                                                        <option value="{{ $key }}"> {{ $key }} </option>    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin">Date Filter</legend>
                                                <select data-placeholder="" class="select" id="select_date_type">
                                                    <option value="1"> Lodged Date </option> 
                                                    {{-- <option value="2"> Confirmation Date </option>  --}}
                                                    <option value="3"> Target Completion Date </option> 
                                                    <option value="4"> Completion Date </option> 
                                                    <option value="5"> Closing Date </option> 
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin"> From </legend>
                                                <input class="form-control" type="date" id="select_date_from">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <legend class="text-size-mini text-muted no-border no-padding no-margin"> To </legend>
                                                <input class="form-control" type="date" id="select_date_to">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    
    <script type="text/javascript">

        var unit = {!! $unit !!};
        var query_filter = { 
            'status' : 0, 
            'priority' : 0, 
            'unit_type': 0,  
            'category': 0, 
            'location': 0, 
            'block': 0, 
            'level': 0, 
            'unit': 0,
            'date_type': 0, 
            'from': 0, 
            'to': 0,
            'contractor': 0
        };

        var current_page = 1;
        var filter = [];
        var tabs = [];
        let status = {!! json_encode($status) !!};
        let priority = {!! json_encode($priority) !!};

        status.forEach((element, key) => {
            tabs.push('status_' + key);
        });

        for (var i in priority) {
            tabs.push('priority_' + i);
        }
        
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            }
        });

        $(document).ready(function() {
            $('.select').select2();

            localStorage.setItem('prev_url', window.location.href);

            filter = {!! json_encode($current_active) !!};

            $('body').addClass('sidebar-xs');

            getCount();
            applyFilter(filter);

            // history.pushState(null, null, 'issue');

            $("[id^=select_]").change(function(event) {
                if (event.target.id != "select_date_from" && event.target.id != "select_date_to") {
                    var key = event.target.id.replace("select_", "");
                    query_filter[key] = event.target.value;

                    if (key == "block") {
                        var new_arr = event.target.value == 0 
                            ? unit.reduce((unique, o) => { if(!unique.some(obj => obj.level === o.level)) { unique.push(o); } return unique; },[])
                            : unit.filter(element => element["block"] == event.target.value).reduce((unique, o) => { if(!unique.some(obj => obj.level === o.level)) { unique.push(o); } return unique; },[]);

                        $("#select_level").html("").append(new Option("All", 0, false, false));

                        new_arr.forEach(element => {
                            var newOption = new Option(element["level"], element["level"], false, false);
                            $("#select_level").append(newOption);
                        });

                        $("#select_level").trigger("change");
                    } else if (key == "level") {
                        var new_arr = event.target.value == 0 
                            ? unit.reduce((unique, o) => { if(!unique.some(obj => obj.unit === o.unit)) { unique.push(o); } return unique; },[])
                            : unit.filter(element => element["level"] == event.target.value).reduce((unique, o) => { if(!unique.some(obj => obj.unit === o.unit)) { unique.push(o); } return unique; },[]);

                        $("#select_unit").html("").append(new Option("All", 0, false, false));

                        new_arr.forEach(element => {
                            var newOption = new Option(element["unit"], element["unit"], false, false);
                            $("#select_unit").append(newOption);
                        });

                        $("#select_unit").trigger("change");
                    }
                } else {
                    if (event.target.value != "") {
                        query_filter['date_type'] = $("#select_date_type").val();
                        
                        if (event.target.id == "select_date_from") {
                            query_filter['from'] = event.target.value;
                        } else {
                            query_filter['to'] = event.target.value;
                        }
                    }
                }
                
                current_page = 1;
                applyFilter({ 'status' : 0 });
                getCount();
            });
        });

        function getCount() {
            $.ajax({
                url:"{{ route('issue.getCount') }}",
                type:"GET",
                data: query_filter,
                success:function(response) {
                    tabs.forEach(element => {
                        var count = response[element] !== undefined ? (response[element] !== null ? response[element] : 0) : 0;
                        $("#" + element + " span").text(count);
                    });
                }
            });
        }

        function exportListing() {
            $("#form_export_listing").html("");
            
            for (var i in query_filter) {
                $("#form_export_listing").append("<input name=" + i + " value= " + query_filter[i] + " />");
            }

            setTimeout(() => {
                $("#form_export_listing").submit();
            }, 1000);
        }

        function applyFilter(request) {
            query_filter["status"] = query_filter["priority"] = 0;
            $("[id^=status_], [id^=priority_]").removeClass('active');

            for(var i in request){
                query_filter[i] = request[i];
    
                $("#" + i + "_" + request[i]).addClass('active');
            }
            getIssuesListing();
        }

        function gotoPage(page) {
            current_page = page;
            getIssuesListing();
        }

        function prevPage() {
            current_page--;
            getIssuesListing();
        }

        function nextPage() {
            current_page++;
            getIssuesListing();
        }

        function getIssuesListing() {            
            $(".loader").show();

            $.ajax({
                url:"{{ route('issue.getListing') }}" + '?page=' + current_page,
                type:"GET",
                data: query_filter,
                success:function(response) {
                    $(".table-body").html("").html(response);
                    $(".loader").hide();

                    $('table.table tbody tr').click(function () {
                        window.location.href = $(this).data('url');
                    });
                }
            });
        }

        function clearFilter() {
            query_filter['active'] = 0; 
            query_filter['unit_type'] = 0; 
            query_filter['category'] = 0;
            query_filter['location'] = 0;
            query_filter['block'] = 0;
            query_filter['level'] = 0;
            query_filter['unit'] = 0;
            query_filter['date_type'] = 0;
            query_filter['from'] = 0;
            query_filter['to'] = 0;

            $('#select_unit_type, #select_category, #select_location, #select_block, #select_level, #select_unit').val('0').trigger('change');
            $('#select_date_type').val('1').trigger('change');
            $("#form_filter_listing").trigger("reset");
            current_page = 1;
            applyFilter({ 'status' : 0 });
            getCount();
        }
    </script>

@endsection
