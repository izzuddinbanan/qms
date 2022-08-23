<!doctype html>
@php
    $block_name = $unit->block ? $unit->block .'-' : ''; 
    $level_name = $unit->level ? $unit->level .'-' : ''; 
    $unit_name = $block_name .''. $level_name .''. ($unit->unit ? $unit->unit : '') .' - '. $project->name; 
@endphp
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>{{ $unit_name }}</title>

        @include('pdf.styles.bootstrap')
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

            div.breakNow { page-break-inside:avoid; page-break-after:always; }


            .strong {
              font-weight: bold;
            }

            .whiteBc {
                background-color: white
            }

        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-12 text-center">
                <h4><b>ACCEPTANCE OF TAKEOVER RECORD</b></h4>
            </div>
        </div>

        <!-- UNIT INFO -->

        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <table>
                    <tr>
                        <td class="strong" width="20%">Phase</td>
                        <td>: {{ $unit->phase ? $unit->phase .' - ' : '' }} {{ $project->name }}</td>
                    </tr>
                    <tr>
                        <td class="strong">Block</td>
                        <td>: {{ $unit->block ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="strong">Level</td>
                        <td>: {{ $unit->level ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="strong">Unit</td>
                        <td>: {{ $unit->unit ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <table style="margin-top: 10px;">
                    <tr>
                        <th class="whiteBc"><u>Purchaser Name</u></th>
                        <th class="whiteBc"><u>NRIC/Passport No</u></th>
                        <th class="whiteBc"><u>Mobile No</u></th>
                        <th class="whiteBc"><u>Office No</u></th>
                    </tr>
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->ic_no }}</td>
                        <td>{{ $user->phone_no }}</td>
                        <td>{{ $user->office_no }}</td>
                    </tr>
                </table>
                <table style="margin-top: 10px;">
                    <tr>
                        <th colspan="4" class="whiteBc">
                            <u>CORRESPONDENCE ADDRESS</u>
                            <br>
                            {{ $user->mailing_address }}
                        </th>
                    </tr>
                </table>

                <table style="margin-top: 10px;">
                    <tr>
                        <th colspan="2" class="whiteBc"><u>HANDING OVER DETAILS</u></th>
                    </tr>
                    <tr>
                        <td class="strong" width="20%">SPA Date</td>
                        <td>: {{ $unit->spa_date }}</td>
                    </tr>
                    <tr>
                        <td class="strong">Vacant Possession Date</td>
                        <td>: {{ $unit->vp_date }}</td>
                    </tr>
                    <tr>
                        <td class="strong">Handover Date</td>
                        <td>: {{ $unit->dlp_expiry_date }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @include('pdf.handover-form-submissions.components.acceptance')

        @include('pdf.handover-form-submissions.components.key')

        @include('pdf.handover-form-submissions.components.es')

        @include('pdf.handover-form-submissions.components.waiver')

        @if($form->photo_submission)
        <div class="breakNow"></div>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>
                            PHOTO ATTACHMENT
                        </th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                
            </div>
            <div class="col-md-12 text-center">
                @foreach($form->photo_submission as $photo)
                    <img src="{{ $photo['url'] }}" class="img img-responsive">
                @endforeach
            </div>
        </div>
        
        @endif

    </body>
</html>