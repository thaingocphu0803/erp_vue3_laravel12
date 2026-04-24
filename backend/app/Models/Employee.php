<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
	protected $searchable = [
		'name',
		'code'
	];

	protected $fillable = [
		'user_id',
		'code',
		'address',
		'phone_number',
		'gender',
		'birth_date',
		'avatar',
		'department_id',
		'position_id',
		'is_leader',
		'name',
		'ward_code',
		'province_code',
		'created_by',
		'updated_by',
	];

	public function credential()
	{
		return $this->belongsTo(User::class);
	}

	public function department()
	{
		return $this->belongsTo(Department::class);
	}

	public function position()
	{
		return $this->belongsTo(Position::class);
	}

	public function ward()
	{
		return $this->belongsTo(Ward::class);
	}

	public function province()
	{
		return $this->belongsTo(Province::class);
	}
}
