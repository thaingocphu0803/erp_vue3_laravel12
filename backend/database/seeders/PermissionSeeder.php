<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::upsert([
			// Department permission
			['name' => 'common.permission.action.show', 'slug'=> 'department.show', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.department'],
			['name' => 'common.permission.action.create', 'slug'=> 'department.create', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.department'],
			['name' => 'common.permission.action.update', 'slug'=> 'department.update', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.department'],
			['name' => 'common.permission.action.delete', 'slug'=> 'department.delete', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.department'],
			// Position permission
			['name' => 'common.permission.action.show', 'slug'=> 'position.show', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.position'],
			['name' => 'common.permission.action.create', 'slug'=> 'position.create', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.position'],
			['name' => 'common.permission.action.update', 'slug'=> 'position.update', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.position'],
			['name' => 'common.permission.action.delete', 'slug'=> 'position.delete', 'supported_scopes' => json_encode(['ALL', 'DEPT', 'OWN']), 'module' => 'common.subModule.position'],

		],
		['slug'],
		['name', 'module', 'supported_scopes']);
    }
}
