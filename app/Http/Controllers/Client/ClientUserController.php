<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;

class ClientUserController extends Controller
{
    public function index(Request $request)
    {
        $projectId = auth('client')->user()->project_id;
        $users = User::where('project_id', $projectId)->orderByDesc('id')->get();
        return view('client.users.list', compact('users'));
    }
} 