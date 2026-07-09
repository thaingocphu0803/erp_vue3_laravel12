# Đặc tả chức năng phân quyền — HR System

## 1. Mô hình phân quyền: RBAC + ABAC

- **RBAC layer**: Role → Permission (quyền được / không được làm gì)
- **ABAC layer**: Scope trên pivot `role_permissions` (phạm vi dữ liệu được phép xem / thao tác)

### 1.1. Role

| Role | Mô tả |
|---|---|
| Admin | Toàn quyền |
| Department Head | Trưởng phòng — scope = department |
| HR | Nhân sự — scope = all (trừ một số giới hạn đặc thù) |
| Employee | Nhân viên — scope = self |

Một user có thể có **nhiều role** cùng lúc.

### 1.2. Scope

| Scope | Ý nghĩa |
|---|---|
| `all` | Toàn bộ dữ liệu |
| `department` | Chỉ dữ liệu thuộc phòng ban của user (qua `employee.department_id`) |
| `self` | Chỉ dữ liệu của chính user đó (qua `employee.user_id`) |

---

## 2. DB Schema

```sql
-- Bảng role
CREATE TABLE roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL UNIQUE,   -- admin, department_head, hr, employee
    display_name VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng permission
CREATE TABLE permissions (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,   -- employee.view, employee.create, employee.export, attendance.export, department.list, v.v.
    resource    VARCHAR(50)  NOT NULL,          -- employee, attendance, department, salary, leave, position
    action      VARCHAR(50)  NOT NULL,          -- view, create, update, delete, export, list
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_resource_action (resource, action)
);

-- Pivot: role ↔ permission (kèm scope)
CREATE TABLE role_permissions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id         BIGINT UNSIGNED NOT NULL,
    permission_id   BIGINT UNSIGNED NOT NULL,
    scope           ENUM('all', 'department', 'self') NOT NULL DEFAULT 'self',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id)       REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY uk_role_perm (role_id, permission_id)
);

-- Pivot: user ↔ role
CREATE TABLE user_roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    role_id     BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_role (user_id, role_id)
);
```

### 2.1. Bảng user / employee (hiện tại)

```sql
users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100),
    email           VARCHAR(255) UNIQUE,
    password        VARCHAR(255),
    -- không có department_id ở đây
);

employee (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL UNIQUE,
    department_id   BIGINT UNSIGNED NOT NULL,
    -- ... các cột khác
    FOREIGN KEY (user_id)       REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);
```

### 2.2. Ví dụ dữ liệu seed

```sql
-- Permission
INSERT INTO permissions (name, resource, action) VALUES
('employee.view',   'employee',   'view'),
('employee.create', 'employee',   'create'),
('employee.update', 'employee',   'update'),
('employee.delete', 'employee',   'delete'),
('employee.export',  'employee',   'export'),
('attendance.view',  'attendance', 'view'),
('attendance.export', 'attendance', 'export'),
('department.list',  'department', 'list'),
('salary.view',      'salary',     'view'),
('leave.approve',    'leave',      'approve');

-- Role
INSERT INTO roles (name, display_name) VALUES
('admin',           'Admin'),
('department_head', 'Trưởng phòng'),
('hr',              'HR'),
('employee',        'Nhân viên');

-- Gán permission + scope cho từng role
-- Admin: all
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'all'
FROM roles r, permissions p
WHERE r.name = 'admin';

-- HR: employee.{view,create,update} = all, employee.delete = self (chỉ xoá được user do mình tạo? tuỳ logic)
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'all'
FROM roles r, permissions p
WHERE r.name = 'hr'
  AND p.name IN ('employee.view','employee.create','employee.update','attendance.view','attendance.export','salary.view','leave.approve','department.list');
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'self'
FROM roles r, permissions p
WHERE r.name = 'hr'
  AND p.name = 'employee.delete';

-- Department head: employee.view = department, employee.export = department
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'department'
FROM roles r, permissions p
WHERE r.name = 'department_head'
  AND p.name IN ('employee.view','employee.export','attendance.view','department.list');
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'self'
FROM roles r, permissions p
WHERE r.name = 'department_head'
  AND p.name = 'employee.update';

-- Employee: employee.view = self, attendance.view = self
INSERT INTO role_permissions (role_id, permission_id, scope)
SELECT r.id, p.id, 'self'
FROM roles r, permissions p
WHERE r.name = 'employee'
  AND p.name IN ('employee.view','employee.update','attendance.view');
```

---

## 3. Luồng hoạt động (Authorization Flow)

### 3.1. Khi user login

```
User login → backend trả JWT + permissions cache
                                    ↓
                    Frontend lưu permissions vào Pinia Store
                                    ↓
                    Router guard kiểm tra trước khi vào route
                    UI component dùng <Can> / v-can để ẩn/hiện
```

### 3.2. Khi gọi API

```
Client request (kèm Bearer token)
         ↓
Laravel middleware: auth:sanctum/passport
         ↓
Custom middleware: CheckPermission
   - Giải token → user_id
   - Lấy permissions từ Redis (key: permissions:{user_id})
     - Nếu miss → query DB → set Redis (TTL 1 giờ)
   - Parse route → resource + action (VD: GET /api/employees → employee.list)
   - Check user có permission không → không → 403
   - Check scope:
       - all:  cho qua
       - department: filter employee.department_id = auth user's department_id
       - self:   filter employee.user_id = auth()->id()
         ↓
Controller → service → response
```

### 3.3. Cache invalidation

Khi có thay đổi về role / permission / user_role:
- Observer / event: `PermissionUpdated`, `RoleAssigned`, `RoleRemoved`
- Clear cache key `permissions:{user_id}` của user bị ảnh hưởng

---

## 4. Backend — Laravel

### 4.1. Service layer

```
app/Services/PermissionService.php
  - getPermissionsForUser($userId): array   — lấy từ Redis hoặc DB
  - checkPermission($user, $resource, $action): bool
  - applyScopeFilter($query, $resource, $action, $user): Builder
  - clearUserCache($userId): void
```

### 4.2. Middleware

```
app/Http/Middleware/CheckPermission.php
  handle($request, Closure $next, $resource, $action)
```

Gắn vào route:

```php
Route::middleware(['auth:sanctum', 'permission:employee,view'])
    ->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
    });
```

### 4.3. Controller — sử dụng PermissionService

```php
class EmployeeController extends Controller
{
    public function __construct(private PermissionService $permService) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $this->permService->checkPermission($user, 'employee', 'view');

        $query = Employee::query();
        $query = $this->permService->applyScopeFilter($query, 'employee', 'view', $user);

        return $query->paginate();
    }
}
```

### 4.4. API Response — lỗi

```json
// 403
{
    "message": "Bạn không có quyền thực hiện hành động này.",
    "error": "FORBIDDEN",
    "required_permission": "employee.delete"
}
```

### 4.5. Redis cache structure

```json
Key: "permissions:{user_id}"
Value:
{
  "employee": {
    "view":   { "scope": "department" },
    "create": { "scope": "all" },
    "export": { "scope": "department" }
  },
  "attendance": {
    "view":   { "scope": "self" },
    "export": { "scope": "all" }
  },
  "department": {
    "list":   { "scope": "department" }
  },
  "salary": {
    "view":   { "scope": "all" }
  },
  "leave": {
    "approve": { "scope": "all" }
  }
}
TTL: 3600s
```

---

## 5. Frontend — Vue 3 + Vuetify

### 5.1. Pinia Store

```typescript
// stores/permissions.ts
interface PermissionEntry {
  scope: 'all' | 'department' | 'self';
}

interface PermissionMap {
  [resource: string]: {
    [action: string]: PermissionEntry;
  };
}

export const usePermissionStore = defineStore('permissions', () => {
  const permissions = ref<PermissionMap>({});

  async function fetchPermissions() {
    const res = await api.get('/user/permissions');
    permissions.value = res.data;
  }

  function can(resource: string, action: string): boolean {
    return !!permissions.value[resource]?.[action];
  }

  function getScope(resource: string, action: string): string | null {
    return permissions.value[resource]?.[action]?.scope ?? null;
  }

  function clear() {
    permissions.value = {};
  }

  return { permissions, fetchPermissions, can, getScope, clear };
});
```

### 5.2. Vue Router Guard

```typescript
// router/index.ts
router.beforeEach(async (to, from, next) => {
  const store = usePermissionStore();
  const meta = to.meta as { permission?: string };

  if (meta.permission) {
    const [resource, action] = meta.permission.split('.');
    if (!store.can(resource, action)) {
      next({ name: 'forbidden' });
      return;
    }
  }
  next();
});
```

```typescript
// Route definition:
{
  path: '/employees',
  name: 'employees',
  component: EmployeeList,
  meta: { permission: 'employee.view' }
}
```

### 5.3. Component `<Can>`

```vue
<!-- components/Can.vue -->
<template>
  <slot v-if="allowed" />
  <template v-else>
    <slot name="fallback">
      <v-tooltip text="Bạn không có quyền">
        <span><slot name="disabled" /></span>
      </v-tooltip>
    </slot>
  </template>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePermissionStore } from '@/stores/permissions';

const props = defineProps<{
  permission: string;
}>();

const store = usePermissionStore();
const [resource, action] = props.permission.split('.');
const allowed = computed(() => store.can(resource!, action!));
</script>
```

### 5.4. Custom Directive `v-can`

```typescript
// directives/can.ts
import type { Directive } from 'vue';
import { usePermissionStore } from '@/stores/permissions';

export const vCan: Directive = {
  mounted(el: HTMLElement, binding) {
    const store = usePermissionStore();
    const [resource, action] = (binding.value as string).split('.');
    if (!store.can(resource!, action!)) {
      el.parentNode?.removeChild(el);
    }
  }
};
```

### 5.5. Sử dụng trong Vuetify component

```vue
<!-- V dụ: EmployeeList.vue -->
<template>
  <v-data-table :headers="headers" :items="employees">
    <template v-slot:top>
      <Can permission="employee.create">
        <v-btn color="primary" @click="openCreateDialog">
          Thêm nhân viên
        </v-btn>
      </Can>
    </template>

    <template v-slot:item.actions="{ item }">
      <Can permission="employee.update">
        <v-icon @click="edit(item)">mdi-pencil</v-icon>
      </Can>
      <Can permission="employee.delete">
        <v-icon @click="remove(item)">mdi-delete</v-icon>
      </Can>
      <Can permission="employee.export">
        <v-icon @click="exportCsv(item)">mdi-download</v-icon>
      </Can>
    </template>
  </v-data-table>
</template>
```

### 5.6. Cache invalidation (Pinia)

Khi user logout → `store.clear()`.
Khi role thay đổi (ví dụ Admin gán role mới cho user):
- Backend clear Redis cache của user đó
- Trong cùng phiên: frontend gọi lại `fetchPermissions()` (hoặc có thể dùng WebSocket/SSE notification)

---

## 6. Ví dụ chi tiết: Employee List

**Tình huống:** Department Head (K. Trưởng phòng Kỹ thuật, department_id = 1) gọi `GET /api/employees`.

### Backend flow

1. Middleware `CheckPermission` parse route → resource = `employee`, action = `view`
2. Redis: `permissions:{user_id}` → tìm `employee.view.scope = 'department'`
3. `applyScopeFilter`: thêm `WHERE employee.department_id = 1`
4. Controller trả về danh sách employee của phòng Kỹ thuật

### API Response

```json
GET /api/employees?page=1
Authorization: Bearer xxx

Response 200:
{
  "data": [
    { "id": 1, "name": "Nguyễn Văn A", "department": { "id": 1, "name": "Kỹ thuật" } },
    { "id": 2, "name": "Trần Thị B", "department": { "id": 1, "name": "Kỹ thuật" } }
  ],
  "meta": { "total": 2, "page": 1 }
}
```

### Frontend

- Department Head **không thấy** button "Thêm nhân viên" (không có `employee.create`)
- Department Head **thấy** icon export (có `employee.export scope = department`)
- Employee (scope = self) chỉ thấy chính họ, API cũng trả đúng 1 record

---

## 8. Admin UI — Role Detail

### 8.1. Permission assignment: dev-defined

Permission được **lập trình viên seed qua migration** khi thêm module mới. Admin **không tạo** permission mới qua UI — chỉ việc gán permission có sẵn vào role.

Luồng:
```
Dev thêm module "Salary"
  → viết migration seed salary.view, salary.create, salary.update, salary.delete
  → deploy
  → Admin vào Role Detail, thấy permission mới hiện ra
  → tick checkbox + chọn scope
```

### 8.2. UI layout — Role Detail page

```
┌─────────────────────────────────────────────────┐
│  ← Roles                        Trạng thái: Đang edit │
│                                                   │
│  ┌─────────────────────────────────────────────┐  │
│  │  Tên role: [Department Head           ]     │  │
│  │  Mô tả:    [Trưởng phòng              ]     │  │
│  └─────────────────────────────────────────────┘  │
│                                                   │
│  ┌─ Permission ───────────────┬──────────┬──────┐ │
│  │  Resource / Action         │ Allowed  │Scope │ │
│  ├────────────────────────────┼──────────┼──────┤ │
│  │  ▸ Employee (4/5)          │          │      │ │
│  │    □ View                  │ ✔        │ dept │ │
│  │    □ Create                │ ☐        │  —   │ │
│  │    □ Update                │ ✔        │ self │ │
│  │    □ Delete                │ ☐        │  —   │ │
│  │    □ Export                │ ✔        │ dept │ │
│  ├────────────────────────────┼──────────┼──────┤ │
│  │  ▸ Attendance (2/2)       │          │      │ │
│  │    □ View                  │ ✔        │ dept │ │
│  │    □ Export                │ ☐        │  —   │ │
│  ├────────────────────────────┼──────────┼──────┤ │
│  │  ▸ Salary (1/4)           │          │      │ │
│  │    □ View                  │ ✔        │ all  │ │
│  │    □ Create                │ ☐        │  —   │ │
│  │    □ Update                │ ☐        │  —   │ │
│  │    □ Delete                │ ☐        │  —   │ │
│  ├────────────────────────────┼──────────┼──────┤ │
│  │  ▸ Department (1/1)       │          │      │ │
│  │    □ List                  │ ✔        │ dept │ │
│  ├────────────────────────────┼──────────┼──────┤ │
│  │  ▸ Leave (1/1)            │          │      │ │
│  │    □ Approve               │ ☐        │  —   │ │
│  └────────────────────────────┴──────────┴──────┘ │
│                                                   │
│  [ Cancel ]              [ Save Changes ]         │
└─────────────────────────────────────────────────┘
```

### 8.3. Component tree

```
RoleDetailPage.vue
├── v-text-field: name
├── v-text-field: display_name
├── v-expansion-panels (mỗi panel = một resource)
│   └── v-expansion-panel
│       ├── v-checkbox: "Chọn tất cả action của resource này"
│       └── v-row (mỗi row = một action)
│           ├── v-checkbox: allowed
│           └── v-select: scope (all / department / self)
│               └── disabled nếu checkbox tắt
└── v-btn: Cancel + Save
```

### 8.4. API

```http
GET /api/admin/roles/{role}
→ { id, name, display_name, permissions: [{ id, resource, action, pivot: { scope } }, ...] }

PUT /api/admin/roles/{role}/permissions
Body:
{
  "permissions": [
    { "permission_id": 1, "scope": "department" },
    { "permission_id": 3, "scope": "self" }
  ]
}
→ Xoá các pivot cũ, insert các pivot mới (sync)
→ Backend clear cache của tất cả user có role này

GET /api/admin/permissions
→ Danh sách tất cả permission (để frontend render các hàng)
```

---

## 9. Tổng kết các thành phần

| Layer | Công nghệ | Vai trò |
|---|---|---|
| DB | MySQL | roles, permissions, role_permissions, user_roles |
| Cache | Redis | permissions:{user_id} — TTL 1h |
| Backend auth | Laravel Sanctum/Passport | JWT token |
| Backend permission | CheckPermission middleware + PermissionService | Check + scope filter query |
| Frontend state | Pinia Store | permissions map |
| Frontend UI | <Can> / v-can / Router guard | Ẩn/hiện theo permission |
