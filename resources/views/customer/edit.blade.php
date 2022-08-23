@extends('components.template-limitless.main')

@section('main')
    <div class="content">
        <div class="panel panel-flat" >
            @include('components.body.panel-head', [
                'title'     => trans('main.customer'),
                'desc'      => trans('main.update'),
                'icon'      => 'icon-users',
            ])

            <div class="panel-body">
                <div class="row">
                    
                    <form action="{{ route('customer.update', [$customer->id]) }}" method="POST" enctype="multipart/form-data" id="form-document">
                        @csrf
                        @method('put')

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.name') :</label>
                                <input type="text" class="form-control" name="name" value="{{$customer->name}}" autocomplete="off" autofocus="" required="" placeholder="name">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.email') :</label>
                                <input type="text" class="form-control" name="email" value="{{$customer->email}}" autocomplete="off" autofocus="" required="" placeholder="email">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-semibold">@lang('main.contact') :</label>
                                <input type="text" class="form-control" name="contact" value="{{$customer->contact}}" autocomplete="off" autofocus="" required="" placeholder="contact">
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        
                        @include('components.forms.basic-button', [
                            'route'     => route('customer.index'),
                            'div_size'  => 'col-md-6',
                            'position'  => 'right',
                            'function'  => 'check(event)',
                        ])

                    </form>
                </div>

            </div>
        </div>
    </div>
    



    
@endsection