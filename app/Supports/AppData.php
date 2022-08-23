<?php

namespace App\Supports;

use App\Entity\AppVersion;
use Dingo\Api\Http\Request;

trait AppData
{

    /**
     * @return mixed
     */
    public function prepareAppData(Request $request, $subData = array(), $status = array())
    {
        // zhengrong request change imei to os
        if ($request->filled('data.os') || $request->filled('os')) {

            $os = $request->filled('data.os') ? explode(':', $request->input('data.os'))[0] : explode(':', $request->input('os'))[0];
            $LastAppVersion = AppVersion::where('os', 'like', $os)->where('status', 'Active')->orderBy('created_at', 'DESC')->first();
            $LastCriticalAppVersion = AppVersion::where('os', 'like', $os)->where('type', 'Critical')->where('status', 'Active')->first();

        }
        $data = collect([
            'totalRecords'                         => count($subData),
            'last_updated_at'                      => count($subData) ? get_last_updated_at($subData) : $this->appData['settingIndex'],
            'version'                              => '',
            'status'                               => isset($status['status']) ? $status['status'] : 'success',
            'error_code'                           => isset($status['error_code']) ? $status['error_code'] : '',
            'message'                              => isset($status['message']) ? $status['message'] : '',
            'last_app_update_version'              => isset($LastAppVersion) && $LastAppVersion->version ? $LastAppVersion->version : 0,
            'last_app_update_description'          => isset($LastAppVersion) && $LastAppVersion->description ? $LastAppVersion->description : '',
            'last_critical_app_update_version'     => isset($LastCriticalAppVersion) && $LastCriticalAppVersion->version ? $LastCriticalAppVersion->version : 0,
            'last_critical_app_update_description' => isset($LastCriticalAppVersion) && $LastCriticalAppVersion->description ? $LastCriticalAppVersion->description : '',
        ])
            ->each(function ($item, $key) {
                $this->appData->put($key, $item);
            });

        return $this->appData;
    }

    /**
     * @param $error_message
     */
    public function failedAppData($error_message, $error_code = 999999)
    {
        return [
            'status'     => 'failed',
            'error_code' => $error_code,
            'message'    => $error_message,
        ];
    }

}
