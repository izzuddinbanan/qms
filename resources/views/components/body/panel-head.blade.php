<div class="panel-heading">
    <h3 class="panel-title" style="text-transform: uppercase;">
        <i class="{{ $icon }}"></i> {{ $title }} 
        @isset($desc)
        	<text class="text-muted">({{ $desc }})</text>
        @endisset

        @isset($route)
	        <a href="{{ $route }}">
	            <button class="btn btn-primary pull-right" style="text-transform: uppercase;"><i class="fa fa-plus"></i> @lang('main.add_new')</button>
	        </a>
        @endisset

        @isset($back_route)
            <a href="{{ $back_route }}">
                <button class="btn btn-primary pull-right">@lang('main.back')</button>
            </a>
        @endisset
    </h3>
</div>