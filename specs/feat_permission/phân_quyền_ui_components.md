# UI Component Division — Frontend phân quyền

Cấu trúc thư mục:

```
src/
  components/
    Can.vue                    -- Component wrapper
  directives/
    can.ts                     -- Custom directive
  stores/
    permissions.ts             -- Pinia store
  router/
    index.ts                   -- Router guard
  pages/
    admin/
      RoleList.vue             -- (T10) Danh sách role + gán permission
      UserRoleManager.vue      -- (T10) Gán role cho user
    employees/
      EmployeeList.vue         -- Danh sách nhân viên
    Forbidden.vue              -- Trang 403
  App.vue
```

---

## 1. Can.vue — Component

```vue
<!-- src/components/Can.vue -->
<template>
  <slot v-if="allowed" />
  <v-tooltip v-else :text="tooltip" location="bottom">
    <template v-slot:activator="{ props }">
      <span v-bind="props" class="pe-none opacity-50">
        <slot name="disabled" />
      </span>
    </template>
  </v-tooltip>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePermissionStore } from '@/stores/permissions'

const props = defineProps<{
  permission: string
  tooltip?: string
}>()

const store = usePermissionStore()
const [resource, action] = props.permission.split('.')
const allowed = computed(() => store.can(resource!, action!))
</script>
```

### Sử dụng:

```vue
<!-- Hiện button nếu có quyền -->
<Can permission="employee.create">
  <v-btn color="primary">Thêm nhân viên</v-btn>
  <template #disabled>
    <v-btn disabled>Thêm nhân viên</v-btn>
  </template>
</Can>

<!-- Export — disable thay vì ẩn -->
<Can permission="employee.export" tooltip="Chỉ HR và Admin mới export được">
  <template #disabled>
    <v-btn disabled>
      <v-icon>mdi-download</v-icon> Export CSV
    </v-btn>
  </template>
  <v-btn @click="exportCsv">
    <v-icon>mdi-download</v-icon> Export CSV
  </v-btn>
</Can>
```

---

## 2. can.ts — Directive

```typescript
// src/directives/can.ts
import type { Directive, DirectiveBinding } from 'vue'
import { usePermissionStore } from '@/stores/permissions'

export const vCan: Directive<HTMLElement, string> = {
  mounted(el: HTMLElement, binding: DirectiveBinding<string>) {
    const store = usePermissionStore()
    const [resource, action] = binding.value.split('.')
    if (!store.can(resource!, action!)) {
      el.remove()
    }
  },
}
```

### Sử dụng:

```vue
<v-icon v-can="'employee.delete'" @click="remove(item)">mdi-delete</v-icon>
```

---

## 3. Pinia Store — permissions.ts

```typescript
// src/stores/permissions.ts
import { defineStore } from 'pinia'
import { api } from '@/lib/axios'

interface ScopeEntry {
  scope: 'all' | 'department' | 'self'
}

type PermissionMap = Record<string, Record<string, ScopeEntry>>

export const usePermissionStore = defineStore('permissions', () => {
  const permissions = ref<PermissionMap>({})

  async function fetchPermissions() {
    try {
      const res = await api.get<PermissionMap>('/user/permissions')
      permissions.value = res.data
    } catch {
      permissions.value = {}
    }
  }

  function can(resource: string, action: string): boolean {
    return !!permissions.value[resource]?.[action]
  }

  function getScope(resource: string, action: string): string | null {
    return permissions.value[resource]?.[action]?.scope ?? null
  }

  function clear() {
    permissions.value = {}
  }

  return { permissions, fetchPermissions, can, getScope, clear }
})
```

---

## 4. Router Guard

```typescript
// src/router/index.ts
import { createRouter, createWebHistory } from 'vue-router'
import { usePermissionStore } from '@/stores/permissions'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/employees',
      name: 'employees',
      component: () => import('@/pages/employees/EmployeeList.vue'),
      meta: { permission: 'employee.view' },
    },
    {
      path: '/employees/create',
      name: 'employee-create',
      component: () => import('@/pages/employees/EmployeeForm.vue'),
      meta: { permission: 'employee.create' },
    },
    {
      path: '/forbidden',
      name: 'forbidden',
      component: () => import('@/pages/Forbidden.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/pages/NotFound.vue'),
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const store = usePermissionStore()

  // Nếu chưa có permissions và không phải trang public → fetch
  if (Object.keys(store.permissions).length === 0 && to.name !== 'login') {
    await store.fetchPermissions()
  }

  const permission = to.meta.permission as string | undefined
  if (permission) {
    const [resource, action] = permission.split('.')
    if (!store.can(resource!, action!)) {
      next({ name: 'forbidden' })
      return
    }
  }
  next()
})
```

---

## 5. App.vue — setup permissions sau login

```vue
<!-- src/App.vue -->
<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { usePermissionStore } from '@/stores/permissions'

const auth = useAuthStore()
const perm  = usePermissionStore()

onMounted(async () => {
  if (auth.isLoggedIn) {
    await perm.fetchPermissions()
  }
})
</script>
```

---

## 6. Forbidden.vue

```vue
<!-- src/pages/Forbidden.vue -->
<template>
  <v-container class="text-center" style="margin-top: 80px">
    <v-icon size="80" color="warning">mdi-shield-off</v-icon>
    <h1 class="text-h4 mt-4">403</h1>
    <p class="text-body-1 mt-2">Bạn không có quyền truy cập trang này.</p>
    <v-btn color="primary" class="mt-4" @click="$router.push('/')">
      Về trang chủ
    </v-btn>
  </v-container>
</template>
```

---

## 7. EmployeeList.vue — full example

```vue
<!-- src/pages/employees/EmployeeList.vue -->
<template>
  <v-container>
    <v-row class="align-center mb-4">
      <v-col><h2>Danh sách nhân sự</h2></v-col>
      <v-col class="text-right">
        <Can permission="employee.create" tooltip="Chỉ HR và Admin mới thêm được">
          <template #disabled>
            <v-btn disabled color="primary">Thêm nhân sự</v-btn>
          </template>
          <v-btn color="primary" @click="openCreate">Thêm nhân sự</v-btn>
        </Can>
      </v-col>
    </v-row>

    <!-- data table -->
    <v-data-table :headers="headers" :items="employees" :loading="loading">
      <!-- cột hành động -->
      <template v-slot:item.actions="{ item }">
        <Can permission="employee.update">
          <v-icon class="mr-2" @click="edit(item)">mdi-pencil</v-icon>
        </Can>
        <Can permission="employee.delete">
          <v-icon @click="remove(item)">mdi-delete</v-icon>
        </Can>
        <Can permission="employee.export">
          <v-icon @click="exportOne(item)">mdi-download</v-icon>
        </Can>
      </template>
    </v-data-table>

    <!-- nút export bulk chỉ HR/Admin thấy -->
    <Can permission="employee.export">
      <v-btn variant="outlined" class="mt-2" @click="exportAll">
        <v-icon>mdi-download</v-icon> Export CSV (all)
      </v-btn>
    </Can>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '@/lib/axios'
import Can from '@/components/Can.vue'

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Họ tên', key: 'user.name' },
  { title: 'Phòng ban', key: 'department.name' },
  { title: 'Chức vụ', key: 'position.name' },
  { title: 'Thao tác', key: 'actions', sortable: false },
]

const employees = ref([])
const loading   = ref(false)

onMounted(async () => {
  loading.value = true
  const res = await api.get('/employees')
  employees.value = res.data.data
  loading.value = false
})

function openCreate() { /* ... */ }
function edit(item: any) { /* ... */ }
function remove(item: any) { /* ... */ }
function exportOne(item: any) { /* ... */ }
function exportAll() { /* ... */ }
</script>
```
