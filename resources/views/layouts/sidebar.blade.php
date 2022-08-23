<div class="sidebar sidebar-main sidebar-default">
               
    <div class="sidebar-content">

        <!-- SIDEBAR MENU -->
        <div class="sidebar-category sidebar-category-visible">

            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">

                    <!-- MENU FOR ADMIN | CLIENT -->
                    @include('layouts.sidebar-user-type.admin')
                    <!-- MENU FOR ADMIN | CLIENT -->
                    

                    <!-- MENU FOR SUPER USER -->
                    @include('layouts.sidebar-user-type.super-user')
                    <!-- MENU FOR SUPER USER -->
                    

                </ul>
            </div>
        </div>
        <!-- /SIDEBAR MENU -->
    </div>
</div>