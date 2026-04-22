<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    /**
     * 显示登录界面。
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 登录
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentials  = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) { // 认证成功
            session()->flash('success', '登录成功，欢迎回来');
            return redirect()->route('users.show', [Auth::user()]);
        } else { // 认证失败
            session()->flash('danger', '抱歉，邮箱或密码错误');
            return redirect()->back()->withInput($credentials);
        }
    }

    /**
     * 退出登录
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        session()->flash('success', '已成功退出登录');
        return redirect()->route('home');
    }
}
