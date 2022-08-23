<div class="row">
    <div class="col-xs-12 col-md-6 dashboard-heading">
        <span class="dashboard-project-name"> {{ $project->name }} </span> <br>
        <span class="dashboard-title" style="font-size: 30px;"> Issues </span>
    </div>     
    <div class="col-xs-12 col-md-6 dashboard-pagination">
            <button type="button" class="btn bg-theme btn-sm pull-right" onclick="exportListing()"> Export <i class="fa fa-arrow-circle-o-right"></i></button>
    </div>
</div>   

<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="dashboard-table-heading">
                    <tr>
                        <th>Block</th>
                        <th>Level</th>
                        <th>Unit</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Lodged Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $listIssue)
                    <tr style="cursor:pointer" data-url="{{ route('issue.show', [$listIssue->id]) }}">
                        <td>{{ ($listIssue->block ? $listIssue->block : 'N/A') }}</td>
                        <td>{{ ($listIssue->level ? $listIssue->level : 'N/A') }}</td>
                        <td>{{ ($listIssue->unit ? $listIssue->unit : 'N/A') }}</td>
                        <td>{{ ($listIssue->category ? $listIssue->category : 'N/A') }}</td>
                        <td>{{ ($listIssue->issue ? $listIssue->type . ' - ' . $listIssue->issue : 'N/A') }}</td>
                        <td>{{ ($listIssue->status ? $listIssue->status : 'N/A') }}</td>
                        <td>{{ ($listIssue->priority_id ? $listIssue->priority_type : 'N/A') }}</td>
                        <td>{{ ($listIssue->creation_date ? $listIssue->creation_date : 'N/A') }}</td>
                    </tr>
                    @empty
                    <tr data-url="javascript:void();">
                        <td colspan="8" class="center-inTable"><i>There are no results found.</i></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>     
    </div>     
    
    <div class="col-xs-12 col-md-12 dashboard-pagination" style="margin-top: 50px!important">
        <div align="center">{!! $issues->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>
