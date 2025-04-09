<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    // 注册页面
    public function create()
    {
        return view('users.create');
    }

    // 用户详情页面
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // 注册功能
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|unique:users|max:60',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|confirmed|min:6'
            ]
        );
        $data = $request->all();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        Auth::login($user);
        session()->flash('success', '注册成功，欢迎开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    // 编辑页面
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // 编辑功能
    public function update(User $user, Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:60',
                'password' => 'nullable|confirmed|min:6'
            ]
        );
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功');
        return redirect()->route('users.show', $user->id);
    }
}
