<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-header" style="min-width: 0px;">
        <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ url('assets/images/qms_logo.png') }}" alt=""></a>

        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">

        <!-- SWITCH ROLE -->
        <ul class="nav navbar-nav">
            <li>
                <a class="sidebar-control sidebar-main-toggle hidden-xs">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
            

            <!-- Project list | TO change project-->
            @if(session('project_id'))
            <li>
                <a href="{{ route('project.index') }}" class="sidebar-control" data-popup="tooltip" title="@lang('navbar.project')" data-placement="bottom">
                    <i class="icon-circle-left2"></i>
                </a>
            </li>
            <li>
                <form action="{{ route('project.show') }}" method="POST">
                    {{ csrf_field() }}
                    
                    <div class="sidebar-control" style="padding-top: 5px;">
                        <select class="select-search" name="project_id" id="project_option" data-placeholder="Project" onchange="this.form.submit()">
                            <option value="">Please Select</option>
                            @foreach(Helper::list_project() as $project)
                                @php
                                $select = "selected";
                                if($project->id != session('project_id')){
                                    $select = '';
                                }
                                @endphp
                                <option value="{{ $project->id }}" {{ $select }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </li>
            @endif
            <!-- Project list -->

            @if(count(Helper::list_client()) > 1)
                @if(Auth::user()->roles->first()->id != '1')
                <li class="dropdown language-switch">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-git-compare  position-left" alt=""></i>
                        {{ Helper::curret_client()->roles->display_name }} [{{ Helper::curret_client()->clients->name }}]
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(Helper::list_client() as $roles)
                            <li class="{{ ( $roles->id == session('role_user_id') ) ? 'active' : '' }}">
                                @if( $roles->id == session('role_user_id') )
                                    <a href="javascript:void(0);">{{ $roles->roles->display_name }} [{{ $roles->clients->name }}]</a>

                                @else
                                    <a href="{{ route('switchClient', [$roles->id]) }}">{{ $roles->roles->display_name }} [{{ $roles->clients->name }}]</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
                @endif
            @endif

        </ul>
        <!-- /SWITCH ROLE -->

        <ul class="nav navbar-nav navbar-right">
            
        </ul>
        
        <!-- MENU Notification/ LOGOUT/ SETTING/ -->
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown language-switch">
                <a class="dropdown-toggle" data-toggle="dropdown">{{ Auth::user()->language->name }}<i class="caret"></i></a>

                <ul class="dropdown-menu">
                    @foreach(get_language() as $language)
                        @if($language->id  != Auth::user()->language->id)
                            <li><a href="{{ route('switch-language',[$language->id]) }}" > {{ $language->name }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    @if(Auth::user()->newNotification->count() > 0)
                        <i class="icon-bell3"></i>
                        <span class="visible-xs-inline-block position-right">Notification</span>
                        <span class="badge bg-warning-400">{{ Auth::user()->newNotification->count() }}</span>
                    @else
                        <i class="icon-bell2"></i>
                        <span class="visible-xs-inline-block position-right">Notification</span>
                    @endif
                </a>
                
                <div class="dropdown-menu dropdown-content">

                    <ul class="media-list dropdown-content-body width-350">
    
                        @forelse(Helper::listNotification() as $dataNotification)
                        @php
                            $data_logo = $dataNotification["issue"]["location"]["DrawingPlan"]["DrawingSet"]["project"]["logo"];

                            if($data_logo == null){
                                $logo_project = 'assets/images/placeholder.jpg';
                            }else{
                                $logo_project = 'uploads/project_logo/'. $data_logo;
                            }
                        @endphp
                        <a href="{{ route('viewNotification', [$dataNotification->id]) }}" style="color: black;">
                            @if($dataNotification["read_status_id"] == 0)
                                <li class="media" style="background-color: rgba(210, 205, 205, 0.2);border-radius: 10px;">
                            @else
                                <li class="media">
                            @endif
                                <div class="media-left" style="padding-top: 20px;padding-left: 20px;">
                                        <img src="{{ url($logo_project) }}" class="img-thumb img-lg" alt="">
                                </div>
                                
                                <div class="media-body">
                                    <b>Issue {{ $dataNotification["issue"]["reference"] }}</b> from <b>{{ $dataNotification["issue"]["location"]["DrawingPlan"]["DrawingSet"]["project"]["name"] }}'s {{ $dataNotification["issue"]["location"]["name"] }}</b> is under <text style="color: {{ $dataNotification['issue']['status']['internal_color'] }}">{{ $dataNotification["issue"]["status"]["internal"] }}</text> status.
                                    <div class="media-annotation">{{  $dataNotification["created_at"]->format('d M Y, h:i a') }}&nbsp;&nbsp;&nbsp;{{ $dataNotification["created_at"]->diffForHumans() }}</div>

                                </div>
                            </li>
                        </a>
                        <hr style="margin-top: 0px;margin-bottom: 0px;border-top: 1px solid #b8b1b1;" width="90%">
                        @empty
                        <li class="media">
                            <div class="media-body">
                                <div class="media-annotation text-center"><i>@lang('navbar.noNotification')</i>    </div>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                    <div class="dropdown-content-footer">
                        <a href="#"><i class="icon-menu display-block"></i></a>
                    </div>
                </div>
            </li>

            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ (Auth::user()->avatar != '') ? url('uploads/avatars/'. Auth::user()->avatar) : url('assets/images/placeholder.jpg')  }}" alt="">
                    <span>{{ Auth::user()->name }}</span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    
                    <li><a href="#" class="viewProfile" id="{{ Auth::user()->id }}"><i class="icon-profile"></i> @lang('navbar.profile')</a></li>
                    <li><a href="{{ route('updatePass.index') }}"><i class="icon-lock"></i> @lang('navbar.password')</a></li>
                    <li class="divider"></li>
                    
                    @if(Session::has('original_user_id'))
                        <li><a href="{{ route('switchUser', auth()->user()->id)}}"><i class="icon-git-compare"></i> @lang('navbar.switchBack')</a></li>
                    @endif
                    <li><a href="{{ url('logout') }}"><i class="icon-switch2"></i> @lang('navbar.logout')</a></li>
                </ul>
            </li>
        </ul>
        <!-- //MENU LOGOUT/SETTING/ -->

    </div>
</div>