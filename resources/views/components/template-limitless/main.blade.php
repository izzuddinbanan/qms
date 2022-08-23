<!DOCTYPE html>
<html lang="en">
<head>
	@include('components.template-limitless.header')

	@include('components.template-limitless.notify')
	@yield('script')
</head>

<body class="navbar-top {{ (Route::currentRouteName() == 'plan.index' ? 'sidebar-xs' : '') }}">

    <div class="loader" style="display: none;"></div>
	@include('components.template-limitless.navbar')

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">


			@include('components.template-limitless.sidebar')


			<!-- Main content -->
			<div class="content-wrapper">

			

				<!-- Content area -->
				<div class="content">

					@yield('main')


					@include('components.template-limitless.footer')

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->


</body>
</html>
