<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
	}

	function index()
	{
		return redirect()->route('lv-admin.dashboard');
	}
}
