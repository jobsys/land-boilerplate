<template>
	<a-layout>
		<a-layout-sider v-model:collapsed="isCollapsed" collapsible class="overflow-auto h-screen !fixed left-0 top-0 bottom-0" width="240">
			<div class="logo">
				<a href="#" class="flex items-center justify-center !bg-[#002140]">
					<img :src="isCollapsed ? LogoMini : Logo" />
				</a>
			</div>
			<simplebar style="height: calc(100% - 60px)" :auto-hide="false">
				<a-menu v-model:selectedKeys="selectedKeys" v-model:openKeys="openKeys" theme="dark" mode="inline">
					<template v-for="item in menuItems" :key="item.page || item.key">
						<a-sub-menu v-if="item.children && item.children.length" :key="item.page || item.key">
							<template v-if="item.icon" #icon>
								<!--								<NewbieIcon :icon="item.icon"></NewbieIcon>-->
							</template>
							<template #title>{{ item.displayName }}</template>
							<a-menu-item v-for="child in item.children" :key="child.page">
								<Link :href="route(child.page)">
									<template v-if="child.icon">
										<!--										<NewbieIcon :icon="child.icon"></NewbieIcon>-->
									</template>
									<span>{{ child.displayName }}</span>
								</Link>
							</a-menu-item>
						</a-sub-menu>
						<a-menu-item v-else :key="item.page || item.key">
							<Link :href="route(item.page)">
								<template v-if="item.icon">
									<!--									<NewbieIcon :icon="item.icon"></NewbieIcon>-->
								</template>
								<span>{{ item.displayName }}</span>
							</Link>
						</a-menu-item>
					</template>
				</a-menu>
			</simplebar>
		</a-layout-sider>

		<a-layout class="bg-gray-100" :style="{ marginLeft: isCollapsed ? '80px' : '240px', transition: 'ease all 0.3s' }">
			<a-layout-header class="!bg-white !p-0 !h-[64px]">
				<div class="relative px-4 h-full flex items-center shadow">
					<div class="basis-4"></div>
					<div class="grow shrink basis-0"></div>
					<div class="flex items-center h-full">
						<a-popover placement="bottom">
							<span class="h-full leading-[64px] px-3 cursor-pointer hover:bg-gray-100">
								<a-badge :count="totalUnreadCount">
									<BellOutlined style="font-size: 16px; padding: 4px; vertical-align: middle"></BellOutlined>
								</a-badge>
							</span>
							<template #content>
								<NotificationBox class="w-[600px]" :use-store="notificationStore"></NotificationBox>
							</template>
						</a-popover>

						<a-dropdown>
							<span class="h-full leading-[64px] px-3 cursor-pointer hover:bg-gray-100">
								<PlusSquareOutlined style="font-size: 16px; padding: 4px; vertical-align: middle"></PlusSquareOutlined>
							</span>

							<template #overlay>
								<a-menu>
									<!--
									<a-menu-item key="project" v-if="$auth('api.manager.project.edit')">
										<Link :href="route('page.manager.project.edit')">
											<NewbieIcon icon="iconfont-projector-line"></NewbieIcon>
											新增项目
										</Link>
									</a-menu-item>
									-->
								</a-menu>
							</template>
						</a-dropdown>

						<a-dropdown>
							<div class="h-full px-3 flex items-center cursor-pointer hover:bg-gray-100">
								<span class="h-6 w-6 leading-6">
									<img class="h-full w-full rounded-full" :src="profile.avatar.url || DefaultAvatar" />
								</span>
								<span class="ml-2 text-lg">{{ profile.nickname || profile.name }}</span>
							</div>

							<template #overlay>
								<a-menu class="w-[120px]">
									<a-menu-item key="0">
										<Link :href="route('page.manager.center.profile')">
											<UserOutlined></UserOutlined>
											人个设置
										</Link>
									</a-menu-item>
									<a-menu-item key="1">
										<Link :href="route('page.manager.center.password')">
											<LockOutlined></LockOutlined>
											修改密码
										</Link>
									</a-menu-item>
									<a-menu-divider />
									<a-menu-item key="logout">
										<Link :href="route('page.logout')">
											<LogoutOutlined></LogoutOutlined>
											退出登录
										</Link>
									</a-menu-item>
								</a-menu>
							</template>
						</a-dropdown>
					</div>
				</div>
			</a-layout-header>
			<a-layout-content>
				<div class="breadcrumb-container bg-white p-[16px]">
					<a-breadcrumb>
						<a-breadcrumb-item
							v-for="bread in breads"
							:key="bread.href"
							:href="bread.href"
							@click="() => bread.href && router.visit(bread.href)"
						>
							<!--							<NewbieIcon v-if="bread.icon" :icon="bread.icon"></NewbieIcon>-->
							{{ bread.label }}
						</a-breadcrumb-item>
					</a-breadcrumb>
				</div>
				<div class="main-container bg-white p-5 rounded relative z-10" :style="{ margin: '16px 16px 60px', overflow: 'initial' }">
					<slot></slot>
				</div>
			</a-layout-content>
			<a-layout-footer
				class="text-center !text-gray-300 font-bold !py-2 !text-[12px] fixed bottom-0 right-0 !bg-transparent z-0"
				:class="[isCollapsed ? 'left-[80px]' : 'left-[240px]']"
			>
				<p class="mb-1">XXXXXXXX管理系统 ©版权所属：XXXXXXXXX</p>
				<p class="mb-0">技术支持： <a href="https://jobsys.cn" target="_blank" class="text-gray-300 font-bold">职迅科技 JOBSYS.cn</a></p>
			</a-layout-footer>
		</a-layout>
	</a-layout>
</template>
<script setup>
import { router, Link } from "@inertiajs/vue3"
import { computed, inject, ref } from "vue"
import { UserOutlined, LockOutlined, LogoutOutlined, PlusSquareOutlined, BellOutlined } from "@ant-design/icons-vue"
import simplebar from "simplebar-vue"
import Logo from "@public/images/logo.png"
import LogoMini from "@public/images/logo-mini.png"
import DefaultAvatar from "@public/images/default-avatar.png"
import { useNotificationStore, useUserStore } from "@manager/stores"
import { find } from "lodash-es"
import NotificationBox from "@modules/Starter/Resources/views/web/components/NotificationBox.vue"

const route = inject("route")

const userStore = useUserStore()
const notificationStore = useNotificationStore()
notificationStore.setBriefUrl(route("api.manager.starter.notification.brief"))

const totalUnreadCount = computed(() => notificationStore.totalUnread)

const profile = computed(() => userStore.profile)
const menuItems = computed(() => userStore.menus)

const openKeys = ref([]) // 当前展开的菜单
const selectedKeys = ref([]) // 当前选中的菜单
const isCollapsed = ref(false) // 菜单是否折叠
const breads = ref([]) // 面包屑

const setupMenu = (currentPage) => {
	breads.value = []
	const currentRoute = route().current()
	const openFolder = find(menuItems.value, (item) => {
		if (item.page && item.page === currentRoute) {
			breads.value.push({
				label: item.displayName,
				icon: item.icon,
				href: route(item.page),
			})
			selectedKeys.value = [currentRoute]
			return true
		}

		if (item.children && item.children.length) {
			return find(item.children, (child) => {
				if (child.page && (child.page === currentRoute || currentRoute.startsWith(`${child.page}.`))) {
					selectedKeys.value = [child.page || child.key]
					breads.value.push({
						label: item.displayName,
						icon: item.icon,
					})
					breads.value.push({
						label: child.displayName,
						href: route(child.page),
					})
					if (child.page !== currentRoute) {
						breads.value.push({
							label: currentPage.props.pageTitle || "",
						})
					}
					return true
				}
				return false
			})
		}
		return false
	})
	openKeys.value = openFolder ? [openFolder.key || openFolder.page] : []
}

router.on("navigate", (e) => {
	setupMenu(e.detail.page)
})
</script>
<style lang="less">
.logo {
	a {
		padding: 10px;

		img {
			max-width: 150px;
			max-height: 44px;
		}
	}
}
</style>
