<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>{{ $issue['project'] }} Issue ID - {{ $issue['id'] }}</title>

        <style>
            @page {
                size: A4;
                margin-top:2cm;
                margin-bottom:2cm;
                margin-left:1.2cm;
                margin-right:1.2cm;
                padding: 0;

                body {
                    font-family: helvetica !important;
                    font-size: 11pt;
                    position: relative;
                } 
            }

            header, footer {
                position: fixed; 
                left: 0px; 
                right: 0px; 
                height: 20px; 
                font-size: 14px;
                color: gray;
            }

            header { 
                top: -40px; 
            }

            footer { 
                bottom: -40px; 
            }

            #footer_content {
                position: absolute;
                width: 500px;
                height: 20px;
                z-index: 15;
                top: 50%;
                left: 50%;
                margin: -10px 0 0 -250px;
                text-align: center;
            }

            table {
                width: 100%;
            }

            #wrapper td {
                text-align: center;
            }

            .top, .top th, .top td,
            .middle, .middle th, .middle td,
            .bot, .bot th, .bot td,
            {
                border-collapse: collapse !important;
                border: 1px solid grey;
            }

            .top thead th {
                font-size: 20px; font-weight: bold;
            }

            .top td {
                padding-top: 6px! important;
                padding-bottom: 6px! important;
            }

            th
            {
                background-color: lightgray;
                padding-left: 10px; 
                padding-top: 6px !important;
                padding-bottom: 6px !important;
            }

            td {
                padding-left: 10px! important;
            }

            .detail td {
                min-height: 38px;
                height: 38px;
                padding-bottom: 4px !important;
            }

            .responsive {
                height: 220px;
                width: auto;
            } 

        </style>
    </head>
    <body>
        <header>
            {{ $issue['project'] }}, {{ $issue['client_name'] }}
        </header>
        <footer>
            <div id="footer_content">
                {{ $issue['project'] }} Issue ID - {{ $issue['id'] }}
            </div>
        </footer>
        <div id="content">
            @if ($issue['client_logo'])
            <div class="row" style="padding-bottom: 10px !important">
                <table id="wrapper">
                    <tr>
                        <td>
                            <img style="height: 60px;" src="{{ $issue['client_logo'] }}" alt="" />
                        </td>
                    </tr>
                </table>
            </div>    
            @endif
            
            <div class="row">
                <table class="top">
                    <thead>
                        <tr>
                            <th colspan="4"> {{ $issue['project'] }} Issue ID - {{ $issue['id'] }} </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 20%"> Block </td>
                            <td style="width: 30%"> {{ $issue['block'] }} </td>
                            <td style="width: 20%"> Level </td>
                            <td style="width: 30%"> {{ $issue['level'] }} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"> Unit </td>
                            <td style="width: 30%"> {{ $issue['unit'] }} </td>
                            <td style="width: 20%"> Status </td>
                            <td style="width: 30%"> {{ $issue['status'] }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row" style="padding-top: 10px !important">
                <table class="middle">
                    <thead>
                        <tr>
                            <th colspan="3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="detail">
                            <td valign="top" style="width: 20%;"> {{ $issue['detail'][0]['key'] }} </td>
                            <td valign="top" style="width: 30%;"> {{ $issue['detail'][0]['value'] }} </td>
                            <td valign="top" style="width: 50%;" rowspan="{{ count($issue['detail']) }}"> 
                                <div class="row" style="position: relative; margin: 20px !important">
                                    <img src="{{ $issue['drawing_plan_image'] }}" style="height: auto; width: {{ $ratio_width }}px;">
                                    @php
                                        $position_x = ($issue['position_x'] / $ratio) - 13 ;
                                        $position_y = ($issue['position_y'] / $ratio) - 26;
    
                                        $issue_icon = "";
                                        if($issue['status_id'] == 2)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_orange.png');
                                        else if($issue['status_id'] == 3)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_brown.png');
                                        else if($issue['status_id'] == 4)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_biege.png');
                                        else if($issue['status_id'] == 5)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_yellow.png');
                                        else if($issue['status_id'] == 6)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_lightblue.png');
                                        else if($issue['status_id'] == 7)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_grey.png');
                                        else if($issue['status_id'] == 8)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_purple.png');
                                        else if($issue['status_id'] == 9)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_pink.png');
                                        else if($issue['status_id'] == 10)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_green.png');
                                        else if($issue['status_id'] == 1)
                                            $issue_icon = URL::asset('/assets/images/icon/pin_marker_blue.png');
    
                                    @endphp
                                    <img src="{{ $issue_icon }}" style="cursor: grab; position: absolute; width: 26px; height: 26px; top: {{ $position_y }}px; left: {{ $position_x }}px;">
                                </div>
                            </td>
                        </tr>
                        
                        @for ($i = 1; $i < count($issue['detail']); $i++)
                        <tr class="detail">
                            <td valign="top"> {{ $issue['detail'][$i]['key'] }} </td>
                            <td valign="top"> {{ $issue['detail'][$i]['value'] }} </td>
                        </tr>
                        @endfor
                        <tr>
                            <th colspan="3"> Remarks </th>
                        </tr>
                        <tr>
                            <td colspan="3" valign="top" style="padding-top: 4px !important; padding-bottom: 4px !important"> {{ $issue['remarks'] }} </td>
                        </tr>   
                    </tbody>
                </table>
            </div>
            
            @if (count($issue['photo']))
            <div class="row" style="padding-top: 10px !important">
                <table class="bot">
                    <tbody>
                        <tr>
                            <th colspan="2"> Photos </th>
                        </tr>
                        <tr>
                        @foreach ($issue['photo'] as $photo)
                            <td style="width: 50%; height: 100px" align="center">  
                                <div><img class="responsive" src="{{ $photo['image'] }}"/></div>
                                <div> Photo {{ $photo['created_at'] }} </div>
                            </td>    
                        @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <script type="text/php">
            if ( isset($pdf) ) {
                $font = $fontMetrics->getFont("helvetica", "normal");
                $pdf->page_text(516, 815, "Page {PAGE_NUM} / {PAGE_COUNT}", $font, 9.5, array(120/255,120/255,120/255));
            }
        </script>
    </body>
</html>