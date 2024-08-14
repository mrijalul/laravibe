<?php

namespace App\Http\Controllers;

use App\Models\Permission;
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
			$data = Role::with('users')->select(['id', 'uuid', 'name', 'created_at'])->orderBy('created_at', 'desc');
			return DataTables::of($data)
				->addColumn('users', function ($row) {
					if ($row->users->isEmpty()) {
						return '<span class="badge text-bg-warning">Belum memiliki relasi</span>';
					} else {
						return $row->users->map(function ($user) {
							return '<span class="badge text-bg-success">' . $user->name . '</span>';
						})->implode(' ');
					}
				})
				->addColumn('action', function ($row) {
					return '<button class="btn btn-sm btn-warning editData" data-uuid="' . $row->uuid . '"><i class="bi bi-pencil-square"></i></button>
					<button class="btn btn-sm btn-danger deleteData" data-uuid="' . $row->uuid . '"><i class="bi bi-trash"></i></button>';
				})
				->addColumn('role', function ($row) {
					return '<button class="btn btn-sm btn-info assignRole" data-rolename="' . $row->name . '" data-uuid="' . $row->uuid . '"><i class="bi bi-person-plus"></i></button>';
				})
				->addColumn('permission', function ($row) {
					return '<button class="btn btn-sm btn-info assignPermission" data-rolename="' . $row->name . '" data-uuid="' . $row->uuid . '"><i class="bi bi-shield-plus"></i></button>';
				})
				->rawColumns(['action', 'users', 'role', 'permission'])
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
		$request->validate([
			'user_id' => 'required|array',
			'user_id.*' => 'exists:users,uuid',
			'role_id' => 'required|exists:roles,uuid',
		]);

		$role = Role::where('uuid', $request->role_id)->firstOrFail();

		$user_ids = User::whereIn('uuid', $request->user_id)->pluck('id');

		$role->users()->sync($user_ids);

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

	function assignPermissions(Request $request)
	{
		$request->validate([
			'role_id' => 'required|exists:roles,uuid',
			'permission_id' => 'required|array',
			'permission_id.*' => 'exists:permissions,uuid',
		]);

		$role = Role::where('uuid', $request->role_id)->firstOrFail();
		$permissionIds = Permission::whereIn('uuid', $request->permission_id)->pluck('id');

		$role->permissions()->sync($permissionIds);

		return response()->json(['success' => 'Permissions berhasil di-assign ke role.']);
	}

	function getPermissionsData(Request $request)
	{
		$search = $request->input('search');
		$permissions = Permission::where('name', 'like', "%{$search}%")
			->get(['id', 'uuid', 'name']);

		return response()->json(['permissions' => $permissions]);
	}

	function getAssignedPermissions(Request $request)
	{
		$role_id = $request->input('role_id');

		$assignedPermissions = Permission::select('uuid', 'name')
			->whereHas('roles', function ($query) use ($role_id) {
				$query->where('roles.uuid', $role_id);
			})
			->get();

		return response()->json(['assignedPermissions' => $assignedPermissions]);
	}
}
