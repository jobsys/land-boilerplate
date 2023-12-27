import route from "ziggy-js"

export const ZiggyVue = {
	install(app, options) {
		const r = (name, params, absolute, config = options) => route(name, params, absolute, config)
		app.provide("appRoute", r)
	},
}
