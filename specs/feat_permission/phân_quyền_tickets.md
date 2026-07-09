# Ticket breakdown — Phân quyền HR System

Thứ tự ưu tiên từ trên xuống.

---

## T1 — DB Migration + Seeder

**Mô tả:** Tạo migrations và seeder cho hệ thống phân quyền.

**Checklist:**
- [ ] Migration: `roles` table
- [ ] Migration: `permissions` table
- [ ] Migration: `role_permissions` table
- [ ] Migration: `user_roles` table
- [ ] Seeder: danh sách permissions (employee.*, attendance.*, v.v.)
- [ ] Seeder: 4 role (admin, department_head, hr, employee)
- [ ] Seeder: gán permission + scope cho từng role
- [ ] Chạy `php artisan migrate:fresh --seed` — verify data

**Phụ thuộc:**
- Nhánh: `feature/permission-db`

---

## T2 — Models + Relationships

**Mô tả:** Khai báo Eloquent models và relationships.

**Checklist:**
- [ ] `Role` model: `belongsToMany(Permission)` với `withPivot('scope')`
- [ ] `Role` model: `belongsToMany(User)`
- [ ] `Permission` model: `belongsToMany(Role)` với `withPivot('scope')`
- [ ] `User` model: thêm `belongsToMany(Role)`
- [ ] `User` model: thêm `hasOne(Employee)`
- [ ] `Employee` model: thêm `belongsTo(User)`
- [ ] Test: tạo user gán role — verify relationships hoạt động

**Phụ thuộc:** T1
**Nhánh:** `feature/permission-models`

---

## T3 — PermissionService

**Mô tả:** Service xử lý permission check + scope filter + Redis cache.

**Checklist:**
- [ ] `PermissionService::getPermissionsForUser()` — query DB → build map
- [ ] `PermissionService::checkPermission()` — check existence
- [ ] `PermissionService::getScope()` — lấy scope
- [ ] `PermissionService::applyScopeFilter()` — filter query theo scope
- [ ] `PermissionService::clearUserCache()` — xoá Redis cache
- [ ] Cache: key `permissions:{user_id}`, TTL 3600s
- [ ] Test: mock DB → verify cache hit/miss, scope merge ưu tiên

**Phụ thuộc:** T2
**Nhánh:** `feature/permission-service`

---

## T4 — Middleware + Routes

**Mô tả:** CheckPermission middleware và gắn vào route.

**Checklist:**
- [ ] Middleware `CheckPermission`: check auth → check permission → 403 hoặc pass
- [ ] Middleware gắn `permission_resource`, `permission_action`, `permission_scope` vào request
- [ ] Đăng ký alias `'permission'` trong kernel
- [ ] Route: `GET /user/permissions`
- [ ] Route nhóm Employee (index, store, update, delete, export) — mỗi route kèm middleware permission
- [ ] Test: gọi API với token — verify 200/403

**Phụ thuộc:** T3
**Nhánh:** `feature/permission-middleware`

---

## T5 — Controller scope check + PermissionController

**Mô tả:** Áp dụng scope filter trong controller và API trả permissions cho frontend.

**Checklist:**
- [ ] `EmployeeController::index()`: apply scope filter
- [ ] `EmployeeController::update()`: check scope self
- [ ] `EmployeeController::destroy()`: check scope self
- [ ] `PermissionController::myPermissions()`: trả về permission map
- [ ] Test: Department Head chỉ thấy employee phòng mình
- [ ] Test: Employee chỉ thấy chính họ
- [ ] Test: Admin thấy tất cả

**Phụ thuộc:** T4
**Nhánh:** `feature/permission-controller`

---

## T6 — Cache Invalidation

**Mô tả:** Observer/event xoá Redis cache khi role/permission thay đổi.

**Checklist:**
- [ ] Observer `UserRoleObserver` — clear cache khi attach/detach role
- [ ] Hoặc model event trong `User::booted()`
- [ ] Khi Admin thay đổi permission của role → clear cache cho tất cả user có role đó
- [ ] Test: gán role mới cho user → API cũ trả 403 → API mới trả 200 (sau cache miss)

**Phụ thuộc:** T3
**Nhánh:** `feature/permission-cache`

---

## T7 — Frontend: Pinia Store

**Mô tả:** Store lưu permissions, fetch từ API.

**Checklist:**
- [ ] `stores/permissions.ts`: define store, state `permissions`, action `fetchPermissions()`, `can()`, `getScope()`, `clear()`
- [ ] Gọi `fetchPermissions()` sau login
- [ ] Gọi `clear()` khi logout

**Phụ thuộc:** T5
**Nhánh:** `feature/permission-pinia`

---

## T8 — Frontend: Router Guard

**Mô tả:** Guard chặn route không có permission.

**Checklist:**
- [ ] `router/index.ts`: `beforeEach` check `to.meta.permission`
- [ ] Route `/forbidden` — trang báo lỗi
- [ ] Gắn `meta: { permission: 'employee.view' }` vào các route cần check
- [ ] Test: vào route không có permission → redirect /forbidden

**Phụ thuộc:** T7
**Nhánh:** `feature/permission-router-guard`

---

## T9 — Frontend: <Can> component + v-can directive

**Mô tả:** UI component/directive ẩn/hiện element.

**Checklist:**
- [ ] `components/Can.vue`: slots `default`, `fallback`, `disabled`
- [ ] `directives/can.ts`: custom directive `v-can`, remove element nếu không có quyền
- [ ] Áp dụng vào EmployeeList.vue: button Thêm, icon Sửa/Xoá/Export
- [ ] Test: Employee không thấy nút "Thêm nhân viên"

**Phụ thuộc:** T7
**Nhánh:** `feature/permission-ui`

---

## T10 — Admin UI quản lý role + permission

**Mô tả:** Giao diện Admin quản lý role (CRUD) và gán permission cho role.

### 10a — Backend API

**Checklist:**
- [ ] API `GET /api/admin/roles` — danh sách roles
- [ ] API `POST /api/admin/roles` — tạo role mới
- [ ] API `GET /api/admin/roles/{role}` — role detail + permissions kèm pivot scope
- [ ] API `PUT /api/admin/roles/{role}` — cập nhật tên role
- [ ] API `DELETE /api/admin/roles/{role}` — xoá role
- [ ] API `GET /api/admin/permissions` — danh sách tất cả permissions (nhóm theo resource)
- [ ] API `PUT /api/admin/roles/{role}/permissions` — sync permissions + scope
      Body: `{ permissions: [{ permission_id, scope }, ...] }`
- [ ] API `GET /api/admin/users` — danh sách users (để gán role)
- [ ] API `POST /api/admin/users/{user}/roles` — sync role cho user
- [ ] Cache: sau khi sync permission → clear cache tất cả user có role đó

### 10b — Frontend: Role List

**Mô tả:** Trang danh sách role, có nút Create + Edit + Delete.

**Checklist:**
- [ ] `pages/admin/roles/RoleList.vue` — v-data-table roles
- [ ] Dialog Create/Edit Role (name + display_name)
- [ ] Confirm dialog trước khi delete role
- [ ] Link to Role Detail (click row hoặc button)

### 10c — Frontend: Role Detail (Permission gán)

**Mô tả:** Trang chi tiết role, hiển thị tất cả permission theo nhóm resource.

**UI layout (Vuetify):**

```
RoleDetailPage.vue
├── v-text-field: name
├── v-text-field: display_name
├── v-expansion-panels (mỗi panel = một resource)
│   └── v-expansion-panel
│       ├── v-checkbox: "Chọn tất cả" (toggle all actions trong resource)
│       └── v-row (mỗi row = một action)
│           ├── v-checkbox: allowed (v-model: pivot tồn tại / không)
│           └── v-select: scope (all / department / self)
│               └── :disabled="!checkbox.checked"
└── v-btn: Cancel + Save
```

**Checklist:**
- [ ] `pages/admin/roles/RoleDetail.vue`
- [ ] v-expansion-panels nhóm permission theo resource
- [ ] Mỗi expansion panel: checkbox "Chọn tất cả" → toggle all actions
- [ ] Mỗi action row: checkbox allowed + v-select scope
- [ ] v-select scope disabled khi checkbox tắt
- [ ] Khi Save: gọi API `PUT .../roles/{role}/permissions`
- [ ] Backend trả về thành công → toast "Đã lưu"

### 10d — Frontend: Gán Role cho User

**Mô tả:** Modal trên User List để chọn role.

**Checklist:**
- [ ] `pages/admin/users/UserList.vue` — cột "Roles" hiển thị chip
- [ ] Dialog: danh sách roles với v-checkbox (multi-select)
- [ ] Save → gọi API sync roles

**Phụ thuộc:** T9
**Nhánh:** `feature/permission-admin-ui`
