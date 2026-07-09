# Service & Middleware Implementation

---

## 1. Models

### Permission Model

```php
<?php
// app/Models/Permission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'resource', 'action'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withPivot('scope')
                    ->withTimestamps();
    }
}
```

### Role Model

```php
<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withPivot('scope')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }
}
```

### User Model (thêm relationship)

```php
<?php
// app/Models/User.php

// Thêm:
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
}

public function employee()
{
    return $this->hasOne(Employee::class);
}
```

## 2. PermissionService

```php
<?php
// app/Services/PermissionService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    private const CACHE_TTL = 3600;

    /**
     * Lấy permissions của user — từ Redis (Cache) nếu có, nếu không query DB.
     *
     * Return format:
     * [
     *   'employee' => [
     *     'view'   => ['scope' => 'department'],
     *     'create' => ['scope' => 'all'],
     *   ],
     *   'attendance' => [
     *     'view' => ['scope' => 'self'],
     *   ],
     * ]
     */
    public function getPermissionsForUser(int $userId): array
    {
        $cacheKey = "permissions:{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return $this->buildPermissionsFromDb($userId);
        });
    }

    /**
     * Query DB và build structured permissions map.
     */
    private function buildPermissionsFromDb(int $userId): array
    {
        $user = User::with('roles.permissions')->find($userId);

        if (!$user || $user->roles->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($user->roles as $role) {
            foreach ($role->permissions as $perm) {
                $resource = $perm->resource;
                $action   = $perm->action;
                $scope    = $perm->pivot->scope;

                // Nếu user có nhiều role, ưu tiên scope có quyền rộng hơn:
                // all > department > self
                $current = $result[$resource][$action]['scope'] ?? 'self';

                $priority = ['self' => 1, 'department' => 2, 'all' => 3];

                if ($priority[$scope] > $priority[$current]) {
                    $result[$resource][$action] = ['scope' => $scope];
                }
            }
        }

        return $result;
    }

    /**
     * Kiểm tra user có permission resource.action không.
     * Trả về true/false.
     */
    public function checkPermission(User $user, string $resource, string $action): bool
    {
        $perms = $this->getPermissionsForUser($user->id);
        return isset($perms[$resource][$action]);
    }

    /**
     * Lấy scope của resource.action cho user.
     * Trả về null nếu không có permission.
     */
    public function getScope(User $user, string $resource, string $action): ?string
    {
        $perms = $this->getPermissionsForUser($user->id);
        return $perms[$resource][$action]['scope'] ?? null;
    }

    /**
     * Apply scope filter lên query builder.
     * Tuỳ scope, thêm WHERE condition tương ứng.
     *
     * @param Builder $query  — Query đang xây dựng (VD: Employee::query())
     * @param string  $table  — Tên bảng chính (VD: 'employee') để prefix column
     */
    public function applyScopeFilter(Builder $query, string $resource, string $action, User $user): Builder
    {
        $scope = $this->getScope($user, $resource, $action);

        if ($scope === 'all') {
            return $query;
        }

        if ($scope === 'department') {
            $departmentId = $user->employee?->department_id;
            if ($departmentId) {
                return $query->whereHas('employee', fn ($q) =>
                    $q->where('department_id', $departmentId)
                );
            }
            // Nếu user không có employee → trả về empty
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Clear cache cho một user.
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("permissions:{$userId}");
    }

    /**
     * Clear cache cho nhiều user.
     */
    public function clearUsersCache(array $userIds): void
    {
        foreach ($userIds as $id) {
            Cache::forget("permissions:{$id}");
        }
    }
}
```

## 3. Middleware

```php
<?php
// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Sử dụng: Route::middleware('permission:employee,view')
     *          Route::middleware('permission:employee,delete')
     */
    public function handle(Request $request, Closure $next, string $resource, string $action): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!$this->permissionService->checkPermission($user, $resource, $action)) {
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện hành động này.',
                'error'   => 'FORBIDDEN',
                'required_permission' => "{$resource}.{$action}",
            ], 403);
        }

        // Gắn permission + scope vào request để controller dùng
        $request->merge([
            'permission_resource' => $resource,
            'permission_action'   => $action,
            'permission_scope'    => $this->permissionService->getScope($user, $resource, $action),
        ]);

        return $next($request);
    }
}
```

## 4. Đăng ký middleware

```php
<?php
// bootstrap/app.php (Laravel 11)
// — hoặc — app/Http/Kernel.php (Laravel 10)

use App\Http\Middleware\CheckPermission;

// Laravel 11 — bootstrap/app.php:
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(...)
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
    })
    ->create();

// Laravel 10 — app/Http/Kernel.php:
// protected $middlewareAliases = [
//     'permission' => \App\Http\Middleware\CheckPermission::class,
// ];
```

## 5. Route example

```php
<?php
// routes/api.php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // User permissions
    Route::get('/user/permissions', [PermissionController::class, 'myPermissions']);

    // Employee
    Route::middleware('permission:employee,view')
         ->get('/employees', [EmployeeController::class, 'index']);

    Route::middleware('permission:employee,create')
         ->post('/employees', [EmployeeController::class, 'store']);

    Route::middleware('permission:employee,update')
         ->put('/employees/{employee}', [EmployeeController::class, 'update']);

    Route::middleware('permission:employee,delete')
         ->delete('/employees/{employee}', [EmployeeController::class, 'destroy']);

    Route::middleware('permission:employee,export')
         ->get('/employees/export', [EmployeeController::class, 'export']);
});
```

## 6. Controller example

```php
<?php
// app/Http/Controllers/Api/EmployeeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Employee::with('user', 'department');
        $query = $this->permissionService->applyScopeFilter(
            $query,
            $request->input('permission_resource'),
            $request->input('permission_action'),
            $user
        );

        return response()->json(
            $query->paginate(20)
        );
    }

    public function store(Request $request)
    {
        // Chỉ cần middleware check create → scope check ít cần vì create không filter
        $employee = Employee::create($request->validated());
        return response()->json($employee, 201);
    }

    public function update(Request $request, Employee $employee)
    {
        $user = $request->user();

        // Kiểm tra scope cho update (self chỉ sửa được chính mình)
        $scope = $this->permissionService->getScope($user, 'employee', 'update');
        if ($scope === 'self' && $employee->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee->update($request->validated());
        return response()->json($employee);
    }

    public function destroy(Request $request, Employee $employee)
    {
        $user = $request->user();

        $scope = $this->permissionService->getScope($user, 'employee', 'delete');
        if ($scope === 'self' && $employee->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee->delete();
        return response()->json(null, 204);
    }
}
```

## 7. PermissionController (trả permissions cho frontend)

```php
<?php
// app/Http/Controllers/Api/PermissionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function myPermissions(Request $request)
    {
        $user  = $request->user();
        $perms = $this->permissionService->getPermissionsForUser($user->id);

        return response()->json($perms);
    }
}
```

## 8. Observer — cache invalidation

```php
<?php
// app/Observers/UserRoleObserver.php

namespace App\Observers;

use App\Models\Role;
use App\Models\User;
use App\Services\PermissionService;

class UserRoleObserver
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Khi user được gán role mới (pivot sync/attach).
     */
    public function roleAttached(User $user): void
    {
        $this->permissionService->clearUserCache($user->id);
    }

    /**
     * Khi user bị xoá role (pivot detach).
     */
    public function roleDetached(User $user): void
    {
        $this->permissionService->clearUserCache($user->id);
    }
}
```

Đăng ký observer trong `AppServiceProvider`:

```php
// AppServiceProvider::boot()
User::observe(UserRoleObserver::class);
```

Hoặc dùng model event trong `User` model:

```php
protected static function booted(): void
{
    static::saved(function (User $user) {
        if ($user->roles()->wasChanged()) {
            app(PermissionService::class)->clearUserCache($user->id);
        }
    });
}
```
