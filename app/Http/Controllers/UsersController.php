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
}
