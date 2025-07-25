<template>
	<a-page-header class="bg-white! mb-4! hover-card" title="工作台">
		<div class="flex items-center">
			<div class="w-16 h-16 mr-10">
				<img class="h-full w-full rounded-full" :src="profile.avatar?.url || DefaultAvatar" />
			</div>
			<div class="flex-1">
				<div class="text-2xl font-bold mb-3">{{ profile.nickname || profile.name }}，您好</div>
				<div class="text-gray-500">
					<div v-if="departments.length" class="mb-1!">
						<span class="mr-2">部门:</span>
						<a-tag v-for="department in departments" :key="department" color="red">
							{{ department }}
						</a-tag>
					</div>
					<div v-if="roles.length">
						<span class="mr-2">角色:</span>
						<a-tag v-for="role in roles" :key="role" color="green">{{ role }}</a-tag>
					</div>
				</div>
			</div>

			<div class="flex stat-items justify-end gap-10"></div>
		</div>
	</a-page-header>

	<a-row :gutter="16">
		<a-col :sm="24" :md="24" :lg="14">
			<a-card size="small" title="最近项目" class="hover-card" :body-style="{ height: '510px', overflow: 'auto' }"> </a-card>
		</a-col>
		<a-col :sm="24" :md="24" :lg="10">
			<a-row :gutter="16" class="mb-4!">
				<a-col :span="24">
					<a-card size="small" title="常用功能" class="hover-card">
						<template #extra>
							<a-button type="link" :icon="h(EditOutlined)" @click="() => (state.showMenuModal = true)"> 编辑 </a-button>
						</template>
						<a-empty v-if="!dailyFunctions?.length" description="暂无常用功能"></a-empty>

						<a-space v-else wrap>
							<a-button
								v-for="func in dailyFunctions"
								:key="func.key"
								type="default"
								style="width: 140px"
								@click="() => router.visit(route(func.key))"
							>
								{{ func.title }}
							</a-button>
						</a-space>
					</a-card>
				</a-col>
			</a-row>
			<MessageBox :use-store="messageStore" class="hover-card"></MessageBox>
		</a-col>
	</a-row>

	<NewbieModal v-model:visible="state.showMenuModal" title="选择功能">
		<div class="flex flex-col justify-center">
			<div class="max-h-[400px] overflow-auto">
				<a-tree
					:tree-data="state.menusOptions"
					default-expand-all
					v-model:checked-keys="state.dailyFunctionsKeys"
					check-strictly
					checkable
				></a-tree>
			</div>

			<div class="flex justify-center mt-4">
				<NewbieButton :fetcher="state.menusFetcher" type="primary" @click="onSaveDailyMenu">保存</NewbieButton>
			</div>
		</div>
	</NewbieModal>
</template>

<script setup>
import { useMessageStore, useUserStore } from "@manager/stores"
import { EditOutlined } from "@ant-design/icons-vue"
import { computed, h, reactive, inject } from "vue"
import DefaultAvatar from "@public/images/default-avatar.png"
import { router } from "@inertiajs/vue3"
import MessageBox from "@modules/Starter/Resources/views/web/components/MessageBox.vue"
import { cloneDeep } from "lodash-es"
import { useFetch, useFindOptionByValue, useProcessStatusSuccess } from "jobsys-newbie/hooks"

const props = defineProps({
	roles: { type: Array, default: () => [] },
	departments: { type: Array, default: () => [] },
	projects: { type: Array, default: () => [] },
	posts: { type: Array, default: () => [] },
	brief: { type: Object, default: () => ({}) },
	dailyFunctions: { type: Array, default: () => [] },
})

const route = inject("route")

const profile = computed(() => userStore.profile)

const userStore = useUserStore()
const messageStore = useMessageStore()

const menus = computed(() => userStore.menus)

const tidyMenus = (menusOptions) =>
	menusOptions.map((menu) => {
		if (menu.children?.length) {
			return {
				disabled: true,
				key: menu.page || menu.key,
				children: tidyMenus(menu.children),
				title: menu.displayName,
			}
		}

		return {
			key: menu.page || menu.key,
			title: menu.displayName,
		}
	})

const state = reactive({
	showMenuModal: false,
	menusFetcher: {},
	menusOptions: tidyMenus(cloneDeep(menus.value)),
	dailyFunctionsKeys: {
		checked: props.dailyFunctions?.map((func) => func.key) || [],
	},
})

const onSaveDailyMenu = async () => {
	const dailyMenus = state.dailyFunctionsKeys.checked?.map((item) =>
		useFindOptionByValue(state.menusOptions, item, {
			label: "title",
			value: "key",
			children: "children",
		}),
	)

	const res = await useFetch(state.menusFetcher).post(route("api.manager.center.personal-configuration"), {
		key: "daily_functions",
		value: dailyMenus,
	})

	useProcessStatusSuccess(res, () => {
		state.showMenuModal = false
		router.reload({ only: ["dailyFunctions"] })
	})
}
</script>
