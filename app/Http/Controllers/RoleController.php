<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
	function getdata(Request $request)
	{
		if ($request->ajax()) {
			$data = Role::select(['uuid', 'name', 'created_at'])->orderBy('created_at', 'desc');
			return DataTables::of($data)
				->addColumn('action', function ($row) {
					return '
                    <button class="btn btn-sm btn-warning editData" data-uuid="' . $row->uuid . '"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-danger deleteData" data-uuid="' . $row->uuid . '"><i class="bi bi-trash"></i></button>
                    <button class="btn btn-sm btn-info assignRole" data-rolename="' . $row->name . '" data-uuid="' . $row->uuid . '"><i class="bi bi-person-plus"></i></button>
                    <button class="btn btn-sm btn-secondary removeRole" data-uuid="' . $row->uuid . '" data-user-id=""><i class="bi bi-person-x"></i></button>
                ';
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	function index()
	{
		return view('backend.role.index');
	}

	function store(Request $request)
	{
		$request->validate([
			'name' => [
				'required',
				'string',
				'max:255',
				Rule::unique('roles')->ignore($request->role_id, 'uuid'),
			],
		], [
			'name.required' => 'Nama role tidak boleh kosong.',
			'name.max' => 'Nama role tidak boleh lebih dari 255 karakter.',
			'name.unique' => 'Nama role sudah digunakan.',
		]);

		$data = ['name' => $request->name];

		if ($request->role_id) {
			$role = Role::where('uuid', $request->role_id)->firstOrFail();
			$role->update($data);
			$message = 'Role berhasil diperbarui.';
		} else {
			$data['uuid'] = (string) Str::uuid();
			$role = Role::create($data);
			$message = 'Role baru berhasil ditambahkan.';
		}

		return response()->json(['success' => $message]);
	}

	function edit(string $id)
	{
		$role = Role::where('uuid', $id)->firstOrFail();
		return response()->json($role);
	}

	function destroy(string $id)
	{
		$role = Role::where('uuid', $id)->firstOrFail();
		$role->delete();

		return response()->json(['success' => 'Role berhasil dihapus.']);
	}

	function getdatauser(Request $request)
	{
		$search = $request->input('search');

		$users = User::select('uuid', 'name')
			->when($search, function ($query, $search) {
				return $query->where('name', 'like', "%{$search}%");
			})
			->get();

		return response()->json(['users' => $users]);
	}

	function assignRole(Request $request)
	{
		// Validasi input
		$request->validate([
			'user_id' => 'required|array',
			'user_id.*' => 'exists:users,uuid',
			'role_id' => 'required|exists:roles,uuid',
		]);

		// Temukan role berdasarkan UUID
		$role = Role::where('uuid', $request->role_id)->firstOrFail();

		// Temukan semua users berdasarkan UUID
		$user_ids = User::whereIn('uuid', $request->user_id)->pluck('id');

		// Assign role ke users
		$role->users()->syncWithoutDetaching($user_ids);

		return response()->json(['success' => 'Role berhasil ditambahkan ke user.']);
	}

	function getAssignedUsers(Request $request)
	{
		$role_id = $request->input('role_id');

		$assignedUsers = User::select('uuid', 'name')
			->whereHas('roles', function ($query) use ($role_id) {
				$query->where('roles.uuid', $role_id);
			})
			->get();

		return response()->json(['assignedUsers' => $assignedUsers]);
	}

	function removeRole(Request $request)
	{
		$request->validate([
			'user_id' => 'required|exists:users,uuid',
			'role_id' => 'required|exists:roles,uuid',
		]);

		$user = User::where('uuid', $request->user_id)->firstOrFail();
		$role = Role::where('uuid', $request->role_id)->firstOrFail();

		$user->roles()->detach($role->id);

		return response()->json(['success' => 'Role berhasil dihapus dari user.']);
	}
}
