<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{ url('template-limitless/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ url('template-limitless/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ url('template-limitless/css/core.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ url('template-limitless/css/components.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ url('template-limitless/css/colors.css') }}" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/loaders/pace.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/core/libraries/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/core/libraries/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/loaders/blockui.min.js') }}"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/visualization/d3/d3.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/styling/switchery.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/ui/moment/moment.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/pickers/daterangepicker.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/plugins/ui/nicescroll.min.js') }}"></script>

	<script type="text/javascript" src="{{ url('template-limitless/js/core/app.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/pages/dashboard.js') }}"></script>
	<script type="text/javascript" src="{{ url('template-limitless/js/pages/layout_fixed_custom.js') }}"></script>
	<!-- /theme JS files -->

</head>

<body class="navbar-top">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.html"><img src="{{ url('template-limitless/images/logo_light.png') }}" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				


				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{ url('template-limitless/images/placeholder.jpg') }}" alt="">
						<span>Victoria</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="#"><i class="icon-user-plus"></i> My profile</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="icon-switch2"></i> Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main sidebar-fixed">
				<div class="sidebar-content">

					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li><a href="index.html"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
								<li>
									<a href="#"><i class="icon-versions"></i> <span>Responsive options</span></a>
									<ul>
										<li  class="active"><a href="table_responsive.html">Responsive basic tables</a></li>
										<li><a href="datatable_responsive.html">Responsive data tables</a></li>
									</ul>
								</li>
								<!-- /tables -->
								<!-- /page kits -->

							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

			

				<!-- Content area -->
				<div class="content">


					<!-- Footer -->
					<div class="footer text-muted" style="bottom: 0">
						<div class="row">
							<div class="col-md-12">
								&copy; 2019. Quality Manage System (QMS)
							</div>
						</div>
					</div>
					<!-- /footer -->

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
