<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feed_items = [];
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(10);
            $user = Auth::user();
        }
        return view('static_page.home', compact('feed_items', 'user'));
    }

    public function help()
    {
        return view('static_page.help');
    }

    public function about()
    {
        return view('static_page.about');
    }
}
