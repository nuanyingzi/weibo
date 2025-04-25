<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    // 忘记密码模板
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * 发送重置密码邮件
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1 验证邮箱
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 2 获取对应用户
        $user = User::where('email', $email)->first();

        // 3 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4 生成token，在视图emails.reset_link里拼接链接
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 5 入库，使用updateOrInsert来保证email的唯一性
        DB::table('password_resets')->updateOrInsert(['email' => $email],[
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);

        // 6 将token链接发给用户
        Mail::send('emails.reset_link', compact('token'), function ($message) use($email) {
            $message->to($email)->subject('忘记密码');
        });

        session()->flash('success', '重置密码邮件发送成功，请注意查收');
        return redirect()->back();
    }
}
