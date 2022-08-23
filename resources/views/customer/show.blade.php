@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            @include('components.body.panel-head', [
                'title'     => trans('main.customer'),
                'desc'      => trans('main.view'),
                'icon'      => 'icon-users',
            ])

            <div class="panel-body">
                <table class="table table-xxs">
                    <tr style="font-size: 14px">
                        <td style="font-weight: bold">@lang('main.name'):<br><br>@lang('main.email'):<br><br>@lang('contact'):</td>
                        <td> {{ $customer->name }} <br><br> {{$customer->email}} <br><br> {{$customer->contact}}</td>
                    </tr>   
                </table>


            </div>
        </div>
    </div>

    <div class="content">
        <div class="panel panel-flat" >
            <div class="panel-body">
                <div class="panel-body table-body no-padding" style="margin-top: 20px!important;">
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var current_page = 1;
        getListing();

        function gotoPage(page) {
            current_page = page;
            getListing();
        }

        function prevPage() {
            current_page--;
            getListing();
        }

        function nextPage() {
            current_page++;
            getListing();
        }

        function getListing() {
            $(".loader").show();

            var url = "{{ route('customer.getListing') }}";
            
            data = { 
                'customer_id' : {!! json_encode($customer->id) !!},
                'project_id' : {!! $project_id !!},
            };

            $.ajax({
                url: url + "?page=" + current_page,
                type:"GET",
                data: data,
                success:function(response) {
                    console.log(response)
                    $(".table-body").html("").html(response);
                    $('table.table tbody tr').click(function () {
                        window.location.href = $(this).data('url');
                    });

                    $(".loader").hide();
                }
            });
        }
    </script>


    
@endsection