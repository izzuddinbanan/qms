@extends('components.template-limitless.main')

@section('main')
<style type="text/css">
    .center-inTable{
        text-align: center;
    }
</style>
        
    <div class="content row">
        <div class="col-md-12 col-xs-12">
            <form id="form_export_listing" action="{{ route('contractors.general.export') }}" target="_blank" style="display: none;"></form>     
            <div class="panel panel-flat">
                <div id="issue_tab"> 
                    <ul class="nav nav-tabs bg-theme">
                        @foreach ($tabs as $key => $val)
                        <li id="{{ $val }}"><a onclick="applyFilter({ 'active': '{{ $val }}' })">{{ $key }} <span class="badge bg-slate position-right"></span></a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="panel-body" style="padding-left: 0px!important; padding-right: 0px!important; padding-top: 0px !important">
                    
                </div>
            </div>
        </div>
    </div>

    
    <script type="text/javascript">

        var query_filter = { 
            'active' : 'all'
        };

        var current_page = 1;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            }
        });

        $(document).ready(function() {
            $('.select').select2();

            $('body').addClass('sidebar-xs');

            applyFilter(query_filter);
            getCount();
        });

        function exportListing() {
            $("#form_export_listing").html("");
            
            for (var i in query_filter) {
                $("#form_export_listing").append("<input name=" + i + " value= " + query_filter[i] + " />");
            }

            setTimeout(() => {
                $("#form_export_listing").submit();
            }, 1000);
        }

        function applyFilter(request) {
            $("#issue_tab ul li").removeClass('active');
            $("#" + request.active).addClass('active');
            
            query_filter.active = request.active;
            getIssuesListing();
        }

        function getCount() {
            $.ajax({
                url:"{{ route('contractors.getCount') }}",
                type:"GET",
                data: query_filter,
                success:function(response) {
                    for (var i in response) {
                        $("#" + i + " span").text(response[i]);
                    }
                }
            });
        }

        function gotoPage(page) {
            current_page = page;
            getIssuesListing();
        }

        function prevPage() {
            current_page--;
            getIssuesListing();
        }

        function nextPage() {
            current_page++;
            getIssuesListing();
        }

        function getIssuesListing() {
            $(".loader").show();

            $.ajax({
                url:"{{ route('contractors.getListing') }}" + "?page=" + current_page,
                type:"GET",
                data: query_filter,
                success:function(response) {
                    $(".panel-body").html("").html(response);
                    $('table.table tr').click(function () {
                        window.location.href = $(this).data('url');
                    });

                    $(".loader").hide();
                }
            });
        }
    </script>

@endsection
