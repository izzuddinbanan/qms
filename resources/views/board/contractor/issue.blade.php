<div class="row">
    <div class="col-xs-6 col-md-6 dashboard-heading">
        <span class="dashboard-project-name"> {{ $project->name }} </span> <br>
        <span class="dashboard-title" style="font-size: 30px;"> {{ $contractor->display_name }} </span>
    </div>     
    <div class="col-xs-12 col-md-6 dashboard-pagination">
        <button class="btn bg-theme btn-sm dropdown-toggle pull-right" type="button" data-toggle="dropdown"> Export <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">
            <li><a onclick="exportListing('excel')"><i class="fa fa-file-excel-o"></i> Export Excel</a></li>
            <li><a onclick="exportListing('pdf')"><i class="fa fa-file-pdf-o"></i> Export PDF</a></li>
        </ul>
    </div>
</div>  
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table">
                <thead class="dashboard-table-heading"> 
                    <tr>
                        <th style="width: 5%"> Block </th>
                        <th style="width: 5%"> Level </th>
                        <th style="width: 5%"> Unit </th>            
                        <th style="width: 10%"> Location </th>
                        <th style="width: 5%"> Type </th>
                        <th style="width: 10%"> Description </th>
                        <th style="width: 5%"> Status </th>
                        <th style="width: 10%"> Target/Compl. Date </th>
                        {{-- <th style="width: 8%"> Nb. Days WIP </th> --}}
                    </tr>
                </thead>
                <tbody> 
                    @forelse ($issues as $key => $val)
                    <tr style="cursor:pointer" data-url="{{ route('issue.show', [$val->id]) }}">
                        <td> {{ $val->block ? $val->block : 'N/A'}} </td>
                        <td> {{ $val->level ? $val->level : 'N/A'}} </td>
                        <td> {{ $val->unit ? $val->unit : 'N/A'}} </td>
                        <td> {{ $val->location }} </td>
                        <td> {{ $val->category }} </td>
                        <td> {{ $val->type . ' - ' . $val->issue }} </td>
                        <td> {{ $val->status }} </td>
                        <td> {{ $val->target_completion_date }} </td>
                        {{-- <td> 123 </td> --}}
                    </tr>    
                    @empty
                    <tr data-url="javascript:void();">
                        <td colspan="7" align="center"><i>There are no results found.</i></td>
                    </tr>
                    @endforelse 
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 dashboard-pagination">
        <div align="middle">{!! $issues->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>