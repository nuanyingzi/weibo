<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function __construct()
    {
        // 限流访问，10分钟3次
        $this->middleware('throttle:3,10', [
            'only' => ['showLinkRequestForm']
        ]);
    }

    // 忘记密码界面
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

    // 重置密码界面
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');
        return view('auth.passwords.reset', compact('token'));
    }

    /**
     * 重置密码
     * @param Request $request
     * @return void
     */
    public function reset(Request $request)
    {
        // 1. 验证数据是否合规
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $email = $request->email;
        $token = $request->token;
        // 找回密码链接的有效时间
        $expires = 60 * 10;

        // 2. 获取对应用户
        $user = User::where("email", $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 读取重置的记录
        $record = (array) DB::table('password_resets')->where('email', $email)->first();

        // 5. 记录存在
        if ($record) {
            // 5.1. 检查是否过期
            if (Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新尝试');
                return redirect()->back();
            }

            // 5.2. 检查是否正确
            if ( ! Hash::check($token, $record['token'])) {
                session()->flash('danger', '令牌错误');
                return redirect()->back();
            }

            // 5.3. 一切正常，更新用户密码
            $user->update(['password' => bcrypt($request->password)]);

            // 5.4. 提示用户更新成功
            session()->flash('success', '密码重置成功，请使用新密码登录');
            return redirect()->route('login');
        }

        // 6. 记录不存在
        session()->flash('danger', '未找到重置记录');
        return redirect()->back();
    }
}
