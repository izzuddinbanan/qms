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
                            @foreach ($tabs as $key => $val)
                            <li id="{{ $val }}"><a onclick="applyFilter({ 'active': '{{ $val }}' })">{{ $key }} <span class="badge bg-slate position-right"></span></a></li>
                            @endforeach
                        </ul>

                        <ul class="nav nav-tabs bg-theme no-margin" style="margin-top: 2px!important">
                            @foreach ($second_tabs as $key => $val)
                            <li id="{{ $val }}"><a onclick="applyFilter({ 'active': '{{ $val }}' })">{{ $key }} <span class="badge bg-slate position-right"></span></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <div class="panel-body table-body no-padding" style="margin-top: 20px!important;">
                    
                </div>
            </div>
        </div>
        @php
            $block_option = $unit->groupBy('block');
            $level_option = $unit->groupBy('level');
            $unit_option = $unit->groupBy('unit');
        @endphp
        <div class="col-md-3 hidden-xs">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="form_export_listing" action="{{ $type == 'unit' ? route('unit.general.export') : route('commonarea.general.export') }}" target="_blank" style="display: none;"></form>
                            <button type="button" class="btn bg-theme-2 btn-sm" onclick="clearFilter()"> Clear Filter </button>                            
                        </div>
                        <div class="col-md-12" style="margin-top: 20px! important"> 
                            <form id="form_filter_listing">
                                <fieldset>
                                    <legend class="text-semibold">Filter Option</legend>
                                    <div class="row">
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
                                                <select data-placeholder="Select Level" class="select" id="select_unit">
                                                    <option value="0"> All </option> 
                                                    <div id="unit_select">
                                                    @foreach ($unit_option as $key => $val)
                                                        <option value="{{ $key }}"> {{ $key }} </option>    
                                                    @endforeach
                                                    </div>
                                                </select>
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
        var type = "{!! $type !!}";
        var query_filter = { 
            'active' : 'all',
            'block' : 0,
            'level' : 0,
            'unit' : 0
        };

        var current_page = 1;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            }
        });

        $(document).ready(function() {
            $('.select').select2();

            $('body').addClass('sidebar-xs');

            applyFilter(query_filter);
            getCount();

            $("[id^=select_]").change(function(event) {
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

                current_page = 1;
                applyFilter({ 'active': 'all'});
                getCount();
            });
        });

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
            $("#issue_tab ul li").removeClass('active');
            $("#" + request.active).addClass('active');
            
            query_filter.active = request.active;
            current_page = 1;
            getIssuesListing();
        }

        function getCount() {
            var url = type == "unit" ? "{{ route('unit.getCount') }}" : "{{ route('commonarea.getCount') }}";

            $.ajax({
                url: url,
                type:"GET",
                data: query_filter,
                success:function(response) {
                    for (var i in response) {
                        $("#" + i + " span").text(response[i]);
                    }
                }
            });
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

            var url = type == "unit" ? "{{ route('unit.getListing') }}" : "{{ route('commonarea.getListing') }}";

            $.ajax({
                url: url + "?page=" + current_page,
                type:"GET",
                data: query_filter,
                success:function(response) {
                    $(".table-body").html("").html(response);
                    $('table.table tbody tr').click(function () {
                        window.location.href = $(this).data('url');
                    });

                    $(".loader").hide();
                }
            });
        }

        function clearFilter() {
            query_filter = { 
                'active' : 'all',
                'block' : 0,
                'level' : 0,
                'unit' : 0
            };

            $('#select_block, #select_level, #select_unit').val('0').trigger('change');
            getIssuesListing();
        }
    </script>

@endsection
