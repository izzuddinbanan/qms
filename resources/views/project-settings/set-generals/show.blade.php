@extends('components.template-limitless.main')

@section('main')
@include('project-settings.components.tab')

<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <h4 class="panel-title textUpperCase"><i class="icon-user-tie"></i> @lang('project.headerGeneral')</h4>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
                <a href="javascript:void(0)" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right" id="changeLanguage"><b><i class=" icon-stack-text"></i></b> Select Project Language</a>
                <a href="{{ route('set-drawing-set.index') }}" class="btn btn-primary" data-popup="tooltip" title="@lang('general.next')" data-placement="top"><i class="fa fa-forward"></i></a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        @if(count($language) > 1)
        <div class="row">
            <div class="col-md-12"></div>
            <div class="col-md-4">
                <label class="lable">Current language</label>
                <select data-placeholder="Select Language" class="select-search" name="select_language" id="select_language">
                    @foreach(get_language() as $lang_choose)
                        @if(in_array($lang_choose->id, $project->language_id))
                            <option value="{{ $lang_choose->id }}">{{ $lang_choose->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
        @endif

        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" action="{{ route('set-general.update', [$project->id]) }}" method="POST" enctype="multipart/form-data">
                    @method('put')
                    @csrf

                    @foreach($language as $lang_id)

                    @php
                        $name = $lang_id->id == 1 ? $project->name : ($project->data_lang[$lang_id->abbreviation_name]["name"] ?? '');
                        $abv = $lang_id->id == 1 ? $project->abbreviation_name : $project->data_lang[$lang_id->abbreviation_name]["abbreviation_name"] ?? '';

                        $contract_no = $lang_id->id == 1 ? $project->contract_no : $project->data_lang[$lang_id->abbreviation_name]["contract_no"] ?? '';

                        $description = $lang_id->id == 1 ? $project->description : $project->data_lang[$lang_id->abbreviation_name]["description"] ?? '';

                    @endphp

                    <div id="form_{{ $lang_id->id }}" class="form-section" style="display: {{ $lang_id->id == 1 ? 'show' : 'none' }}">
                        
                        <fieldset class="content-group">
                            
                            @if($lang_id->id == 1)
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">Project Id</label>
                                <div class="col-md-6 col-xs-8">
                                    <input type="text" class="form-control" name="project_id" autocomplete="off" autofocus="" value="{{ $project->project_id }}">
                                </div>
                            </div>
                            @endif

                            <!-- Project Name -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.projectName')</label>
                                <div class="col-md-6 col-xs-8">
                                    <input type="text" class="form-control" name="name[{{ $lang_id->id }}]" autocomplete="off" autofocus="" value="{{ $name }}" placeholder="e.g Ara Oasis Residency">
                                </div>
                            </div>

                            <!-- Project Abbreviation Name -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.abvName')</label>
                                <div class="col-md-6 col-xs-8">
                                    <input type="text" class="form-control" name="abv[{{ $lang_id->id }}]" autocomplete="off" value="{{ $abv }}"  placeholder="e.g AOR">
                                </div>
                            </div>

                            <!-- Contract No -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.contractNo')</label>
                                <div class="col-md-6 col-xs-8">
                                    <input type="text" class="form-control" name="contract[{{ $lang_id->id }}]" value="{{ $contract_no }}" autocomplete="off" autofocus="" placeholder="e.g AOR000001">
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.description')</label>
                                <div class="col-md-6 col-xs-8">
                                    <textarea class="form-control" name="description[{{ $lang_id->id }}]"  placeholder="">{{ $description }}</textarea>
                                </div>
                            </div>

                            <!-- Logo -->
                            @if($lang_id->id == 1)

                            <!-- Logo -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.logo')</label>
                                <div class="col-md-4 col-xs-8">
                                    <input type="file" class="dropify" name="logo[{{ $lang_id->id }}]" value="" accept="image/*" @if($project->logo) data-default-file="{{ $project->logo_url }}" @endif>
                                    <span class="help-block">@lang('general.acceptUploadImage')</span>
                                </div>


                                <label class="control-label col-md-1 col-xs-2 right">@lang('project.applogo')</label>
                                <div class="col-md-4 col-xs-8">
                                    <input type="file" class="dropify" name="app_logo[{{ $lang_id->id }}]" value="" accept="image/*" @if($project->app_logo) data-default-file="{{ $project->app_logo_url }}" @endif>
                                    <span class="help-block">@lang('general.acceptUploadImage')</span>
                                </div>
                            </div>

                           <!--Email Notification -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.emailNoti')</label>
                                <div class="col-md-6 col-xs-8">
                                    <div class="checkbox">
                                        <input type="checkbox" class="styled" name="email_notify[{{ $lang_id->id }}]" value="1" {{ $project->email_notification ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <!--App Logo -->
                            <div class="form-group">
                                <label class="control-label col-md-2 col-xs-2 right">@lang('project.emailNotiAt')</label>
                                <div class="col-md-6 col-xs-8">
                                    <input type="time" class="form-control" name="notify_at[{{ $lang_id->id }}]" value="{{ $project->email_notification_at }}" >
                                </div>
                            </div>
                            @endif
                        </fieldset>

                        @if($lang_id->id == 1)
                            <legend>PDF template</legend>
                            <fieldset>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-xs-2 right">Header</label>
                                    <div class="col-md-4 col-xs-8">
                                        <input type="file" class="dropify" name="header" value="" accept="image/*" @if($project->header) data-default-file="{{ $project->header_url }}" @endif>
                                        <span class="help-block">@lang('general.acceptUploadImage')</span>
                                    
                                    </div>
                                    <label class="control-label col-md-1 col-xs-2 right">Footer</label>
                                    <div class="col-md-4 col-xs-8">
                                        
                                        <input type="file" class="dropify" name="footer" value="" accept="image/*" @if($project->footer) data-default-file="{{ $project->footer_url }}" @endif>
                                        <span class="help-block">@lang('general.acceptUploadImage')</span>
                                    </div>
                                </div>
                                
                            </fieldset>
                        @endif
                    </div>
                    @endforeach
                    
                    <div class="col-md-12 col-xs-12 right">
                        <div class="text-right">
                            <a href="{{ route('project.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>

                            <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>


<div id="modalLanguage" class="modal fade" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('project.chooseLangMsg')</h5>
            </div>

            <form action="{{ route('set-general.setLang') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        @foreach(get_language() as $lang)

                           <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="lang[]" value="{{ $lang->id }}" {{ $lang->id == 1 || in_array($lang->id, $project->language_id )? 'checked=""' : '' }} {{ $lang->id == 1 ? 'disabled=""' : '' }}> 
                                    {{ $lang->name }} <text class="text-muted">{{ $lang->id == 1 ? '(Default)' : ''}}</text>
                                </label>
                            </div>

                        @endforeach
                    </div>               
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" style="width: 100%">@lang('general.submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
    <script type="text/javascript">
        $(document).ready(function(){

            $("#changeLanguage").click(function(){
                event.preventDefault();
                $("#btnModal").show();
                $("#modalLanguage").modal('show');
            });

            $("#select_language").change(function(){

                $(".form-section").hide();
                $("#form_" + $(this).val()).show()

            });

        });

    </script>
@endsection
