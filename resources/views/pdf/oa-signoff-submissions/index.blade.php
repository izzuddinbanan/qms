<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>Title</title>

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

        </style>
    </head>
    <body>

        <div class="row">
            <div class="text-center">
                <h4><b>Owner Acceptance Sign Off</b></h4>
            </div>
        </div>

        <div class="row">
            <table style="width: 100%">
                <tr>
                    <td width="10%">Phase</td>
                    <td>: {{ $unit->phase }}</td>
                </tr>
                <tr>
                    <td>Block</td>
                    <td>: {{ $unit->block }}</td>
                </tr>
                <tr>
                    <td>Level</td>
                    <td>: {{ $unit->level }}</td>
                </tr>
                <tr>
                    <td>Unit</td>
                    <td>: {{ $unit->unit }}</td>
                </tr>
            </table>
        </div>

        <div class="row" style="margin-top: 10px">
            <table style="width: 100%">
                <tr style="background-color: white">
                    <th  style="background-color: white"><u>Owner Name</u></th>
                    <th style="background-color: white"><u>NRIC/Passport No</u></th>
                    <th style="background-color: white"><u>Mobile No</u></th>
                    <th style="background-color: white"><u>Office No</u></th>
                </tr>
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->ic_no }}</td>
                    <td>{{ $user->phone_no }}</td>
                    <td>{{ $user->office_no }}</td>
                </tr>
                <tr>
                    <th colspan="4" style="background-color: white"><u>CORRESPONDENCE ADDRESS</u></th>
                </tr>
                <tr>
                    <td>{{ $user->mailing_address }}</td>
                </tr>
            </table>
        </div>

        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <h6><b>Issue Listing</b></h6>
                <table style="width: 100%">
                    <tr style="background-color: white">
                        <th  style="background-color: white"><u>Reference Number</u></th>
                        <th style="background-color: white"><u>Status</u></th>
                    </tr>
                    @if(count($accept_issues)>0)
                    @foreach($accept_issues as $ai)
                    <tr>
                        <td>{{ $ai->reference }}</td>
                        <td><span style="color:green">Accept</span></td>
                    </tr>
                    @endforeach
                    @endif
                    
                    @if(count($redo_issues)>0)
                    @foreach($redo_issues as $ri)
                    <tr>
                        <td>{{ $ri->reference }}</td>
                        <td><span style="color:red">Redo</span></td>
                    </tr>
                    @endforeach
                    @endif
                </table>
            </div>    
        </div>

        @if($form->details)
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <h6><b>ACKNOWLEDGEMENT BY OWNER</b></h6>
            </div>
        </div>
        <div class="row" style="margin-top: 40px;">
            <table>
                <tr>
                    <td>
                        <p>Owner</p>
                        <br>
                        <img src="{{ $form->details[0]['signature_owner'] ? url('uploads/signatures/' . $form->details[0]['signature_owner']) : '' }}" class="img" style="width: 160px;">
                        <hr style="width: 180px; padding: 0px;margin: 0px;" align="right">
                        <br>
                        <p>Name : {{ $form->details[0]['signature_owner_name'] ?? '' }} </p>
                        <p>Date : {{ $form->details[0]['signature_owner_datetime'] ?? '' }} </p>
                        
                    </td>
                    <td style="width: 30%">
                        <p>Handler</p>
                        <br>
                        <img src="{{ $form->details[0]['signature_handler'] ? url('uploads/signatures/' . $form->details[0]['signature_handler']) : '' }}" class="img" style="width: 160px;">
                        <hr style="width: 180px; padding: 0px;margin: 0px;" align="right">
                        <br>
                        <p>Name : {{ $form->details[0]['signature_handler_name'] ?? '' }} </p>
                        <p>Date : {{ $form->details[0]['signature_handler_datetime'] ?? '' }} </p>
                        
                    </td>
                </tr>
            </table>
        </div>
       
        @endif
       
    </body>
</html>