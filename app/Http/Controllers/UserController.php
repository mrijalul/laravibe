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
			$data = User::with('roles')->select(['id', 'uuid', 'name', 'email', 'updated_at', 'created_at'])->orderBy('created_at', 'desc');
			return DataTables::of($data)
				->addColumn('roles', function ($row) {
					if ($row->roles->isEmpty()) {
						return '<span class="badge text-bg-warning">Belum memiliki relasi</span>';
					} else {
						return $row->roles->map(function ($role) {
							return '<span class="badge text-bg-success">' . $role->name . '</span>';
						})->implode(' ');
					}
				})
				->addColumn('updated_at', function ($row) {
					return $row->updated_at->format('Y-m-d H:i:s');
				})
				->addColumn('action', function ($row) {
					$btn = '<a href="javascript:void(0)" data-uuid="' . $row->uuid . '" class="edit btn btn-warning btn-sm editData"><i class="bi bi-pencil-square"></i></a>';
					$btn .= ' <a href="javascript:void(0)" data-uuid="' . $row->uuid . '" class="btn btn-danger btn-sm deleteData"><i class="bi bi-trash"></i></a>';
					return $btn;
				})
				->rawColumns(['action', 'roles'])
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
			'name' 	=> 'required|string|max:255',
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				Rule::unique('users')->ignore($request->user_id, 'uuid'),
			],
			'password' => $request->user_id ? 'nullable|string|min:8' : 'required|string|min:8',
		], [
			'name.required' 	=> 'Nama tidak boleh kosong.',
			'name.max' 			=> 'Nama tidak boleh lebih dari 255 karakter.',
			'email.required' 	=> 'Email wajib diisi.',
			'email.email' 		=> 'Format email tidak valid.',
			'email.unique' 		=> 'Email ini sudah terdaftar.',
			'password.required' => 'Password wajib diisi.',
			'password.min' 		=> 'Password harus memiliki minimal 8 karakter.',
		]);

		$data = [
			'name' 	=> $request->name,
			'email' => $request->email,
		];

		if ($request->password) {
			$data['password'] = bcrypt($request->password);
		}

		if ($request->user_id) {
			$user 		= User::where('uuid', $request->user_id)->firstOrFail();
			$user->update($data);
			$message 	= 'User berhasil diperbarui.';
		} else {
			$data['uuid'] 	= (string) Str::uuid();
			$user 			= User::create($data);
			$message 		= 'User baru berhasil ditambahkan.';
		}

		return response()->json(['success' => $message]);
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
