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

    // POST /test
    public function store(Request $request)
    {
        return "store";
    }

    // GET /test/:id
    public function show($id)
    {
        return "show";
    }

    // PUT /test/:id
    public function update(Request $request, $id)
    {
        return "update";
    }

    // DELETE /test/:id
    public function destroy($id)
    {
        return "destroy";
    }
}
