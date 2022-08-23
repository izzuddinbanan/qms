<div class="row">
    <div class="col-xs-12 col-md-6 dashboard-heading">
        <!-- <span class="dashboard-project-name"> {{ $project->name }} </span> <br> -->
        <span class="dashboard-title" style="font-size: 30px;"> Unit </span>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listing as $ls)
                        <tr style="cursor:pointer" data-url="{{ route('unit.show', [$ls->id]) }}">
                            <td>{{ ($ls->block ? $ls->block : 'N/A') }}</td>
                            <td>{{ ($ls->level ? $ls->level : 'N/A') }}</td>
                            <td>{{ ($ls->unit ? $ls->unit : 'N/A') }}</td>
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
        <div align="center">{!! $listing->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>