<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name|姓名' => 'required|unique:users|max:60',
                'email|电子邮箱' => 'required|email|unique:users|max:255',
                'password|密码' => 'required|confirmed|min:6'
            ]
        );
        halt($request);
        return;
    }
}
