<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import CONFIG from '@/config/constants'
import { navMenu } from '@/config/menu'

const authStore = useAuthStore()
const user = authStore.user
const avatar = user?.avatar ?? CONFIG.avatar
</script>
<template>
	<v-navigation-drawer mobile-breakpoint="md" v-bind="$attrs">
		<!-- User Info -->
		<v-list>
			<v-list-item :prepend-avatar="avatar">
				<v-list-item-title class="text-capitalize" data-testId="user-name">{{
					user?.name
				}}</v-list-item-title>
				<v-list-item-subtitle data-testId="user-email">{{
					user?.email
				}}</v-list-item-subtitle>
			</v-list-item>
		</v-list>

		<v-divider></v-divider>

		<!-- Navigation Menu -->
		<v-list density="compact" nav>
			<template v-for="item in navMenu" :key="item.value">
				<!-- Group Menu -->
				<v-list-group v-if="item.children" :value="item.value">
					<template v-slot:activator="{ props }">
						<v-list-item
							v-bind="props"
							:title="$t(item.title)"
							:value="item.value"
							:prepend-icon="item.icon"
						></v-list-item>
					</template>

					<!-- Sub Menu -->
					<v-list-item
						v-for="subItem in item.children"
						:prepend-icon="subItem.icon"
						:key="subItem.value"
						:title="$t(subItem.title)"
						:to="{ name: subItem.routeName }"
						:value="subItem.value"
					></v-list-item>
				</v-list-group>

				<!-- Single Menu -->
				<v-list-item
					v-else
					:prepend-icon="item.icon"
					:value="item.value"
					:to="{ name: item?.routeName }"
					:title="$t(item.title)"
					class="text-capitalize"
					data-testId="nav-menu-item"
				>
				</v-list-item>
			</template>
		</v-list>
	</v-navigation-drawer>
</template>

