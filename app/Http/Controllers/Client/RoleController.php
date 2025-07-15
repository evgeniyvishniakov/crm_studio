<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = config('roles');
        return view('client.roles.index', compact('roles'));
    }
}
