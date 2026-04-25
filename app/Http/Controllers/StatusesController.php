<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Models\User;

class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 发布微博
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:600',
        ]);
        $user = Auth::user();
        $user->statuses()->create([
            'content' => $request->content
        ]);
        session()->flash('success', '发布成功');
        return redirect()->back();
    }

    public function destroy(Status $status)
    {
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '删除成功');
        return redirect()->back();
    }
}
