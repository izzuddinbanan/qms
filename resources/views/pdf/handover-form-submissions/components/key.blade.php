@if($form->key_submission)
<div class="breakNow"></div>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <th>
                    {{ $form->key_submission["name"] ?? '' }}
                </th>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

        <table class="table table-bordered table-xs">

            <tr>
                <th>Item</th>
                <th style="width: 10%" class="text-center">Quantity</th>
                <th style="width: 10%" class="text-center">Available</th>
                <th style="width: 20%">Comment</th>
            </tr>
            @foreach($form->key_submission["section"] as $section)
                @foreach($section["item"] as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '' }}</td>
                        <td class="text-center">{{ $item['quantity'] ?? '' }}</td>
                        <td class="text-center">{{ $item['value']  ?? '' }}</td>
                        <td>{{ $item['remarks'] ?? '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    </div>
</div>
@endif