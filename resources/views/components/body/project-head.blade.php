<div class="panel-heading">
    <div class="row">
        <div class="col-md-6 col-xs-6">
            <h5 class="panel-title">
                <i class="{{ $head_icon }}"></i> {{ $head_name }}
            </h5>
        </div>

        <div class="col-md-6 col-xs-6 text-right">
            @isset($route_back)
                <a href="{{ $route_back }}">
                    <button class="btn btn-primary btn-sm"><i class="fa fa-backward"></i> Back</button>
                </a>
            @endisset

            @isset($route_add)
                <a href="{{ $route_add }}">
                    <button class="btn btn-primary btn-sm"><i class="icon-add"></i></button>
                </a>
            @endisset

            @isset($route_modal)
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="{{ $route_modal }}">
                    <i class="icon-add"></i>
                </button>
            @endisset

            @isset($route_next)
                <a href="{{ $route_next }}">
                    <button class="btn btn-primary btn-sm">Next <i class="fa fa-forward"></i></button>
                </a>
            @endisset
        </div>
    </div>
    <center>
        
        <hr style="margin: 2px;width: 60%;">
    </center>

@isset($help_msg)

    <ol>
        @foreach($help_msg as $msg)
        <li>{!! $msg !!}</li>
        @endforeach
    </ol>
@endisset
</div>