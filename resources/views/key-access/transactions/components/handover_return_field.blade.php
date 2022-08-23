<div id="handover_return_field" class="transcation_field" style="display: none;">
    <div class="row">
        <div class="col-md-9 col-md-offset-1">

            <div class="col-md-12">
                <label>List of Items:</label>
            </div>

            <!-- <div class="col-md-2"></div> -->
            <div class="col-md-12">
                <table class="table table-xxs">
                    <thead>
                        <tr>
                            <td class="" >Code</td>
                            <td class="" >Name</td>
                            <td class="" >Possessor</td>
                            <td class="" width="10%"></td>
                        </tr>`
                    </thead>
                    <tbody>
                        @foreach($ItemSubmitted as $item)
                            @if($item->possessor == 'handler')
                                <tr>
                                    <td class="" >{{ $item->code }}</td>
                                    <td class="" >{{ $item->name }}</td>
                                    <td class="" >{{ ucwords($item->possessor) }}</td>
                                    <td class="" width="10%">
                                            <input type="checkbox" name="item_handover[]" value="{{ $item->id }}">
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
