<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class TestController extends BaseController
{
    // GET /test
    public function index()
    {
        return "hello there";
    }

}
