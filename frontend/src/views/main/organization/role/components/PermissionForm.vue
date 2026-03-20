<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import BaseSearchBtn from '@/components/BaseSearchBtn.vue'
import { useTableModule } from '@/composables/useTableModule'
import { t } from '@/plugins/vueI18n'
import { usePermissionStore, type RolePermission, type suportedScopes } from '@/stores/permission'


const selectedPermissions = defineModel<RolePermission>({ default: () => ({}) })
const { permissionScopeHeaders } = useTableModule()
const { permissionGroup } = storeToRefs(usePermissionStore())
const { permissionFetch } = usePermissionStore()

const loadingPermission = ref<boolean>(false)
const searchModule = ref<string>('')

onMounted(async () => {
	try {
		loadingPermission.value = true

		await permissionFetch()

		updateSelectedPermisions()
	} catch (error: any) {
		console.log(error)
	} finally {
		loadingPermission.value = false
	}
})

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
		[permissionId]: scope
	}
}
</script>

<template>
	<v-row dense class="ga-3">
		<v-col cols="12" class="text-center" v-if="loadingPermission">
			<v-progress-circular indeterminate></v-progress-circular>
		</v-col>

		<v-col cols="12" v-if="!loadingPermission">
			<!-- Search Module Input -->
			<div class="mt-3 mb-10">
				<base-search-btn
					v-model="searchModule"
					:label="$t('common.filter.permissionModule')"
				></base-search-btn>
			</div>

			<template v-if="!!!permissionGroup">
				<div class="text-center text-body-1">
					({{ $t('common-list.alert.error.getList') }})
				</div>
			</template>

			<v-table
				v-else
				class="elevation-1 border"
				density="comfortable"
				hover
				height="50vh"
				fixed-header
			>
				<thead>
					<tr>
						<th></th>

						<th v-for="header in permissionScopeHeaders" class="text-center">
							<v-tooltip :text="$t('role.tooltip.applyToAll')" location="top">
								<template #activator="{ props }">
									<v-btn
										v-bind="props"
										class="text-center font-weight-bold text-capitalize"
										density="compact"
										variant="text"
										@click.prevent="updateSelectedPermisions(header.key)"
									>
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

							<td
								v-for="header in permissionScopeHeaders"
								:key="header.key"
								class="text-center"
							>
								<v-radio
									v-if="
										permission.supported_scopes.includes(header.key) ||
										header.key === 'NONE'
									"
									:model-value="selectedPermissions[permission.id] === header.key"
									@click.prevent="selectPermisionScope(permission.id, header.key)"
									hide-details
									color="primary"
									class="d-flex justify-center"
								></v-radio>
								<v-icon class="text-center" v-else>mdi-minus-thick</v-icon>
							</td>
						</tr>
					</template>
				</tbody>
			</v-table>
		</v-col>
	</v-row>
</template>
