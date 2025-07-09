<template>
	<div class="bg-gray-100">
		<a-page-header class="bg-white! mb-4! hover-card" title="工作台">
			<div class="flex items-center">
				<div class="w-16 h-16 mr-10">
					<img class="h-full w-full rounded-full" :src="profile.avatar?.url || DefaultAvatar" />
				</div>
				<div class="flex-1">
					<div class="text-2xl font-bold mb-3">{{ profile.name }}</div>
					<div class="text-gray-500">
						<div v-if="departments.length">
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

				<div class="flex stat-items justify-end">Statistics here!</div>
			</div>
		</a-page-header>

		<a-row :gutter="16">
			<a-col :sm="24" :md="24" :lg="14">
				<a-card title="进行中的项目" size="small" class="hover-card">
					<template #extra>
						<Link>全部项目</Link>
					</template>
					<a-empty v-if="!projects.length" description="暂无进行中的项目"></a-empty>
					<a-card-grid v-else v-for="project in projects" :key="project.id" style="width: 33.33%">
						<Link></Link>
					</a-card-grid>
				</a-card>

				<a-row :gutter="16" class="my-4">
					<a-col :span="12">
						<a-card title="新闻公告" size="small" class="hover-card">
							<template #extra>
								<Link>查看更多</Link>
							</template>

							<a-list :data-source="posts">
								<template #renderItem="{ item }">
									<a-list-item class="cursor-pointer">
										<a-tag color="green">{{ item.group.display_name }}</a-tag>
										<div class="text-ellipsis overflow-hidden truncate">{{ item.title }}</div>
									</a-list-item>
								</template>
							</a-list>
						</a-card>
					</a-col>
					<a-col :span="12">
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
			</a-col>
			<a-col :sm="24" :md="24" :lg="10">
				<NotificationBox :use-store="notificationStore" class="hover-card"></NotificationBox>
			</a-col>
		</a-row>
	</div>
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
import { useNotificationStore, useUserStore } from "@manager/stores"
import { EditOutlined } from "@ant-design/icons-vue"
import { computed, h, inject, reactive } from "vue"
import DefaultAvatar from "@public/images/default-avatar.png"
import { Link, router } from "@inertiajs/vue3"
import NotificationBox from "@modules/Starter/Resources/views/web/components/NotificationBox.vue"
import { cloneDeep } from "lodash-es"
import { useFetch, useFindOptionByValue, useProcessStatusSuccess } from "jobsys-newbie/hooks"

const userStore = useUserStore()
const notificationStore = useNotificationStore()

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

<style lang="less" scoped>
.stat-items {
	width: 700px;

	.stat-item {
		padding: 10px 32px;
		border-right: 1px solid #f0f0f0;

		&:last-child {
			border-right: none;
		}

		.ant-statistic {
			.ant-statistic-content {
				text-align: center;
			}
		}
	}
}

.project-stat {
	.project-name {
		line-height: 20px;
		height: 40px;
		@apply text-ellipsis overflow-hidden;
	}

	.project-meta {
		position: relative;
		display: flex;
		border-right: 1px solid #f0f0f0;
		padding-right: 10px;
		box-sizing: border-box;

		&:last-child {
			padding-right: 0;
			border-right: none;
		}

		.value {
			font-weight: bold;
		}
	}
}
</style>
