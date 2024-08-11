<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
	function getdata(Request $request)
	{
		if ($request->ajax()) {
			$data = Post::latest();
			return DataTables::of($data)
				->addColumn('action', function ($row) {
					$btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-warning btn-sm editData">Edit</a>';
					$btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteData">Delete</a>';
					return $btn;
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	function index()
	{
		//
	}

	function create()
	{
		//
	}

	function store(Request $request)
	{
		//
	}

	function show(string $id)
	{
		//
	}

	function edit(string $id)
	{
		//
	}

	function update(Request $request, string $id)
	{
		//
	}

	function destroy(string $id)
	{
		//
	}
}
