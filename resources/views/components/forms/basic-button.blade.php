<div class="clearfix"></div>

<div class="{{ $div_size .' '. $position }}">
    <a href="{{ $route }}">
        <button type="button" class="btn btn-danger">
        	<i class=" icon-circle-left2"></i> @lang('main.cancel')
        </button>
    </a>

    <button type="submit" class="btn btn-primary" onclick="{{ isset($function) ? $function : '' }}">
    	@lang('main.submit') <i class="icon-circle-right2"></i>
    </button>
</div>