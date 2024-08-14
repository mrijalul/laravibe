<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
	use HasFactory;

	protected $fillable = ['uuid', 'name'];

	protected $casts = [
		'uuid' => 'string',
	];

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
	}

	protected static function boot()
	{
		parent::boot();
		static::creating(function ($model) {
			$model->uuid = (string) Str::uuid();
		});
	}
}
