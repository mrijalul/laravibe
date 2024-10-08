<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    function dashboard()
    {
        return view('backend.dashboard');
    }
}
