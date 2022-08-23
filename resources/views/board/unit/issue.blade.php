<div class="row">
    <div class="col-xs-12 col-md-6 dashboard-heading">
        <span class="dashboard-title" style="font-size: 30px;"> Issues </span>
    </div>     
    <div class="col-xs-12 col-md-6 dashboard-pagination">
        <button class="btn bg-theme dropdown-toggle pull-right" type="button" data-toggle="dropdown"> Export </i>
        <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right">
            <li><a onclick="exportListing('excel')"><i class="fa fa-file-excel-o"></i> Export Excel</a></li>
            <li><a onclick="exportListing('pdf')"><i class="fa fa-file-pdf-o"></i> Export PDF</a></li>
        </ul>
    </div>
</div>   

<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="dashboard-table-heading"> 
                    <tr>
                        <th> S/N </th>
                        <th> Reference </th>
                        <th> Location </th>
                        <th> Type </th>
                        <th> Description </th>
                        <th> Status </th>
                        <th> Lodged Date </th>
                    </tr>
                </thead>
                <tbody> 
                    @forelse ($issues as $key => $val)
                    <tr style="cursor:pointer" data-url="{{ route('issue.show', [$val->id]) }}">
                        <td> {{ ($issues->currentPage() * 10) - 10 + $key + 1 }} </td>
                        <td> {{ $val->reference }} </td>
                        <td> {{ $val->location }} </td>
                        <td> {{ $val->category }} </td>
                        <td> {{ $val->type . ' - ' . $val->issue }} </td>
                        <td> {{ $val->status }} </td>
                        <td> {{ $val->creation_date }} </td>
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
    <div class="col-xs-12 col-md-12 dashboard-pagination" style="margin-top: 10px!important">
        <div align="center">{!! $issues->render("pagination::ajax-pagination") !!}</div>
    </div>
</div>