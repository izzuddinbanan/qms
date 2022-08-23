<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Redirector;
use L5Swagger\Http\Controllers\SwaggerController;

class SwaggerAPIController extends SwaggerController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Redirector $redirect)
    {
        // $this->middleware('isAdmin');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return parent::api();
    }
}
