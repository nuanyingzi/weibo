<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    // 登录页
    public function create()
    {
        return view('sessions.create');
    }

    // 登录
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials, $request->has('remember'))) {
            // 登录成功
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth()->user()]);
        } else {
            // 登录失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    // 退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '账号已退出，欢迎下次登录~');
        return redirect()->route('login');
    }
}
