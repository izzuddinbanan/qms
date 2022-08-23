<div class="row"> 
    <form method="GET" role="search" onsubmit="searchForDocument()">
        <div class="form-group pull-right" >
            <div class="col-md-12">
                <div class="input-group input-group-xlg">
                    <span class="input-group-addon"><i class="icon-search4"></i></span>
                    <input type="text" id="form_string" value="" class="form-control" placeholder="@lang('general.searchPlaceHolder')..." autocomplete="off">
                </div>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table text-nowrap">
        <thead>
            <tr>
                <th>Reference No</th>
                <th class="col-md-3">Submitted By</th>
                <th class="col-md-2">Status</th>
                <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
            </tr>
        </thead>
        <tbody id="issue_documents_panel">
        @forelse ($formSubmissions as $record)
            <tr>
                <td>
                    <div class="media-left">
                        <div class=""><span class="text-default text-semibold">{{ $record->reference_no }}</span></div>
                    </div>
                </td>
                <td>
                    <div class="media-left">
                        <div class=""><span class="text-default text-semibold">{{ $record->user->name }}</span></div>
                        <div class="text-muted text-size-small">{{ $record->created_at->format('d M Y, h:i a') }}</div> 
                    </div>
                </td>
                <td>
                    <span class="label {{ $record->status->name == "Accept" ? 'bg-success' : ($record->status->name == "" ? 'bg-danger' : 'bg-blue') }}">{{ $record->status->name }}</span>
                </td>
                <td class="text-center" align="center">
                    <a data-popup="tooltip" data-placement="top" data-original-title="Edit">
                    <i class="fa fa-edit largeIcon" onclick="showFormSubmission({{ $record->id }});"></i></a>
                </td> 
            </tr>
        @empty
        <tr>
            <td colspan="6" align="center"><i>No Results Found.</i></td>
        </tr>
        @endforelse   
        </tbody>
    </table>
    {{-- Showing <b>{{ $formSubmissions->from }}</b> to <b>{{ $formSubmissions->to }}</b> of  <b>{{ $formSubmissions->total }}</b> entries --}}
</div>
<div class="row" align="center">
    {{-- {!! $data->render("pagination::bootstrap-4") !!} --}}
</div>