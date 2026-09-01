<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return 'AdminController@index';
    }

    public function create()
    {
        return 'AdminController@create';
    }

    public function store(Request $request)
    {
        return 'AdminController@store';
    }

    public function show(string $id)
    {
        return "AdminController@show, id: {$id}";
    }

    public function edit(string $id)
    {
        return "AdminController@edit, id: {$id}";
    }

    public function update(Request $request, string $id)
    {
        return "AdminController@update, id: {$id}";
    }

    public function destroy(string $id)
    {
        return "AdminController@destroy, id: {$id}";
    }

    public function info(string $id)
    {
        return "AdminController@info, id: {$id}";
    }
}
