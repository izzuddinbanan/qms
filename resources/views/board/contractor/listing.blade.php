<div class="row">
    <div class="col-xs-6 col-md-6 dashboard-heading">
        <span class="dashboard-project-name"> {{ $project->name }} </span> <br>
        <span class="dashboard-title" style="font-size: 30px;"> Contractors </span>
    </div>
    
    <div class="col-xs-6 col-md-6 dashboard-heading">
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
                            <th style="width:20%">Contractor Name</th>
                            <th style="width:12%">New Issues</th>
                            <th style="width:12%">Pending Start Issues</th>
                            <th style="width:12%">WIP Issues</th>
                            <th style="width:12%">Overdue Issues</th>
                            <th style="width:12%">Completed Issues</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contractors as $contractor)
                        <tr style="cursor:pointer" data-url="{{ route('contractors.show', [$contractor->id]) }}">                    
                            <td>{{ ($contractor->contractor_name ? $contractor->contractor_name : 'N/A') }}</td>
                            <td>{{ ($contractor->new_issues ? $contractor->new_issues : 0) }}</td>
                            <td>{{ ($contractor->pending_start_issues ? $contractor->pending_start_issues : 0) }}</td>
                            <td>{{ ($contractor->wip_issues ? $contractor->wip_issues : 0) }}</td>
                            <td>{{ ($contractor->overdue_issues ? $contractor->overdue_issues : 0) }}</td>
                            <td>{{ ($contractor->completed_issues ? $contractor->completed_issues : 0) }}</td>                            
                        </tr>
                        @empty
                        <tr data-url="javascript:void();">
                            <td colspan="5" class="center-inTable"><i>There are no results found.</i></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>     
        </div>
    </div>
    <div class="col-xs-12 col-md-12 dashboard-pagination" style="margin-top: 50px!important">
        <div align="center">{!! $contractors->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>