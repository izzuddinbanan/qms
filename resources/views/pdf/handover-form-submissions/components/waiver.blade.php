@if($form->waiver_submission)
<div class="breakNow"></div>
    
<div class="row">
    <div class="col-md-12">
        <table>
            <tr><td>{!! $waiver->description !!}</td></tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table>
            <tr>
                <td>
                    <img src="{{ $form->waiver_submission['signature'] ? url('uploads/signatures/' . $form->waiver_submission['signature']) : '' }}" class="img" style="width: 160px;">
                    <hr style="width: 180px; padding: 0px;margin: 0px;" align="right">
                    <p>Name : {{ $form->waiver_submission['name'] ?? '' }}</p>
                    <p>Date : {{ $form->waiver_submission['created_at'] ?? '' }}</p>
                </td>
            </tr>
        </table>
        
    </div>
</div>
@endif