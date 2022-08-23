<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title> {{ $client_detail->project }} </title>

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
            {{ $client_detail->project }}, {{ $client_detail->client }}
        </header>
        <footer>
            <div id="footer_content">
                {{ $client_detail->project .  ' - ' . $contractor->display_name . ' (' . $contractor->abbreviation_name . ')' }} <span style="text-transform: uppercase;">
            </div>
        </footer>
        <div id="content">
            @if ($client_detail->logo)
            <div class="row" style="padding-bottom: 10px !important">
                <table id="wrapper">
                    <tr>
                        <td>
                            <img style="height: 60px;" src="{{ $client_detail->logo }}" alt="" />
                        </td>
                    </tr>
                </table>
            </div>    
            @endif
            <div class="row" style="background-color: lightgray; border: 1px solid gray">
                <div style="padding-top: 4px !important; padding-bottom: 4px !important; padding-left: 10px !important;">
                    <div style="font-weight: bolder; font-size: 20px"> {{ $client_detail->project . ' - ' . $contractor->display_name  . ' (' . $contractor->abbreviation_name . ')' }} </div>
                </div>
            </div>

            <div class="row" align="center" style="font-size: 18px; color: gray;">
                ISSUES
            </div>

            @foreach ($issues_detail as $key => $detail)
            <div class="row" style="padding-top: 10px !important; page-break-inside: avoid">
                <table class="detail"> 
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
                                <td style="width: 10%; text-transform: capitalize;">{{ implode(' ', explode('_', $skey)) }}</td>
                                <td style="width: 30%">{{ $val }}</td>

                                @if ($index == 0)
                                    @forelse ($detail['photo'] as $photo)
                                        <td align="center" valign="top" colspan="{{ count($detail['photo']) < 2 ? 2 : 1 }}" rowspan="{{ count($detail['detail']) }}">
                                            <div style="padding-top: 5px!important"><img style="width: 120px; height: auto" src="{{ $photo['image'] }}" /></div>
                                            <div> Photo {{ $photo['created_at'] }} </div>
                                        </td>   
                                    @empty
                                        <td valign="top" colspan="2" rowspan="{{ count($detail['detail']) }}"></td>  
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