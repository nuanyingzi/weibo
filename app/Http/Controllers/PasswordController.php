<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{

    /**
     * 显示密码重置链接请求表单。
     *
     * @return \Illuminate\Http\Response
     */
     public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * 发送密码重置请求。
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1 验证邮箱
        $request->validate([
            'email' => 'required|email|max:255',
        ]);
        $email = $request->email;

        // 2 获取对应用户
        $user = User::where('email', $email)->firstOrFail();
        if (!$user) {
            session()->flash('danger', '该邮箱未注册');
            return redirect()->back()->withInput();
        } 

        // 3 生成token
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 4 入库
        DB::table('password_resets')->updateOrInsert([
            'email' => $email,
        ], [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);

        // 5 发送邮件
        Mail::send('emails.reset_link', compact('token'), function ($message) use($email) {
            $message->to($email)->subject('密码重置链接');
        });

        // 6 提示用户检查邮箱
        session()->flash('info', '密码重置链接已发送至您的邮箱');
        return redirect()->back();

    }

    /**
     * 显示密码重置表单。
     *
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route('token');
        return view('auth.passwords.reset', compact('token'));
    }

    /**
     * 重置密码。
     *
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        // 1 验证数据
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);
        $email = $request->email;
        $password = $request->password;
        $token = $request->token;
        $expire = 10 * 60;

        // 2 获取用户
        $user = User::where('email', $email)->firstOrFail();
        if (!$user) {
            session()->flash('danger', '该邮箱未注册');
            return redirect()->back()->withInput();
        } 

        // 3 读取重置的记录
        $record = (array)DB::table('password_resets')->where('email', $email)->first();
        if (!$record) {
            session()->flash('danger', '该邮箱未请求重置密码');
            return redirect()->back()->withInput();
        }

        // 4 检查是否过期
        if (Carbon::parse($record['created_at'])->addSeconds($expire)->isPast()) {
            session()->flash('danger', '该重置链接已过期');
            return redirect()->route('password.request');
        }

        // 5 检查token是否匹配
        if (!Hash::check($token, $record['token'])) {
            session()->flash('danger', '该重置链接无效');
            return redirect()->back()->withInput();
        }

        // 正常流程
        $user->password = bcrypt($password);
        $user->save();
        DB::table('password_resets')->where('email', $email)->delete();
        session()->flash('success', '密码重置成功，请用新密码登录。');
        return redirect()->route('login');

    }
}
