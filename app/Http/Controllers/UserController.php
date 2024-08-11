<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	function getdata(Request $request)
	{
		if ($request->ajax()) {
			$data = User::select(['uuid', 'name', 'email', 'updated_at']);
			return DataTables::of($data)
				->addColumn('updated_at', function ($row) {
					return $row->updated_at->format('Y-m-d H:i:s');
				})
				->addColumn('action', function ($row) {
					$btn = '<a href="javascript:void(0)" data-uuid="' . $row->uuid . '" class="edit btn btn-warning btn-sm editData"><i class="bi bi-pencil-square"></i></a>';
					$btn .= ' <a href="javascript:void(0)" data-uuid="' . $row->uuid . '" class="btn btn-danger btn-sm deleteData"><i class="bi bi-trash"></i></a>';
					return $btn;
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	function index()
	{
		return view('backend.user.index');
	}

	function store(Request $request)
	{
		// dd($request->all());
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				Rule::unique('users')->ignore($request->user_id, 'uuid'),
			],
			'password' => $request->user_id ? 'nullable|string|min:8' : 'required|string|min:8',
		]);

		$data = [
			'name' => $request->name,
			'email' => $request->email,
		];

		if ($request->password) {
			$data['password'] = bcrypt($request->password);
		}

		if ($request->user_id) {
			$user = User::where('uuid', $request->user_id)->firstOrFail();
			$user->update($data);
		} else {
			$data['uuid'] = (string) Str::uuid();
			$user = User::create($data);
		}

		return response()->json(['success' => 'User saved successfully.']);
	}

	function edit(string $uuid)
	{
		$user = User::where('uuid', $uuid)->first(['uuid', 'name', 'email']);
		if (!$user) {
			return response()->json(['error' => 'User not found.'], 404);
		}
		return response()->json($user);
	}

	function destroy($uuid)
	{
		$user = User::where('uuid', $uuid)->firstOrFail();
		$user->delete();
		return response()->json(['success' => 'User deleted successfully.']);
	}
}
