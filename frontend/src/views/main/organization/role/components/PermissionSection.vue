<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import BaseSearchBtn from '@/components/BaseSearchBtn.vue'
import { useTableModule } from '@/composables/useTableModule'
import { t } from '@/plugins/vueI18n'
import { usePermissionStore, type RolePermission } from '@/stores/permission'
import RetryBtn from '@/components/RetryBtn.vue'
import type { suportedScopes } from '@/types/common'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'
import { useThrottleStore } from '@/stores/throttle'

const emit = defineEmits(['update:selectedPermissions'])

const { permissionScopeHeaders } = useTableModule()
const { permissionGroup } = storeToRefs(usePermissionStore())
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()
const { permissionFetch } = usePermissionStore()

const selectedPermissions = ref<RolePermission>({})
const loadingPermission = ref<boolean>(false)
const searchModule = ref<string>('')
const isError = ref<boolean>(false)

const loadData = async () => {
	if (isDisabled.value('permissionFetch')) return

	try {
		loadingPermission.value = true
		await permissionFetch()
		updateSelectedPermisions()
		isError.value = false
	} catch (error: any) {
		isError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['permissionFetch'] = Number(error.response.headers['retry-after'])
			startThrottle('permissionFetch')
		}
	} finally {
		loadingPermission.value = false
	}
}

const displayedModules = computed(() => {
	if (!permissionGroup.value) return {}

	if (!searchModule.value) return permissionGroup.value

	const searchLowercase = searchModule.value.toLowerCase()

	const result = Object.fromEntries(
		Object.entries(permissionGroup.value).filter(([key]) => {
			const moduleTranslate = t(key).toLowerCase()
			return moduleTranslate.includes(searchLowercase)
		}),
	)

	return result
})

const updateSelectedPermisions = (scope: suportedScopes = 'NONE') => {
	if (!permissionGroup.value) return

	const newSelected = { ...selectedPermissions.value }
	Object.values(permissionGroup.value)
		.flat()
		.forEach((item) => (newSelected[item.id] = scope))

	selectedPermissions.value = newSelected
}

const selectPermisionScope = (permissionId: number, scope: suportedScopes) => {
	selectedPermissions.value = {
		...selectedPermissions.value,
		[permissionId]: scope,
	}
}

onMounted(() => {
	loadData()
	initThrottle('permissionFetch')

	if (isDisabled.value('permissionFetch')) {
		isError.value = true
	}
})

watch(
	selectedPermissions,
	() => {
		emit('update:selectedPermissions', selectedPermissions.value)
	},
	{ deep: true },
)
</script>

<template>
	<v-row dense class="ga-3">
		<v-col cols="12" class="text-center" v-if="loadingPermission">
			<v-progress-circular indeterminate></v-progress-circular>
		</v-col>

		<!-- Error State -->
		<v-col cols="12" class="text-center mt-10" v-else-if="isError">
			<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
				{{ $t('common.error.fetchDataFailed') }}
			</div>
			<retry-btn color="primary" :disabled="isDisabled('permissionFetch')" variant="outlined"
				prepend-icon="mdi-refresh" @click="loadData"></retry-btn>

			<throttle-alert :show="isDisabled('permissionFetch')"
				:time="throttle['permissionFetch'] || 0"></throttle-alert>
		</v-col>

		<!-- Success State: Data loaded -->
		<v-col cols="12" v-else>
			<!-- Search Module Input -->
			<div class="mt-3 mb-10">
				<base-search-btn v-model="searchModule" :label="$t('common.filter.permissionModule')"></base-search-btn>
			</div>

			<v-table class="elevation-1 border" density="comfortable" hover height="50vh" fixed-header>
				<thead>
					<tr>
						<th></th>

						<th v-for="header in permissionScopeHeaders" class="text-center">
							<v-tooltip :text="$t('role.tooltip.applyToAll')" location="top">
								<template #activator="{ props }">
									<v-btn v-bind="props" class="text-center font-weight-bold text-capitalize"
										density="compact" variant="text"
										@click.prevent="updateSelectedPermisions(header.key)">
										{{ header.title }}
									</v-btn>
								</template>
							</v-tooltip>
						</th>
					</tr>
				</thead>

				<tbody>
					<template v-for="(permissions, module) in displayedModules" :key="module">
						<!-- Module Header Row -->
						<tr class="bg-grey-lighten-3">
							<td colspan="5">
								<span class="text-uppercase font-weight-bold">{{
									$t(module)
								}}</span>
							</td>
						</tr>

						<!-- Permissions inside module -->
						<tr v-for="permission in permissions" :key="permission.id">
							<td class="text-body-2 text-grey-darken-3">
								{{ $t(permission.name) }}
							</td>

							<td v-for="header in permissionScopeHeaders" :key="header.key" class="text-center">
								<v-radio v-if="
									permission.supported_scopes.includes(header.key) ||
									header.key === 'NONE'
								" :model-value="selectedPermissions[permission.id] === header.key"
									@click.prevent="selectPermisionScope(permission.id, header.key)" hide-details
									color="primary" class="d-flex justify-center"></v-radio>
								<v-icon class="text-center" v-else>mdi-minus-thick</v-icon>
							</td>
						</tr>
					</template>
				</tbody>
			</v-table>
		</v-col>
	</v-row>
</template>
