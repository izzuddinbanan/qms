@if($form->acceptance_submission)
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <table>
                <tr>
                    <td><h6><b>ACKNOWLEDGEMENT BY OWNER</b></h6></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <table>
                <tr>
                    <td>
                        @if($acceptance)
                            {!! $acceptance->termsConditions !!}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
            
    </div>
    <div class="row">
        <div class="col-md-12">
            <table>
                <tr>
                    <td>
                        <h6>Remarks : </h6>{{ $form->acceptance_submission["remarks"] }} 
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <table>
                <tr>
                    <td>
                        <p>Received By</p>
                        <img src="{{ $form->acceptance_submission['received_by_signature'] ? url('uploads/signatures/' . $form->acceptance_submission['received_by_signature']) : '' }}" class="img" style="width: 160px;">
                        <hr style="width: 180px; padding: 0px;margin: 0px;" align="right">
                        <br>
                        <p>Name : {{ $form->acceptance_submission['received_by_name'] ?? '' }} </p>
                        <p>IC/Passport No : {{ $form->acceptance_submission['received_by_ic_passport'] ?? '' }} </p>
                        <p>Date : {{ $form->acceptance_submission['received_by_datetime'] ?? '' }} </p>
                        
                    </td>
                    <td style="width: 30%">
                        <p>Attended By</p>
                        <img src="{{ $form->acceptance_submission['attended_by_signature'] ? url('uploads/signatures/' . $form->acceptance_submission['attended_by_signature']) : '' }}" class="img" style="width: 160px;">
                        <hr style="width: 180px; padding: 0px;margin: 0px;" align="right">
                        <br>
                        <p>Name : {{ $form->acceptance_submission['attended_by_name'] ?? '' }} </p>
                        <p>Designation : {{ $form->acceptance_submission['attended_by_designation'] ?? '' }} </p>
                        <p>Date : {{ $form->acceptance_submission['attended_by_datetime'] ?? '' }} </p>
                        
                    </td>
                </tr>
            </table>
        </div>
    </div>
       
@endif