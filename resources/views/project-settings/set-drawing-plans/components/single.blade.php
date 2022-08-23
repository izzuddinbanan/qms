<div id="single-field" class="drawing-plan-field">
    <div class="form-group">
        <div class="col-md-6">
            <label class="text-semibold">Drawing Plan :</label>
            <input type="file" name="drawing_plan" id="drawing_plan" class="form-control dropify" accept="image/*">
        </div>
        <div class="col-md-6">
            <div class="row">
                
                <div class="col-md-12">
                    <label>@lang('project.planType')</label>
                    <select class="select-search" name="type_plan" id="type_plan">
                        <option value="unit">Unit</option>
                        <option value="common">Common</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>


                    
                <div class="col-md-6 col-xs-6 plan-field">
                    <label>Phase</label>
                    <input type="text" name="phase" id="plan_phase" value="{{ old('phase') }}" autocomplete="off" class="form-control" placeholder="e.g 1">
                </div>

                <div class="col-md-6 col-xs-6 plan-field">
                    <label>Block</label>
                    <input type="text" name="block" id="plan_block" value="{{ old('block') }}" autocomplete="off" class="form-control" placeholder="e.g ABC">
                </div>
                <div class="col-md-6 col-xs-6 plan-field">
                    <label>Level</label>
                    <input type="text" name="level" id="plan_level" value="{{ old('level') }}" autocomplete="off" class="form-control" placeholder="e.g 2">
                </div>
                <div class="col-md-6 col-xs-6 plan-field">
                    <label>Unit</label>
                    <input type="text" name="unit" id="plan_unit" value="{{ old('unit') }}" autocomplete="off" class="form-control" placeholder="e.g 10-a">
                </div>
                <div class="col-md-12 col-xs-12 custom-field">
                    <label>Display Name</label>
                    <input type="text" name="display_name" id="plan_name" value="{{ old('display_name') }}" autocomplete="off" class="form-control" placeholder="e.g Master Plan">
                </div>
            </div>
        </div>
    </div>
</div>


<!-- OLD CODE -->
{{-- 
<div class="col-md-12">
    <i class="icon-info22"></i> The format of file name for type unit and common must in this format :     <b>phase_block_level_unit</b>,  e.g p1_a_2_unit Ab 1
</div>

<div class="form-group">
    <div class="col-md-6"  style="margin-top: 10px !important;margin-bottom: 10px  !important;">
        <label>@lang('project.planType')</label>
        <select class="select-search" name="type" id="type">
            <option value="unit">Unit</option>
            <option value="common">Common</option>
            <option value="custom">Custom</option>
        </select>
    </div>
</div>

<div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
    <div class="col-md-8 col-xs-8">
      <div class="dropzone dropzone-file-area" id="my-dropzone" name="image">
      </div>
    </div>
</div>

<div class="form-group">
    <div class="col-md-8 col-xs-8"  style="margin-top: 10px !important;margin-bottom: 10px  !important;">
        <div class="text-right">
            <a href="{{ route('set-drawing-plan.show', [session('drawing_set_id')]) }}" type="button" class="btn bg-danger-400 btn-labeled"><b><i class=" icon-circle-left2"></i></b> @lang('main.back')</a>

            <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right">@lang('general.submit')<b><i class="icon-circle-right2"></i></b></button>
        </div>
    </div>
</div>
--}}