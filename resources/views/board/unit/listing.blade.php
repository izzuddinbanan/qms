<div class="row">
    <div class="col-xs-12 col-md-6 dashboard-heading">
        <span class="dashboard-project-name"> {{ $project->name }} </span> <br>
        <span class="dashboard-title" style="font-size: 30px;"> {{ $type == 'unit' ? 'Units' : 'Common Areas' }} </span>
    </div>     

    <div class="col-xs-12 col-md-6 dashboard-heading">
        <button type="button" style="margin-right:20px" class="btn bg-theme btn-sm pull-right" onclick="exportListing()"> Export <i class="fa fa-arrow-circle-o-right"></i></button>
    </div>     
    
</div>   

<div class="row">
    <div class="col-xs-12 col-md-12">
        <div id="solid-rounded-justified-tab1">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="dashboard-table-heading">
                        <tr style="font-weight: bold">
                            <th style="width:5%">Block</th>
                            <th style="width:5%">Level</th>
                            <th style="width:5%">Unit</th>
                            <th style="width:10%">New Issue</th>
                            <th style="width:10%">WIP Issue</th>
                            <th style="width:10%">Completed Issue</th>
                            <th style="width:10%">Closed Issue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $listIssue)
                        <tr style="cursor:pointer" data-url="{{ $type == 'common' ? route('commonarea.show', [$listIssue->id]) : route('unit.show', [$listIssue->id]) }}">
                            <td>{{ ($listIssue->block ? $listIssue->block : 'N/A') }}</td>
                            <td>{{ ($listIssue->level ? $listIssue->level : 'N/A') }}</td>
                            <td>{{ ($listIssue->unit ? $listIssue->unit : 'N/A') }}</td>
                            <td>{{ ($listIssue->new_issues ? $listIssue->new_issues : 0) }}</td>
                            <td>{{ ($listIssue->wip_issues ? $listIssue->wip_issues : 0) }}</td>
                            <td>{{ ($listIssue->completed_issues ? $listIssue->completed_issues : 0) }}</td>
                            <td>{{ ($listIssue->closed_issues ? $listIssue->closed_issues : 0) }}</td>
                        </tr>
                        @empty
                        <tr data-url="javascript:void();">
                            <td colspan="10" class="center-inTable"><i>There are no results found.</i></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>     
        </div>
    </div>
    <div class="col-xs-12 col-md-12 dashboard-pagination">
        <div align="center">{!! $issues->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>