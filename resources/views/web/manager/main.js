import "./styles/style.css"
import { createApp, h } from "vue"
import { createInertiaApp } from "@inertiajs/vue3"
import { ZiggyVue } from "@/js/plugins/ziggy"
import Newbie from "jobsys-newbie"
import { auth } from "jobsys-newbie/directives"
import dayjs from "dayjs"
import "dayjs/locale/zh-cn"
import { createPinia } from "pinia"
import { http } from "@/js/plugins"
import NewbieApp from "./NewbieApp.vue"
import CommonLayout from "./shared/CommonLayout.vue"
import { useAppConfig } from "./config"
import { useSystemStore, useUserStore } from "@manager/stores"
/*
 * 打印插件
import { hiPrintPlugin } from "vue-plugin-hiprint"
hiPrintPlugin.disAutoConnect()
*/

dayjs.locale("zh-cn")
const pinia = createPinia()

function resolvePage(pageUri) {
	const [pageInfo, layout] = pageUri.split("#")
	const [page, module] = pageInfo.split("@")

	let pages
	let pagePath
	if (!module) {
		pages = import.meta.glob("./views/**/*.vue", { eager: true })
		pagePath = `./views/${page}.vue`
	} else {
		pages = import.meta.glob("/Modules/*/Resources/views/web/**/*.vue", { eager: true })
		pagePath = `/Modules/${module}/Resources/views/web/${page}.vue`
	}

	const resolvedPage = pages[pagePath]
	if (typeof resolvedPage === "undefined") {
		throw new Error(`Page not found: ${pagePath}`)
	}

	if (page.startsWith("Nude")) {
		resolvedPage.default.layout = undefined
	} else if (layout) {
		const layouts = import.meta.glob("./shared/**/*.vue", { eager: true })
		resolvedPage.default.layout = [CommonLayout, layouts[`./shared/${layout}.vue`].default]
	} else {
		resolvedPage.default.layout = CommonLayout
	}

	return resolvedPage
}

createInertiaApp({
	progress: {
		color: useAppConfig("theme.token.colorPrimary"),
		showSpinner: true,
	},
	title: (title) => `${title}`,
	resolve: (name) => resolvePage(name),
	setup({ el, App, props, plugin }) {
		const app = createApp(h(NewbieApp, {}, () => [h(App, props)]))

		app.use(Newbie).use(plugin).use(http).use(pinia)
		// .use(hiPrintPlugin, "$pluginName")

		// 先初始化用户信息再挂载App
		const userStore = useUserStore()
		userStore.init(window.landUserSetup || {})

		app.use(auth, { defaultPermissions: userStore.permissions })

		useSystemStore()

		app.config.globalProperties.$http.get("api/ziggy/manager").then((routes) => {
			app.use(ZiggyVue, routes)
			app.mount(el)
		})
	},
})
