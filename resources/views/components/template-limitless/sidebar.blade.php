<!--Main sidebar -->
<div class="sidebar sidebar-main sidebar-fixed">
	<div class="sidebar-content">

		<!-- Main navigation -->
		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding">
				<ul class="navigation navigation-main navigation-accordion">


					@include('components.template-limitless.sidebar-user-type.admin')
                    <!-- MENU FOR ADMIN | CLIENT -->
                    

                    <!-- MENU FOR SUPER USER -->
                    @include('components.template-limitless.sidebar-user-type.super-user')

				</ul>
			</div>
		</div>
		<!-- /main navigation -->

	</div>
</div>
<!-- /main sidebar