@if(role_user()->role_id != get_role('super_user')->id)
                        
        @if(session::has('project_id'))
            <li class="{{ Request::is('plan*') ? 'active' : '' }}"><a href="{{ route('plan.index') }}"><i  class="icon-map5"></i> <span>@lang('sideMenu.plan_viewer') </span></a></li>
            <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ route('home') }}"><i  class="icon-graph"></i> <span>@lang('sideMenu.dashboard')</span></a></li>
            <li class="{{ Request::is('property-unit*') ? 'active' : '' }}"><a href="{{ route('property-unit.index') }}"><i  class="icon-map5"></i> <span>Property Unit </span></a></li>
            <li>
                <a href="#"><i class="icon-file-text3"></i> <span>@lang('sideMenu.reports')</span></a>
                <ul>
                    <li class="{{ Request::is('issue*') ? 'active' : '' }}"><a href="{{ route('issue.index') }}"><i class="fa fa-wrench"></i> <span>@lang('sideMenu.reportIssue') </span></a></li>
                    <li class="{{ Request::is('unit*') ? 'active' : '' }}"><a href="{{ route('unit.index') }}"><i class="fa fa-building-o"></i> <span>@lang('sideMenu.reportUnit') </span></a></li>
                    <li class="{{ Request::is('commonarea*') ? 'active' : '' }}"><a href="{{ route('commonarea.index') }}"><i class="fa fa-building-o"></i> <span>@lang('sideMenu.reportCommonArea') </span></a></li>
                    

                    <li class="{{ Request::is('handoverreport*') ? 'active' : '' }}"><a href="{{ route('handoverreport.index') }}"><i  class="fa fa-building-o"></i> <span>@lang('sideMenu.reportHandoverReport') </span></a></li>
                    <li class="{{ Request::is('contractors*') ? 'active' : '' }}"><a href="{{ route('contractors.index') }}"><i  class="fa fa-user"></i> <span>@lang('sideMenu.reportContractor') </span></a></li>
                </ul>
            </li>
            <li class="{{ Request::is('project*') ? 'active' : '' }}"><a href="{{ route('set-general.show', [session('project_id')]) }}"><i  class="icon-gear"></i> <span>@lang('sideMenu.projectSet') </span></a></li>

            <li>
            <li class="{{ Request::is('key-access*') ? 'active' : '' }}"><a href="{{ route('key-access.index') }}"><i  class="icon-key"></i> <span>Access Items </span></a></li>
            <li>
            <a href="#"><i class="icon-cogs"></i> <span>@lang('sideMenu.system_set')</span></a>
            <ul>
                <li class="{{ Request::is('user*') ? 'active' : '' }}"><a href="{{ route('user.index') }}"><i class="icon-user-tie"></i><span> @lang('sideMenu.employee')</span></a></li>
                <li class="{{ Request::is('buyer*') ? 'active' : '' }}"><a href="{{ route('buyer.index') }}"><i  class="icon-users"></i> <span>Buyer </span></a></li>
                
                <!-- <li class="{{ Request::is('customer*') ? 'active' : '' }}"><a href="{{ route('customer.index') }}"><i  class="icon-users"></i> <span>@lang('sideMenu.customer') </span></a></li> -->
                <li class="{{ Request::is('group*') && !Request::is('group-form*') ? 'active' : '' }}"><a href="{{ route('group.index') }}"><i class="icon-users4"></i><span> @lang('sideMenu.companyContractor')</span></a></li>
                                                                <li>
                <a href="#"><i class="icon-cogs"></i> <span>@lang('sideMenu.issue_config')</span></a>
                <ul>
                    <li class="{{ Request::is('setting_category*') ? 'active' : '' }}"><a href="{{ route('setting_category.index') }}">@lang('sideMenu.category')</a></li>
                    <li class="{{ Request::is('setting_type*') ? 'active' : '' }}"><a href="{{ route('setting_type.index') }}">@lang('sideMenu.type')</a></li>
                    <li class="{{ Request::is('setting_issue*') ? 'active' : '' }}"><a href="{{ route('setting_issue.index') }}">@lang('sideMenu.issue')</a></li>
                    <li class="{{ Request::is('setting_priority*') ? 'active' : '' }}"><a href="{{ route('setting_priority.index') }}">@lang('sideMenu.priority')</a></li>
                </ul>
                </li>
                <li class="{{ Request::is('form*') ? 'active' : '' }}"><a href="{{ route('form.index') }}"><i class="icon-insert-template"></i><span> Digital Forms</span></a></li>
                <li class="{{ Request::is('group-form*') ? 'active' : '' }}"><a href="{{ route('group-form.index') }}"><i class="icon-stack"></i><span> @lang('main.group-form')</span></a></li>
                <li class="{{ Request::is('document*') ? 'active' : '' }}"><a href="{{ route('document.index') }}"><i class="icon-files-empty2"></i><span> @lang('main.document')</span></a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="icon-cogs"></i> <span>Handover Setting</span></a>
            <ul>
                <li class="{{ Request::is('handover-setting/checklist-form*') ? 'active' : '' }}"><a href="{{ route('checklist-form.index') }}"><i  class="icon-file-check2"></i> <span>Checklist Form Settings</span></a></li>
                <li class="{{ Request::is('handover-asd') && !Request::is('handover-form*') ? 'active' : '' }}"><a href="{{ route('handover.index') }}"><i class="icon-cogs"></i><span> @lang('sideMenu.handover_set')</span></a></li>
            </ul>
        <li>
            <li class="{{ Request::is('audit*') ? 'active' : '' }}"><a href="{{ route('audit.index') }}"><i class="icon-file-text2"></i><span> @lang('sideMenu.audit_trails')</span></a></li>
        </li>

        @else
        
        <li class="{{ Request::is('dashboard*') ? 'active' : '' }}"><a href="{{ route('mainDashboard') }}"><i class="icon-graph"></i><span> @lang('sideMenu.dashboard')</span></a></li>
        <li class="{{ Request::is('project*') ? 'active' : '' }}"><a href="{{ route('project.index') }}"><i class="icon-city"></i><span> @lang('sideMenu.project')</span></a></li>
        <li>
            <a href="#"><i class="icon-cogs"></i> <span>@lang('sideMenu.system_set')</span></a>
            <ul>
                <li class="{{ Request::is('user*') ? 'active' : '' }}"><a href="{{ route('user.index') }}"><i class="icon-user-tie"></i><span> @lang('sideMenu.employee')</span></a></li>
                <li class="{{ Request::is('buyer*') ? 'active' : '' }}"><a href="{{ route('buyer.index') }}"><i  class="icon-users"></i> <span>Buyer </span></a></li>
                <li class="{{ Request::is('group*') && !Request::is('group-form*') ? 'active' : '' }}"><a href="{{ route('group.index') }}"><i class="icon-users4"></i><span> @lang('sideMenu.companyContractor')</span></a></li>
                
                <li>
                <a href="#"><i class="icon-cogs"></i> <span>@lang('sideMenu.issue_config')</span></a>
                <ul>
                    <li class="{{ Request::is('setting_category*') ? 'active' : '' }}"><a href="{{ route('setting_category.index') }}">@lang('sideMenu.category')</a></li>
                    <li class="{{ Request::is('setting_type*') ? 'active' : '' }}"><a href="{{ route('setting_type.index') }}">@lang('sideMenu.type')</a></li>
                    <li class="{{ Request::is('setting_issue*') ? 'active' : '' }}"><a href="{{ route('setting_issue.index') }}">@lang('sideMenu.issue')</a></li>
                    <li class="{{ Request::is('setting_priority*') ? 'active' : '' }}"><a href="{{ route('setting_priority.index') }}">@lang('sideMenu.priority')</a></li>
                </ul>
                </li>
                <li class="{{ Request::is('form*') ? 'active' : '' }}"><a href="{{ route('form.index') }}"><i class="icon-insert-template"></i><span> Digital Forms</span></a></li>
                <li class="{{ Request::is('group-form*') ? 'active' : '' }}"><a href="{{ route('group-form.index') }}"><i class="icon-stack"></i><span> @lang('main.group-form')</span></a></li>
                <li class="{{ Request::is('document*') ? 'active' : '' }}"><a href="{{ route('document.index') }}"><i class="icon-files-empty2"></i><span> @lang('main.document')</span></a></li>
            </ul>
        </li>
        <li>
            <li class="{{ Request::is('audit*') ? 'active' : '' }}"><a href="{{ route('audit.index') }}"><i class="icon-file-text2"></i><span> @lang('sideMenu.audit_trails')</span></a></li>
        </li>
        @endif

@endif