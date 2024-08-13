<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
	function getdata(Request $request)
	{
		if ($request->ajax()) {
			$permissions = Permission::select(['id', 'uuid', 'name']);

			return datatables()->eloquent($permissions)
				->addColumn('action', function ($row) {
					return '
				<button class="btn btn-sm btn-warning editData" data-uuid="' . $row->uuid . '"><i class="bi bi-pencil-square"></i></button>
				<button class="btn btn-sm btn-danger deleteData" data-uuid="' . $row->uuid . '"><i class="bi bi-trash"></i></button>';
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	function index()
	{
		return view('backend.permission.index');
	}

	function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255|unique:permissions,name,' . $request->input('permission_id') . ',uuid',
		], [
			'name.required' => 'Nama permission tidak boleh kosong.',
			'name.max' => 'Nama permission tidak boleh lebih dari 255 karakter.',
			'name.unique' => 'Nama permission sudah digunakan.',
		]);

		if ($request->has('permission_id') && !empty($request->input('permission_id'))) {

			$permission = Permission::where('uuid', $request->input('permission_id'))->firstOrFail();
			$permission->name = $request->input('name');
			$permission->save();

			return response()->json(['success' => 'Permission berhasil diupdate.']);
		} else {
			$permission = Permission::create([
				'name' => $request->input('name'),
				'uuid' => (string) Str::uuid(),
			]);

			return response()->json(['success' => 'Permission berhasil ditambahkan.']);
		}
	}

	function edit($uuid)
	{
		$permission = Permission::where('uuid', $uuid)->firstOrFail();
		return response()->json($permission);
	}

	function destroy($uuid)
	{
		$permission = Permission::where('uuid', $uuid)->firstOrFail();
		$permission->delete();

		return response()->json(['success' => 'Permission berhasil dihapus.']);
	}
}
