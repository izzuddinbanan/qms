@if($form->es_submission)
<div class="breakNow"></div>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <th>{{ $form->es_submission["name"] ?? '' }}</th>
            </tr>
        </table>


        <table>
            <tr>
                <td colspan="2"><h5><b>Details : </b></h5></td>
            </tr>
            <tr>
                <td style="width: 30%;">Car Park Bay No.</td>
                <td>: {{ $form->es_submission["unit_owner"]["car_park"] ?? '' }}</td>
            </tr>
            <tr>
                <td>Access Card(s)</td>
                <td>: {{ $form->es_submission["unit_owner"]["access_card"] ?? '' }}</td>
            </tr>
            <tr>
                <td>Key Fob(s)</td>
                <td>: {{ $form->es_submission["unit_owner"]["key_fob"] ?? '' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="row" style="margin-top: 30px;">
    <div class="col-md-12">
        <table>
            <tr><td><h5><b>Meter Reading : </b></h5></td></tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th style="width: 2%" class="text-center">No.</th>
                <th>Category</th>
                <th style="width: 30%%">Reading</th>
                <th style="width: 30%%" class="text-center">Date & Time</th>
            </tr>
            <tr>
                <td class="text-center">1.</td>
                <td>Electricity</td>
                <td>{{ $form->es_submission["meter_read"]["electricity"] ?? '' }}</td>
                <td class="text-center">{{ $form->es_submission["meter_read"]["date"] ? date('d M Y, h:i a', strtotime($form->es_submission["meter_read"]["date"]["date"])) : '' }}</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td>Water</td>
                <td>{{ $form->es_submission["meter_read"]["water"] ?? '' }}</td>
                <td class="text-center">{{ $form->es_submission["meter_read"]["date"] ? date('d M Y, h:i a', strtotime($form->es_submission["meter_read"]["date"]["date"])) : '' }}</td>
            </tr>
        </table>

    </div>
    <div class="col-md-12" style="margin-top: 15px;">
        <table class="table table-bordered">
            <tr>
                <th>Item</th>
                <th style="width: 10%" width="20">Quantity</th>
                <th style="width: 10%" width="20">Available</th>
                <th style="width: 20%" width="20">Comment</th>
            </tr>
            @foreach($form->es_submission["section"] as $section)
                @foreach($section["item"] as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '' }}</td>
                        <td>{{ $item['quantity'] ?? '' }}</td>
                        <td>{{ $item['value']  ?? '' }}</td>
                        <td>{{ $item['remarks'] ?? '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    </div>
</div>
@endif