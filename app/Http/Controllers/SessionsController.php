<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create'],
        ]);
        // 登录限流 10次/10分
        $this->middleware('throttle:20,10', [
            'only' => ['store'],
        ]);
    }
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
            // 邮件是否验证成功
            if (Auth::user()->activated) {
                session()->flash('success', '登录成功，欢迎回来');
                $fallbackUrl = route('users.show', [Auth::user()]);
                return redirect()->intended($fallbackUrl);
            } else {
                Auth::logout();
                session()->flash('warning', '您的账号未激活，请前往邮箱中的注册邮件进行激活账号操作。');
                return redirect()->route('home');
            }
            
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
