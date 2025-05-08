<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        // 登录用户访问接口
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'confirmEmail']
        ]);
        // 游客访问接口
        $this->middleware('guest', [
            'only' => ['create']
        ]);
        // 注册限流 1小时内只能提交10次
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }

    // 注册页面
    public function create()
    {
        return view('users.create');
    }

    // 用户详情页面
    public function show(User $user)
    {
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.show', compact('user', 'statuses'));
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
        $this->sendEmailConfirmationTo($user); // 发送邮件进行认证
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
/*        Auth::login($user);
        session()->flash('success', '注册成功，欢迎开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);*/
    }

    // 编辑页面
    public function edit(User $user)
    {
        $this->authorize('update', $user); // 授权策略
        return view('users.edit', compact('user'));
    }

    // 编辑功能
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user); // 授权策略
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

    // 用户列表
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    // 删除功能
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '删除用户成功！');
        return back();
    }

    /**
     * 发送验证邮件
     * @param $user
     * @return void
     */
    protected function sendEmailConfirmationTo($user): void
    {
        $view = 'emails.confirm';
        $data = compact('user');
//        $from = '852947475@qq.com';
//        $name = 'nuanyingzi';
        $to = $user->email;
        $subject = '感谢注册Weibo应用！请确认您的邮箱。';

        Mail::send($view, $data, function ($message) use($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * 验证邮件
     * @param $token
     * @return RedirectResponse
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activation_token = null;
        $user->activated = true;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜您，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

}
