<template>
	<a-layout>
		<a-layout-sider v-model:collapsed="isCollapsed" collapsible class="overflow-auto h-screen !fixed left-0 top-0 bottom-0" width="240">
			<div class="logo">
				<a href="#" class="flex items-center justify-center !bg-[#002140]">
					<img :src="isCollapsed ? miniLogoUrl : logoUrl" />
				</a>
			</div>
			<simplebar style="height: calc(100% - 60px)" :auto-hide="false">
				<a-menu
					v-model:selectedKeys="selectedKeys"
					v-model:openKeys="openKeys"
					:items="menuItems"
					theme="dark"
					mode="inline"
					@click="onClickMenu"
				></a-menu>
			</simplebar>
		</a-layout-sider>

		<a-layout :style="{ marginLeft: isCollapsed ? '80px' : '240px', transition: 'ease all 0.3s', backgroundColor: '#f0f2f5 !important' }">
			<a-layout-header class="!bg-white !p-0 !h-[54px]">
				<div class="relative px-4 h-full flex items-center shadow">
					<div class="basis-4"></div>
					<div class="grow shrink basis-0"></div>
					<div class="flex items-center h-full">
						<a-space-compact block>
							<a-popover placement="bottomRight">
								<a-badge :count="totalUnreadCount">
									<a-button>
										<BellOutlined></BellOutlined>
									</a-button>
								</a-badge>
								<template #content>
									<NotificationBox class="w-[800px]" :use-store="notificationStore"></NotificationBox>
								</template>
							</a-popover>
							<!--<a-dropdown>
								<a-button>
																		<PlusSquareOutlined></PlusSquareOutlined>
								</a-button>
								<template #overlay>
									<a-menu>
										<a-menu-item key="project">
												<Link :href="route('page.manager.maintenance', { mint: 1 })">
													<Component :is="Icons['SnippetsOutlined']"></Component>
													新建工单
												</Link>
											</a-menu-item>
									</a-menu>
								</template>
							</a-dropdown>-->
						</a-space-compact>

						<a-space-compact block class="ml-4">
							<a-tooltip :title="appStore.appRuntime.isCompactMode ? '正常模式' : '紧凑模式'">
								<a-button @click="onToggleCompact">
									<ExpandOutlined v-if="appStore.appRuntime.isCompactMode"></ExpandOutlined>
									<CompressOutlined v-else></CompressOutlined>
								</a-button>
							</a-tooltip>
						</a-space-compact>

						<a-dropdown>
							<div class="h-full px-3 flex items-center cursor-pointer ml-4 hover:bg-gray-100">
								<span class="h-6 w-6 leading-6">
									<img class="h-full w-full rounded-full" :src="profile?.avatar?.url || DefaultAvatar" />
								</span>
								<span class="ml-2 text-nowrap mt-1">{{ profile.nickname || profile.name }}</span>
							</div>

							<template #overlay>
								<a-menu class="w-[120px]">
									<a-menu-item key="0">
										<Link :href="route('page.manager.center.profile')">
											<UserOutlined></UserOutlined>
											个人设置
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
				<div class="breadcrumb-container bg-white px-[16px] py-[10px]">
					<a-breadcrumb>
						<a-breadcrumb-item
							v-for="bread in breads"
							:key="bread.href"
							:href="bread.href"
							@click="() => bread.href && router.visit(bread.href)"
						>
							{{ bread.label }}
						</a-breadcrumb-item>
					</a-breadcrumb>
				</div>
				<div class="main-container relative z-10" :style="{ margin: '16px 16px 60px', overflow: 'initial' }">
					<slot></slot>
				</div>
			</a-layout-content>
			<a-layout-footer
				class="text-center !text-gray-300 font-bold !py-2 !text-[12px] fixed bottom-0 right-0 !bg-transparent z-0"
				:class="[isCollapsed ? 'left-[80px]' : 'left-[240px]']"
			>
				<p class="mb-1">职迅学生工作管理系统 ©版权所属</p>
				<p class="mb-0">技术支持： <a href="https://jobsys.cn" target="_blank" class="text-gray-300 font-bold">职迅科技 JOBSYS.cn</a></p>
			</a-layout-footer>
		</a-layout>
	</a-layout>
</template>
<script setup>
import { Link, router } from "@inertiajs/vue3"
import { computed, inject, ref } from "vue"
import { BellOutlined, CompressOutlined, ExpandOutlined, LockOutlined, LogoutOutlined, UserOutlined } from "@ant-design/icons-vue"
import simplebar from "simplebar-vue"
import DefaultAvatar from "@public/images/default-avatar.png"
import { useAppStore, useNotificationStore, useUserStore } from "@manager/stores"
import NotificationBox from "@modules/Starter/Resources/views/web/components/NotificationBox.vue"
import Icons from "./icons"
import { useLandCustomerAsset } from "@/js/hooks/land"
import { theme } from "ant-design-vue"

const route = inject("route")

const appStore = useAppStore()
const userStore = useUserStore()
const notificationStore = useNotificationStore()
notificationStore.setBriefUrl(route("api.manager.starter.notification.brief"))

const totalUnreadCount = computed(() => notificationStore.totalUnread)

const profile = computed(() => userStore.profile)
const menuItems = computed(() => {
	if (!userStore.menus?.length) {
		return []
	}

	return convertMenus(userStore.menus)
})
const openKeys = ref([]) // 当前展开的菜单
const selectedKeys = ref([]) // 当前选中的菜单
const isCollapsed = ref(false) // 菜单是否折叠
const breads = ref([]) // 面包屑

const miniLogoUrl = useLandCustomerAsset("/images/default/logo-badge-white.png")
const logoUrl = useLandCustomerAsset("/images/default/logo.png")

const convertMenus = (menus) =>
	menus.map((menu) => {
		const menuItem = {
			label: menu.displayName,
			key: menu.key || menu.page,
		}

		if (menu.icon) {
			menuItem.icon = Icons[menu.icon]
		}

		if (menu.page) {
			menuItem.page = menu.page
		}

		if (menu.type) {
			menuItem.type = menu.type
		}

		if (menu.children?.length && !menu.page) {
			//如果有 page 属性，说明会跳转具体页面进行展示，不需要再进行递归
			menuItem.children = convertMenus(menu.children)
		}

		return menuItem
	})
const setupMenu = (currentPage) => {
	breads.value = []
	const currentRoute = route().current()

	//递归查找当前路由对应的菜单，只要有一个匹配，就返回true，否则返回false
	const findRouteInMenu = (items, parentBreads = []) =>
		items.some((item) => {
			if (item.page && (item.page === currentRoute || currentRoute?.startsWith(`${item.page}.`))) {
				// 添加所有父级面包屑（过滤掉type属性不为空的）
				breads.value = [
					...parentBreads.filter((b) => !b.type),
					{
						label: item.label,
						href: route(item.page),
					},
				]

				// 如果当前路由不是直接匹配的页面，添加页面标题
				if (item.page !== currentRoute) {
					breads.value.push({
						label: currentPage.props.pageTitle || "",
					})
				}

				selectedKeys.value = [item.page || item.key]
				return true
			}

			if (item.children?.length) {
				const newParentBreads = [...parentBreads]
				if (!item.type) {
					// 正常的 menu 没有type
					newParentBreads.push({
						label: item.label,
						icon: item.icon,
					})
				}

				if (findRouteInMenu(item.children, newParentBreads)) {
					// 设置当前展开的菜单
					if (item.key || item.page) {
						openKeys.value = [item.key || item.page]
					}
					return true
				}
			}
			return false
		})

	findRouteInMenu(menuItems.value)
}
const onToggleCompact = () => {
	appStore.appSetting.theme.algorithm = appStore.appRuntime.isCompactMode ? theme.defaultAlgorithm : theme.compactAlgorithm
	appStore.appRuntime.isCompactMode = !appStore.appRuntime.isCompactMode
}

const onClickMenu = ({ item }) => {
	if (item.page) {
		router.visit(route(item.page))
	}
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
