<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    /**
     * @var array
     */
    protected $appData;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct()
    {
        $now = Carbon::now('Asia/Kuala_Lumpur')->format('Y-m-d, H:i:s');

        $this->appData = collect([
            'settingIndex' => $now,
            'portal_url'   => route('home'),
            'download_url' => route('home'),
            'web_url'      => route('home'),
            'version'      => '',
        ]);

        $this->data = array();

    }

}
