<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title> {{ $unit_detail->project }} </title>

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
                font-size: 12px;
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
                font: 12px !important;
            }

            #wrapper td {
                text-align: center;
            }
            
            .summary, .summary th, .summary td,
            .detail, .detail th, .detail td,
            {
                border-collapse: collapse !important;
                border: 1px solid grey;
            }

            thead tr th {
                background-color: lightgray;
            }

            td {
                padding-left: 3px! important;
            }

            .detail td {
                min-height: 24px;
                height: 28px;
            }

        </style>
    </head>
    <body>
        <header>
            {{ $unit_detail->project }}, {{ $unit_detail->client }}
        </header>
        <footer>
            <div id="footer_content">
                {{ $unit_detail->project }} Block <span style="text-transform: uppercase;">{{ $unit_detail->block }}</span> Level <span style="text-transform: uppercase;">{{ $unit_detail->level }}</span> Unit <span style="text-transform: uppercase;">{{ $unit_detail->unit }}</span>
            </div>
        </footer>
        <div id="content">
            @if ($unit_detail->logo)
            <div class="row" style="padding-bottom: 10px !important">
                <table id="wrapper">
                    <tr>
                        <td>
                            <img style="height: 60px;" src="{{ $unit_detail->logo }}" alt="" />
                        </td>
                    </tr>
                </table>
            </div>    
            @endif
            <div class="row" style="background-color: lightgray; border: 1px solid gray">
                <div style="padding-top: 4px !important; padding-bottom: 4px !important; padding-left: 10px !important;">
                    <div style="font-weight: bolder; font-size: 20px"> {{ $unit_detail->project }} </div>
                    <div style="font-weight: bolder; font-size: 16px"> Block:  <span style="text-transform: uppercase;"> {{ $unit_detail->block }} </span>  Level: <span style="text-transform: uppercase;"> {{ $unit_detail->level }} </span> Unit: <span style="text-transform: uppercase;"> {{ $unit_detail->unit }} </span> </div>
                </div>
            </div>

            <div class="row" align="center" style="padding-top: 12px !important; padding-bottom: 12px !important; font-size: 18px; color: gray">
                <span> ISSUES SUMMARY </span>
            </div>

            <div> 
                <table class="summary">
                    <thead>
                        <tr>
                            <th align="center" style="width: 5%"> S/N </th>
                            <th align="center" style="width: 20%"> Reference </th>
                            <th align="center" style="width: 10%"> Location </th>
                            <th align="center" style="width: 15%"> Type </th>
                            <th align="center"> Description </th>
                            <th align="center" style="width: 10%"> Status </th>
                            <th align="center" style="width: 10%"> Lodged Date </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issues_detail as $key => $issue)
                        <tr>
                            <td> {{ $key + 1 }} </td>
                            <td> {{ $issue['detail']['reference'] }} </td>
                            <td> {{ $issue['detail']['location'] }} </td>
                            <td> {{ $issue['detail']['type'] }} </td>
                            <td> {{ $issue['detail']['description'] }} </td>
                            <td> {{ $issue['detail']['status'] }} </td>
                            <td> {{ $issue['creation_date'] }} </td>
                        </tr>    
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
            
            @if ($unit_detail->drawing_plan_image)
            <div class="row" style="padding-left: 100px !important; padding-right: 100px !important; page-break-before: always;">
                <img style="width: {{ $ratio_width }}px; height: auto" src="{{ $unit_detail->drawing_plan_image }}" />
                @foreach ($issues_detail as $issue)
                    @php
                        $position_x = ($issue['position_x'] / $ratio) + 100 - 15 ;
                        $position_y = ($issue['position_y'] / $ratio) - 0 - 30;

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
                        <img src="{{ $issue_icon }}" style="cursor: grab; position: absolute; width: 30px; height: 30px; top: {{ $position_y }}px; left: {{ $position_x }}px;">
                @endforeach
            </div>
            @endif

            <div class="row" align="center" style="font-size: 18px; color: gray; page-break-before: always;">
                ISSUES
            </div>

            @foreach ($issues_detail as $key => $detail)
            <div class="row" style="page-break-inside: avoid; margin-top:10px !important">
                <table class="detail" style="width: 703px"> 
                    <thead>
                        <tr style="font: 14px bolder !important;">
                            <th colspan="3" style="border-right: 0"> 
                                {{ $key + 1 . ') Reference No: ' . $detail['detail']['reference'] }}
                            </th>
                            <th colspan="1" style="border-left: 0; "> 
                                <span style="float: right; margin-right: 10px">{{ 'Status: ' . $detail['detail']['status'] }}</span>
                            </th>
                        </tr>
                    </thead>                
                    <tbody>
                        {{ $index = 0 }}
                        @foreach ($detail['detail'] as $skey => $val)
                            @if ($skey != 'reference' && $skey != 'status')    
                            <tr>
                                <td style="width: 15%; text-transform: capitalize;">{{ implode(' ', explode('_', $skey)) }}</td>
                                <td style="width: 35%">{{ $val }}</td>

                                @if ($index == 0)
                                    @forelse ($detail['photo'] as $photo)
                                        <td align="center" valign="middle" colspan="{{ count($detail['photo']) < 2 ? 2 : 1 }}" rowspan="{{ count($detail['detail']) - 2 }}">
                                            <div style="padding-top: 5px!important"><img style="width: 120px; height: auto" src="{{ $photo['image'] }}" /></div>
                                            <div> Photo {{ $photo['created_at'] }} </div>
                                        </td>   
                                    @empty
                                        <td valign="top" colspan="2" rowspan="{{ count($detail['detail']) - 2 }}"></td>  
                                    @endforelse
                                    
                                    {{ $index = 1 }}
                                @endif
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <thead>
                        <tr>
                            <th colspan="4"> Remarks </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4"> {{ $detail['remarks'] }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach
        </div> 

        <script type="text/php">
            if ( isset($pdf) ) {
                $font = $fontMetrics->getFont("helvetica", "normal");
                $pdf->page_text(516, 815, "Page {PAGE_NUM} / {PAGE_COUNT}", $font, 9.5, array(120/255,120/255,120/255));
            }
        </script>
    </body>
</html>