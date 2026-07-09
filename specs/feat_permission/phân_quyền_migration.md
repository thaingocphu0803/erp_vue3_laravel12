# Migration — Database cho phân quyền

Tạo lần lượt theo thứ tự dưới đây.

---

## 1. Migration: roles

```php
<?php
// database/migrations/2026_01_01_000001_create_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

## 2. Migration: permissions

```php
<?php
// database/migrations/2026_01_01_000002_create_permissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('resource', 50);
            $table->string('action', 50);
            $table->timestamps();
            $table->unique(['resource', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
```

## 3. Migration: role_permissions

```php
<?php
// database/migrations/2026_01_01_000003_create_role_permissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->enum('scope', ['all', 'department', 'self'])->default('self');
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
```

## 4. Migration: user_roles

```php
<?php
// database/migrations/2026_01_01_000004_create_user_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
```

## 5. Seeder: permissions + roles (tham khảo)

```php
<?php
// database/seeders/PermissionSeeder.php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // --- Permissions ---
        $perms = [
            ['name' => 'employee.view',   'resource' => 'employee',   'action' => 'view'],
            ['name' => 'employee.create', 'resource' => 'employee',   'action' => 'create'],
            ['name' => 'employee.update', 'resource' => 'employee',   'action' => 'update'],
            ['name' => 'employee.delete', 'resource' => 'employee',   'action' => 'delete'],
            ['name' => 'employee.export',  'resource' => 'employee',   'action' => 'export'],
            ['name' => 'attendance.view',  'resource' => 'attendance', 'action' => 'view'],
            ['name' => 'attendance.export', 'resource' => 'attendance', 'action' => 'export'],
            ['name' => 'department.list',  'resource' => 'department', 'action' => 'list'],
            ['name' => 'salary.view',      'resource' => 'salary',     'action' => 'view'],
            ['name' => 'leave.approve',    'resource' => 'leave',      'action' => 'approve'],
        ];
        foreach ($perms as $p) {
            Permission::create($p);
        }

        // --- Roles ---
        $admin   = Role::create(['name' => 'admin',           'display_name' => 'Admin']);
        $deptHead = Role::create(['name' => 'department_head','display_name' => 'Trưởng phòng']);
        $hr      = Role::create(['name' => 'hr',              'display_name' => 'HR']);
        $emp     = Role::create(['name' => 'employee',        'display_name' => 'Nhân viên']);

        // --- Role ↔ Permission ---
        // Admin: tất cả scope = all
        foreach (Permission::all() as $p) {
            $admin->permissions()->attach($p->id, ['scope' => 'all']);
        }

        // HR: employee.view, employee.create, employee.update, attendance.*, salary.view, leave.approve, department.list = all
        //     employee.delete = self
        $hr->permissions()->attach(
            Permission::whereIn('name', [
                'employee.view','employee.create','employee.update',
                'attendance.view','attendance.export',
                'salary.view','leave.approve','department.list',
            ])->pluck('id'),
            ['scope' => 'all']
        );
        $hr->permissions()->attach(
            Permission::where('name', 'employee.delete')->first()->id,
            ['scope' => 'self']
        );

        // Department Head: employee.view, employee.export, attendance.view, department.list = department
        //                   employee.update = self
        $deptHead->permissions()->attach(
            Permission::whereIn('name', [
                'employee.view','employee.export','attendance.view','department.list',
            ])->pluck('id'),
            ['scope' => 'department']
        );
        $deptHead->permissions()->attach(
            Permission::where('name', 'employee.update')->first()->id,
            ['scope' => 'self']
        );

        // Employee: employee.view, employee.update, attendance.view = self
        $emp->permissions()->attach(
            Permission::whereIn('name', [
                'employee.view','employee.update','attendance.view',
            ])->pluck('id'),
            ['scope' => 'self']
        );
    }
}
```

> **Ghi chú:** Models `Permission` và `Role` cần khai báo `belongsToMany` với `->withPivot('scope')`. Xem phần service để biết chi tiết.
