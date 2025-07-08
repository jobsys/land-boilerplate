import route from "ziggy-js"


/**
 * 由于移动端 Vant 注入了 route 方法，导致和 Ziggy 冲突，这里重命名为 appRoute
 **/
export const ZiggyVue = {
	install(app, options) {
		const r = (name, params, absolute, config = options) => route(name, params, absolute, config)
		window.route = r
		app.provide("appRoute", r)
	},
}
