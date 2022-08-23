@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-office"></i> List of Projects - {{ $role_user->clients->name }}
                    <a href="{{ route('project.create') }}">
                        <button class="btn btn-primary btn-sm pull-right"><i class="icon-add"></i> New project</button>
                    </a>
                </h5>
            </div>


            <div class="panel-body">
                <div class="row">
                    <form action="{{ request()->fullUrl() }}" method="GET" role="search" id="searchFOrm">
                        <div class="form-group pull-right" >
                            <div class="col-md-12">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon"><i class="icon-search4"></i></span>
                                    <input type="text" name="search" value="" class="form-control" placeholder="@lang('general.searchPlaceHolder')..." autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-framed">
                        <thead>
                            <tr class="">
                                <th class="indexNo">@sortablelink('created_at', '#')</th>
                                <th>Name</th>
                                <th>Contact No</th>
                                <th>Email Notification</th>
                                <th class="action"><i class="fa fa-gears"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($data->currentpage()-1)* $data->perpage(); @endphp
                                @forelse($data as $client)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td><a href="{{ route('project.step1', [$client->id]) }}">{{ $client->name }}</a></td>
                                <td></td>
                                <td></td>

                                @php
                                    if($client->logo != null){
                                        $logo = 'uploads/clients/'.$client->logo;
                                    }else{
                                    $logo = 'assets/images/no_image.png';
                                }
                                @endphp

                                <td align="center">
                                    <a href="{{ route('client.edit', [$client->id]) }}" data-popup="tooltip" title="Edit" data-placement="top">
                                        <i class="fa fa-edit largeIcon" ></i>
                                    </a>
                                    <a href="{{ route('client.delete', [$client->id]) }}" data-popup="tooltip" title="Delete" data-placement="top" onclick="confirmAlert({{ $client->id }})">
                                        <i class="fa fa-trash largeIcon"></i>
                                    </a>
                                </td>
                                @empty

                                <td colspan="6" align="center"> <i>No Records Found.<i></td>
                                
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" align="center">
                {!! $data->render("pagination::bootstrap-4") !!}
            </div>
            <br>
        </div>
    </div>

@endsection
