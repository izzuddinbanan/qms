@extends('components.template-limitless.main')

@section('main')
    <style>
        .font-label {
            font-size: 14px !important;
        }

        .font-numbering {
            font-size: 28px !important;
        }

        .count-color{
            color: white;
        }
    </style>

    <div class="loader" style=""></div>

    <div class="content">
        <div class="panel panel-flat">
            <div class="tabbable">
                <ul class="nav nav-tabs bg-theme no-margin">
                    <li class="active"><a href="#mini-tab1" data-toggle="tab"> @lang('dashboard.dashboard') </a></li>
                    <li><a href="#mini-tab2" data-toggle="tab">@lang('dashboard.byType')</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="mini-tab1">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <i class="icon-graph"></i> @lang('dashboard.dashboard') <strong> {{$project->name}}</strong>
                                <small class="display-block">{{ get_day_type() }} <strong>{{ Auth::user()->name }}</strong>! @lang('dashboard.niceDay').</small>
                            </h5>
                        </div>
                
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 col-xs-7">
                                    <div class="chart-container text-center" style="position: relative;">
                                        <div class="display-inline-block" id="dashboardPie"></div>
                                    </div>
            
                                </div>
                                <div class="col-md-6 col-xs-5" style="text-align: center;">
                                    @if($project->logo != null)
                                    <img src="{{ url('uploads/project_logo/' . $project->logo) }}" class="img-responsive">
                                    @endif
                                </div>
                            </div>
                            <div class="row" style="padding-top: 20px;">
                                <fieldset class="content-group">
                                    <div class="col-xs-12">
                                        <legend class="text-bold"> Status </legend>
                                    </div>
                                    @foreach ($issue as $key => $val)
                                        @if ($key == "new" || $key == "pending_start" || $key == "wip" || $key == "completed")
                                        <div class="col-xs-12 col-md-4 col-lg-3">
                                            <a href="{{ url('issue?status=' . $status[$key]['id']) }}">
                                                <div class="panel panel-body has-bg-image" style="background-color: {{ $status[$key]['internal_color'] }}">
                                                    {{-- <div class="media"> --}}
                                                        <div class="media-body">
                                                            <div class="no-margin count-color font-numbering">
                                                                <b align="top">{{ $val ? $val : 0 }}</b>
                                                                <div class="text-uppercase text-size-mini count-color">
                                                                    <b class="font-label">{{ implode(' ', explode('_', $key)) }}</b>
                                                                </div>
                                                            </div>
                                                        </div>
                    
                                                        <div class="media-right media-middle">
                                                            <i class="icon-clipboard4 icon-3x opacity-75"></i>
                                                        </div>
                                                    {{-- </div> --}}
                                                </div>
                                            </a>
                                        </div>
                                        @endif
                                    @endforeach
                                </fieldset>
            
                                <fieldset class="content-group">
                                    <div class="col-xs-12">
                                        <legend class="text-bold"> Priority </legend>
                                    </div>
                                    @foreach ($setting_priority as $key => $val)
                                        <div class="col-xs-12 col-md-4 col-lg-3">    
                                            <div class="media no-margin">
                                                <div class="media-body">
                                                    <a style="color: #8D8D8D" href="{{ url('issue?priority=' . $val['id']) }}"><span class="text-uppercase text-size-mini font-label"><b>{{ $val['type'] }} ({{ $val['no_of_days'] }} days)</b></span></a>
                                                    <h3 class="no-margin font-numbering" style="color: red; font-weight: bolder"><b>{{ $priority['priority_' . $val['id']] ? $priority['priority_' . $val['id']] : 0 }}</b></h3>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="mini-tab2">
                        <div class="panel-heading">
                            <h5 class="panel-title"><b>@lang('dashboard.byType')</b></h5>
                        </div>
                        <div class="panel-body" style="padding: 0px;">
                            <div class="row">
                                <div class="col-xs-12 col-md-12">
                                    <div class="chart-container" style="max-width: 90%">
                                        <div class="chart" id="type-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        window.onload = function() { $('.loader').show(); }   

        var project_id = '{{ session('project_id') }}';

        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(checkTabActive);

        $(document).ready(function() {            
            $(window).on('resize', resize);
            $(".sidebar-control").on('click', resize);

            function resize() {
                checkTabActive();
            }
        });

        $(".tabbable ul li").click(function(){
            checkTabActive();
        });
        
        function checkTabActive() {
            setTimeout(() => {
                var tab = $(".tabbable ul li.active")[0].firstChild.hash;
                
                if (tab == "#mini-tab1") {  
                    var data = {!! json_encode($issue) !!};
                    var status = {!! json_encode($status) !!};

                    var detail = google.visualization.arrayToDataTable([
                        ['Issue', 'Total'],
                        ['Lodged',  parseInt(data.lodged)],
                        ['New',  parseInt(data.new)],
                        ['Pending Start',  parseInt(data.pending_start)],
                        ['VOID', parseInt(data.void)],
                        ['WIP', parseInt(data.wip)],
                        ['Not Me', parseInt(data.not_me)],
                        ['Reassign', parseInt(data.reassign)],
                        ['Completed', parseInt(data.completed)],
                        ['Redo', parseInt(data.redo)],
                        ['Closed', parseInt(data.closed)]
                    ]);
                
                    // Options
                    var options_donut = {
                        fontName: 'Roboto',
                        pieHole: 0.55,
                        height: 300,
                        width: 500,
                        chartArea:{left:20,top:10,width:'70%',height:'70%'},
                        title:'Issue By Status',
                        sliceVisibilityThreshold:0,
                        slices: {
                            0: { color: status.lodged['internal_color'] },
                            1: { color: status.new['internal_color'] },
                            2: { color: status.pending_start['internal_color'] },
                            3: { color: status.void['internal_color'] },
                            4: { color: status.wip['internal_color'] },
                            5: { color: status.not_me['internal_color'] },
                            6: { color: status.reassign['internal_color'] },
                            7: { color: status.completed['internal_color'] },
                            8: { color: status.redo['internal_color'] },
                            9: { color: status.close_internal['internal_color'] }
                        }
                    };

                    var donut = new google.visualization.PieChart($('#dashboardPie')[0]);

                    google.visualization.events.addListener(donut, 'select', () => {
                        var selectedItem = donut.getSelection()[0];
            
                        var link = "{{ url('issue?status=') }}";
                        if (selectedItem){
                            var topping = detail.getValue(selectedItem.row, 0);
                            
                            topping = topping.toLowerCase().replace(" ", "_");
                            window.location = link + status[topping]['id'];
                        }
                    });   
                    
                    donut.draw(detail, options_donut);
                } else if (tab == "#mini-tab2") {
                    $('#type-chart').html("");
                    var type_count = {!! json_encode($type_count) !!};
                    var count = type_count.length;
                    
                    if (count > 0) {
                        
                        var detail = [];
                        detail.push(['Types', 'Lodged', 'New', 'Rejected', 'Not Me', 'Reassign','Pending Start', 'WIP', 'Completed', 'Redo' ,'Closed' ]);

                        var height = count * 30;

                        var max = 0;
                        type_count.forEach(element => {
                            detail.push([
                                element.issue_name + ', ' + element.sum, 
                                parseInt(element.lodged_issues), 
                                parseInt(element.new_issues), 
                                parseInt(element.rejected_issues), 
                                parseInt(element.not_me_issues), 
                                parseInt(element.reassign_issues), 
                                parseInt(element.pending_start_issues), 
                                parseInt(element.wip_issues), 
                                parseInt(element.completed_issues), 
                                parseInt(element.redo_issues), 
                                parseInt(element.closed_issues)
                            ]);

                            if (parseInt(element.sum) > max) {
                                max = parseInt(element.sum);
                            }
                        });

                        var data = google.visualization.arrayToDataTable(detail);
                        // Options
                        var options_bar_stacked = {
                            fontName: 'Roboto',
                            height: 50 + height,
                            fontSize: 12,
                            chartArea: { left: '40%', width: '45%', height: height },
                            isStacked: true,
                            tooltip: { textStyle: { fontName: 'Roboto', fontSize: 13 }},
                            hAxis: {
                                gridlines:{ color: '#e5e5e5', count: 6 },
                                format: 'short'
                            },
                            legend: { position: 'right', textStyle: { fontSize: 12 }},
                            series: {
                                0:{color:'#2952D6'},
                                1:{color:'#F99100'},
                                2:{color:'#EFC089'},
                                3:{color:'#1C9DFF'},
                                4:{color:'#7F7F7F'},
                                5:{color:'#BD5607'},
                                6:{color:'#EFD000'},
                                7:{color:'#DF00D0'},
                                8:{color:'#FF4A93'},
                                9:{color:'#58BA63'},
                            }
                        };

                        // Draw chart
                        var type_bar = new google.visualization.BarChart($('#type-chart')[0]);
                        type_bar.draw(data, options_bar_stacked);
                    } else {
                        $('#type-chart').append("<p style='padding-left: 20px !important'>No Data For Display </p>")
                    }
                }

                $('.loader').hide();
            }, 500);
        } 

    </script>

@endsection
