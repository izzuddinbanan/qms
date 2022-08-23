@if(role_user()->role_id == get_role('super_user')->id)

	<li class="{{ Request::is('/') ? 'active' : '' }}">
		<a href="{{ url('/') }}">
			<i class="icon-graph"></i> <span>@lang('sideMenu.dashboard')</span>
		</a>
	</li>



	<li  class="{{ Request::is('client*') ? 'active' : '' }}">
		<a href="{{ route('client.index') }}">
			<i class="icon-user-tie"></i> <span>@lang('sideMenu.client')</span>
		</a>
	</li>

	<li class="{{ Request::is('app-version*') ? 'active' : '' }}">
		<a href="{{ route('app-version.index') }}">
			<i class="icon-mobile"></i> <span>@lang('sideMenu.appVersion')</span>
		</a>
	</li>

	<hr>

	<li>
		<a href="{{ route('poster.index') }}" target="_blank">
			<i class="icon-circle-code"></i> <span>Poster</span>
		</a>
	</li>

	<li class="{{ Request::is('audit*') ? 'active' : '' }}">
		<a href="{{ route('audit.index') }}">
			<i class="icon-clipboard6"></i><span> @lang('sideMenu.audit_trails')</span>
		</a>
	</li>

	<li class="{{ Request::is('logviewer*') ? 'active' : '' }}">
		<a href="{{ route('log-viewer.index') }}">
			<i class="icon-clipboard6"></i><span> @lang('sideMenu.logViewer')</span>
		</a>
	</li>

@endif
