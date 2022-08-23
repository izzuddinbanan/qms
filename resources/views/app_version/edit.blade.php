@extends('components.template-limitless.main')

@section('main')


<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <h4 class="panel-title textUpperCase"><i class="icon-mobile"></i> New App Version</h4>
            </div>
            <div class="col-md-4 col-xs-4 text-right">
                <a href="{{ route('app-version.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('app-version.update', [$data->id]) }}" method="POST">
                    @method('put')
                    @csrf

                    <fieldset class="content-group">
                        
                        <!-- CLIENT/COMPANY COLUMN -->
                        <div class="col-md-8 col-md-offset-2">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-semibold">OS</label>
                                        <select class="select-search" name="OS" required="">
                                            @php
                                                $OSversion = array("IOS"=> "IOS" , "AND" => "Android");
                                            @endphp
                                            @foreach($OSversion as $key => $val)
                                                <option value="{{ $key }}" {{ $key == $data->os ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-semibold">Type</label>
                                        <select class="select-search" name="type" required="">
                                            <option value="">Please Select</option>
                                            @php
                                                $typeVersion = array("optional" , "critical");
                                            @endphp
                                            @foreach($typeVersion as $type)
                                                <option value="{{ $type }}" {{ $type == $data->type ? 'selected' : '' }}>
                                                    {{ ucwords($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="text-semibold">Version</label>
                                <input type="text" class="form-control" name="version"  placeholder="e.g. 1.0.1" value="{{ $data->version }}">
                            </div>

                            <div class="form-group">
                                <label class="text-semibold">Description</label>
                                <textarea class="form-control" name="description">{{ $data->description }}</textarea>
                            </div>


                            <div class="form-group">
                                <label class="text-semibold">Status</label>
                                <select class="select-search" id="status" name="status" required="">
                                    @php
                                        $status = array("Active" , "Inactive");
                                    @endphp
                                    @foreach($status as $statusVersion)
                                        <option value="{{ $statusVersion }}" {{ $statusVersion == $data->status ? 'selected' : '' }}>
                                            {{ ucwords($statusVersion) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- BUTTON SUBMIT/BACK -->
                        <div class="col-md-12 col-xs-12 right">
                            <div class="text-right">
                                <a href="{{ route('app-version.index') }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>
                                <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $("#version").blur(function(){
                var version = $(this).val();
                var type = $("#type").val();


                $.ajax({
                    url:'{{ url("app_version/checkVersion")}}',
                    type:'post',
                    data:{"version":version , "type":type},
                    success:function(response){
                     console.log(response);
                      
                    }
                });
            });
        });
    </script>
@endsection
