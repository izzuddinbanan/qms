@php
$setup_steps = [
    'project-setting/set-general' => [ 'icon' => 'icon-office', 'title' => 'General', 'route' => session('project_id') ? route('set-general.show', [session('project_id')]) : route('set-general.create') ], 

    'project-setting/set-drawing' => [ 'icon' => 'fa fa-map-o', 'title'=> 'Drawing', 'route' => route('set-drawing-set.index') ], 

    'project-setting/set-link' => [ 'icon' => 'fa fa-link', 'title'=> 'Link', 'route' => route('set-link.index') ], 
    
    'project-setting/set-inspection' => [ 'icon' => 'fa fa-wpforms', 'title'=> 'Inspection', 'route' => route('set-inspection.index') ],
    'project-setting/set-location' => [ 'icon' => 'fa fa-map-marker', 'title'=> 'Location', 'route' => route('set-location.index') ], 
    'project-setting/set-employee' => [ 'icon' => 'fa fa-user', 'title'=> 'Employee', 'route' => route('set-employee.index') ], 

    'project-setting/set-contractor' => [ 'icon' => 'fa fa-users', 'title'=> 'Contractor', 'route' => route('set-contractor.index') ], 
    'project-setting/set-issue' => [ 'icon' => 'fa fa-pencil-square-o', 'title'=> 'Issue', 'route' => route('set-issue.index') ], 
    'project-setting/set-document' => [ 'icon' => 'fa fa-file-pdf-o', 'title'=> 'Documents', 'route' => route('set-document.index') ]
]
@endphp

<div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        @foreach ($setup_steps as $key => $step)
            <div class="stepwizard-step">
                @php
                    $href = Request::is($key . '*') ? 'javascript:void(0)' : $step['route'];
                    $btn_active = Request::is($key . '*') ? 'btn-primary' : 'btn-default';
                @endphp
                <a href="{{ Request::is('project/set-general/create*') ? 'javacript:void(0)' : $href }}" type="button" class="btn {{ Request::is('project/create*') && $key == 'step1' ? 'btn-primary' : $btn_active }} btn-circle" {{ Request::is('project-setting/set-general/create*') ? 'disabled' : '' }}><i class="{{ $step['icon'] }}"></i></a>
                <p><strong>
                {{ $step['title'] }} 
                </strong></p>
            </div>
        @endforeach
    </div>
</div>