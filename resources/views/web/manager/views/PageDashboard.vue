<template>
	<div class="m-[-20px] bg-gray-100">
		<a-page-header class="bg-white rounded mb-4" title="工作台">
			<div class="flex items-center">
				<div class="w-16 h-16 mr-10">
					<img class="h-full w-full rounded-full" :src="profile.avatar.url || DefaultAvatar" />
				</div>
				<div class="flex-1">
					<div class="text-2xl font-bold mb-3">{{ profile.name }}</div>
					<div class="text-gray-500">
						<div v-if="departments.length">
							<span class="mr-2">部门:</span>
							<a-tag v-for="department in departments" :key="department.name" color="red">
								{{ department.display_name }}
							</a-tag>
						</div>
						<div v-if="roles.length">
							<span class="mr-2">角色:</span>
							<a-tag v-for="role in roles" :key="role.name" color="green">{{ role.display_name }}</a-tag>
						</div>
					</div>
				</div>

				<div class="flex stat-items justify-end">Statistics here!</div>
			</div>
		</a-page-header>

		<a-row :gutter="16">
			<a-col :sm="24" :md="24" :lg="14">
				<a-card title="进行中的项目">
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
						<a-card title="新闻公告">
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
						<a-card title="常用功能">
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作一</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作二</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作三</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作四</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作五</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作六</a-button>
							<a-button type="text" style="width: 25%; margin-bottom: 10px">操作七</a-button>
							<a-button type="primary" style="width: 25%; margin-bottom: 10px">
								<template #icon>
									<PlusOutlined></PlusOutlined>
								</template>
								添加
							</a-button>
						</a-card>
					</a-col>
				</a-row>
			</a-col>
			<a-col :sm="24" :md="24" :lg="10">
				<NotificationBox :use-store="notificationStore"></NotificationBox>
			</a-col>
		</a-row>
	</div>
</template>

<script setup>
import { useNotificationStore, useUserStore } from "@manager/stores"
import { PlusOutlined } from "@ant-design/icons-vue"
import { computed } from "vue"
import DefaultAvatar from "@public/images/default-avatar.png"
import { Link } from "@inertiajs/vue3"

import NotificationBox from "@modules/Starter/Resources/views/web/components/NotificationBox.vue"

const userStore = useUserStore()
const notificationStore = useNotificationStore()

defineProps({
	roles: { type: Array, default: () => [] },
	departments: { type: Array, default: () => [] },
	projects: { type: Array, default: () => [] },
	posts: { type: Array, default: () => [] },
	brief: { type: Object, default: () => ({}) },
})

const profile = computed(() => userStore.profile)
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
