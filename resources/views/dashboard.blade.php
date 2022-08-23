@extends('components.template-limitless.main')

@section('main')
    <style type="text/css">
        th {
            font-size: 12px;
        }
    </style>
    <div class="content">
        <div class="panel panel-flat">
            <!-- Mini size -->
            <div class="tabbable">
                <ul class="nav nav-tabs bg-theme no-margin">
                    <li class="active"><a href="#mini-tab1" data-toggle="tab">@lang('dashboard.byIssue')</a></li>
                    <li><a href="#mini-tab2" data-toggle="tab">@lang('dashboard.byContractor')</a></li>
                    <li><a href="#mini-tab3" data-toggle="tab">@lang('dashboard.byType')</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="mini-tab1">
                        <!-- Extra mini table -->
                        <div class="panel-heading">
                            <h5 class="panel-title"><b>@lang('dashboard.issue')</b></h5>
                        </div>
                        <div class="panel-body" style="padding: 0px;">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>@lang('dashboard.project')</th>
                                                    <th style="text-align: center;">Unit</th>
                                                    <th style="text-align: center;"><font style="color: #F99100">New</font></th>
                                                    <th style="text-align: center;"><font style="color: #BD5607">Pending</font></th>
                                                    <th style="text-align: center;"><font style="color: #EFD000">WIP</font></th>
                                                    <th style="text-align: center;"><font style="color: #DF00D0">Completed</font></th>
                                                    <th style="text-align: center;"><font style="color: #58BA63">Closed</font></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @forelse($projects as $project)
                                                    <tr>
                                                        <td>{{ $project->project_name }}</td>
                                                        <td style="text-align: center;">{{ $project->units_count }}</td>
                                                        <td style="text-align: center;">{{ isset($issue_count[$project->project_id]) ? $issue_count[$project->project_id]->new_issues : 0 }}</td>
                                                        <td style="text-align: center;">{{ isset($issue_count[$project->project_id]) ? $issue_count[$project->project_id]->pending_start_issues : 0 }}</td>
                                                        <td style="text-align: center;">{{ isset($issue_count[$project->project_id]) ? $issue_count[$project->project_id]->wip_issues : 0 }}</td>
                                                        <td style="text-align: center;">{{ isset($issue_count[$project->project_id]) ? $issue_count[$project->project_id]->completed_issues : 0 }}</td>
                                                        <td style="text-align: center;">{{ isset($issue_count[$project->project_id]) ? $issue_count[$project->project_id]->closed_issues : 0 }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" style="text-align: center;"><i>@lang('general.no_result').</i></td>
                                                    </tr>
                                                    @endforelse
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-md-12">
                                    <h6 style="padding-top: 10px; padding-bottom: 10px; padding-right: 0px; padding-left: 15px; background-color: lightgray;">
                                        <b>@lang('dashboard.aging')</b>
                                    </h6>
                                </div>

                                <div class="col-xs-12 col-md-12">
                                    <div class="chart-container">
                                        <div class="chart" id="google-bar-stacked"></div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                            
                    <div class="tab-pane" id="mini-tab2">
                        <!-- Extra mini table -->
                        <div class="panel-heading">
                            <h5 class="panel-title">@lang('dashboard.listContractor')</h5>    
                        </div>
                        <div class="panel-body" style="padding: 0px;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%">@lang('dashboard.contractor')</th>
                                            <th style="text-align: center;"><font style="color: #EFD000">WIP</font></th>
                                            <th style="text-align: center;"><font style="color: red">OVERDUE</font></th>
                                            <th style="text-align: center;"><font style="color: #DF00D0">COMPLETE</font></th>
                                            <th style="text-align: center;"><font>TOTAL</font></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @forelse($contractors as $contractor)
                                                <tr>
                                                    <td>{{ $contractor->contractor_name }} ({{ $contractor->contractor_abb_name }})</td>
                                                    <td style="text-align: center;">{{ $contractor->wip_issues }}</td>
                                                    <td style="text-align: center;">{{ $contractor->overdue_issues }}</td>
                                                    <td style="text-align: center;">{{ $contractor->completed_issues }}</td>
                                                    <td style="text-align: center;">{{ $contractor->wip_issues + $contractor->completed_issues }}</td>
                                                </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" style="text-align: center;"><i>@lang('general.no_result').</i></td>
                                            </tr>
                                            @endforelse
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="mini-tab3">
                        <div class="panel-heading">
                            <h5 class="panel-title"><b>@lang('dashboard.types')</b></h5>
                        </div>
                        <div class="panel-body" style="padding: 0px;">
                            <div class="row">
                                <div class="col-xs-12 col-md-12">
                                    <div class="chart-container">
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

        var project = {!! json_encode($projects) !!};
        var issue_count = {!! json_encode($issue_count) !!};
        var type_count = {!! json_encode($type_count) !!};

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
                    var count = '{!! count($projects) !!}';
                    var chart_height = count * 50;
                    
                    if (count > 0) {
                        var detail = [];
                        detail.push(['Genre', '< 7 Days', '7 - 14 Days', '15 - 22 Days', '23 - 30 Days', '> 30 Days' ]);

                        var max = 0;
                        project.forEach(element => {
                            var detail_count = issue_count[element['project_id']] !== undefined ? issue_count[element['project_id']] : null;

                            var issues_less_than_7_days = parseInt(detail_count ? detail_count['issues_less_than_7_days'] : 0);
                            var issues_7_to_14_days = parseInt(detail_count ? detail_count['issues_7_to_14_days'] : 0);
                            var issues_15_to_22_days = parseInt(detail_count ? detail_count['issues_15_to_22_days'] : 0);
                            var issues_23_to_30_days = parseInt(detail_count ? detail_count['issues_23_to_30_days'] : 0);
                            var issues_more_than_30_days = parseInt(detail_count ? detail_count['issues_more_than_30_days'] : 0);

                            var sum_of_record = issues_less_than_7_days + issues_7_to_14_days + issues_15_to_22_days + issues_23_to_30_days + issues_more_than_30_days;
                            
                            if (sum_of_record > max) {
                                max = sum_of_record;
                            }

                            detail.push([ 
                                element['project_name'], 
                                issues_less_than_7_days,
                                issues_7_to_14_days,
                                issues_15_to_22_days,
                                issues_23_to_30_days,
                                issues_more_than_30_days
                            ]);

                            var data = google.visualization.arrayToDataTable(detail);

                            // Options
                            var options_bar_stacked = {
                                fontName: 'Roboto',
                                height: 50 + chart_height,
                                fontSize: 12,
                                chartArea: { left: '20%', width: '60%', height: chart_height },
                                isStacked: true,
                                tooltip: { textStyle: { fontName: 'Roboto', fontSize: 13 }},
                                hAxis: {
                                    gridlines:{ color: '#e5e5e5', count: 6 },
                                    viewWindowMode:'explicit',
                                    viewWindow: { min: 0, max: Math.round((max + 5)/ 10) * 10 },
                                    format: 'short'
                                },
                                legend: { position: 'top', alignment: 'center', textStyle: { fontSize: 12 }},
                                series: {
                                    0:{color:'#109618'},
                                    1:{color:'#7ec141'},
                                    2:{color:'#eaa60f'},
                                    3:{color:'#f47119'},
                                    4:{color:'#e54d39'}
                                }
                            };

                            // Draw chart
                            var bar_stacked = new google.visualization.BarChart($('#google-bar-stacked')[0]);
                            bar_stacked.draw(data, options_bar_stacked);
                        });    
                    } else {
                        $('#google-bar-stacked').html("").append("<center><p style='padding-left: 20px !important'>@lang('general.no_display')</p></center>")
                    }
                } else if (tab == "#mini-tab3") {
                    $('#type-chart').html("");
                    var count = type_count.length;

                    if (count > 0) {
                        var max = type_count[0].sum;
                        var detail = [];
                        detail.push(['Type', 'Count']);

                        var height = count * 30;
                        
                        type_count.forEach(element => {
                            detail.push([element.issue_name, parseInt(element.sum)]);
                        });

                        var data = google.visualization.arrayToDataTable(detail);
                        // Options
                        var options_bar = {
                            fontName: 'Roboto',
                            height: height + 50,
                            fontSize: 12,
                            chartArea: { left: '40%', width: '45%', height: height },
                            tooltip: { textStyle: { fontName: 'Roboto', fontSize: 13 }},
                            hAxis: {
                                gridlines:{ color: '#e5e5e5', count: 5 },
                                format: 'short'
                            },
                            legend: { position: "none" },

                        };

                        // Draw chart
                        var type_bar = new google.visualization.BarChart($('#type-chart')[0]);
                        type_bar.draw(data, options_bar);
                    } else {
                        $('#type-chart').append("<p style='padding-left: 20px !important'>No Data For Display </p>")
                    }
                }
            }, 500);
        }    
    </script>

@endsection
