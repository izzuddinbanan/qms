<?php

// use Carbon\Carbon;

if (!function_exists('get_authenticate_user')) {

    function get_authenticate_user()
    {
        return auth()->user();
    }
}

if (!function_exists('get_last_updated_at')) {

    /**
     * @return int
     */
    function get_last_updated_at($datas)
    {
        $last_updated_at = collect();

        foreach ($datas as $key => $value) {

            if ($value instanceof Illuminate\Database\Eloquent\Collection) {

                $last_updated_at->push(get_last_updated_at($datas[$key]));
            } else {
                if (isset($value->updated_at)) {
                    # code...
                    $last_updated_at->push($value->updated_at);
                }
            }
        }

        if ($last_updated_at->isEmpty()) {
            return 0;
        }

        $date = max($last_updated_at->toArray());
        return \Carbon\Carbon::parse($date)->format("Y-m-d H:i:s");
    }
}

if (!function_exists('convert_null_to_string')) {

    /**
     * @param $data
     */
    function convert_null_to_string($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = is_null($value) ? '' : $value;

            }
        } else {
            $data = is_null($data) ? '' : $data;
        }

        $new_data = $data;

        return $new_data;
    }
}

if (!function_exists('sum_between_two_arrays')) {

    /**
     * @param $data
     */
    function sum_between_two_arrays($array1, $array2)
    {
        return array_map(function (...$arrays) {
            return array_sum($arrays);
        }, $array1, $array2);
    }
}

if(!function_exists('edit_button')){

    function edit_button($path){

        $button = '<a href="' . $path . '" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" class="edit_button tooltip-show">
            <i class="fa fa-edit action-icon"></i>
        </a>';

        return $button;
    }
}

if(!function_exists('delete_button')){

    function delete_button($path){

        $button = '<a href="' .$path. '" data-popup="tooltip" title="'. trans('main.delete') .'" data-placement="top" class="ajaxDeleteButton tooltip-show" style="color:red;">
            <i class="fa fa-trash-o action-icon"></i>
        </a>';

        return $button;
    }
}

if(!function_exists('role_user')){

    function role_user(){

        return App\Entity\RoleUser::find(session('role_user_id'));

    }
}

// FOR pROJECT SETTING ADD 
if(!function_exists('set_route_session')){

    function set_route_session($path){

        Session::put('route-set-project', $path);
    }
}


if(!function_exists('destroy_route_session')){

    function destroy_route_session(){

        Session::forget('route-set-project');
    }
}

if(!function_exists('get_route_session')){

    function get_route_session(){

        if(Session::has('route-set-project')){
            return session('route-set-project');
        }

        return false;
    }
}
// FOR pROJECT SETTING ADD 



if(!function_exists('set_curret_route')){

    function set_curret_route($path){

        Session::put('current_route', $path);
    }
}

if(!function_exists('get_day_type')){

    function get_day_type(){

        $time = date("H");
        /* Set the $timezone variable to become the current timezone */
        $timezone = date("e");
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            $dayType = Lang::get('dashboard.morning');
        } elseif /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
        ($time >= "12" && $time < "17") {
            $dayType = Lang::get('dashboard.noon');
        } elseif /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
        ($time >= "17" && $time < "19") {
            $dayType = Lang::get('dashboard.evening');
        } elseif /* Finally, show good night if the time is greater than or equal to 1900 hours */
        ($time >= "19") {
            $dayType = Lang::get('dashboard.night');
        }

        return $dayType;
        
    }
}


if(!function_exists('get_role')){

    function get_role($name) {

        return App\Entity\Role::where('name', $name)->first();

    }
}


if(!function_exists('get_language')){

    function get_language() {

        $language = App\Entity\Language::get();
        return $language;

    }
}


if(!function_exists('list_notification')){

    function list_notification() {

        $user = \Auth::user();

        return App\Entity\Notification::where('user_id', $user->id)->where('read_status_id', 0)->latest()->get();
        
    }
}


if(!function_exists('list_project_user')){

    function list_project_user() {

        $role_user = role_user();

        if($role_user->role_id == 2){
            $data = \App\Entity\Project::where('client_id', $role_user->client_id )->get();
        }

        ##ADMIN
        if($role_user->role_id == 3){
            
            $projectUser = RoleUser::where('user_id', $role_user->user_id)->where('client_id', $role_user->client_id)->select('project_id')->get();

            $data = \App\Entity\Project::whereIn('id', $projectUser )->get();

        }
        
        return $data;
        
    }
}

if (!function_exists('third_party_response')) {

    /**
     * @return mixed
     */
    function third_party_response($error_code, $status, $message, $result)
    {
        return response()->json([
            'error_code' => $error_code,
            'status'     => $status,
            'message'    => $message,
            'result'     => $result,
        ]);
    }
}
