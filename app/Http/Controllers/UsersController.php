<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * 注册页面
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 用户详情
     * @param User $user
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required|unique:users|max:50",
            "email" => "required|email|unique:users|max:255",
            "password" => "required|confirmed|min:6",
        ]);
        return;
    }
}
