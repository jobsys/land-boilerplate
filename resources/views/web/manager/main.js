import { createApp, h } from "vue"
import { createInertiaApp } from "@inertiajs/vue3"
import { ZiggyVue } from "@/js/plugins/ziggy"
import "ant-design-vue/dist/reset.css"
import "jobsys-newbie/dist/style.css"
import "./styles/style.less"
import Newbie from "jobsys-newbie"
import { auth } from "jobsys-newbie/directives"
import dayjs from "dayjs"
import "dayjs/locale/zh-cn"
import useUserStore from "@manager/stores/user"
import { createPinia } from "pinia"
import { http } from "@/js/plugins"
import NewbieApp from "./NewbieApp.vue"
import Layout from "./shared/CommonLayout.vue"
import appConfig from "./config"

dayjs.locale("zh-cn")
const pinia = createPinia()

function resolvePage(pageUri) {
	const [page, module] = pageUri.split("@")

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

	resolvedPage.default.layout = page.startsWith("Nude") ? undefined : Layout

	return resolvedPage
}

createInertiaApp({
	progress: {
		color: appConfig.primaryColor,
	},
	title: (title) => `${title}`,
	resolve: (name) => resolvePage(name),
	setup({ el, App, props, plugin }) {
		const app = createApp(h(NewbieApp, {}, () => [h(App, props)]))

		app.use(Newbie).use(plugin).use(http).use(pinia)

		// 先初始化用户信息再挂载App
		const userStore = useUserStore()
		userStore.init(window.starterInit || {})

		app.use(auth, { defaultPermissions: userStore.permissions })

		app.config.globalProperties.$http.get("api/ziggy/manager").then((routes) => {
			app.use(ZiggyVue, routes)

			app.mount(el)
		})
	},
})
