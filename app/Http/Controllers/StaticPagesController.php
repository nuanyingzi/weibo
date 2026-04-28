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
        }
        return view('static_page.home', compact('feed_items'));
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
